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
# if (empty($message))
# 	$message = 'forced fail: too abstract for the generic edit process - most checks are going to be custom';
process_failure($message);

# do it!

######################
# CYCLE!
######################

# prerequire
# - cycle when channel is created
# - all previous cycles with no breaks!

# get 3 most recent non-future cycles
# min cycles that can exist at this point?
$sql = '
	select
		cce.id as cycle_id,
		cce.modified as cycle_modified,
		cnl.id as channel_id,
		cnl.parent_id as channel_parent_id,
		cnl.modified as channel_modified
	from
		' . $prefix . 'cycle cce,
		' . $prefix . 'channel cnl
	where
		cnl.id = cce.channel_id and
		cnl.id = ' . (int)$lookup['channel_parent_id'] . ' and
		cce.modified < now() and
		cce.active = 1 and
		cnl.active = 1
	order by
		cce.modified desc
	limit
		3
';

$i1 = 1;
$result = mysql_query($sql) or die(mysql_error());
while ($row = mysql_fetch_assoc($result)) {
	switch ($i1) {
		case '1':
			# if brand new team does this contain the channel data for inserting a renewal
			$cycle['current'] = $row;
		break;
		case '2':
			# key for current channel data when inseting renewal
			$cycle['previous'] = $row;
		break;
		case '3':
			$cycle['prepre'] = $row;
		break;
	}
	$i1++;
}

# guarantee only and at least 1 possible future cycle
$sql = '
	select
		cce.id as cycle_id,
		cce.modified as cycle_modified,
		cnl.id as channel_id,
		cnl.parent_id as channel_parent_id,
		cnl.modified as channel_modified
	from
		' . $prefix . 'cycle cce,
		' . $prefix . 'channel cnl
	where
		cnl.id = cce.channel_id and
		cnl.id = ' . (int)$lookup['channel_parent_id'] . ' and
		cce.modified > now() and
		cce.active = 1 and
		cnl.active = 1
	order by
		cnl.modified asc
	limit
		1
';
$result = mysql_query($sql) or die(mysql_error());
while ($row = mysql_fetch_assoc($result)) {
	$cycle['future'] = $row;
}
if (!empty($cycle['current']['cycle_modified'])) {
	$sql = '
		select
			id as channel_id,
			parent_id as channel_parent_id,
			value as channel_value,
			offset as channel_offset
		from
			' . $prefix . 'channel
		where
			modified < ' . to_sql($cycle['current']['cycle_modified']) . ' and 
			id = ' . (int)$lookup['channel_parent_id'] . '
		order by
			modified desc
		limit
			1
	';
	$result = mysql_query($sql) or die(mysql_error());
	while ($row = mysql_fetch_assoc($result)) {
		# overwriting should overwrite with same values if they exist
		$cycle['future']['channel_id'] = $row['channel_id'];
		$cycle['future']['channel_parent_id'] = $row['channel_parent_id'];
		$cycle['future']['channel_value'] = $row['channel_value'];
		$cycle['future']['channel_offset'] = $row['channel_offset'];

		$cycle['future']['cycle_modified'] = date('Y-m-d', strtotime($cycle['current']['cycle_modified']) + $row['channel_offset']*86400);
	}
}
# ensure future cycle exists in db
if (empty($cycle['future']['cycle_id'])) {
	$sql = '
		insert into
			' . $prefix . 'cycle
		set
			modified = ' . to_sql($cycle['future']['cycle_modified']) . ',
			channel_id = ' . (int)$cycle['future']['channel_id'] . ',
			active = 1
	';
	$result = mysql_query($sql) or die(mysql_error());
	$cycle['future']['cycle_id'] = mysql_insert_id();
}
# future cycle now exists

######################
# RENEWAL!
######################

