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

# variable
$data['renewal_process']['cycle'] = array();
$data['renewal_process']['renewal'] = array();

# alias
$renewal = & $data['renewal_process']['renewal'];
$cycle = & $data['renewal_process']['cycle'];

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

######################
# CYCLE!
######################

$data['cycle'] = array();
get_cycle_array($data['cycle'], $lookup['channel_parent_id']);
$cycle = & $data['cycle'];

######################
# RENEWAL!
######################

# get most recent renewal data
$sql = '
	select
		rnal.id as renewal_id,
		rnal.point_id,
		rnal.start as renewal_start
	from
		' . $prefix . 'renewal rnal,
		' . $prefix . 'cycle cce,
		' . $prefix . 'channel cnl
	where
		cce.id = rnal.cycle_id and
		cnl.id = cce.channel_id and
		cnl.parent_id = ' . (int)$lookup['channel_parent_id'] . ' and
		rnal.user_id = ' . (int)$_SESSION['login']['login_user_id'] . ' and
		rnal.start >= now() and
		rnal.active = 1 and
		cce.active = 1 and
		cnl.active = 1
	order by
		rnal.start desc
	limit
		1
';
$result = mysql_query($sql) or die(mysql_error());
while ($row = mysql_fetch_assoc($result)) {
	$renewal['future'] = $row;
}

# not keeping track of when the renewal was actually start (form submitted)!
if (!empty($renewal['future'])) {
	# future cycle?
	$i1 = strtotime($renewal['start']);
	if ($i1 >= strtotime($cycle['future']['start'])) {


		$sql = '
			insert into
				' . $prefix . 'renewal
			set
				point_id = ' . (int)$lookup['point_id'] . '
				id = ' . (int)$renewal['future']['renewal_id'] . '
		';
		# since only 1 renewal is possible per cycle cycle_id and id can be used to find the parent renewal
				
		$sql = '
			update
				' . $prefix . 'renewal
			set
				point_id = ' . (int)$lookup['point_id'] . '
			where
				id = ' . (int)$renewal['future']['renewal_id'] . '
			limit
				1
		';
		mysql_query($sql) or die(mysql_error());
	}
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

	$d1 = $renewal['future']['renewal_to_cycle_value'] = get_renewal_to_cycle_length($todo_user_id, $todo_cycle_id) * $cycle['future']['channel_value'];
	$d2 = $renewal['future']['cycle_to_renewal_value'] = get_cycle_to_renewal_length($todo_user_id, $todo_cycle_id) * $cycle['future']['channel_value'];
	$renewal['future']['computed_renewal_value'] = $d1 + $d2;

	# todo compute value from:
	# $cycle['future']['channel_value'];
	# $cycle['future']['my_rating_value']
	$cycle['future']['computed_rating_value'] = 0;

	# todo ts_transaction will be another computation of computed_rating_value and computed_renewal_value

	$a1 = array(
		# start point
		'1',
		# continue/end/nextend point
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
				point_id = ' . (int)$v1 . ',
				user_id = ' . (int)$_SESSION['login']['login_user_id'] . ',
				rating_value = ' . (int)$cycle['future']['computed_rating_value'] . ',
				value = ' . (double)$cycle['future']['computed_renewal_value'] . ',
				start = now(),
				modified = now(),
				active = 1
		';
	}
}

# todo funds charge transaction will happen 1 day before in cron

# placeholders for troubleshooting
echo '<hr />';
echo '<h2>$cycle</h2>';
echo '<pre>'; print_r($cycle); echo '</pre>';
echo '<h2>$renewal</h2>';
echo '<pre>'; print_r($renewal); echo '</pre>';

# set message
$message = 'made it to the end';
process_success(tt('element', 'transaction_complete') . ($email_sent ? ' : ' . tt('element', 'email_sent') : ''));
