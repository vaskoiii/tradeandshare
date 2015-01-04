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

# description: custom code based on the timeline for renewing a membership also handles "first" renewals and upcoming

# note
# cycle length
# - min = 1 day?
# - max = 1 year? ( probably people wont use )
# - estimated norm = 30 days

# todo allow user to confirm before billing
# todo process funds charge transaction

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

$message = 'force failure until debug messages are no longer needed';
process_failure($message);

# do it!

# initialize cycle
$data['cycle'] = array(
	'future' => array(),
	'current' => array(),
	'previous' => array(),
);
$fcycle = & $data['cycle']['future'];
$ccycle = & $data['cycle']['current'];
$pcycle = & $data['cycle']['previous'];

# initialize renewal
$data['renewal'] = array(
	'future' => array(),
	'current' => array(),
	'previous' => array(),
);
$frenewal = & $data['renewal']['future'];
$crenewal = & $data['renewal']['current'];
$prenewal = & $data['renewal']['previous'];

# first ever cycle
# people should not be concerened with when a cycle first starts
function prepare_cycle_array(& $cycle, $channel_parent_id) {
	global $prefix;
	# cycle already inserted?
	$i1 = get_db_single_value('
			cce.id
		from
			' . $prefix . 'channel cnl,
			' . $prefix . 'cycle cce
		where
			cnl.id = cce.channel_id and
			channel_id = ' . (int)$channel_parent_id
	,1);
	# double check that a valid channel was supplied
	$i2 = 0;
	if (!$i1) {
		$i2 = get_db_single_value('
				parent_id
			from
				' . $prefix . 'channel
			where
				parent_id = ' . (int)$channel_parent_id
		,1);
	}
	# todo allow specifying arbitrary cycle start
	if (!empty($i2)) {
		$sql = '
			insert into
				' . $prefix . 'cycle
			set
				channel_id = ' . (int)$channel_parent_id . ',
				timeframe_id = 2,
				start = now(),
				modified = now(),
				active = 1
		';
		echo '<hr>' . $sql;
		mysql_query($sql) or die(mysql_error());
		$cycle['current']['cycle_id'] = mysql_insert_id(); 
	}

	# will run again when get_cycle_future_array is called (todo optimize)
	insert_cycle_future($data['cycle'], $channel_parent_id);
}

prepare_cycle_array($data['cycle'], $lookup['channel_parent_id']);
get_cycle_array($data['cycle'], $lookup['channel_parent_id']);

