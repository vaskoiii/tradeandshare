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

# description: compute the payout for users based on members to members only. (Do not compute all ratings)

# todo: test multiple channels

# todo figure out what to do with no member to member ratings at all
# maybe just hold the pot until next time and do a check. note that rating yourself anything above 0 value would mean you get the whole pot!-10% for TS

# todo get computation date
# keep adding $config['cycle_length'] days to the start until we have the correct day?
# unused config variable: $config['cycle_start']
# currently overriding the cycle end/reporting date and assuming it is now for testing

# todo make it so that users do not have to be manually entered into ts_renewal

# not doing a full rating list (ie. hidden ratings) because only visible votes can be transparent and accountable
# also decentralization is important and hidden will not be practical if hidden and decentralized

# day of report making (end of cycle)
$data['user_report']['cycle_restart']['yyyy-mm-dd'] = date('Y-m-d');
$cycle_restart = & $data['user_report']['cycle_restart']; # alias
$a1 = explode('-', $cycle_restart['yyyy-mm-dd']);
$cycle_restart['yyyy'] = $a1[0];
$cycle_restart['mm'] = $a1[1];
$cycle_restart['dd'] = $a1[2];
$cycle_restart['previous_restart'] = date('Y-m-d', (mktime(0, 0, 0, $cycle_restart['mm'], $cycle_restart['dd'], $cycle_restart['yyyy']) - 30*86400));

$sql = '
	select
		channel_id
	from
		' . $config['mysql']['prefix'] . 'renewal
	where
		modified >= ' . to_sql($cycle_restart['previous_restart']) . '
	group by
		channel_id
	order by
		channel_id asc
';
$result = mysql_query($sql) or die(mysql_error());
$data['user_report']['channel_list'] = array();
$channel = & $data['user_report']['channel_list']; # alias
while ($row = mysql_fetch_assoc($result)) {
	$channel[$row['channel_id']] = array();
}
foreach($channel as $k1 => $v1) {
	$sql = '
		select
			cnl.name,
			ct.value as cost, -- max cost?
			' . (int)$config['cycle_length'] . ' as time
		from
			' . $config['mysql']['prefix'] . 'channel cnl,
			' . $config['mysql']['prefix'] . 'cost ct
		where
			cnl.id = ct.channel_id and
			cnl.id = ' . (int)$k1
	;
	$result = mysql_query($sql) or die(mysql_error());
	while ($row = mysql_fetch_assoc($result))
		$channel[$k1]['info'] = $row;
	$sql = '
		select
			user_id
		from
			' . $config['mysql']['prefix'] . 'renewal rnal
		where
			channel_id = ' . (int)$k1 . ' and
			modified >= ' . to_sql($cycle_restart['previous_restart'])
	;
	$result = mysql_query($sql) or die(mysql_error());
	while ($row = mysql_fetch_assoc($result))
		$channel[$k1]['member_list'][$row['user_id']] = $row['user_id'];

	$channel[$k1]['member_cost'] = array(); # how much paid for the timeframe ie) discounted membership from good rating?
	$channel[$k1]['member_time'] = array(); # days (out of $config['cycle_length'])
	$channel[$k1]['average_weight_sum'] = array();
}
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
					r.channel_id = ' . (int)$kc1 . ' and
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
				channel_id = ' . (int)$kc1 . ' and
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
			# self-rating is not counted
	}
	# how much paid for the timeframe ie) discounted membership from good rating
	foreach ($channel[$kc1]['source_user_id'] as $ks1 => $vs1) {
		$kis = & $channel[$kc1]['source_user_id'][$ks1]; # alias
		$sql = '
			select
				rating_value,
				value as renewal_value
			from
				' . $config['mysql']['prefix'] . 'renewal
			where
				user_id = ' . (int)$ks1 . ' and
				-- inequalities are intended
				modified < ' . to_sql($cycle_restart['yyyy-mm-dd']) . ' and
				modified >= ' . to_sql($cycle_restart['previous_restart'])
		;
		$result = mysql_query($sql) or die(mysql_error());
		while($row = mysql_fetch_assoc($result)) {
			$kis['previous_average'] = $row['rating_value'];
			$kis['member_cost'] = $row['renewal_value']; # $
			# todo get channel cost based on value 1 month ago
			$kis['cost_weight'] = $kis['member_cost'] / $channel[$kc1]['info']['cost'];
			$channel[$kc1]['member_cost'][$ks1] = $kis['member_cost'];
		}
	}
	# time with the current membership period?
	foreach ($channel[$kc1]['source_user_id'] as $ks1 => $vs1) {
		$kis = & $channel[$kc1]['source_user_id'][$ks1]; # alias
		$sql = '
			select
				modified
			from
				' . $config['mysql']['prefix'] . 'renewal
			where
				-- inequalities are intended
				modified < ' . to_sql($cycle_restart['yyyy-mm-dd']) . ' and
				modified >= ' . to_sql($cycle_restart['previous_restart'])
		;
		$result = mysql_query($sql) or die(mysql_error());
		while($row = mysql_fetch_assoc($result)) {
			$kis['member_restart'] = $row['modified'];
			$kis['member_time'] = (strtotime($cycle_restart['yyyy-mm-dd']) - strtotime($kis['member_restart']))/86400 ;
			$kis['time_weight'] = $kis['member_time'] / $config['cycle_length'];
			$channel[$kc1]['member_time'][$ks1] = $kis['member_time'];
		}
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
