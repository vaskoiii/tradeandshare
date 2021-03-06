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

# description: allow adding a channel

# see includes from:
# edit_process.php

if ($process)
foreach ($process as $k1 => $v1)
if ($v1)
foreach ($v1 as $k2 => $v2)
switch ($k2) {
	default:
		if (str_match('_description', $k2))
			$process[$k1][$k2] = trimmage(get_gp($k2));
		else
			$process[$k1][$k2] = get_gp($k2);
	break;
}

process_field_missing('action_content_1');
process_data_translation('action_content_1');

# extra conversion for the offset
# todo this is actually a $lookup not a $process
$process['action_content_1']['channel_offset'] = get_gp('channel_offset_day') * 86400;

# prevent channel name change
if (1) {
}

if (!empty($id)) {
	$s1 = get_db_single_value('
			name
		from
			' . $prefix . 'channel
		where
			id = ' . (int)$id
	);
	if ($_POST['channel_name'] != $s1)
		$message = tt('element', 'error_name_change_not_allowed');
	# $message = 'can not modify channels until logic is complete only add';
}
process_failure($message);

# do it

# todo rename to parent_channel_id to be more clear */ 
$s1 = '
	parent_id = 0,
	timeframe_id = 3,
';
if (!empty($id)) {
	$s1 = '
		parent_id = ' . (int)$lookup['channel_id'] . ',
		timeframe_id = 2,
	';
}
$sql = '
	insert into
		' . $prefix . 'channel
	SET
		user_id = ' . (int)$login_user_id . ',
		value = ' . to_sql($action_content_1['channel_value']) . ',
		offset = ' . to_sql($action_content_1['channel_offset']) . ',
		percent = ' . to_sql($action_content_1['channel_percent']) . ',
		name = ' . to_sql($action_content_1['channel_name']) . ',
		description = ' . to_sql($action_content_1['channel_description']) . ',
		' . $s1 . '
		modified = now(),
		active = 1
';
mysql_query($sql) or die(mysql_error());
if (empty($id)) {
	$i1 = mysql_insert_id();
	$sql = '
		update
			' . $prefix . 'channel
		set
			parent_id = ' . (int)$i1 . '
		where
			id = ' . (int)$i1 . '
		limit
			1
	';
	mysql_query($sql) or die(mysql_error());
}

process_success(tt('element', 'transaction_complete') . ($email_sent ? ' : ' . tt('element', 'email_sent') : ''));
exit;
