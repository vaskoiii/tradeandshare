<?
/*
Copyright 2003-2012 John Vasko III

This file is part of Trade and Share.

Trade and Share is free software: you can redistribute it and/or modify
it under the terms of the GNU General Public License as published by
the Free Software Foundation, either version 3 of the License, or
(at your option) any later version.

Trade and Share is distributed in the hope that it will be useful,
but WITHOUT ANY WARRANTY; without even the implied warranty of
MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
GNU General Public License for more details.

You should have received a copy of the GNU General Public License
along with Trade and Share.  If not, see <http://www.gnu.org/licenses/>.
*/

# description: process "first" cycles/renewals and upcoming renewals - (does not process upcoming cycles)

# note
# cycle length
# - min = 1 day? ( current timeframe_id lags by the frequency of periodic_cycle.php )
# - max = 1 year?
# - estimated norm = 30 days

# todo allow user to confirm before billing
# todo process funds charge transaction

# variable
$first_cycle = 0;
$first_renewal = 0;
$now = date('Y-m-d H:i:s');
# hardcode for testing
if ($config['debug'] == 1)
	$now = '2015-07-01 04:58:09';

# initialize cycle
$data['cycle'] = array(
	'next' => array(),
	'current' => array(),
	'previous' => array(),
);
$ncycle = & $data['cycle']['next'];
$ccycle = & $data['cycle']['current'];
$pcycle = & $data['cycle']['previous'];

# initialize renewal
$data['renewal'] = array(
	'next' => array(),
	'current' => array(),
	'previous' => array(),
);
$nrenewal = & $data['renewal']['next'];
$crenewal = & $data['renewal']['current'];
$prenewal = & $data['renewal']['previous'];

$process['action_content_1']['channel_name'] = get_gp('channel_name');
$process['action_content_1']['point_name'] = get_gp('point_name');

$lookup['channel_parent_id'] = get_db_single_value('
		parent_id
	from
		' . $prefix . 'channel cnl
	where
		name = ' . to_sql($process['action_content_1']['channel_name']) . ' and
		active = 1
');

$lookup['point_id'] = get_db_single_value('
		id
	from
		' . $prefix . 'point
	where
		name = ' . to_sql($process['action_content_1']['point_name'])
);

process_field_missing('action_content_1');

# todo merge into process_does_not_exist('action_content_1');
if (!$lookup['channel_parent_id']) {
	$message = tt('element', 'channel_name') . ' : ' . tt('element', 'error_does_not_exist');
}
if ($lookup['point_id'] == 1) {
	$message = tt('point', 'start') . ' : ' . tt('element', 'error');
}

# cycle
# once started keeps going forever for simplicity
# todo write a logic so there is no need for empty placeholder cycles
$first_cycle = is_cycle_start($lookup['channel_parent_id']);
if (!empty($first_cycle))
	insert_cycle_start($lookup['channel_parent_id']);
get_cycle_array($data['cycle'], $lookup['channel_parent_id'], $now);
insert_cycle_next($data['cycle'], $lookup['channel_parent_id'], $now);
get_cycle_next_array($data['cycle'], $lookup['channel_parent_id'], $now);

# todo have to build the renewal array before the renewal cost is known
if (1) {
	$check = array(); # special array just to check values before hand
	$first_renewal = is_renewal_start($data['cycle']['current']['cycle_id'], $login_user_id);
	if ($config['debug'] == 1) {
		# test first renewal
		if (0) {
			$first_renewal = 1;
			$login_user_id = -1;
		}
	}
	if (!empty($first_renewal))
		$check['renewal']['current'] = dummy_renewal_start($now);
	# todo with dummy data this should be skipped
	get_renewal_array($data['cycle'], $check['renewal'], $lookup['channel_parent_id'], $login_user_id);
	get_renewal_next_data($data['cycle'], $check['renewal']);
	get_renewal_accounting_array($check['renewal'], $login_user_id);
	# echo '<pre>'; print_r($check); echo '</pre>'; exit;
	if ($config['debug'] == 1) {
		echo '<pre>'; print_r($check['renewal']['accounting']); echo '</pre>';
	}
	if (empty($message)) {
		if ($check['renewal']['accounting']['resulting_balance'] < 0)
			$message = 'error: insufficient funds';
	}
	if ($config['debug'] == 1)
		echo $message;
}

# $message = 'force failure until debug messages are no longer needed';
process_failure($message);

# do it!
# renewal
$first_renewal = is_renewal_start($ccycle['cycle_id'], $login_user_id);
if (!empty($first_renewal))
	insert_renewal_start($ccycle['cycle_id'], $login_user_id);
get_renewal_array($data['cycle'], $data['renewal'], $lookup['channel_parent_id'], $login_user_id);
get_renewal_next_data($data['cycle'], $data['renewal']);
get_renewal_accounting_array($data['renewal'], $login_user_id);
insert_renewal_next($data['cycle'], $data['renewal'], $lookup['channel_parent_id'], $login_user_id, $lookup['point_id'], $now);

if ($config['debug'] == 1) {
	echo '<hr><pre>'; print_r($nrenewal); echo '</pre>';
}

# cleanup
if (empty($first_cycle)) {
	# is the renewal in the current cycle or next cycle?
	# todo make sure a user can not be renewed while a periodic_renewal.php is running
	$i1rn = get_db_single_value('
			rnal.id
		from
			' . $prefix . 'renewal rnal,
			' . $prefix . 'cycle cce,
			' . $prefix . 'channel cnl
		where
			cce.id = rnal.cycle_id and
			cnl.id = cce.channel_id and
			cnl.parent_id = ' . (int)$lookup['channel_parent_id'] . ' and
			rnal.user_id = ' . (int)$login_user_id . ' and
			rnal.start > now() and
			rnal.active = 1 and
			cce.active = 1 and
			cnl.active = 1
		order by
			rnal.id desc
	');
	$sql = '
		update
			' . $prefix . 'renewal
		set
			timeframe_id = 1,
			point_id = ' . (int)$lookup['point_id'] . ',
			timeframe_id = 3,
			modified = now()
		where
			id = ' . (int)$i1rn . '
	';
	if ($config['debug'] == 1)
		echo '<hr>' . $sql;
	mysql_query($sql) or die(mysql_error());
}
# continuing memberships will be processed by cron

if ($config['debug'] == 1)
	echo '<hr>';
process_success(tt('element', 'transaction_complete') . ($email_sent ? ' : ' . tt('element', 'email_sent') : ''));
