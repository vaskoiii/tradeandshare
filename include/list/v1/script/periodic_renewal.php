<?
# author: vaskoiii
# description: process the autorenewing memberships from a cron ( first implementation will be daily )

# issue
# - todo: cycles and renewals need to be handled separately
# - doesn't account for credit
# - doesn't account for rating value

# charge users the day before the cycle begins
# easy start is to not deal with available funds

# script dependancies are different from normal dependancies because they do not have access to .htaccess
# CONFIG

# see also:
# config/dependancy.php

# config
$script['include_path'] = '/www/site/list/include/list/v1/';

# dependancy
include($script['include_path'] . 'config/preset.php');
include($script['include_path'] . 'inline/mysql_connect.php');
include($script['include_path'] . 'function/main.php');

# todo add limits on the shortness of an interval because buffer time will be needed for the autorenew script to run

# check the renewals table for users that autorenew tomorrow
$sql = '
	select
		*
	from
		' . $config['mysql']['prefix'] . 'renewal
	where
		-- if running php periodic_renewal late might have to use date_sub()
		modified > date_sub(now(), interval 1 day)
		-- modified > now()
';
$result = mysql_query($sql) or die(mysql_error());
while ($row = mysql_fetch_assoc($result)) {
	$data['renewal'][$row['id']] = $row;
	$data['cycle'][$row['cycle_id']] = array();
}
print_r($data['renewal']);

if (empty($data['renewal']))
	die('cant autorenew anything - future renewals DNE - please set manually' . "\n");

# add new cycles
# todo function ensure_horizon_cycle($channel_id)
# might already have some logic in renewal_process.php
# do this manually for now
/*
insert into
	ts_cycle
set
	modified = "",
	channel_id = "",
	active = 1
*/

# also manually set:
$i1 = 40;
foreach($data['cycle'] as $k1 => $v1) {
	$data['cycle'][$k1]['new_cycle_id'] = 40;
}

# print_r($data['renewal']);

# insert a new entry
foreach($data['renewal'] as $k1 => $v1) {
	switch($v1['point_id']) {
		case '1':
			# start
			# shouldnt happen
		break;
		case '2':
			# continue
			# todo need to compute the rating value and the charge amount
			$sql = '
				insert into '  .
					$config['mysql']['prefix'] . 'renewal
				set
					cycle_id = ' . (int)$data['cycle'][$v1['cycle_id']]['new_cycle_id'] . ',
					point_id = 2,
					user_id = ' . (int)$v1['user_id'] . ',
					rating_value = ' . (double)$v1['rating_value'] . ',
					value = ' . (double)$v1['value'] . ',
					modified = date_add(' . to_sql($v1['modified']) . ', interval 30 day),
					active = 1
			';
			echo $sql . "\n";
			mysql_query($sql) or die(mysql_error());
		break;
		case '3':
			# end
		case '4':
			# nextend
			# possibly due to insufficient funds
			# todo compute rating value as above
			$sql = '
				insert into '  .
					$config['mysql']['prefix'] . 'renewal
				set
					cycle_id = ' . (int)$data['cycle'][$v1['cycle_id']]['new_cycle_id'] . ',
					point_id = ' . (int)$v1['point_id'] . ',
					user_id = ' . (int)$v1['user_id'] . ',
					rating_value = ' . (double)$v1['rating_value'] . ',
					value = 0,
					modified = date_add(' . to_sql($v1['modified']) . ', interval 30 day),
					active = 1
			';
			echo $sql . "\n";
			mysql_query($sql) or die(mysql_error());
		break;
		
	}
}
