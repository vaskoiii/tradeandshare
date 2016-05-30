<?
# author: vaskoiii
# description: process the payouts since last run - important because it allows for membership.php to be run with arbitrary time frames for debugging

# header
echo "wrap\n";
echo "================\n";

# config
require __DIR__ . '/../../../../include/list/v1/config/preset.php';

# include
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

# do it
if (1) {
	# todo mail a summary with the following info
	print_r_debug($a1);
	insert_runner_modified(1, $s1);
}

# run for any arbitrary timeframe (no chunking needed)
if (1) {
	$s2 = 'php ' . $config['run_path'] . 'list/v1/scheduled/membership.php ' .
		'\'' . $a1['info']['previous'] . '\' ' .
		'\'' . $a1['info']['current'] . '\' '
	;
	$s3 = `$s2`;
	echo "\n" . $s3;
}
