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

# override
$config['run'] = 1;
$config['protect'] = 2; # must be 2 for live data (will not write to the db if 1)
$config['craft'] = 2; # comment out to not use crafted data
$config['debug'] = 1; # script should always run in debug mode ( ui will not be affected )

# see also:
# config/dependancy.php
include($config['include_path'] . 'list/v1/inline/mysql_connect.php');
include($config['include_path'] . 'list/v1/function/main.php');
include($config['include_path'] . 'list/v1/function/member.php');

# error checking
# todo better error checking
if (empty($argv[1]))
	die('error: no start date');
if (empty($argv[2]))
	die('error: no end date');

echo "argv ";
print_r_debug($argv);

$data['run']['after']['channel'] = array();
$data['run']['after']['user'] = array();
# bcycle data structure is totally different from acycle
$data['run']['before']['cycle'] = array();
$data['run']['after']['cycle'] = array();

# alias
$start = & $argv[1];
$end = & $argv[2];
$prefix = & $config['mysql']['prefix'];
$acycle = & $data['run']['after']['cycle'];
$auser = & $data['run']['after']['user'];

echo "info\n\t" . 'continuing with cycles that have renewals' . "\n";
if (1) {
	$sql = '
		select
			rnal.cycle_id
		from
			' . $prefix . 'renewal rnal
		where
			rnal.start >= ' . to_sql($start) . ' and
			rnal.start < ' . to_sql($end) . ' and
			rnal.point_id in (2, 3, 4)
			-- no "1" because can not start a renewal here
		group by
			rnal.cycle_id
	';
	print_debug($sql);
	$result = mysql_query($sql) or die(mysql_error());
	while ($row = mysql_fetch_assoc($result)) {
		$acycle[$row['cycle_id']] = array();
	}
}

if (empty($acycle))
	echo "info\n\tno cycles have renewals\n";
if (!empty($acycle)) {
foreach ($acycle as $k1 => $v1) {
	echo "renewal1 - cycle: $k1\n";
	echo "{\n";
	$channel_parent_id = get_single_channel_parent_id('cycle', $k1);
	get_cycle_array($acycle[$k1], $channel_parent_id, $end);
	# todo remove insert_cycle_next() if it is not necessary here
		# insert_cycle_next($acycle[$k1], $channel_parent_id, $end);
	# todo next cycle should have already been inserted as well
		# get_cycle_next_array($acycle[$k1], $channel_parent_id, $end);
	# renewals
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
	print_debug($sql);
	$result = mysql_query($sql) or die(mysql_error());
	while ($row = mysql_fetch_assoc($result)) {
		$auser[$row['user_id']] = array();
	}
	echo 'foreach user: '; print_r($auser); echo "\n";
	if (empty($auser))
		echo "no after user renewal\n";
	if (!empty($auser)) {
	foreach ($auser as $k2 => $v2) {
		echo "renewal2 - user: $k2\n";
		echo "{{\n";
		get_renewal_array($acycle[$k1], $auser[$k2], $channel_parent_id, $k2);
		get_renewal_next_data($acycle[$k1], $auser[$k2]);
		# echo 'dataprint'; print_r($data); exit;
		# print_r($auser[$k2]); echo "\n";
		if (!empty($auser[$k2]['next']['renewal_id'])) {
			echo "do nothing renewal is already handled (may not happen)\n";
		}
		else {
			if ($config['craft'] == 1) {
				# first version of crafted data
				echo 'crafting $auser[$k2]' . "\n";
				# todo crafting an autorenewal?
				# this get set for each user!
				# todo make it so only a single user is easily targeted in debug mode
				# (may have to set hardcodes in the functions too)
				$auser[$k2] = array(
					'current' => array(
						'renewal_id' => 571,
						'renewal_start' => '2016-01-14 01:54:43',
						'point_id' => 2,
					),
					'previous' => array(
						'renewal_id' => 566,
						'renewal_start' => '2016-01-07 01:54:43',
						'point_id' => 2,
					),
					'next' => array(
						'r2c_second' => 6.8388425925926,
						'r2c_ratio' => 0.023022486772487,
						'r2c_score' => 0,
						'r2c_renewal' => 2.3022486772487,
						'c2r_second' => 0.16115740740741,
						'c2r_ratio' => 0.97697751322751,
						'c2r_score' => 0,
						'c2r_renewal' => 97.697751322751,
						'renewal_start' => '2016-01-21 01:54:43',
					),
				);
			}
			$i1 = $auser[$k2]['current']['point_id'];
			switch($i1) {
				# insert a new entry for continue/nextend
				case '4':
					$i1 = 3; # not autorenewing
				# nobreak;
				case '2':
					get_renewal_accounting_array($auser[$k2], $k2);

					echo 'accounting_start' . "\n";
					print_r($auser[$k2]);
					if (($auser[$k2]['accounting']['resulting_balance'] >= 0))
						insert_renewal_next($acycle[$k1], $auser[$k2], $channel_parent_id, $k2, $i1, $end);
					else {
						echo 'not autorenewing due to insufficient funding' . "\n";
						$sql = '
							update
								' . $prefix . 'renewal
							set
								point_id = 3
							where
								user_id = ' . (int)$k2 . ' and
								renewal_id = ' . (int)$auser[$k2]['current']['renewal_id'] . '
							limit 1
						';
						print_debug($sql);
						mysql_query_process($sql);
					}
					echo 'accounting_end' . "\n";
				break;
				default:
					echo 'no insertion for point_id = ' . (int)$i1 . "\n";
				break;
			}
			# todo fix invalid timeframe logic
			# invalid logic because renewals before the previous renewal are the ones that are past
			# not taking into account before timeframe (previous cycle may not have happened yet)
			if (1) {
				print_r($auser[$k2]); echo "\n";
				# set previous renewal to current (runs 1 day ahead)
				$sql = '
					update
						' . $prefix . 'renewal
					set
						timeframe_id = 1
					where
						id = ' . (int)$auser[$k2]['previous']['renewal_id']
				;
				print_debug($sql);
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
					print_debug($sql);
					mysql_query_process($sql);
				}
				# next renewal was already set to future by insert_renewal_next()
			}
		}
		unset($auser[$k2]);
		echo "}}\n";
	} }
	unset($acycle[$k1]);
	echo "}\n";
} }
unset($acycle);
unset($auser);
