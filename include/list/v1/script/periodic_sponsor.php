<?
# author: vaskoiii
# description: process the autorenewing sponsors happening tomorrow not today

# see also periodic_renewal.php for similar scripting issues

# todo account for credit in accounting (charge users the day before the sponsor begins)
# todo factor in available funds
# todo start using UTC_TIMESTAMP to keep all data in the same timezone
# todo chunk out potentially large dataset for processing in parts

# config
# needs the magic variable for cron
require(__DIR__ . '/../config/preset.php');

# override
# $config['write_protect'] = 1; # must be 2 for live data (will not write to the db if 1)
$config['debug'] = 1; # script should always run in debug mode ( ui will not be affected )
$config['craft'] = 2; # comment out to not use crafted data

# see also:
# config/dependancy.php
include($config['include_path'] . 'list/v1/inline/mysql_connect.php');
include($config['include_path'] . 'list/v1/function/main.php');
include($config['include_path'] . 'list/v1/function/member.php');

# var
if ($config['craft'] == 1)
	$data['run']['datetime'] = array(
		'previous' => '2015-10-13 00:00:00',
		'current' => '2015-10-14 00:00:00',
		'next' => '2015-10-15 00:00:00',
		'horizon' => '2015-10-16 00:00:00',
	);
else
	$data['run']['datetime'] = get_run_datetime_array();

echo "rdatetime\n";
echo "{\n";
print_r($data['run']['datetime']);
echo "}\n";

$data['run']['after']['user'] = array();
# bcycle data structure is totally different from acycle

# alias
$rdatetime = & $data['run']['datetime'];
$prefix = & $config['mysql']['prefix'];

$sql = '
	select
		ssr.id as sponsor_id,
		ssr.start as sponsor_start,
		ssr.point_id,
		dne.id as donate_id,
		dne.user_id,
		dne.channel_parent_id,
		dne.offset as donate_offset,
		dne.value as donate_value
	from
		' . $prefix . 'sponsor ssr,
		' . $prefix . 'donate dne
	where
		dne.id = ssr.donate_id and
		ssr.start >= ' . to_sql($rdatetime['next']) . ' and
		ssr.start < ' . to_sql($rdatetime['horizon']) . '
';
print_debug($sql);
$result = mysql_query($sql) or die(mysql_error());
while ($row = mysql_fetch_assoc($result)) {
	$data['sponsor_id'][$row['sponsor_id']] = $row;
}

if (!empty($data['sponsor_id'])) {
foreach ($data['sponsor_id'] as $k1 => $v1) {
	$i1 = 0; # for point_id
	$b1 = 2; # continue to insert?
	update_sponsor_timeframe($v1);
	switch($v1['point_id']) {
		case '4':
			$i1 = 3;
			$b1 = 1;
		break;
		case '3':
			# do not autorew the cycle is ending	
		break;
		case '2':
			$i1 = 2;
			$b1 = 1;
		break;
		case '1':
			die('renewing a start point_id should not happen');
		break;
		default:
			# already checked in update_sponsor timeframe
		break;
	}
	if ($b1 == 1) {
		$sql = '
			insert into	
				' . $prefix . 'sponsor
			set
				donate_id = ' .  (int)$v1['donate_id'] . ',
				point_id = ' . (int)$i1 . ',
				timeframe_id = 3,
				start = date_add(' . to_sql($v1['sponsor_start']) . ', interval ' . (int)$v1['donate_offset'] . ' day),
				modified = now(),
				active = 1
		';
		print_debug($sql);
		mysql_query($sql) or die(mysql_error());
	}
} }
