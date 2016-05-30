<?
# author: vaskoiii
# description: functions for cron, command line, or other scheduled tasks

function insert_runner_modified($i1, $dt1) {
	global $config;
	$sql = '
		insert into
			' . $config['mysql']['prefix'] . 'runner
		set
			modified = ' . to_sql($dt1) . ',
			script_id = ' . (int)$i1
	;
	print_debug($sql);
	mysql_query_process($sql);
}

function get_runner_modified_current() {
	return date('Y-m-d H:i:s');
}

function get_runner_modified_previous($i1, $dt1) {
	global $config;
	# make sure now() has the same value in every instance
	# https://dev.mysql.com/doc/refman/5.5/en/date-and-time-functions.html#function_now
	$s1 = '';
	$sql = '
		select
			modified as previous
		from
			' . $config['mysql']['prefix'] . 'runner
		where
			script_id = ' . (int)$i1 . ' and
			modified < ' . to_sql($dt1) . '
		order by
			modified desc
		limit
			1
	';
	$result = mysql_query($sql) or die(mysql_error());
	while ($row = mysql_fetch_assoc($result)) {
		$s1 = $row['previous'];
	}
	if (empty($s1)) {
		insert_runner_modified($i1, $dt1);
		die("info\n\tfirst run so no need for further processing\n");
		# todo better way to handle this abort? is it ok?
	}
	return $s1;
}

function get_runner_modified_daydiff($previous, $current) {
	return (
		(
			strtotime($current) -
			strtotime($previous)
		) / 86400
	);
}

function get_runner_chunk_array($previous, $current) {
	$i1 = 0;
	$a1 = array();
	$i1daydiff = (int)get_runner_modified_daydiff($previous, $current);
	# arbitrary constant for 1 year to prevent infinate loop
	while ($i1 <= $i1daydiff && $i1 < 365) {
		$a1['chunk'][$i1]['start'] = date(
			'Y-m-d H:i:s',
			strtotime($previous) + ($i1 * 86400)
		); 
		if (($i1daydiff - $i1) > 0)
			$a1['chunk'][$i1]['end'] = date(
				'Y-m-d H:i:s',
				strtotime($previous) + (($i1 + 1) * 86400)
			); 
		else
			$a1['chunk'][$i1]['end'] = $current;
		$i1++;
	}
	return $a1['chunk'];
}
