<?
# author: vaskoiii
# description: process the autorenewing cycles then renewals then sponsors (in that order) since the last run time ensurigin that there is exactly 1 of each for the corresponding entity in the future if applicable - run intervals can not be longer than the shortest cycle (currently intervals must be less than 1 day)

# todo possible to revert? ie. linked table for what was added ie. do something based on run timeframes

# header
echo "all\n";
echo "================\n";

# config
# needs the magic variable for cron
require __DIR__ . '/../../../../include/list/v1/config/preset.php';

# see also:
# config/dependancy.php
include($config['include_path'] . 'list/v1/inline/mysql_connect.php');
include($config['include_path'] . 'list/v1/function/main.php');
include($config['include_path'] . 'list/v1/function/runner.php');

# prerun
$data['getopt'] = periodic_script_getopt();
periodic_script_setup($data['getopt']);

# var
$a1['info'] = array(
	'now' => '',
	'current' => '',
	'previous' => '',
	'end' => '',
	'start' => '',
	'daydiff' => '',
);

# alias
$info = & $a1['info'];
$end = & $a1['info']['end'];
$start = & $a1['info']['start'];

# translation

$info['now'] = get_runner_modified_now();
$info['current'] = get_runner_modified_previous(1, $info['now']);

# todo
$info['previous'] = get_runner_modified_previous(1, $info['current']);

$end = $info['current'];
if (!empty($data['getopt']['lt']))
	$end = $data['getopt']['lt'];
$start = get_runner_modified_previous(1, $end);
if (!empty($data['getopt']['gte']))
	$start = $data['getopt']['gte'];

# extra call for debug readability
$a1['info']['daydiff'] = get_runner_modified_daydiff($end, $start);
print_r_debug($a1);

# error checking
if (empty($start))
	die("error\n\tno start found - end date too early?\n");
if (empty($end))
	die("error\n\tno end found\n");
if (empty($start)) {
	# todo insert 1st entry (weird case)
	insert_runner_modified($i1, $start);
	die("info\n\tfirst run so no need for further processing\n");
}
if (isset($data['getopt']['w'])) {
	if (!empty($data['getopt']['gte']))
		die("error\n\tcan not specify start if writing to the db\n");
	if (!empty($data['getopt']['lt']))
	if ($info['current'] >= $data['getopt']['lt'])
		die("error\n\ta later entry has already been added to the db must be after current runner\n");
}

$a1['chunk'] = get_runner_chunk_array($end, $start);

# do it
insert_runner_modified(1, $end);

# run order must be honored
$a2 = array(
	'cycle',
	'renewal',
	'sponsor',
);
foreach ($a1['chunk'] as $k1 => $v1) {
foreach ($a2 as $k2 => $v2) {
	# each file has separate variable space
	$s2 = 'php ' . $config['run_path'] . 'list/v1/periodic/' . $v2 . '.php ' .
		'--lt \'' . $v1['end'] . '\' ' . 
		'--gte \'' . $v1['start'] . '\' '
	;
	if (!empty($data['getopt']['l']))
		$s2 .= '-l' . (int)$data['getopt']['l'] . ' ';
	if (isset($data['getopt']['w']))
		$s2 .= '-w ';
	;
	echo $s2 . "\n";
	$s3 = `$s2`;
	echo $s3;
} }
