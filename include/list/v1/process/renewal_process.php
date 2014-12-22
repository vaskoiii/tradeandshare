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

# description: custom code based on the timeline for renewing a membership

# issue:
# - rough code!
# - have to deal with dynamically changing cycles
# - todo allow up to seconds in the cycles/renewals
# - todo another script will have to run daily to check renewals

# cycle length
# - min = 1 day?
# - max = 1 year? ( probably people wont use )
# - estimated norm = 30 days

# renewals happen 1 day before "expiration" from cron based on the endpoints:
# can only be 1 possible future renewal
# - continue
# - end
# - nextend

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
if (empty($message))
	$message = 'forced fail: too abstract for the generic edit process - most checks are going to be custom';
process_failure($message);

# do it!

$data['cycle'] = array();
get_cycle_array($data['cycle'], $lookup['channel_parent_id']);
$fcycle = & $data['cycle']['future'];
$ccycle = & $data['cycle']['current'];
$pcycle = & $data['cycle']['previous'];

$data['renewal'] = array();
get_renewal_array($data['cycle'], $data['renewal'], $lookup['channel_parent_id'], $_SESSION['login']['login_user_id']);
$frenewal = & $data['renewal']['future'];
$crenewal = & $data['renewal']['current'];
$prenewal = & $data['renewal']['previous'];

# not keeping track of when the renewal was actually start (form submitted)!
if (!empty($frenewal['renewal_id'])) {
	# since only 1 renewal is possible per cycle cycle_id and id can be used to find the parent renewal
	$sql = '
		update
			' . $prefix . 'renewage
		set
			point_id = ' . (int)$lookup['point_id'] . '
		where
			renewage_id = ' . (int)$frenewal['renewal_id'] . '
		limit
			1
	';
	# mysql_query($sql) or die(mysql_error());
}
# new/expired membership
# continuing memberships will be processed by cron
else {
	# todo compute payment # base on start and finish of
	# 2 renewals ago
	# ensures everyone starts receiving payment after exactly 3 variable cycle lengths
	# though they will pay according to renewals

	# if less than 2 renewals
	# just use $cycle['future']['channel_value']

	if (0) {
		$frenewal['renewal_to_cycle_value'] =
			get_datetime_difference('','') *
			$fcycle['channel_value']
		;
		$frenewal['cycle_to_renewal_value'] =
			get_datetime_difference('','') *
			$ccycle['channel_value']
		;
		$frenewal['computed_renewal_value'] =
			$frenewal['renewal_to_cycle_value'] +
			$frenewal['cycle_to_renewal_value']
		;
	}

	# todo compute value from:
	# $cycle['future']['channel_value'];
	# $cycle['future']['my_rating_value']
	$fcycle['computed_rating_value'] = 0;

	# todo ts_transaction will be another computation of computed_rating_value and computed_renewal_value

	$a1 = array(
		# insert start point
		'1',
		# insert continue/end/nextend point
		$lookup['point_id'],
	);
	foreach ($a1 as $k1 => $v1) {
		# fix the start time
		# todo calculate % into current cycle
		# todo calculate cost for remaintder of cycle
		# todo calculate % into future cycle
		# todo calculate cost for first part of future cycle
		$sql = '
			insert into
				' . $prefix . 'renewal
			set
				user_id = ' . (int)$_SESSION['login']['login_user_id'] . ',
				rating_value = ' . (int)$fcycle['computed_rating_value'] . ',
				value = ' . (double)$fcycle['computed_renewal_value'] . ',
				start = now(),
				modified = now(),
				active = 1
		';
		$i1 = mysql_insert_id();
		# insert renewage
		$sql = '
			insert into
				' . $prefix . 'renewage
			set
				point_id = ' . (int)$v1 . ',
				renewal_id = ' . (int)$i1 . ',
				timeframe_id = ' . (int)($v1 == 1 ? '2' : '3') . ',
				modified = now()
		';
	}
}

# todo funds charge transaction will happen 1 day before in cron

# placeholders for troubleshooting
# echo '<h2>$cycle</h2>';
# echo '<pre>'; print_r($data); echo '</pre>';
# die('check data');

# set message
$message = 'made it to the end';
process_success(tt('element', 'transaction_complete') . ($email_sent ? ' : ' . tt('element', 'email_sent') : ''));
