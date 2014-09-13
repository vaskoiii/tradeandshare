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

# description: compute the payout for users based on members only. (Do not compute all ratings)

# todo: logic is needed for membership - ie) member_list is not yet implemented 
# todo: test multiple channels

# not doing a full rating list (ie. hidden ratings) because only visable votes can be transparent and accountable
# also decentralization is important and hidden will not be practical if hidden and decentralized

$data['user_report']['channel_list'] = array(
	'test' => array(
		'info' => array(
			'name' => 'nice',
			'cost' => 100, # maybe should be max cost
			'time' => 30,
		),
		# todo when this gets organized make this a table join on the sql below
		'member_list' => array(
			'132' => '132',
			'134' => '134', # david
			'135' => '135', # eileen
			'137' => '137', # pattyjo
			'147' => '147', # jack 
			'149' => '149', # soraya
		),
		# todo how much paid for the timeframe ie) discounted membership from good rating?
		'member_cost' => array(
		),
		# todo how many days with the current membership period? (out of 30)
		'member_day' => array(
		),
		'average_weight_sum' => array(
		),
	)
);
$channel = & $data['user_report']['channel_list'];

foreach ($channel as $kc1 => $vc1) {
	# prepare additional computation arrays
	foreach ($vc1['member_list'] as $k1 => $v1) {
		$channel[$kc1]['destination_user_id'][$k1] = array();
		$channel[$kc1]['source_user_id'][$k1] = array();
	}
	# destination user
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
					r.source_user_id != r.destination_user_id and
					r.grade_id = g.id AND
					r.team_id = ' . (int)$config['everyone_team_id'] . ' and 
					r.source_user_id = ' . (int)$v1 . ' and 
					r.destination_user_id = ' . (int)($kd1) . ' and
					r.active = 1
			';
			$result = mysql_query($sql) or die(mysql_error());
			while ($row = mysql_fetch_assoc($result)) {
				if ($row['counter'])
					$kid['source_user_id_rating_average'][$row['source_user_id']] = $row['summer'] / $row['counter'];
			}
		}
		# if a user pays membership but doesn't rate anyone that money goes into the pot but doesn't affect the ratings
		## like paying taxes but not voting
		$channel[$kc1]['average_sum'][$kd1] = 0;
		if (!empty($kid['source_user_id_rating_average']))
			$channel[$kc1]['average_sum'][$kd1] = array_sum($kid['source_user_id_rating_average']);
		$channel[$kc1]['average_weight'][$kd1] = count($kid['source_user_id_rating_average']);
		$channel[$kc1]['average_average'][$kd1] = 0;
		if (!empty($kid['source_user_id_rating_average']))
			$channel[$kc1]['average_average'][$kd1] = array_sum($kid['source_user_id_rating_average']) / count($kid['source_user_id_rating_average']);
	}
	# source user
	# how many users rated
	foreach ($channel[$kc1]['source_user_id'] as $ks1 => $vs1) {
		$kis = & $channel[$kc1]['source_user_id'][$ks1]; # alias
		$kis['user_rating_count'] = 0;
		$sql = '
			select
				count(distinct destination_user_id) as user_rating_count
			FROM
				' . $config['mysql']['prefix'] . 'rating
			WHERE
				source_user_id in (' . implode(', ', $channel[$kc1]['member_list']) . ') and
				destination_user_id in (' . implode(', ', $channel[$kc1]['member_list']) . ') and
				source_user_id != destination_user_id and
				team_id = ' . (int)$config['everyone_team_id'] . ' and 
				source_user_id = ' . (int)$ks1 . ' and 
				active = 1
		';
		$result = mysql_query($sql) or die(mysql_error());
		while ($row = mysql_fetch_assoc($result)) {
			if ($row['user_rating_count']) {
				$kis['user_rating_count'] = $row['user_rating_count'];
			}
		}
	}
	foreach ($channel[$kc1]['source_user_id'] as $ks1 => $vs1) {
		$kis = & $channel[$kc1]['source_user_id'][$ks1]; # alias
		if (empty($kis['user_rating_count']))
			$kis['count_weight'] = 0;
		else
			$kis['count_weight'] = 1 / $kis['user_rating_count'];
			# $kis['count_weight'] = (count($vc1['member_list']) - 1) / $kis['user_rating_count']; # -1 = self rating not counted
	}
	## how much paid for the timeframe ie) discounted membership from good rating?
	foreach ($channel[$kc1]['source_user_id'] as $ks1 => $vs1) {
		$kis = & $channel[$kc1]['source_user_id'][$ks1]; # alias
		$kis['previous_average'] = '4'; # needed to compute cost weight
		$kis['member_cost'] = $channel[$kc1]['info']['cost']; # $
		$kis['cost_weight'] = $kis['member_cost'] / 100;
	}
	## how many days with the current membership period? (out of 30)
	foreach ($channel[$kc1]['source_user_id'] as $ks1 => $vs1) {
		$kis = & $channel[$kc1]['source_user_id'][$ks1]; # alias
		$kis['member_time'] = $channel[$kc1]['info']['time']; # days
		$kis['time_weight'] = $kis['member_time'] / 30;
	}
	## weighted cost
	foreach ($channel[$kc1]['source_user_id'] as $ks1 => $vs1) {
		$kis = & $channel[$kc1]['source_user_id'][$ks1]; # alias
		$kis['weight_cost'] = $kis['member_cost'] * $kis['cost_weight'] * $kis['time_weight']; 
		# helper array - duplicates data =( - could also do a foreach
		$channel[$kc1]['weight_cost'][$ks1] = $kis['weight_cost'];
	}
	### compute weighted averages
	foreach ($channel[$kc1]['destination_user_id'] as $kd1 => $vd1) {
		$kid = & $channel[$kc1]['destination_user_id'][$kd1]; # alias
		if (!empty($kid['source_user_id_rating_average']))
		foreach ($kid['source_user_id_rating_average'] as $k1 => $v1) {
			$kis = & $channel[$kc1]['source_user_id'][$k1]; # alias
			$kid['source_user_id_rating_weight'][$k1] = $v1 * $kis['count_weight'] * $kis['cost_weight'] * $kis['time_weight'];
		}
	}
	# average_weight_sum
	foreach ($channel[$kc1]['destination_user_id'] as $kd1 => $vd1) {
		$kid = & $channel[$kc1]['destination_user_id'][$kd1]; # alias
		$channel[$kc1]['average_weight_sum'][$kd1] = 0;
		if (!empty($kid['source_user_id_rating_weight']))
			$channel[$kc1]['average_weight_sum'][$kd1] = array_sum($kid['source_user_id_rating_weight']);
	}
}
