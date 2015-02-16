<?

$script['include_path'] = '/www/site/list/include/list/v1/';

include($script['include_path'] . 'config/preset.php');
include($script['include_path'] . 'inline/mysql_connect.php');

$sql = 'delete from ts_cycle';
mysql_query($sql) or die(mysql_error());
$sql = 'delete from ts_renewal';
mysql_query($sql) or die(mysql_error());
$sql = 'delete from ts_renewage';
mysql_query($sql) or die(mysql_error());
$sql = 'delete from ts_gauge_renewal';
mysql_query($sql) or die(mysql_error());
