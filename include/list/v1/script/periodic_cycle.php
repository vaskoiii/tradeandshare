<?
# author: vaskoiii
# description: do accounting for the payout in a cron job for all cycles ending the previous day

# issue
# - script dependancies are different from normal dependancies because they do not have access to .htaccess

# todo prepare crafted data for testing
# todo eliminade delay in payout by remembering the last script run time and updating accordingly on each page load
# todo add limits on the shortness of an interval because buffer time will be needed for the autorenew script to run

# config
# needs the magic variable for cron
include(__DIR__ . '/../config/preset.php');

# override
$config['debug'] = 1; # script should always run in debug mode ( ui will not be affected )
$config['write_protect'] = 2; # must be 2 for live data (will not write to the db if 1)
$config['craft'] = 2; # can set to 1 if needed for testing

# see also:
# config/dependancy.php
include($config['include_path'] . 'list/v1/inline/mysql_connect.php');
include($config['include_path'] . 'list/v1/function/main.php');
include($config['include_path'] . 'list/v1/function/member.php');
include($config['include_path'] . 'list/v1/function/payout.php');
include($config['include_path'] . 'list/v1/function/key.php');

# var
if ($config['craft'] == 1)
	$data['run']['datetime'] = array(
		'previous' => '2015-05-30 00:00:00',
		'current' => '2015-06-01 00:00:00',
		'next' => '2015-06-02 00:00:00',
	);
else
	$data['run']['datetime'] = get_run_datetime_array();

echo "rdatetime\n";
echo "{\n";
print_r($data['run']['datetime']);
echo "}\n";

$data['user_report']['channel_list'] = array();
$channel_list = & $data['user_report']['channel_list'];

# alias
$rdatetime = & $data['run']['datetime'];
$prefix = & $config['mysql']['prefix'];

echo "get cycles that ended yesterday\n";
echo "{\n";
if (1) {
	$sql = '
		select
			cce.id as cycle_id,
			cnl.parent_id as channel_parent_id
		from
			' . $prefix . 'channel cnl,
			' . $prefix . 'cycle cce
		where
			cnl.id = cce.channel_id and
			start >= ' . to_sql($rdatetime['previous']) . ' and
			start < ' . to_sql($rdatetime['current'])
	;
	$result = mysql_query($sql) or die(mysql_error());
	echo "$sql\n";
	while ($row = mysql_fetch_assoc($result)) {
		$channel_list[$row['channel_parent_id']] = array();
	}
	if (!empty($channel_list))
	foreach ($channel_list as $k1 => $v1) {
		# todo check that this function is not tring to get the latest cycle more than 1 time
		$channel_list[$k1]['seed']['cycle_id'] = get_latest_payout_cycle_id($k1);
	}
}
echo "}\n";

# todo check that payout has not already run for the corresponding cycle

# craft for test
if ($config['craft'] == 1) {
	unset($data['user_report']['channel_list']);
	# have to reset alias after craft
	$channel_list = & $data['user_report']['channel_list'];
	
	# todo why isnt getting reset here 
	$data['user_report']['channel_list'][10] = array(
		'seed' => array(
			'cycle_id' => 151,
		),
	);
}

# loop through every cycle that ended yesterday (not just 1)
if (!empty($channel_list)) {
foreach ($channel_list as $k1 => $v1) {

	# todo make things not dependent on GET
	$_GET['cycle_id'] = $v1['seed']['cycle_id'];

	# when setting an alias within a foreach it will have to be set again in the t1 file =(
	$channel = & $channel_list[$k1];

	if (!empty($v1['seed']['cycle_id'])) {
		do_payout_computation($channel, $k1, $v1['seed']['cycle_id']);
		get_payout_array($channel);
		# echo '<pre>'; print_r($channel['payout']); echo '</pre>';

		# kind of a waste of a variable name
		$payout = & $channel['payout'];
		foreach ($payout['user_id'] as $k1p => $v1p) {
			# todo sql for insert
			$sql = '
				insert into
					' . $prefix . 'accounting
				set
					user_id = ' . (int)$k1p . ',
					kind_id = ' . (int)$config['cycle_kind_id'] . ',
					kind_name_id = ' . (int)$v1['seed']['cycle_id'] . ',
					value = ' . (double)$v1p . ',
					modified = now(),
					active = 1
			';
			print_debug($sql);
			if ($config['write_protect'] != 1)
				mysql_query($sql) or die(mysql_error());
		}
		if (1) {
			$sql = '
				insert into
					' . $prefix . 'accounting
				set
					user_id = ' . (int)$config['hostfee_user_id'] . ',
					kind_id = ' . (int)$config['cycle_hostfee_kind_id'] . ',
					kind_name_id = ' . (int)$v1['seed']['cycle_id'] . ',
					value = ' . (double)$payout['hostfee'] . ',
					modified = now(),
					active = 1
			';
			print_debug($sql);
			if ($config['write_protect'] != 1)
				mysql_query($sql) or die(mysql_error());
		}
		if (!empty($payout['missionfee'])) {
			# todo searching for parent and children in the same result set
			$sql = '
				insert into
					' . $prefix . 'accounting
				set
					user_id = ' . (int)$channel['info']['user_id'] . ',
					kind_id = ' . (int)$config['cycle_missionfee_kind_id'] . ',
					kind_name_id = ' . (int)$v1['seed']['cycle_id'] . ',
					value = ' . (double)$payout['missionfee'] . ',
					modified = now(),
					active = 1
			';
			print_debug($sql);
			if ($config['write_protect'] != 1)
				mysql_query($sql) or die(mysql_error());
		}
	}
	else
		echo "channel not ready for payout \n";
} }
else
	die('no cycles ended yesterday');
