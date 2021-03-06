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

# description: process "first" donate/sponsor and upcoming sponsors - (does not process upcoming sponsors)

# note
# sponsor length
# - min = 1 day? ( current timeframe_id lags by the frequency of periodic_sponsor.php )
# - max = 1 year?
# - estimated norm = 30 days

# todo investigate session messages not displaying on the resulting page
# todo allow user to confirm before billing

# logic for sponsoring is significantly different from cycles including database structure and underlying rules
# sponsor has its own "cycle" defined by donate_offset

function is_sponsor_start($channel_parent_id) {
	global $prefix;
	global $config;
	$i1 = get_db_single_value('
			ssr.point_id
		from
			' . $prefix . 'sponsor ssr,
			' . $prefix . 'donate dne
		where
			dne.id = ssr.donate_id and
			dne.channel_parent_id = ' . (int)$channel_parent_id . ' and
			ssr.point_id != 3
		order by
			dne.id desc
	',0);
	switch($i1) {
		case '1': # should not happen
		case '2':
		case '4':
			return 0;
		break;
		case '3': # cycle has ended previously ended and needs restart
		default: # no result 
			return 1;
		break;
	}
}

# variable
$first_sponsor = 0;
$now = date('Y-m-d H:i:s');
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

# simplify by requiring that a donate listing be created first

# todo get the rest of the donation data
# todo eliminate extra sql query above
$sql = '
	select
		id as donate_id,
		offset as donate_offset,
		value as donate_value
	from
		' . $prefix . 'donate
	where
		channel_parent_id = ' . (int)$lookup['channel_parent_id'] . ' and
		user_id = ' . (int)$login_user_id . '
	limit
		1
';
if ($config['debug'] == 1)
	print_debug($sql);
$result = mysql_query($sql) or die(mysql_error());
while ($row = mysql_fetch_assoc($result)) {
	$lookup['donate_id'] = $row['donate_id'];
	$lookup['donate_offset'] = $row['donate_offset'];
	$lookup['donate_value'] = $row['donate_value'];
}

process_field_missing('action_content_1');

# todo merge into process_does_not_exist('action_content_1');
if (empty($message))
if (!$lookup['channel_parent_id'])
	$message = tt('element', 'channel_name') . ' : ' . tt('element', 'error_does_not_exist');
if (empty($message))
if ($lookup['point_id'] == 1)
	$message = tt('point', 'start') . ' : ' . tt('element', 'error');
if (empty($message))
if (empty($lookup['donate_id']))
	$message = tt('element', 'error_donate_id');

# failure
process_failure($message);

# do it
$first_sponsor = is_sponsor_start($lookup['channel_parent_id']);

if (!empty($first_sponsor)) {
	$sql = '
		insert into
			' . $prefix . 'sponsor
		set
			donate_id = ' . (int)$lookup['donate_id'] . ',
			point_id = 1,
			timeframe_id = 2,
			start = ' . to_sql($now) . ',
			modified = now(),
			active = 1
	';
	print_debug($sql);
	mysql_query_process($sql);
	# todo charge the user for this sponsor
	$i1 = mysql_insert_id();

	do_accounting('sponsor', $i1, -$lookup['donate_value'], $login_user_id);

	$sql = '
		insert into
			' . $prefix . 'sponsor
		set
			donate_id = ' . (int)$lookup['donate_id'] . ',
			point_id = ' . (int)$lookup['point_id'] . ',
			timeframe_id = 3,
			start = date_add(' . to_sql($now) . ', interval ' . (int)$lookup['donate_offset'] . ' second),
			modified = now(),
			active = 1
	';
	print_debug($sql);
	mysql_query_process($sql);
	if (1) {
		# todo update the previous timeframe_id
	}
}
if (empty($first_sponsor)) {
	# todo find the last sponsor
	$sql = '
		select
			dne.id as donate_id,
			ssr.id as sponsor_id,
			dne.offset as donate_offset,
			dne.value as donate_value
		from
			' . $prefix . 'sponsor ssr,
			' . $prefix . 'donate dne
		where
			dne.id = ssr.donate_id and
			dne.user_id = ' . $login_user_id . ' and
			dne.channel_parent_id = ' . (int)$lookup['channel_parent_id'] . '
		order by
			ssr.id desc
		limit
			1
	';
	$a1 = array();
	print_debug($sql);
	$result = mysql_query($sql) or die(mysql_error());
	while($row = mysql_fetch_assoc($result)) {
		$a1 = $row;
	}
	# update value/offset of the future sponsor is done through donate
	if (1) {
		$sql = '
			update
				' . $prefix . 'sponsor
			set
				point_id = ' . (int)$lookup['point_id'] . ',
				donate_id = ' . (int)$a1['donate_id'] . ',
				modified = now(),
			where
				id = ' . (int)$a1['sponsor_id'] . '
			limit
				1
		';
		print_debug($sql);
		mysql_query_process($sql);
	}
}
# todo continuing sponsorships will be processed by cron
if ($config['debug'] == 1)
	echo '<hr>';
process_success(tt('element', 'transaction_complete') . ($email_sent ? ' : ' . tt('element', 'email_sent') : ''));
