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

# description: order by the most relevant channels - go by more recent cycle not most recent payout period

# todo show all cycle data trailing back to the payment_cycle
# todo include a warning message on premature payment_cycle data
# todo still display premature payment_cycle data from what is available

add_translation('element', 'search_mixed');
add_translation('page', 'member_report');

# variable
$data['member_report']['order'] = array();
$data['member_report']['channel'] = array();
# todo $data['member_report']['cycle_offset'][$k1]['user_count'] = '???';
# todo $data['member_report']['channel_sort'];

# alias
$channel = & $data['member_report']['channel'];
$order = & $data['member_report']['order'];
$payout_order = & $data['member_report']['payout_order'];

# todo how to find what channels are within the last 3 cycles?
# todo get every single channel parent id
# todo get all the latest renewals for every channel
# todo then see if there is an accounting entry to matchup for those cycles
$sql = '
	select
		distinct(cnl.parent_id)
	from
		' . $config['mysql']['prefix'] . 'channel cnl
	where
		cnl.timeframe_id = 2
';
$result = mysql_query($sql) or die(mysql_error());
while ($row = mysql_fetch_assoc($result)) {
	$channel[$row['parent_id']] = array();
	$order[$row['parent_id']] = 0;
}

foreach($channel as $k1 => $v1) {
	# current channel name lags behind by 1 cycle
	get_channel_cycle_restart_array($channel[$k1], $k1);
	# alias
	$cycle_offset = & $channel[$k1]['cycle_offset'];
	# get name and description
	if (!empty($cycle_offset[0]['start'])) {
		$sql = '
			select
				name,
				description
			from
				' . $config['mysql']['prefix'] . 'channel
			where
				parent_id = ' . (int)$k1 . ' and
				timeframe_id = 2
			order by
				modified desc
			limit
				1 
		';
		$result = mysql_query($sql) or die(mysql_error());
		while ($row = mysql_fetch_assoc($result))
			$channel[$k1]['info'] = array_merge($channel[$k1]['info'], $row);
 	}
	else {
		unset($channel[$k1]);
		unset($order[$k1]);
	}
}

foreach($channel as $k1 => $v1) {
	# alias
	$cycle_offset = & $channel[$k1]['cycle_offset'];
	$info = & $channel[$k1]['info'];

	# get count of current members
	if (1) {
		# get the latest cycle_id for the channel
		$info['cycle_id'] = get_db_single_value('
			cce.id from
				' . $config['mysql']['prefix'] . 'cycle cce,
				' . $config['mysql']['prefix'] . 'channel cnl 
			where
				cce.channel_id = cnl.id and
				cnl.parent_id = ' . (int)$k1 . ' and
				cce.start = ' . to_sql($cycle_offset[0]['start'])
		);
		# cycle_id should already exist because this logic is essentially repeated
		$sql = '
			select
				count(distinct user_id) as count
			from
				' . $config['mysql']['prefix'] . 'renewal
			where
				cycle_id = ' . (int)$info['cycle_id']
		;
		$result = mysql_query($sql) or die(mysql_error());
		while ($row = mysql_fetch_assoc($result)) {
			$order[$k1] = $row['count'];
		}
	}
	if (1) {
		# get the payout cycle_id for the channel
		# offset 3 is the latest payout ( 0 = event horizon / 1 = most recent / 2 = payout hold / 3 payout )
		$info['payout_cycle_id'] = get_db_single_value('
			cce.id from
				' . $config['mysql']['prefix'] . 'cycle cce,
				' . $config['mysql']['prefix'] . 'channel cnl 
			where
				cce.channel_id = cnl.id and
				cnl.parent_id = ' . (int)$k1 . ' and
				cce.start = ' . to_sql($cycle_offset[3]['start'])
		);
		# cycle_id should already exist because this logic is essentially repeated
		$sql = '
			select
				count(distinct user_id) as count
			from
				' . $config['mysql']['prefix'] . 'renewal
			where
				cycle_id = ' . (int)$info['payout_cycle_id']
		;
		$result = mysql_query($sql) or die(mysql_error());
		while ($row = mysql_fetch_assoc($result)) {
			$payout_order[$k1] = $row['count'];
		}
	}
}
arsort($order);
arsort($payout_order);

$data['search']['response']['search_miscellaneous'] = array( 'keyword' => get_gp('keyword'));
$data['search']['response']['search_content_2'] = get_search_content_2();
load_response('search_content_2', $data['search']['response']['search_content_2'], $_SESSION['login']['login_user_id']);
contact_user_mixed_combine($data['search']['response']['search_content_2'], get_gp('lock_user_id'), get_gp('lock_contact_id'), $_SESSION['login']['login_user_id'], 'lock_');
foreach ($data['search']['response'] as $k1 => $v1) {
if (!empty($v1)) {
foreach ($v1 as $k2 => $v2) {
	add_option($k2);
	add_translation('element', $k2);
} } }


# show donors
