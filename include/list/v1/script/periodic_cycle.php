<?
# author: vaskoiii
# description: process the autorenewing cycles for "tomorrow" as Y-m-d ( run with daily cron before renewals )

# issue
# - member function calls are not optimized ie) get_cycle_array() make 3 function calls

# config
# needs the magic variable for cron
require(__DIR__ . '/../config/preset.php');

# override
$config['write_protect'] = 2; # must be 2 for live data (will not write to the db if 1)
$config['craft'] = 2; # comment out to not use crafted data
$config['debug'] = 1; # script should always run in debug mode ( ui will not be affected )

# see also:
# config/dependancy.php
include($config['include_path'] . 'list/v1/inline/mysql_connect.php');
include($config['include_path'] . 'list/v1/function/main.php');
include($config['include_path'] . 'list/v1/function/member.php');

# var
if ($config['craft'] == 1)
	$data['run']['datetime'] = array(
		'previous' => '2015-05-30 00:00:00',
		'current' => '2015-06-01 00:00:00',
		'next' => '2015-06-02 00:00:00',
		'horizon' => '2015-06-03 00:00:00',
	);
else
	$data['run']['datetime'] = get_run_datetime_array();

echo "\n";
echo "cycle\n";
echo "-------\n";
echo "\n";

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

echo "bcycle\n";
echo "{\n";
# get all cycles to check for no renewals
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
	print_debug($sql);
	$result = mysql_query($sql) or die(mysql_error());
	while ($row = mysql_fetch_assoc($result)) {
		$bcycle['all'][$row['cycle_id']] = $row['cycle_id'];
	}
}
if (!empty($bcycle['all'])) {
	# get cycles with renewals
	$bcycle['continue'] = array();
	$sql = '
		select
			rnal.cycle_id
		from
			' . $prefix . 'renewal rnal,
			' . $prefix . 'cycle cce
		where
			cce.id = rnal.cycle_id and
			cce.start >= ' . to_sql($rdatetime['previous']) . ' and
			cce.start < ' . to_sql($rdatetime['current']) . ' and
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
	foreach ($bcycle['all'] as $k1 => $v1)
		if (!in_array($k1, $bcycle['continue']))
			$bcycle['end'][$k1] = $k1;
	# end cycles if no renewals
	if (!empty($bcycle['end'])) {
		$sql = '
			update
				' . $prefix . 'cycle
			set
				point_id = 3,
				timeframe_id = 2
			where
				id in (' . implode(', ', $bcycle['end']) . ')
		';
		print_debug($sql);
		if ($config['write_protect'] != 1)
			mysql_query($sql) or die(mysql_error());
		# (should not have to delete cycles ever)
	}
}
unset($bcycle);
echo "}\n";

echo "acycle\n";
echo "{\n";
# get cycles for "tomorrow" (00:00:00 to 23:59:59)
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
			cce.start >= ' . to_sql($rdatetime['next']) . ' and
			cce.start < ' . to_sql($rdatetime['horizon']) . '
	';
	print_debug($sql);
	$result = mysql_query($sql) or die(mysql_error());
	while ($row = mysql_fetch_assoc($result)) {
		$achannel[$row['channel_parent_id']] = array();
	}
}
if (empty($achannel))
	echo 'no cycle points found for tomorrow' . "\n";
else {
	# not really a "modification" only changing a marker/flag (and doing it early)
	$sql = '
		update
			' . $prefix . 'cycle
		set
			timeframe_id = 2
		where
			start >= ' . to_sql($rdatetime['next']) . ' and
			start < ' . to_sql($rdatetime['horizon']) . ' and
			point_id != 3
	';
	print_debug($sql);
	if ($config['write_protect'] != 1)
		mysql_query($sql) or die(mysql_error());

	foreach ($achannel as $k1 => $v1) {
		get_cycle_array($achannel[$k1], $k1, $rdatetime['next']); 
		# todo make it so that a new cycle is not inserted if there are no renewals
		insert_cycle_next($achannel[$k1], $k1, $rdatetime['next']);
		# todo verify cycles are updating correctly
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
			if ($config['write_protect'] != 1)
				mysql_query($sql) or die(mysql_error());
		}
		unset($achannel[$k1]);
	}
}
unset($acycle);
unset($achannel);
echo "}\n";

exit;