# first ever renewal
function prepare_renewal_array(& $cycle, & $renewal, $channel_parent_id, $user_id, $point_id) {
	# data from this function gets overwritten later with same data todo optimize
	# considered first renewal if no renewals so far this cycle
	# end/nextend are not considered renewals ( only start/continue )
	global $prefix;
	# alias
	$fcycle = & $cycle['future'];
	$ccycle = & $cycle['current'];
	$pcycle = & $cycle['previous'];
	$frenewal = & $renewal['future'];
	$crenewal = & $renewal['current'];
	$prenewal = & $renewal['previous'];
	# current renewal
	$i1 = get_db_single_value('
			rnal.id
		from
			' . $prefix . 'renewal rnal,
			' . $prefix . 'renewage rnae
		where
			rnal.id = rnae.renewal_id and
			rnae.point_id IN (1, 2) and 
			rnal.cycle_id = ' . (int)$ccycle['cycle_id'] . ' and
			rnal.user_id = ' . (int)$user_id . ' and
			rnal.active = 1
	',1);
	if (empty($i1)) {
		$crenewal['renewal_start'] = date('Y-m-d H:i:s');
		$sql = '
			insert into
				' . $prefix . 'renewal
			set
				user_id = ' . (int)$user_id . ',
				start = '  . to_sql($crenewal['renewal_start']) . ',
				active = 1,
				cycle_id = ' . (int)$ccycle['cycle_id']
		;
		echo '<hr>' . $sql;
		mysql_query($sql) or die(mysql_error());
		$renewal['current']['renewal_id'] = mysql_insert_id();
		$sql = '
			insert into
				' . $prefix . 'renewage
			set
				point_id = 1,
				modified = now(),
				timeframe_id = 2,
				renewal_id = ' . (int)$crenewal['renewal_id']
		;
		echo '<hr>' . $sql;
		mysql_query($sql) or die(mysql_error());
		$sql = '
			insert into
				' . $prefix . 'gauge_renewal
			set
				renewal_id = ' . (int)$crenewal['renewal_id'] . ',
				rating_value = 0,
				renewal_value = ' . (double)$ccycle['channel_value']
		;
		echo '<hr>' . $sql;
		mysql_query($sql) or die(mysql_error());
	}
	insert_renewal_future($cycle, $renewal, $channel_parent_id, $user_id, $point_id);
}

prepare_renewal_array($data['cycle'], $data['renewal'], $lookup['channel_parent_id'], $login_user_id, $lookup['point_id']);
get_renewal_array($data['cycle'], $data['renewal'], $lookup['channel_parent_id'], $login_user_id);

function finalize_renewal_array(& $cycle, & $renewal) {
	# alias
	$fcycle = & $cycle['future'];
	$ccycle = & $cycle['current'];
	$pcycle = & $cycle['previous'];
	$frenewal = & $renewal['future'];
	$crenewal = & $renewal['current'];
	$prenewal = & $renewal['previous'];
	# r2c
	# todo calclulate rating
	# todo factor in rating with value
	$frenewal['r2c_day'] = get_day_difference($crenewal['renewal_start'], $fcycle['cycle_start']);
	$frenewal['r2c_ratio'] = 1 - ($frenewal['r2c_day'] / $ccycle['channel_offset']);
	$frenewal['r2c_rating'] = 0;
	$frenewal['r2c_renewal'] = $frenewal['r2c_ratio'] * $ccycle['channel_value'];
	# c2r
	# todo calclulate rating
	# todo factor in rating with value
	$frenewal['c2r_day'] = abs($frnewal['r2c_day'] - $fcycle['channel_offset']);
	$frenewal['c2r_ratio'] = 1 - $frenewal['r2c_ratio'];
	$frenewal['c2r_rating'] = 0;
	$frenewal['c2r_renewal'] = $frenewal['c2r_ratio'] * $fcycle['channel_value'];
	# misc
	$frenewal['renewal_start'] = get_datetime_add_day($fcycle['cycle_start'], $frenewal['r2c_ratio'] * $fcycle['channel_offset']);
	$frenewal['gauge_rating_value'] = ($frenewal['r2c_rating'] * $frenewal['r2c_ratio']) + ($frenewal['c2r_rating'] * $frenewal['c2r_ratio']);
	$frenewal['gauge_renewal_value'] = ($frenewal['r2c_renewal'] * $frenewal['r2c_ratio']) + ($frenewal['c2r_renewal'] * $frenewal['c2r_ratio']);
	# todo grant payout based on:
	# see ascii picture at ~/include/list/v1/page/user_report.php
	# todo ts_transaction will be another computation of computed_rating_value and computed_renewal_value
}
finalize_renewal_array($data['cycle'], $data['renewal']);

if (!empty($frenewal['renewal_id'])) {
	$sql = '
		update
			' . $prefix . 'renewage
		set
			timeframe_id = 1
		where
			renewal_id = ' . (int)$frenewal['renewal_id'] . '
	';
	echo '<hr>'; echo $sql;
	mysql_query($sql) or die(mysql_error());
	$sql = '
		insert into
			' . $prefix . 'renewage
		set
			point_id = ' . (int)$lookup['point_id'] . ',
			renewal_id = ' . (int)$frenewal['renewal_id'] . ',
			timeframe_id = 3,
			modified = now()
	';
	echo '<hr>'; echo $sql;
	mysql_query($sql) or die(mysql_error());
}
# continuing memberships will be processed by cron
# echo '<hr><pre>'; print_r($data); echo '</pre>'; exit;

echo '<hr>';
process_success(tt('element', 'transaction_complete') . ($email_sent ? ' : ' . tt('element', 'email_sent') : ''));
