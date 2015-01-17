<?
# author: vaskoiii
# description: process the autorenewing memberships from a cron ( first implementation will be daily )

# run after periodic_cycle.php ?
# todo possible to integrate periodic_cycle.php?

# issue
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
include($script['include_path'] . 'function/member.php');

# todo add limits on the shortness of an interval because buffer time will be needed for the autorenew script to run
$data['run']['datetime'] = get_run_datetime_array();

# alias
$rdatetime = & $data['run']['datetime'];
$prefix = & $config['mysql']['prefix'];

$acycle = & $data['run']['after']['cycle'];
	$auser = & $data['run']['after']['user'];

# mimize the resulting array size by skipping channel and starting with cycle
$sql = '
	select
		rnal.cycle_id
	from
		' . $prefix . 'renewal rnal,
		' . $prefix . 'renewage rnae
	where
		rnae.renewal_id = rnal.id and
		rnal.start >= ' . to_sql($rdatetime['current']) . ' and
		rnal.start < ' . to_sql($rdatetime['next']) . ' and
		rnae.point_id in (2, 3, 4)
		-- no "1" because can not start a renewal here
	group by
		rnal.cycle_id
';
$result = mysql_query($sql) or die(mysql_error());
while ($row = mysql_fetch_assoc($result)) {
	$acycle[$row['cycle_id']] = array();
}

# try to conserve resources ie) unset()

# hardcode for debug
$acycle[157] = array();

if (!empty($acycle)) {
foreach ($acycle as $k1 => $v1) {
	# todo function get_channel_parent_id($type, $type_id)
	# a way to easily get the channel parent id which is otherwise kind of abstract
	$channel_parent_id = get_db_single_value('
			cnl.parent_id
		from
			' . $prefix . 'channel cnl,
			' . $prefix . 'cycle cce
		where
			cce.channel_id = cnl.id and
			cce.id = ' . (int)$k1
	);

	get_cycle_array($acycle[$k1], $channel_parent_id, $rdatetime['next']);

	# todo get the renewals for this cycle
	# kind of repeat of original logic but retrieve differntly
	$sql = '
		select
			rnal.user_id
		from
			' . $prefix . 'renewal rnal,
			' . $prefix . 'renewage rnae
		where
			rnae.renewal_id = rnal.id and
			rnal.start >= ' . to_sql($rdatetime['current']) . ' and
			rnal.start < ' . to_sql($rdatetime['next']) . ' and
			rnae.point_id in (2, 3, 4) and
			rnal.cycle_id = ' . (int)$k1 . '
			-- no "1" because can not start a renewal here
		group by
			rnal.user_id
	';
	echo $sql;
	mysql_query($sql) or die(mysql_error());
	while ($row = mysql_fetch_assoc($result)) {
		$auser[$row['user_id']] = array();
	}

	# hardcode for debug
	$auser[150] = array();

	if (!empty($auser)) {
	foreach ($auser as $k2 => $v2) {
		# todo get user_id
		# todo get channel_parent_id
		# todo tie in:

		get_renewal_array($acycle[$k1], $auser[$k2], $channel_parent_id, $k2);
		
		print_r($auser[$k2]);

		finalize_renewal_array($acycle[$k1], $auser[$k2]);

		# todo more elegant errors 
		if (empty($auser[$k2]['next']['renewal_id'])) {
			echo 'do nothing renewal is already handled (may not happen)';
		}
		else {
		switch($auser[$k2]['next']['point_id']) {
			# insert a new entry
			case '1':
				# start (shouldn't happen)
			break;
			case '2':
				# continue
				insert_renewal_next($acycle[$k1], $auser[$k2], $channel_parent_id, $k2, 2, $rdatetime['next']);
			break;
			case '3':
				# end
			break;
			case '4':
				# nextend
				insert_renewal_next($acycle[$k1], $auser[$k2], $channel_parent_id, $k2, 4, $rdatetime['next']);
			break;
		} }
		# todo renew these people
		unset($arenewal[$k2]);
	} }
	unset($acycle[$k1]);
} }

# todo make all renewage that started since last run current
$sql = '
	update '  .
		$prefix . 'renewage
	set
		-- not really a modification only a changing a marker
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
