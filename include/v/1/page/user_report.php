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

# Contents/Description: compute the payout for users based on members only. (Do not compute all ratings)

# not doing a full rating list (ie. hidden ratings) because only visable votes can be transparent and accountable
# also decentralization is important and full ratings will not be practical if hidden and decentralized

# todo
# make sure the ratings are public that are computed!
# start and end of ratings
# factor in how many ratings are given by a person (if one person rates everyone vs 1 person)?

$data['user_report']['channel_list'] = array(
	'test' => array(
		'info' => array(
			'name' => 'nice',
			'cost' => 100,
		),
		'member_list' => array(
			'132' => '132',
			'135' => '135', # eileen
			'137' => '137', # pattyjo
		),
		# works for now
		'average_sum' => array(
		),
		'average_average' => array(
		),
		'average_weight' => array(
		),
	)
);

$channel = & $data['user_report']['channel_list'];

foreach ($channel as $kc1 => $vc1) {
	$sql = '
		select
			r.destination_user_id
		from
			' . $config['mysql']['prefix'] . 'rating r,
			' . $config['mysql']['prefix'] . 'grade g
		where
			r.source_user_id in (' . implode(', ', $channel[$kc1]['member_list']) . ') and
			r.grade_id = g.id AND
			r.active = 1
		group by
			r.destination_user_id
		order by
			r.destination_user_id
	';
	$result = mysql_query($sql) or die(mysql_error());
	while ($row = mysql_fetch_assoc($result)) {
		$channel[$kc1]['destination_user_id'][$row['destination_user_id']] = array();
	}


	foreach ($channel[$kc1]['destination_user_id'] as $kd1 => $vd1) {
		# alias
		$kid = & $channel[$kc1]['destination_user_id'][$kd1];
		

		# get sum
		foreach ($channel[$kc1]['member_list'] as $k1 => $v1) {
			$sql = '
				select
					source_user_id,
					sum(g.value) as summer,
					count(g.value) as counter
				FROM
					' . $config['mysql']['prefix'] . 'rating r,
					' . $config['mysql']['prefix'] . 'grade g
				WHERE
					r.source_user_id = ' . (int)$v1 . ' and 
					r.grade_id = g.id AND
					r.active = 1 and
					destination_user_id = ' . (int)($kd1) . '
			';
			$result = mysql_query($sql) or die(mysql_error());
			while ($row = mysql_fetch_assoc($result)) {
				if ($row['counter'])
					$kid['source_user_id_rating_average'][$row['source_user_id']] = $row['summer'] / $row['counter'];
			}
		}

		$channel[$kc1]['average_sum'][$kd1] = array_sum($kid['source_user_id_rating_average']);
		$channel[$kc1]['average_weight'][$kd1] = count($kid['source_user_id_rating_average']);
		$channel[$kc1]['average_average'][$kd1] = array_sum($kid['source_user_id_rating_average']) / count($kid['source_user_id_rating_average']);
	}
}
