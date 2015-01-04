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
# see also: config/dependancy.php

# todo add limits on the shortness of an interval because buffer time will be needed for the autorenew script to run

# get all cycles for "tomorrow"
$sql = '
	select
		*
	from
		' . $config['mysql']['prefix'] . 'cycle
	where
		start > now()
		start < date_add(now(), interval 1 day) and
';
$result = mysql_query($sql) or die(mysql_error());
while ($row = mysql_fetch_assoc($result)) {
	$data['cycle']['tomorrow'][$row['cycle_id']] = array();
}
print_r($data['cycle']);

if (empty($data['cycle']))
	die('cant autorenew anything - future renewals DNE - please set manually' . "\n");

# todo add point_id to cycle such that every single cycle isn't quired to be a db entry ie) if no members

# todo find offset for new cycle (not always the same)

# todo compute future cycle start

# todo find channel_id for new cycle (not always the same)

# todo insert new cycles

if (0)
foreach($data['cycle'] as $k1 => $v1) {
	$sql = '
		insert into '  .
			$config['mysql']['prefix'] . 'cycle
		set
			modified = now(),
			active = 1,
			timeframe_id = 3,
			start = 0,
			channel_id = 0
	';
	# mysql_query($sql) or die(mysql_error());
}

# todo when thinking in terms of current cycle:
# to the script current may be ahead by 1 day
# to the ent user current should not be ahead by 1 day
# is this ok?

# todo get all cycles from "today"
$data['cycle']['today'] = array();
$sql = '
	select
		*
	from
		' . $config['mysql']['prefix'] . 'cycle
	where
		start > date_sub(now(), interval 1 day) and
		start < now()
';
$result = mysql_query($sql) or die(mysql_error());
while ($row = mysql_fetch_assoc($result)) {
	$data['cycle']['today'][$row['cycle_id']] = array();
}
print_r($data['cycle']);

# todo make all cycles that started today current
$sql = '
	update '  .
		$config['mysql']['prefix'] . 'cycle
	set
		modified = now(),
		timeframe_id = 2
	where
		start > date_sub(now(), interval 1 day) and
		start < now()
';
# mysql_query($sql) or die(mysql_error());

# todo make previous cycles for those channels updated above past
