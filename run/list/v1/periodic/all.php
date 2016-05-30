<?
# author: vaskoiii
# description: process the autorenewing cycles then renewals then sponsors (in that order) since the last run time ensurigin that there is exactly 1 of each for the corresponding entity in the future if applicable - assuming that everything is ok up to the previous run time

# todo possible to revert? ie. linked table for what was added ie. do something based on run timeframes

# should command line scripts have a separate living space ie. ~/run/ (fomatting is different from html)
# php also has a separate config file for command line scripts vs html ones

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
# include($config['include_path'] . 'list/v1/function/member.php');

# override
$config['run'] = 1;
$config['protect'] = 1; # must be 2 for live data (will not write to the db if 1)
$config['craft'] = 2; # comment out to not use crafted data
$config['debug'] = 1; # script should always run in debug mode ( ui will not be affected )

# var

# todo should probably use date('c');
$s1 = date('Y-m-d H:i:s');

# ui is most recent to oldest however the arrays below are built oldest to most recent (reading top to bottom)
if (1) {
	# make sure now() has the same value in every instance
	# https://dev.mysql.com/doc/refman/5.5/en/date-and-time-functions.html#function_now
	$sql = '
		select
			modified as previous,
			' . to_sql($s1) . ' as current
		from
			' . $config['mysql']['prefix'] . 'runner
		where
			script_id = 1 and
			modified < ' . to_sql($s1) . '
		order by
			modified desc
		limit
			1
	';
	$result = mysql_query($sql) or die(mysql_error());
	while ($row = mysql_fetch_assoc($result)) {
		$a1['info'] = $row;
	}
}

if (empty($a1)) {
	$sql = '
		insert into
			' . $config['mysql']['prefix'] . 'runner
		set
			modified = ' . to_sql($s1) . ',
			script_id = 1
	';
	print_debug($sql);
	if ($config['protect'] == 1)
		mysql_query($sql) or die(mysql_error());
	die('first run: no need for further processing nothing happened' . "\n");
}
# does not work from the zero date
# -- timestampdiff(day, "0000-00-00", now()) as daydiff

$a1['info']['daydiff'] = (int)(
	(
		strtotime($a1['info']['current']) -
		strtotime($a1['info']['previous'])
	) / 86400
);

$i1 = 0;
while ($i1 <= $a1['info']['daydiff']) {
	$a1['chunk'][$i1]['start'] = date(
		'Y-m-d H:i:s',
		strtotime($a1['info']['previous']) + ($i1 * 86400)
	); 
	if (($a1['info']['daydiff'] - $i1) > 0)
		$a1['chunk'][$i1]['end'] = date(
			'Y-m-d H:i:s',
			strtotime($a1['info']['previous']) + ($i1 + 1 * 86400)
		); 
	else
		$a1['chunk'][$i1]['end'] = $a1['info']['current'];
	$i1++;
}

# do it
if (1) {
	# todo mail a summary with the following info
	print_r_debug($a1);
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
	# $s3 = `$s2`;
	# echo "\n" . $s3;

	$s2 = 'php ' . $config['run_path'] . 'list/v1/periodic/sponsor.php ' .
		'\'' . $a1['chunk'][$k1]['start'] . '\' ' .
		'\'' . $a1['chunk'][$k1]['end'] . '\' '
	;
	# $s3 = `$s2`;
	# echo "\n" . $s3;
}

if (1) {
	$sql = '
		insert into
			' . $config['mysql']['prefix'] . 'runner
		set
			modified = ' . to_sql($s1) . ',
			script_id = 1
	';
	print_debug($sql);
	mysql_query_process($sql);
	die("info\n\tfinished\n");
}
