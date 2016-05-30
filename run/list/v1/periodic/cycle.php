<?
# author: vaskoiii
# description: insert the future cycles based on the void - intended to be a part of periodic_all.php

# issue
# - member function calls are not optimized ie) get_cycle_array() make 3 function calls

# header
echo "cycle\n";
echo "= = = = = = = = \n";

# config
# needs the magic variable for cron
require __DIR__ . '/../../../../include/list/v1/config/preset.php';

# override
# todo take in protect as an argument
$config['run'] = 1;
$config['protect'] = 1; # must be 2 for live data (will not write to the db if 1)
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
	die("error\n\tno start date\n");
if (empty($argv[2]))
	die("error\n\tno end date\n");

# var
$data['run']['after']['channel'] = array();
$data['run']['after']['user'] = array();
# bcycle data structure is totally different from acycle
$data['run']['before']['cycle'] = array();
$data['run']['after']['cycle'] = array();

# alias
$start = & $argv[1];
$end = & $argv[2];

$prefix = & $config['mysql']['prefix'];
$bcycle = & $data['run']['before']['cycle'];
$achannel = & $data['run']['after']['channel'];
$acycle = & $data['run']['after']['cycle'];

# do it
echo "argv ";
print_r_debug($argv);


# todo allow cycles to end (ie. if no renewals)
# currently disabled to keep it simple
if (0) {
echo "bcycle\n";
echo "{\n";
if (1) {
	$sql = '
		select
			id as cycle_id
		from
			' . $prefix . 'cycle
		where
			start >= ' . to_sql('???') . ' and
			start < ' . to_sql('???')
	;
	print_debug($sql, 2);
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
			cce.start >= ' . to_sql('???') . ' and
			cce.start < ' . to_sql('???') . ' and
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
	# todo make sure cycles can be restarted as well!
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
		print_debug($sql, 2);
		mysql_query_process($sql);
		# (should not have to delete cycles ever)
	}
}
unset($bcycle);
echo "}\n";
}

echo "acycle\n";
echo "----------------\n";
# get cycles for insert
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
			cce.start >= ' . to_sql($start) . ' and
			cce.start < ' . to_sql($end) . '
	';
	print_debug($sql, 2);
	$result = mysql_query($sql) or die(mysql_error());
	while ($row = mysql_fetch_assoc($result)) {
		$achannel[$row['channel_parent_id']] = array();
	}
}
if (empty($achannel))
	die("info\n\tno cycle points found for insert\n");
else {
	# not really a "modification" only changing a marker/flag
	$sql = '
		update
			' . $prefix . 'cycle
		set
			timeframe_id = 2
		where
			start >= ' . to_sql($start) . ' and
			start < ' . to_sql($end) . ' and
			point_id != 3
	';
	print_debug($sql, 2);
	mysql_query_process($sql);

	foreach ($achannel as $k1 => $v1) {
		get_cycle_array($achannel[$k1], $k1, $end); 
		# todo make it so that a new cycle is not inserted if there are no renewals
		insert_cycle_next($achannel[$k1], $k1, $end);
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
			print_debug($sql, 2);
			mysql_query_process($sql);
		}
		unset($achannel[$k1]);
	}
}
unset($acycle);
unset($achannel);
