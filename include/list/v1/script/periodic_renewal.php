<?
# author: vaskoiii
# description: process the autorenewing memberships from a cron ( first implementation will be daily )

# issue
# - hardcoded interval (31)
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
		-- autorenew = 1 and
		modified > DATE_SUB(NOW(), INTERVAL 31 day)
';
$result = mysql_query($sql) or die(mysql_error());
while ($row = mysql_fetch_assoc($result)) {
	$data['renewal'][$row['id']] = $row;
	$data['cycle'][$row['cycle_id']] = array();
}
print_r($data['renewal']);

# add new cycles
foreach ($data['cycle'] as $k1 => $v1) {
	$sql = '
		select
			*
		from
			' . $config['mysql']['prefix'] . 'cycle
		where
			channel_id = ' . (int)$k1
	;
	$result = mysql_query($sql) or die(mysql_error());
	while ($row = mysql_fetch_assoc($result)) {
		$data['cycle'][$row['id']] = $row;
	}
	print_r($v1);

	$p1 = & $data['cycle'][$k1];
	$sql = '
		insert into
			' . $config['mysql']['prefix'] . 'cycle
		set
			channel_id  = ' . (int)$p1['channel_id'] . ',
			value = ' . (int)$p1['value'] . ',
			offset = ' . (int)$p1['offset'] . ',
			start = date_add(' . to_sql($p1['start']) . ', interval ' . (int)$p1['offset'] . ' day),
			modified = now(),
			active = 1
	';
	# insert autrenew people with:
	echo $sql . "\n";
	mysql_query($sql) or die(mysql_error());
	$data['cycle'][$k1]['new_cycle_id'] = mysql_insert_id();
}
print_r($data['cycle']);

# insert a new entry
foreach($data['renewal'] as $k1 => $v1) {
	switch($v1['autorenew']) {
		case '1':
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
							channel_id = ' . (int)$v1['channel_id'] . ',
							cycle_id = ' . (int)$data['cycle'][$v1['cycle_id']]['new_cycle_id'] . ',
							point_id = 2,
							user_id = ' . (int)$v1['user_id'] . ',
							rating_value = ' . (double)$v1['rating_value'] . ',
							value = ' . (double)$v1['value'] . ',
							modified = now(),
							autorenew = 1,
							active = 1
					';
					echo $sql . "\n";
					mysql_query($sql) or die(mysql_error());
				break;
				case '3':
					# end
					# possibly due to insufficient funds
					# todo compute rating value as above
					$sql = '
						insert into '  .
							$config['mysql']['prefix'] . 'renewal
						set
							channel_id = ' . (int)$v1['channel_id'] . ',
							cycle_id = ' . (int)$data['cycle'][$v1['cycle_id']]['new_cycle_id'] . ',
							point_id = 3,
							user_id = ' . (int)$v1['user_id'] . ',
							rating_value = ' . (double)$v1['rating_value'] . ',
							value = 0,
							modified = date_add(' . to_sql($v1['modified']) . ', interval 30 day),
							autorenew = 1,
							active = 1
					';
					echo $sql . "\n";
					mysql_query($sql) or die(mysql_error());
				break;
			}
		break;
		case '2':
			# no autorenew
		break;
	}
}
