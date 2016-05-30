<?
# author: vaskoiii
# description: do accounting for the payout for all cycles in the void - intervals can be as long or as short as needed as the only anticipated side effect will be that payouts are porocessed later or earlier

# issue: take care not cause memory overload with a long run

# header
echo "membership\n";
echo "= = = = = = = = \n";

# config
require __DIR__ . '/../../../../include/list/v1/config/preset.php';

# override
$config['run'] = 1;
$config['protect'] = 1; # must be 2 for live data (will not write to the db if 1)
$config['craft'] = 2; # can set to 1 if needed for testing
$config['debug'] = 1; # script should always run in debug mode ( ui will not be affected )

# include
include($config['include_path'] . 'list/v1/inline/mysql_connect.php');
include($config['include_path'] . 'list/v1/function/main.php');
include($config['include_path'] . 'list/v1/function/member.php');
include($config['include_path'] . 'list/v1/function/payout.php');
include($config['include_path'] . 'list/v1/function/key.php');
include($config['include_path'] . 'list/v1/function/runner.php');

# error checking
# todo better error checking
if (empty($argv[1]))
	die("error\n\tno start date\n");
if (empty($argv[2]))
	die("error\n\tno end date\n");

# var
# todo should probably use date('c');
$data['user_report']['channel_list'] = array();

# reference
$r1start = & $argv[1];
$r1end = & $argv[2];
$prefix = & $config['mysql']['prefix'];
$channel_list = & $data['user_report']['channel_list'];

# do it
echo "argv ";
print_r_debug($argv);

echo "info\n\tget cycles in the void\n";
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
			start >= ' . to_sql($r1start) . ' and
			start < ' . to_sql($r1end)
	;
	print_debug($sql);
	$result = mysql_query($sql) or die(mysql_error());
	while ($row = mysql_fetch_assoc($result)) {
		$channel_list[$row['channel_parent_id']] = array();
	}
	if (!empty($channel_list))
	foreach ($channel_list as $k1 => $v1) {
		# todo check that this function is not tring to get the latest cycle more than 1 time
		$channel_list[$k1]['seed']['cycle_id'] = get_latest_payout_cycle_id($k1);
	}
}

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

# loop through every cycle in the void (not just 1)
if (!empty($channel_list)) {
foreach ($channel_list as $k1 => $v1) {

	# todo make things not dependent on GET
	$_GET['cycle_id'] = $v1['seed']['cycle_id'];

	# when setting an alias within a foreach it will have to be set again in the t1 file =(
	$channel = & $channel_list[$k1];

	if (!empty($v1['seed']['cycle_id'])) {
		do_payout_computation($channel, $k1, $v1['seed']['cycle_id']);
		get_payout_array($channel);
		# sponsor accounting happen separate from payouts
		# (do not charge for here)
		$payout = & $channel['payout'];
		foreach ($payout['user_id'] as $k1p => $v1p) {
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
			mysql_query_process($sql);
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
			mysql_query_process($sql);
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
			mysql_query_process($sql);
		}
	}
	else
		echo "info\n\tchannel not ready for payout\n";
} }
else
	die("info\n\tno cycles in the void\n");
