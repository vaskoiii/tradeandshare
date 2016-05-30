<?
# author: vaskoiii
# description: process the autorenewing sponsors happening in the void 

# todo start using UTC_TIMESTAMP to keep all data in the same timezone

# header
echo "sponsor\n";
echo "= = = = = = = = \n";

# config
# needs the magic variable for cron
require __DIR__ . '/../../../../include/list/v1/config/preset.php';

# override
$config['run'] = 1;
$config['protect'] = 1; # must be 2 for live data (will not write to the db if 1)
$config['craft'] = 2; # comment out to not use crafted data
$config['debug'] = 1; # script should always run in debug mode ( ui will not be affected )

# see also:
# config/dependancy.php
include($config['include_path'] . 'list/v1/inline/mysql_connect.php');
include($config['include_path'] . 'list/v1/function/main.php');
include($config['include_path'] . 'list/v1/function/member.php');
include($config['include_path'] . 'list/v1/function/runner.php');

# error checking
# todo better error checking
if (empty($argv[1]))
	die('error: no start date');
if (empty($argv[2]))
	die('error: no end date');

echo "argv ";
print_r_debug($argv);

# might want to break out from here to show the periodic
$data['run']['after']['user'] = array();

# alias
$start = & $argv[1];
$end = & $argv[2];
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
		ssr.start >= ' . to_sql($start) . ' and
		ssr.start < ' . to_sql($end) . '
';
print_debug($sql);
$result = mysql_query($sql) or die(mysql_error());
while ($row = mysql_fetch_assoc($result)) {
	$data['sponsor_id'][$row['sponsor_id']] = $row;
}

if (empty($data['sponsor_id']))
	die("info\n\tno sponsors to renew\n");

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
				start = date_add(' . to_sql($v1['sponsor_start']) . ', interval ' . (int)$v1['donate_offset'] . ' second),
				modified = now(),
				active = 1
		';
		print_debug($sql);
		mysql_query($sql) or die(mysql_error());

		# get the new present sponsor and charge for it
		# (timeframe must be updated first)
		$sql = '
			select
				ssr.id as sponsor_id,
				dne.user_id,
				dne.value as donate_value
			from
				' . $prefix . 'sponsor ssr,
				' . $prefix . 'donate dne
			where
				dne.id = ssr.donate_id and
				dne.channel_parent_id = ' . (int)$v1['channel_parent_id'] . ' and
				ssr.timeframe_id = 2
			limit
				1
		';
		print_debug($sql);
		$result = mysql_query($sql) or die(mysql_error());
		# todo may be useful to use a less temporary array for debug
		$a1 = array();
		while ($a1 = mysql_fetch_assoc($result))
			do_accounting('sponsor', $a1['sponsor_id'], -$a1['donate_value'], $a1['user_id']);
	}
} }
