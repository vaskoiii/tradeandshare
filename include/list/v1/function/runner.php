<?
# author: vaskoiii
# description: functions for cron, command line, or other scheduled tasks


function periodic_script_getopt() {
	$a1o  = array(
		'gte:', # greater than equal to datetime
		'lt:', # less than datetime
	);
	$a1s = array(
		'l:', # debug level
		'w', # write (dry run if not set)
	);
	$s1o = implode('', $a1s); 
	return getopt($s1o, $a1o);
}

function periodic_script_setup($getopt) {
	# get opt was causing proplems when everything wasn't treated with getopt syntax
	# broke ie) php cycle.php 2015-11-04 2015-11-05 -l1 -w
	global $config;
	global $argv;

	# override
	$config['run'] = 1;
	$config['protect'] = 1; # must be 2 for live data (will not write to the db if 1)
	$config['debug'] = 1; # script should always run in debug mode ( ui will not be affected )

	# error checking
	# todo better error checking
	if (empty($argv[0]))
		die("error\n\twhat script is running?\n");
	$a2 = explode('/', $argv[0]);
	$script = $a2[count($a2) - 1];

	switch($script) {
		case 'cycle.php':
		case 'renewal.php':
		case 'sponsor.php':
			# can not run without specifying the --gte and --lt
			if (empty($getopt['gte']))
				die("error\n\tno start date\n");
			if (empty($getopt['lt']))
				die("error\n\tno end date\n");
		# nobreak;
		case 'all.php':
			if (!empty($getopt['gte']))
			if (!empty($getopt['lg']))
			if ($getopt['gte'] >= $getopt['lt'])
				die("error\n\tend must be after start\n");
			if (isset($getopt['w']))
				$config['protect'] = 2;
			if (!empty($getopt['l']))
			switch ($getopt['l']) {
				case '1':
				case '2':
				case '3':
					$config['debug_level'] = $getopt['l'];
				break;
				default:
					die("error\n\tinvalid value for option -l\n");
				break;
			}
		break;
		default:
			die("error\n\tinvalid script\n");
		break;
	}
	echo 'getopt ';
	print_r_debug($getopt);
}

function insert_runner_modified($i1, $dt1) {
	global $config;
	$sql = '
		insert into
			' . $config['mysql']['prefix'] . 'runner
		set
			start = ' . to_sql($dt1) . ',
			modified = now(),
			script_id = ' . (int)$i1
	;
	print_debug($sql);
	mysql_query_process($sql);
}

function get_runner_modified_now() {
	return date('Y-m-d H:i:s');
}

function get_runner_modified_previous($i1, $dt1) {
	global $config;
	# make sure now() has the same value in every instance
	# https://dev.mysql.com/doc/refman/5.5/en/date-and-time-functions.html#function_now
	$s1 = '';
	$sql = '
		select
			start as previous
		from
			' . $config['mysql']['prefix'] . 'runner
		where
			script_id = ' . (int)$i1 . ' and
			start < ' . to_sql($dt1) . '
		order by
			start desc
		limit
			1
	';
	print_debug($sql, 3);
	$result = mysql_query($sql) or die(mysql_error());
	while ($row = mysql_fetch_assoc($result)) {
		$s1 = $row['previous'];
	}
	return $s1;
}

function get_runner_modified_daydiff($current, $previous) {
	return (
		(
			strtotime($current) -
			strtotime($previous)
		) / 86400
	);
}

function get_runner_chunk_array($current, $previous) {
	$i1 = 0;
	$a1 = array();
	$i1daydiff = get_runner_modified_daydiff($current, $previous);
	# arbitrary constant for 1 year to prevent infinite loop
	while ($i1 < $i1daydiff && $i1 < 365) {
		$f1 = $i1daydiff - $i1;
		# end
		if ($f1 > 1)
			$a1[$i1]['end'] = date(
				'Y-m-d H:i:s',
				strtotime($previous) + (($i1 + 1) * 86400)
			); 
		else
			$a1[$i1]['end'] = $current;
		# start
		if (1)
			$a1[$i1]['start'] = date(
				'Y-m-d H:i:s',
				strtotime($previous) + ($i1 * 86400)
			); 
		$i1++;
	}
	return $a1;
}