# get most recent renewal data
$sql = '
	select
		rnal.id as renewal_id,
		rnal.point_id,
		rnal.modified as renewal_modified
	from
		' . $prefix . 'renewal rnal,
		' . $prefix . 'cycle cce,
		' . $prefix . 'channel cnl
	where
		cce.id = rnal.cycle_id and
		cnl.id = cce.channel_id and
		cnl.parent_id = ' . (int)$lookup['channel_parent_id'] . ' and
		rnal.user_id = ' . (int)$_SESSION['login']['login_user_id'] . ' and
		rnal.modified >= now() and
		rnal.active = 1 and
		cce.active = 1 and
		cnl.active = 1
	order by
		rnal.modified desc
	limit
		1
';
$result = mysql_query($sql) or die(mysql_error());
while ($row = mysql_fetch_assoc($result)) {
	$renewal['future'] = $row;
}
# get most recent renewal data
$sql = '
	select
		rnal.id as renewal_id,
		rnal.point_id,
		rnal.modified as renewal_modified
	from
		' . $prefix . 'renewal rnal,
		' . $prefix . 'cycle cce,
		' . $prefix . 'channel cnl
	where
		cce.id = rnal.cycle_id and
		cnl.id = cce.channel_id and
		cnl.parent_id = ' . (int)$lookup['channel_parent_id'] . ' and
		rnal.user_id = ' . (int)$_SESSION['login']['login_user_id'] . ' and
		rnal.modified >= now() and
		rnal.active = 1 and
		cce.active = 1 and
		cnl.active = 1
	order by
		rnal.modified desc
	limit
		1
';
$result = mysql_query($sql) or die(mysql_error());
while ($row = mysql_fetch_assoc($result)) {
	$renewal['future'] = $row;
}
# echo '<pre>'; print_r($renewal); echo '</pre>';


# get most recent renewal data
$sql = '
	select
		rnal.id as renewal_id,
		rnal.point_id,
		rnal.modified as renewal_modified
	from
		' . $prefix . 'renewal rnal,
		' . $prefix . 'cycle cce,
		' . $prefix . 'channel cnl
	where
		cce.id = rnal.cycle_id and
		cnl.id = cce.channel_id and
		cnl.parent_id = ' . (int)$lookup['channel_parent_id'] . ' and
		rnal.user_id = ' . (int)$_SESSION['login']['login_user_id'] . ' and
		rnal.modified >= now() and
		rnal.active = 1 and
		cce.active = 1 and
		cnl.active = 1
	order by
		rnal.modified desc
	limit
		1
';
$result = mysql_query($sql) or die(mysql_error());
while ($row = mysql_fetch_assoc($result)) {
	$renewal['future'] = $row;
}

# not keeping track of when the renewal was actually modified (form submitted)!
if (!empty($renewal['future'])) {
	# future cycle?
	$i1 = strtotime($renewal['modified']);
	if ($i1 >= strtotime($cycle['future']['modified'])) {
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
else {
	# todo compute payment # base on start and finish of
	# 2 renewals ago
	# ensures everyone starts receiving payment after exactly 3 variable cycle lengths
	# though they will pay according to renewals

	# if less than 2 renewals
	# just use $cycle['future']['channel_value']
	$cycle['future']['computed_renewal_value'] = $cycle['future']['channel_value'];

	# todo compute value from:
	# $cycle['future']['channel_value'];
	# $cycle['future']['my_rating_value']
	$cycle['future']['computed_rating_value'] = 0;

	$a1 = array(
		# start point
		'1',
		# continue/end/nextend point
		$lookup['point_id'],
	);
	foreach ($a1 as $k1 => $v1) {
		$sql = '
			insert into
				' . $prefix . 'renewal
			set
				point_id = ' . (int)$v1 . ',
				user_id = ' . (int)$_SESSION['login']['login_user_id'] . ',
				rating_value = ' . (int)$cycle['future']['computed_rating_value'] . ',
				value = ' . (double)$cycle['future']['computed_renewal_value'] . ',
				modified = now(),
				active = 1
		';
	}
}

# todo funds charge transaction will happen 1 day before in cron

# set message
$message = 'made it to the end';
process_success(tt('element', 'transaction_complete') . ($email_sent ? ' : ' . tt('element', 'email_sent') : ''));
