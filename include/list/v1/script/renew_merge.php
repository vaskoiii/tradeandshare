<?
# see also:
# config/dependancy.php

# config
$script['include_path'] = '/www/site/list/include/list/v1/';

# dependancy
include($script['include_path'] . 'config/preset.php');
include($script['include_path'] . 'inline/mysql_connect.php');


$sql = '
	select 
		renewal_id,
		point_id,
		timeframe_id,
		modified
	from
		ts_renewage
';
$result = mysql_query($sql) or die(mysql_error());
while ($row = mysql_fetch_assoc($result)) {

	$sql = '
		update
			ts_renewal
		set
			point_id = ' . (int)$row['point_id'] . ',
			timeframe_id = ' . (int)$row['timeframe_id'] . ',
			modified = "' . mysql_real_escape_string($row['modified']) . '"
		where
			id = ' . (int)$row['renewal_id'] . '
		limit
			1
	';
	mysql_query($sql) or die(mysql_error());
}
