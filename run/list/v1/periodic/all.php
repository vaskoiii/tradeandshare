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

# override
$config['run'] = 1;
$config['protect'] = 1; # must be 2 for live data (will not write to the db if 1)
$config['craft'] = 2; # comment out to not use crafted data
$config['debug'] = 1; # script should always run in debug mode ( ui will not be affected )

# var
# todo should probably use date('c');
$s1 = get_runner_modified_current();
$a1['info']['previous'] = get_runner_modified_previous(1, $s1);
$a1['info']['current'] = $s1;
# extra call for debug readability
$a1['info']['daydiff'] = get_runner_modified_daydiff($a1['info']['previous'], $a1['info']['current']);
$a1['chunk'] = get_runner_chunk_array(
	$a1['info']['previous'],
	$a1['info']['current']
);

# do it
if (1) {
	# todo mail a summary with the following info
	print_r_debug($a1);
	insert_runner_modified(1, $s1);
}

# todo verify that running as such will not cause complications
# (other option is to run all cycles first then run all renewals then run all sponsors)
# ie. c1r1s1 c2r2s2 c3r3s3 instead of c1c2c3 r1r2r3 s1s2s3

foreach ($a1['chunk'] as $k1 => $v1) {
	# todo needs special include path?
	# scripts were originally designed to run separately (though in order)
	# (each file has separate variable space)
	$s2 = 'php ' . $config['run_path'] . 'list/v1/periodic/cycle.php ' .
		'\'' . $a1['chunk'][$k1]['start'] . '\' ' .
		'\'' . $a1['chunk'][$k1]['end'] . '\' '
	;
	$s3 = `$s2`;
	echo "\n" . $s3;

	$s2 = 'php ' . $config['run_path'] . 'list/v1/periodic/renewal.php ' .
		'\'' . $a1['chunk'][$k1]['start'] . '\' ' .
		'\'' . $a1['chunk'][$k1]['end'] . '\' '
	;
	$s3 = `$s2`;
	echo "\n" . $s3;

	$s2 = 'php ' . $config['run_path'] . 'list/v1/periodic/sponsor.php ' .
		'\'' . $a1['chunk'][$k1]['start'] . '\' ' .
		'\'' . $a1['chunk'][$k1]['end'] . '\' '
	;
	$s3 = `$s2`;
	echo "\n" . $s3;
}
