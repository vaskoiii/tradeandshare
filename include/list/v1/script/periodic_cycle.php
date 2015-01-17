<?
# author: vaskoiii
# description: process the autorenewing cycles from a cron ( first implementation will be daily )

# issue
# currently just a placeholder
# the frequency with which this script (and periodic renewal) run may be the limiting factor in how fine grained the renewals can happen. ie) if it runs daily cycles may only be able account for yyyy-mm-dd and not hh:mm:ss (Y-m-d H-i-s)
# if renewals can happen on the hour there will be several hours before what is current is actually marked correctly

# todo charge users the day before the renewal begins

# config
$script['include_path'] = '/www/site/list/include/list/v1/';

# dependancy
# script dependencies are different from normal dependancies because they do not have access to .htaccess
include($script['include_path'] . 'config/preset.php');
include($script['include_path'] . 'inline/mysql_connect.php');
include($script['include_path'] . 'function/main.php');
include($script['include_path'] . 'function/member.php');
# see also: config/dependancy.php

$data['run']['datetime'] = get_run_datetime_array();

# careful with inequalities
$data['run']['after']['channel'] = array();

# alias
$rdatetime = & $data['run']['datetime'];
$achannel = & $data['run']['after']['channel'];
$prefix = & $config['mysql']['prefix'];

print_r($rdatetime);

# todo add limits on the shortness of an interval because buffer time will be needed for the autorenew script to run

# get all cycles for "next" (indirectly obtained from channel)
$sql = '
	select
		channel_id
	from
		' . $prefix . 'cycle
	where
		start >= ' . to_sql($rdatetime['current']) . ' and
		start < ' . to_sql($rdatetime['next']) . '
';
$result = mysql_query($sql) or die(mysql_error());
while ($row = mysql_fetch_assoc($result)) {
	$achannel[$row['channel_id']] = array();
}
# print_r($data['channel']);

# debug hardcode
# $achannel[12] = array();

if (empty($achannel))
	die('cant autorenew anything - future cycles DNE - please set manually' . "\n");

# todo add point_id to cycle such that every single cycle isn't required to be a db entry ie) if no members

foreach ($achannel as $k1 => $v1) {

	# todo find offset for new cycle (not always the same)
	# todo compute future cycle start
	# todo find channel_id for new cycle (not always the same)
	# todo insert new cycles
	# function already does everything
	get_cycle_array($achannel[$k1], $k1, $rdatetime['next']); 

	print_r($achannel[$k1]);
}

# todo when thinking in terms of current cycle:
# to the script current may be ahead by 1 day
# to the end user current should not be ahead by 1 day
# is this ok?

# todo make all cycles that started since last run current
$sql = '
	update '  .
		$prefix . 'cycle
	set
		-- not really a modification only a changing a marker
		modified = now(),
		timeframe_id = 2
	where
		start >= ' . to_sql($rdatetime['previous']) . ' and
		start < ' . to_sql($rdatetime['current'])
;
print_r($sql);
# mysql_query($sql) or die(mysql_error());
echo "\n";

exit;
# todo make previous cycles for those channels updated above past
