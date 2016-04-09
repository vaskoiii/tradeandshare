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

# description: process "first" donate and update donate listings

# donate length
# - min = 1 day? ( current timeframe_id lags by the frequency of periodic_sponsor.php )
# - max = 1 year?
# - estimated norm = 30 days

# donate for now are intended to encourage channel membership
# todo user based donations may be added later
# todo make it less abstract to donate (currently have to add a donate first and then sponsor)
# todo when the sponsor happens just use the most recent donate
# todo orphan data can be removed later

# variable
# $config['write_protect'] = 1;
$process['action_content_1']['channel_name'] = get_gp('channel_name');
$process['action_content_1']['donate_offset'] = get_gp('donate_offset');
$process['action_content_1']['donate_value'] = get_gp('donate_value');

# translation
$lookup['channel_parent_id'] = get_db_single_value('
		parent_id
	from
		' . $prefix . 'channel cnl
	where
		name = ' . to_sql($process['action_content_1']['channel_name']) . ' and
		active = 1
');
$lookup['donate_offset'] = get_gp('donate_offset');
$lookup['donate_value'] = get_gp('donate_value');

process_field_missing('action_content_1');

# todo merge into process_does_not_exist('action_content_1');
if (!$message)
	if (!$lookup['channel_parent_id'])
		$message = tt('element', 'channel_name') . ' : ' . tt('element', 'error_does_not_exist');

# failure
process_failure($message);

# do it
if (1) {
	# todo find the last donate 
	$sql = '
		select
			dne.id as donate_id,
			dne.offset as donate_offset,
			dne.value as donate_value
		from
			' . $prefix . 'donate dne
		where
			dne.user_id = ' . $login_user_id . ' and
			dne.channel_parent_id = ' . (int)$lookup['channel_parent_id'] . '
		order by
			dne.id desc
		limit
			1
	';
	$a1 = array();
	print_debug($sql);
	$result = mysql_query($sql) or die(mysql_error());
	while($row = mysql_fetch_assoc($result)) {
		$a1 = $row;
	}
	$b1 = 1;
	$i2 = 0;
	if (!empty($a1)) {
		# todo verify this part is working
		if ($a1['donate_offset'] == $lookup['donate_offset'])
		if ($a1['donate_value'] == $lookup['donate_value']) {
			$b1 = 2;
			$i2 = $a1['donate_id'];
		}
	}
	if ($b1 == 1) {
		$sql = '
			insert into
				' . $prefix . 'donate
			set
				channel_parent_id = ' . (int)$lookup['channel_parent_id'] . ',
				user_id = ' . (int)$login_user_id . ',
				timeframe_id = 1,
				offset = ' . (int)$lookup['donate_offset'] . ',
				value = ' . (double)$lookup['donate_value'] . ',
				modified = now(),
				active = 1
		';
		print_debug($sql);
		if ($config['write_protect'] != 1)
			mysql_query($sql) or die(mysql_error());

		$i2 = mysql_insert_id();
	}
	if (1) {
		# todo update sponsor to point to the new donate
		# todo show the debug message
		$i1 = 0;
		$sql = '
			select
				ssr.id,
				ssr.point_id
			from
				' . $prefix . 'sponsor ssr,
				' . $prefix . 'donate dne
			where
				dne.id = ssr.donate_id and
				dne.user_id = ' . $login_user_id . ' and
				dne.channel_parent_id = ' . $lookup['channel_parent_id'] . '
			order by
				ssr.id desc
			limit
				1
		';
		print_debug($sql);
		$result = mysql_query($sql) or die(mysql_error());
		while ($row = mysql_fetch_assoc($result))
			$a2 = $row;
		# todo fix bad error checkning
		# no sponsor donate (or resuming donate after sponsor end) will not have a value for $a2['id']
		if (!empty($a2))
		if ($a2['point_id'] != 3) {
			$sql = '
				update
					' . $prefix . 'sponsor
				set
					donate_id = ' . (int)$i2 . '
				where
					id = ' . (int)$a2['id'] . '
				limit
					1	
			';
			print_debug($sql);
			mysql_query($sql) or die(mysql_error());
		}
	}
}
if ($config['debug'] == 1)
	echo '<hr>';
process_success(tt('element', 'transaction_complete') . ($email_sent ? ' : ' . tt('element', 'email_sent') : ''));
