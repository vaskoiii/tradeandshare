<?
# author: vaskoiii
# description: process the autorenewing cycles and renewals together ( first implementation will be daily cron )
# warning: changing a timeframe_id does not count as a modification

# todo allow script to enable debugging such that the database content will not be modified
$cfg['debug'] = 1;

# $bchannel/$cchannel/$achannel ???
# vs
# $pchannel/$cchannel/$nchannel ???

# todo script will run in the background so debug messages may be fine

# issue
# - doesn't account for credit
# - doesn't account for score value
# - the frequency with which this script (and periodic renewal) run may be the limiting factor in how fine grained the renewals can happen. ie) if it runs daily cycles may only be able account for yyyy-mm-dd and not hh:mm:ss (Y-m-d H-i-s)
# - script dependancies are different from normal dependancies because they do not have access to .htaccess
# - member function calls are not optimized ie) get_cycle_array() make 3 function calls

# todo script to reset timeframe_id - if run on every page load there will never be a delay in the correct timeframe
# todo use locking on renewals during script run - if data can change while this script is being run this could be problematic
# todo eliminade delay in marking current cycles by remembering the last script run time and updating accordingly on each page load.
# todo charge users the day before the renewal begins
# todo factor in available funds
# todo add limits on the shortness of an interval because buffer time will be needed for the autorenew script to run

# see also:
# config/dependancy.php

# config
$script['include_path'] = '/www/site/list/include/list/v1/';

# dependancy
include($script['include_path'] . 'config/preset.php');
include($script['include_path'] . 'inline/mysql_connect.php');
include($script['include_path'] . 'function/main.php');
include($script['include_path'] . 'function/member.php');

# var
$data['run']['datetime'] = get_run_datetime_array();
# to debug uncomment and hardcode these dates with something that matches the db!
# also make sure debug is set in the config
if (1) {
	$data['run']['datetime'] = array(
		'previous' => '2015-05-30 00:00:00',
		'current' => '2015-06-01 00:00:00',
		'next' => '2015-06-02 00:00:00',
	);
}

echo "rdatetime\n";
echo "{\n";
print_r($data['run']['datetime']);
echo "}\n";

$data['run']['after']['channel'] = array();
$data['run']['after']['user'] = array();
# bcycle data structure is totally different from acycle
$data['run']['before']['cycle'] = array();
$data['run']['after']['cycle'] = array();

# alias
$rdatetime = & $data['run']['datetime'];
$prefix = & $config['mysql']['prefix'];
$bcycle = & $data['run']['before']['cycle'];
$achannel = & $data['run']['after']['channel'];
$acycle = & $data['run']['after']['cycle'];
$auser = & $data['run']['after']['user'];

echo "bcycle\n";
echo "{\n";
# run first so that a future cycle is not inserted
if (1) {
	$sql = '
		select
			id as cycle_id
		from
			' . $prefix . 'cycle
		where
			start >= ' . to_sql($rdatetime['previous']) . ' and
			start < ' . to_sql($rdatetime['current'])
	;
	$result = mysql_query($sql) or die(mysql_error());
	echo "$sql\n";
	while ($row = mysql_fetch_assoc($result)) {
		$bcycle['all'][$row['cycle_id']] = $row['cycle_id'];
	}
}
# end cycles with no renewals
if (!empty($bcycle['all'])) {
	# prep
	$sql = '
		select
			rnal.cycle_id
		from
			' . $prefix . 'renewal rnal
		where
			rnal.start >= ' . to_sql($rdatetime['previous']) . ' and
			rnal.start < ' . to_sql($rdatetime['current']) . ' and
			rnal.cycle_id in (' . implode(', ', $bcycle['all']) . ') 
		group by
			rnal.cycle_id
	';
	print_debug($sql);
	$result = mysql_query($sql) or die(mysql_error());
	while ($row = mysql_fetch_assoc($result)) {
		$bcycle['continue'][$row['cycle_id']] = $row['cycle_id'];
	}
	# if no renewals then it is an ending cycle
	if (!empty($bcycle['continue']))
	foreach ($bcycle['continue'] as $k1 => $v1)
		if (!in_array($k1, $bcycle['all']))
			$bcycle['end'][$k1] = $k1;
	# todo ended cycle is timeframe_id = 2 (present) or 1 (past)? add to sql:
	# todo what happens if there was a renewal today (before this script ran) but no renewals yesterday?
	if (!empty($bcycle['end'])) {
		$sql = '
			update
				' . $prefix . 'cycle
			set
				point_id = 3
			where
				id in (' . implode(', ', $bcycle['end']) . ')
		';
		print_debug($sql);
		if ($cfg['debug'] != 1)
			mysql_query($sql) or die(mysql_error());
		# remove furture cycles if they were already inserted (safety)
		foreach ($bcycle['end'] as $k1 => $v1) {
			$i1 = get_single_channel_parent_id('cycle', $k1);
			if (!empty($i1)) {
				$sql = '
					select
						cce.id as cycle_id
					from
						' . $prefix . 'cycle cce,
						' . $prefix . 'cycle cnl
					where
						cce.channel_id = cnl.id and
						cce.start >= ' . $rdatetime['current'] . ' and
						cnl.parent_id = ' . (int)$i1
				;
				$result = mysql_query($sql) or die(mysql_error());
				while ($row = mysql_fetch_assoc($result)) {
					$a1[$cycle_id] = $row['cycle_id'];
				}
			}
			$sql = '
				delete from 
					' . $prefix . 'cycle
				where
					id in (' . implode(', ', $a1['cycle_id']) . ')
			';
			print_debug($sql);
			mysql_query($sql) or die(mysql_error());
		}
	}
}
unset($bcycle);
echo "}\n";

