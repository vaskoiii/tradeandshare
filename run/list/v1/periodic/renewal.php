<?
# author: vaskoiii
# description: process the autorenewing cycles and renewals together for the void as needed

# issue
# - script dependancies are different from normal dependancies because they do not have access to .htaccess
# - member function calls are not optimized ie) get_cycle_array() make 3 function calls

# todo use locking on renewals during script run - if data can change while this script is being run this could be problematic

# header
echo "renewal\n";
echo "= = = = = = = = \n";

# config
# needs the magic variable for cron
require __DIR__ . '/../../../../include/list/v1/config/preset.php';

# see also:
# config/dependancy.php
include($config['include_path'] . 'list/v1/inline/mysql_connect.php');
include($config['include_path'] . 'list/v1/function/main.php');
include($config['include_path'] . 'list/v1/function/member.php');
include($config['include_path'] . 'list/v1/function/runner.php');

$data['run']['after']['channel'] = array();
$data['run']['after']['user'] = array();
# bcycle data structure is totally different from acycle
$data['run']['before']['cycle'] = array();
$data['run']['after']['cycle'] = array();

# prerun
$data['getopt'] = periodic_script_getopt();
periodic_script_setup($data['getopt']);

# alias
$start = $data['getopt']['gte'];
$end = $data['getopt']['lt'];
$prefix = & $config['mysql']['prefix'];
$acycle = & $data['run']['after']['cycle'];
$auser = & $data['run']['after']['user'];
$achannel = & $data['run']['after']['channel'];

echo "info\n\t" . 'continuing with cycles that have renewals' . "\n";
if (1) {
	$sql = '
		select
			cnl.parent_id as channel_parent_id,
			rnal.cycle_id
		from
			' . $prefix . 'channel cnl,
			' . $prefix . 'cycle cce, 
			' . $prefix . 'renewal rnal
		where
			cce.id = rnal.cycle_id and
			cnl.id = cce.channel_id and
			rnal.start >= ' . to_sql($start) . ' and
			rnal.start < ' . to_sql($end) . ' and
			rnal.point_id in (2, 3, 4)
			-- no "1" because can not start a renewal here
		group by
			cnl.parent_id,
			rnal.cycle_id
		order by
			cnl.parent_id desc,
			rnal.cycle_id desc
	';
	print_debug($sql, 3);
	$result = mysql_query($sql) or die(mysql_error());
	while ($row = mysql_fetch_assoc($result)) {
		$acycle[$row['cycle_id']] = array(
			'info' => array(
				'channel_parent_id' => $row['channel_parent_id'],
			),
		);
	}
}

if (empty($acycle))
	echo "info\n\tno cycles have renewals\n";
if (!empty($acycle)) {
foreach ($acycle as $k1 => $v1) {
	# $channel_parent_id = get_single_channel_parent_id('cycle', $k1);
	$channel_parent_id = $v1['info']['channel_parent_id'];
	echo "channel: $channel_parent_id\n";
	echo "=  =  =  =  =  =\n";
	echo 'cycle ';
	get_cycle_array($acycle[$k1], $channel_parent_id, $end);

	# todo 
	if (empty($acycle[$k1]['next']['cycle_id']))
		die("error\n\tnext cycle must be inserted before continuing\n");

	# todo remove insert_cycle_next() if it is not necessary here
		# insert_cycle_next($acycle[$k1], $channel_parent_id, $end);
	# todo next cycle should have already been inserted as well
		# get_cycle_next_array($acycle[$k1], $channel_parent_id, $end);
	print_r_debug($acycle[$k1]);
	# renewals
	# remember can be multiple renewals per cycle ie) start and end
	$sql = '
		select
			rnal.user_id
		from
			' . $prefix . 'renewal rnal
		where
			rnal.start >= ' . to_sql($start) . ' and
			rnal.start < ' . to_sql($end) . ' and
			rnal.point_id in (2, 3, 4) and
			rnal.cycle_id = ' . (int)$k1 . '
			-- no "1" because can not start a renewal here
		group by
			rnal.user_id
	';
	print_debug($sql, 3);
	$result = mysql_query($sql) or die(mysql_error());
	while ($row = mysql_fetch_assoc($result)) {
		$auser[$row['user_id']] = array();
	}
	echo "user ";
	print_r_debug($auser);
	if (empty($auser))
		echo "no after user renewal\n";
	if (!empty($auser)) {
	foreach ($auser as $k2 => $v2) {
		echo "user: $k2\n";
		echo "----------------\n";
		get_renewal_array($acycle[$k1], $auser[$k2], $channel_parent_id, $k2);
		echo 'crenewal ';
		print_r_debug($auser[$k2]['current']);
		echo 'prenewal ';
		print_r_debug($auser[$k2]['previous']);

		get_renewal_next_data($acycle[$k1], $auser[$k2]);
		# todo different logic needed if $config['protect'] == 1

		$b1 = 2;
		if ($config['protect'] == 1)
			$b1 = 1;
		if (empty($auser[$k2]['next']['renewal_id']))
			$b1 = 1;
		if ($b1 == 2)
			echo "info\n\tdo nothing renewal is already handled (may not happen)\n";
		else {
			$i1 = $auser[$k2]['current']['point_id'];
			switch($i1) {
				# insert a new entry for continue/nextend
				case '4':
					$i1 = 3; # not autorenewing
				# nobreak;
				case '2':
					get_renewal_accounting_array($auser[$k2], $k2);

					echo "accounting\n";
					echo "- - - - - - - - \n";
					print_r_debug($auser[$k2]['accounting']);
					if (($auser[$k2]['accounting']['resulting_balance'] >= 0))
						insert_renewal_next($acycle[$k1], $auser[$k2], $channel_parent_id, $k2, $i1, $end);
					else {
						echo "info\n\tnot autorenewing due to insufficient funding\n";
						$sql = '
							update
								' . $prefix . 'renewal
							set
								point_id = 3
							where
								user_id = ' . (int)$k2 . ' and
								id = ' . (int)$auser[$k2]['current']['renewal_id'] . '
							limit 1
						';
						print_debug($sql);
						mysql_query_process($sql);
					}
				break;
				default:
					echo "info\n\tno insertion for point_id = " . (int)$i1 . "\n";
				break;
			}
			# todo fix invalid timeframe logic
			# invalid logic because renewals before the previous renewal are the ones that are past
			# not taking into account before timeframe (previous cycle may not have happened yet)
			if (1) {
				# set previous renewal to current
				$sql = '
					update
						' . $prefix . 'renewal
					set
						timeframe_id = 1
					where
						id = ' . (int)$auser[$k2]['previous']['renewal_id']
				;
				print_debug($sql, 3);
				mysql_query_process($sql);
				if (1) {
					# set current renewal to present
					$sql = '
						update 
							' . $prefix . 'renewal
						set
							timeframe_id = 2
						where
							id = ' . (int)$auser[$k2]['current']['renewal_id'] . '
					';
					print_debug($sql, 3);
					mysql_query_process($sql);
				}
				# next renewal was already set to future by insert_renewal_next()
			}
		}
		unset($auser[$k2]);
	} }
	unset($acycle[$k1]);
} }
unset($acycle);
unset($auser);
