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

# number of previous cycles to carry over
# this is a hardcode =(
$config['cycle_carry'] = 3;
# limit set at 8 ie) 2^8 = 512 as a safety but should be less than 3 or less

# variable
$data['user_report']['channel_list'] = array();
# alias
$channel_list = & $data['user_report']['channel_list'];
# get only the cycle that was specified
$sql = '
	select
		cnl.parent_id as channel_parent_id,
		cce.id as cycle_id
	from
		' . $config['mysql']['prefix'] . 'channel cnl,
		' . $config['mysql']['prefix'] . 'cycle cce
	where
		cnl.id = cce.channel_id and
		cce.id = ' . (int)get_gp('cycle_id') . '
	order by
		cnl.id asc
	limit 1
';
$result = mysql_query($sql) or die(mysql_error());
while ($row = mysql_fetch_assoc($result)) {
	$channel_list[$row['channel_parent_id']] = array(
		'seed' => array(
			'cycle_id' => $row['cycle_id'],
		),
	);
}




# huge computation
foreach ($channel_list as $k1 => $v1) {
	# when setting an alias within a foreach it will have to be set again in the t1 file =(
	$channel = & $channel_list[$k1];
	# set sort
	$channel['renewal'] = array();
	$channel['sponsor'] = array();
	$channel['payout'] = array();
	do_payout_computation($channel, $k1, $_GET['cycle_id']);

	# todo allow increasing sponsor but never decreasing
	# todo as such there can be many sponsors for the same cycle from the same user
	# todo make sure the user gets charged everytime for the increasing cost
	$channel['donate_user_id'] = array();
	if (1) {
		$donate_user_id = & $channel['donate_user_id'];
		$sql = '
			select
				"none" as overflow,
				dne.user_id as donate_user_id,
				ssr.id as sponsor_id,
				ssr.point_id,
				ssr.start,
				dne.offset as donate_offset,
				dne.value as donate_value
			from
				ts_donate dne,
				ts_sponsor ssr
			where
				dne.id = ssr.donate_id and
				ssr.start < ' . to_sql($channel['cycle_restart']['yyyy-mm-dd-2x']) . ' and
				ssr.start >= ' . to_sql($channel['cycle_restart']['yyyy-mm-dd-3x']) . ' and
				1
			order by
				dne.id desc
		';
		$result = mysql_query($sql) or die(mysql_error());
		while ($row = mysql_fetch_assoc($result)) {
			$donate_user_id[$row['donate_user_id']][$row['sponsor_id']] = $row;
		}
		# set overflow for the cyspons
		foreach ($donate_user_id as $k1 => $v1) {
			$donate_user_id[$k1] += get_left_cyspon_array($channel['cycle_restart']['yyyy-mm-dd-3x'], $k1);
			$i1 = get_right_cyspon_id($channel['cycle_restart']['yyyy-mm-dd-2x'], $k1);
			$donate_user_id[$k1][$i1]['overflow'] = 'right';
		}
		# since the first and last cycspons are marked it is now possible to calculate
		# already have all the sponsors in the timeframe so just need to fill in the holes
		$d1 = 0; # get the total value
		$k1 = 0;
		foreach ($donate_user_id as $k1 => $v1) {
		foreach ($v1 as $k2 => $v2) {
			switch($v2['overflow']) {
				case 'left':
					$d1 += get_cyspon_left_value(
						$v2,
						$channel['cycle_restart']['yyyy-mm-dd-3x'] 
					);
				break;
				case 'none':
					$d1 += $v2['donate_offset'] / 86400 * $v2['donate_value'];
				break;
				case 'right':
					$d1 += get_cyspon_right_value(
						$v2,
						$channel['cycle_restart']['yyyy-mm-dd-2x']
					);
				break;
				default:
					die('bad type for sponsor');
				break;
			}
		} }
		# todo more concise data structure possible?
		if (!empty($k1))
			$channel['donate_value']['user_id'][$k1] = $d1;
	}

	get_payout_array($channel);
}

# all the numbers are setup to get payouts now