echo "acycle\n";
echo "{\n";
# get all cycles for "next" (indirectly obtained from channel)
if (1) {
	$sql = '
		select
			cce.id as cycle_id,
			cnl.parent_id as channel_parent_id
		from
			' . $prefix . 'cycle cce,
			' . $prefix . 'channel cnl

		where
			cce.channel_id = cnl.id and
			start >= ' . to_sql($rdatetime['current']) . ' and
			start < ' . to_sql($rdatetime['next']) . '
	';
	print_debug($sql);
	$result = mysql_query($sql) or die(mysql_error());
	while ($row = mysql_fetch_assoc($result)) {
		$achannel[$row['channel_parent_id']] = array();
	}
	if (empty($achannel))
		echo 'cant autorenew anything - future cycles DNE - please set manually' . "\n";
	foreach ($achannel as $k1 => $v1) {
		# overwrite each time
		get_cycle_array($achannel[$k1], $k1, $rdatetime['next']); 
		insert_cycle_next($achannel[$k1], $k1, $rdatetime['next']);
		# todo make previous cycles for those channels updated above past
		# incorrect logic because cycles before the previous cycle are the ones that are past
		if (0)
		if (!empty($achannel[$k1]['previous']['cycle_id'])) {
			$sql = '
				update
					' . $prefix . 'cycle
				set
					timeframe_id = 1
				where
					id = ' . (int)$achannel[$k1]['previous']['cycle_id']
			;		
			print_debug($sql);
			if ($cfg['debug'] != 1)
				mysql_query($sql) or die(mysql_error());
		}
		unset($achannel[$k1]);
		# print_r($achannel[$k1]); echo "\n";
	}
	# not really a "modification" only changing a marker/flag
	$sql = '
		update
			' . $prefix . 'cycle
		set
			timeframe_id = 2
		where
			start >= ' . to_sql($rdatetime['previous']) . ' and
			start < ' . to_sql($rdatetime['current']) . ' and
			point_id != 3
	';
	print_debug($sql);
	if ($cfg['debug'] != 1)
		mysql_query($sql) or die(mysql_error());
}
unset($achannel);
echo "}\n";

echo 'continuing with cycles that have renewals' . "\n";
if (1) {
	$sql = '
		select
			rnal.cycle_id
		from
			' . $prefix . 'renewal rnal
		where
			rnal.start >= ' . to_sql($rdatetime['current']) . ' and
			rnal.start < ' . to_sql($rdatetime['next']) . ' and
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
# hardcode for debug
# $acycle[157] = array();
# print_r($acycle); echo "\n";

if (empty($acycle))
	echo "no cycles have renewals\n";
if (!empty($acycle)) {
foreach ($acycle as $k1 => $v1) {
	echo "renewal1 - cycle: $k1\n";
	echo "{\n";
	$channel_parent_id = get_single_channel_parent_id('cycle', $k1);
	get_cycle_array($acycle[$k1], $channel_parent_id, $rdatetime['next']);
	insert_cycle_next($acycle[$k1], $channel_parent_id, $rdatetime['next']);
	get_cycle_next_array($acycle[$k1], $channel_parent_id, $rdatetime['next']);
	# renewals
	$sql = '
		select
			rnal.user_id
		from
			' . $prefix . 'renewal rnal
		where
			rnal.start >= ' . to_sql($rdatetime['current']) . ' and
			rnal.start < ' . to_sql($rdatetime['next']) . ' and
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
	# hardcode for debug
	# $auser[150] = array();
	echo 'foreach user: '; print_r($auser); echo "\n";
	if (empty($auser))
		echo "no after user renewal\n";
	if (!empty($auser)) {
	foreach ($auser as $k2 => $v2) {
		echo "renewal2 - user: $k2\n";
		echo "{{\n";
		get_renewal_array($acycle[$k1], $auser[$k2], $channel_parent_id, $k2);
		get_renewal_next_data($acycle[$k1], $auser[$k2]);
		# print_r($auser[$k2]); echo "\n";
		if (!empty($auser[$k2]['next']['renewal_id'])) {
			echo "do nothing renewal is already handled (may not happen)\n";
		}
		else {
			$i1 = $auser[$k2]['current']['point_id'];
			switch($i1) {
				# insert a new entry for continue/nextend
				case '4':
					$i1 = 3; # not autorenewing
				# nobreak;
				case '2':
					insert_renewal_next($acycle[$k1], $auser[$k2], $channel_parent_id, $k2, $i1, $rdatetime['next']);
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
				if ($cfg['debug'] != 1)
					mysql_query($sql) or die(mysql_error());
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
					if ($cfg['debug'] != 1)
						mysql_query($sql) or die(mysql_error());
				}
				# next renewal was already set to future by marked by insert_renewal_next()
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

# make all renewal that started since last run current

# print_r($acycle[$k1]); echo "\n";
exit;
