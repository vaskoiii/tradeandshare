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

# todo this variable will have to be accounted for in another function

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

	# todo need to split out these before the payout array
	# todo need to make injecting a debug only option

	# todo calculate the values
	# todo allow increasing sponsor but never decreasing
	# todo as such there can be many sponsors for the same cycle from the same user
	# todo make sure the user gets charged everytime for the increasing cost
	$channel['sponsor_user_id'] = array();
	# to be put into an array like:
	if ($config['debug'] == 1) {
		$channel['sponsor_user_id'] = array(
			'132' => array(
				# # todo get the pre cycle sponsor id if applicable
				# # ie. if the sponsor in the cycle will have point_id != 1
				# $sql = '
				# 	select
				# 		id
				# 	from
				# 		ts_sponsor
				# 	where
				# 		id < ' . $non_start_sponsor_id . ' 
				# 	order by
				# 		id desc
				# 	limit
				# 		1
				# ';
				'sponsor_id' => array(
					'1' => array(
						'offset' => '?',
						'overlap' => 1,
						'value' => 2,
					),
					'2' => array(
						'offset' => '?',
						'overlap' => 1,
						'value' => 3,
					),
					'3' => array(
						'offset' => '?',
						'overlap' => 1,
						'value' => 4,
					),
				),
				'computed' => array(
					'donate_total' => 9,
				),
			),
		);
	}
	# set sort
	$channel['renewal'] = array();
	$channel['sponsor'] = array();
	$channel['payout'] = array();


	do_payout_computation($channel, $k1, $_GET['cycle_id']);
	get_payout_array($channel);
}



# all the numbers are setup to get payouts now
