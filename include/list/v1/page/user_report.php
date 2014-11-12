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

# not doing a full rating list (ie. hidden ratings) because only visible votes can be transparent and accountable
# also decentralization is important and hidden will not be practical if hidden and decentralized

### Cycle Diagram #################################################
#                                                                 #
#                     -3x      -2x      -1x       0x (process)    #
#   timeline           |________|________|________|               #
#                                                                 #
#   eligibility        |________|                                 #
#   payout                      |________|                        #
#   review                               |________|               #
#                                                                 #
### Evaluating Payout Membership Renewal ##########################
#                                                                 #
#   start                         |______ (after)                 #
#   continue                    __|______ (before/after)          #
#   end and start               ____|  |_ (before|ignore|after)   #
#   end                         ______|   (before)                #
#                                                                 #
###################################################################

# todo compute rating weight for before and after
# todo use ratings in weighted region for their respective weights ie) ratings after will 

# todo weighed 1 month payout ie) if you are only eligible for 15/30 days for the payout cycle

# todo test multiple channels

# todo figure out what to do with no member to member ratings at all
# maybe just hold the pot until next time and do a check. note that rating yourself anything above 0 value would mean you get the whole pot!-10% for TS

# todo computation dates
# - use ts_cycle
# - implement the cycle_list page ie) show human readable dates in the subject
# - fix unused config variable: $config['cycle_start']
# - fix currently overriding the cycle end/reporting date and assuming it is now for testing

# todo make it so that users do not have to be manually entered into ts_renewal

# day of report making (end of cycle)
# todo get the cycle start from the cycle table
# todo get variable cycle offset from the cycle table
# todo 30 day unchangeable cycle length is unacceptable
$data['user_report']['cycle_restart']['yyyy-mm-dd'] = date('Y-m-d');
$cycle_restart = & $data['user_report']['cycle_restart']; # alias
$cycle_restart['yyyy-mm-dd-1x'] = date('Y-m-d', strtotime($cycle_restart['yyyy-mm-dd']) - 30*86400);
$cycle_restart['yyyy-mm-dd-2x'] = date('Y-m-d', strtotime($cycle_restart['yyyy-mm-dd']) - 60*86400);
$cycle_restart['yyyy-mm-dd-3x'] = date('Y-m-d', strtotime($cycle_restart['yyyy-mm-dd']) - 90*86400);

# todo get cycle 
$sql = '
	select
		cce.channel_id
	from
		' . $config['mysql']['prefix'] . 'renewal rnal,
		' . $config['mysql']['prefix'] . 'cycle cce
	where
		cce.id = rnal.cycle_id and
		rnal.modified >= ' . to_sql($cycle_restart['yyyy-mm-dd-3x']) . '
	group by
		cce.channel_id
	order by
		cce.channel_id asc
';
# $sql = '
# 	select
# 		channel_id
# 	from
# 		' . $config['mysql']['prefix'] . 'renewal
# 	where
# 		modified >= ' . to_sql($cycle_restart['yyyy-mm-dd-3x']) . '
# 	group by
# 		channel_id
# 	order by
# 		channel_id asc
# ';
$result = mysql_query($sql) or die(mysql_error());
$data['user_report']['channel_list'] = array();
$channel = & $data['user_report']['channel_list']; # alias
while ($row = mysql_fetch_assoc($result)) {
	$channel[$row['channel_id']] = array();
}
foreach($channel as $k1 => $v1) {
	# todo 
	# cost has to have had the price set 1 month previous to be valid
	# todo indicate noncurrent prices on display of cost_list
	if (1) { # before
		# $sql = '
		# 	select
		# 		cnl.name,
		# 		' . (int)$config['cycle_length'] . ' as time,
		# 		cnl.value as before_cost
		# 	from
		# 		' . $config['mysql']['prefix'] . 'channel cnl,
		# 		' . $config['mysql']['prefix'] . 'cost ct
		# 	where
		# 		cnl.id = ct.channel_id and
		# 		cnl.id = ' . (int)$k1 . ' and
		# 		ct.modified <= ' . to_sql($cycle_restart['yyyy-mm-dd-2x']) . '
		# 	order by
		# 		ct.modified desc
		# 	limit
		# 		1 
		# ';
		$sql = '
			select
				cnl.name,
				' . (int)$config['cycle_length'] . ' as time,
				cnl.value as before_cost
			from
				' . $config['mysql']['prefix'] . 'channel cnl,
				' . $config['mysql']['prefix'] . 'cycle cce
			where
				cnl.id = cce.channel_id and
				cnl.id = ' . (int)$k1 . ' and
				-- disabled for testing
				-- cce.modified <= ' . to_sql($cycle_restart['yyyy-mm-dd-2x']) . ' and
				1
			order by
				cce.modified desc
			limit
				1 
		';
		$result = mysql_query($sql) or die(mysql_error());
		while ($row = mysql_fetch_assoc($result))
			$channel[$k1]['info'] = $row;
	}
	if (1) { # after
		# $channel[$k1]['info']['after_cost'] = get_db_single_value('
		# 		ct.value as after_cost -- max cost?
		# 	from
		# 		' . $config['mysql']['prefix'] . 'cost ct
		# 	where
		# 		ct.channel_id = ' . (int)$k1 . ' and
		# 		ct.modified <= ' . to_sql($cycle_restart['yyyy-mm-dd-1x']) . '
		# 	order by
		# 		ct.modified desc
		# ');
		$channel[$k1]['info']['after_cost'] = get_db_single_value('
				cnl.value as after_cost -- max cost?
			from
				' . $config['mysql']['prefix'] . 'cycle cce,
				' . $config['mysql']['prefix'] . 'channel cnl
			where
				cnl.id = cce.channel_id and
				cce.channel_id = ' . (int)$k1 . ' and
				-- disabled for testing
				-- cce.modified <= ' . to_sql($cycle_restart['yyyy-mm-dd-1x']) . ' and
				1
			order by
				cce.modified desc
		');
		$sql = '
			select
				rnal.user_id
			from
				' . $config['mysql']['prefix'] . 'renewal rnal,
				' . $config['mysql']['prefix'] . 'cycle cce
			where
				rnal.cycle_id = cce.id and
				cce.channel_id = ' . (int)$k1 . ' and
				-- >= may not have anyone in the current cycle
				rnal.modified > ' . to_sql($cycle_restart['yyyy-mm-dd-3x'])
		;
		$result = mysql_query($sql) or die(mysql_error());
		while ($row = mysql_fetch_assoc($result))
			$channel[$k1]['member_list'][$row['user_id']] = $row['user_id'];
	}

	$channel[$k1]['member_cost']['before'] = array();
	$channel[$k1]['member_time']['before'] = array();
	$channel[$k1]['member_cost']['after'] = array();
	$channel[$k1]['member_time']['after'] = array();
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
		$kid = & $channel[$kc1]['destination_user_id'][$kd1]; # alias
		foreach ($channel[$kc1]['member_list'] as $k1 => $v1) { # get sum
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
					-- modified < ' . $cycle_restart['yyyy-mm-dd-1x'] . ' and 
					-- modified >= ' . $cycle_restart['yyyy-mm-dd-2x'] . ' and 
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
		# todo uncomment the modified so that only the timeframe for ratings is accounted not ratings for all time
		# currently all ratings for all time are accounted for
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
				-- modified < ' . $cycle_restart['yyyy-mm-dd-1x'] . ' and 
				-- modified >= ' . $cycle_restart['yyyy-mm-dd-2x'] . ' and 
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
	# time with the current membership period?
	foreach ($channel[$kc1]['source_user_id'] as $ks1 => $vs1) {
		$kis = & $channel[$kc1]['source_user_id'][$ks1]; # alias
		if (1) {
			$channel[$kc1]['member_time']['before'][$ks1] = 0;
			$channel[$kc1]['member_time']['after'][$ks1] = 0;
			# $kis['after']['time_weight'] = 0;
			$sql = '
				select
					rnal.point_id,
					pt.name as point_name,
					rnal.modified
				from
					' . $config['mysql']['prefix'] . 'renewal rnal,
					' . $config['mysql']['prefix'] . 'point pt
				where
					rnal.point_id = pt.id and
					rnal.user_id = ' . (int)$ks1 . ' and
					pt.name != "end" and
					rnal.modified < ' . to_sql($cycle_restart['yyyy-mm-dd-1x']) . ' and
					rnal.modified >=' . to_sql($cycle_restart['yyyy-mm-dd-2x']) . '
				order by
					rnal.modified asc
			';
			$result = mysql_query($sql) or die(mysql_error());
			while($row = mysql_fetch_assoc($result)) {
				$kis['timeline'][$row['point_name']] = $row['modified'];
			}
			$timeline = & $kis['timeline'];
			if (1) {
				# start
				if (!empty($timeline['start'])) {
				if ( empty($timeline['continue'])) {
				if ( empty($timeline['end'])) {
					# todo fix member restart
					# $kis['before']['member_restart'] = $row['modified'];
					$kis['after']['member_time'] = (
						strtotime($cycle_restart['yyyy-mm-dd-1x'])
						-
						strtotime($timeline['start'])
					)/86400 ;
					$kis['after']['time_weight'] = $kis['before']['member_time'] / $config['cycle_length'];
					$channel[$kc1]['member_time']['before'][$ks1] = $kis['after']['member_time'];
				} } }
				# continue
				if ( empty($timeline['start'])) {
				if (!empty($timeline['continue'])) {
				if ( empty($timeline['end'])) {
					# $kis['before']['member_restart'] = $row['modified'];
					$kis['before']['member_time'] = (
						strtotime($timeline['continue'])
						-
						strtotime($cycle_restart['yyyy-mm-dd-2x'])
					) / 86400 ;
					$kis['before']['time_weight'] = $kis['before']['member_time'] / $config['cycle_length'];
					$channel[$kc1]['member_time']['before'][$ks1] = $kis['before']['member_time'];
					$kis['after']['member_time'] = (
						strtotime($cycle_restart['yyyy-mm-dd-1x'])
						-
						strtotime($timeline['continue'])
					) / 86400 ;
					$kis['after']['time_weight'] = $kis['after']['member_time'] / $config['cycle_length'];
					$channel[$kc1]['member_time']['after'][$ks1] = $kis['after']['member_time'];
				} } }
				# end and start
				if (!empty($timeline['start'])) {
				if ( empty($timeline['continue'])) {
				if (!empty($timeline['end'])) {
					$kis['before']['member_time'] = (
						strtotime($timeline['end'])
						-
						strtotime($cycle_restart['yyyy-mm-dd-2x'])
					) / 86400 ;
					$kis['before']['time_weight'] = $kis['before']['member_time'] / $config['cycle_length'];
					$channel[$kc1]['member_time']['before'][$ks1] = $kis['before']['member_time'];
					$kis['after']['member_time'] = (
						strtotime($cycle_restart['yyyy-mm-dd-1x'])
						-
						strtotime($timeline['start'])
					) / 86400 ;
					$kis['after']['time_weight'] = $kis['after']['member_time'] / $config['cycle_length'];
					$channel[$kc1]['member_time']['after'][$ks1] = $kis['after']['member_time'];
				} } }
				# end
				if ( empty($timeline['start'])) {
				if ( empty($timeline['continue'])) {
				if (!empty($timeline['end'])) {
					$kis['before']['member_time'] = (
						strtotime($timeline['end'])
						-
						strtotime($cycle_restart['yyyy-mm-dd-1x'])
					)/86400 ;
					$kis['after']['time_weight'] = $kis['after']['member_time'] / $config['cycle_length'];
					$channel[$kc1]['member_time']['after'][$ks1] = $kis['after']['member_time'];
				} } }
			}
		}
	}
	# how much paid for the timeframe ie) discounted membership from good rating
	foreach ($channel[$kc1]['source_user_id'] as $ks1 => $vs1) {
		$kis = & $channel[$kc1]['source_user_id'][$ks1]; # alias
		$channel[$kc1]['member_cost']['before'][$ks1] = 0;
		if (!empty($kis['before'])) { # before
			$sql = '
				select
					rnal.rating_value,
					rnal.value as renewal_value
				from
					' . $config['mysql']['prefix'] . 'renewal rnal,
					' . $config['mysql']['prefix'] . 'point pt
				where
					rnal.point_id = pt.id and
					rnal.user_id = ' . (int)$ks1 . ' and
					pt.name != "end" and
					rnal.modified < ' . to_sql($cycle_restart['yyyy-mm-dd-2x']) . ' and
					rnal.modified >= ' . to_sql($cycle_restart['yyyy-mm-dd-3x'])
			;
			$result = mysql_query($sql) or die(mysql_error());
			while($row = mysql_fetch_assoc($result)) {
				$kisb = & $kis['before']; # alias
				$kisb['previous_average'] = $row['rating_value'];
				$kisb['member_cost'] = $row['renewal_value']; # $
				# todo get channel cost based on value 1 month ago ie) price change is invalid if it wasnt up for a month
				# todo before cost weight doesn't take into account the fraction of time
				$kisb['cost_weight'] = $kisb['member_cost'] / $channel[$kc1]['info']['before_cost'];
				$kisb['cost_weight']*= $kisb['time_weight'];
				
				$kisb['cost_weight_math'] = $kisb['member_cost'] . ' / ' . $channel[$kc1]['info']['before_cost'] . ' * ' . $kisb['time_weight'];

				if (!empty($kis['before']['member_cost']))
					$channel[$kc1]['member_cost']['before'][$ks1] = $kis['before']['member_cost'];
			}
		}
		$channel[$kc1]['member_cost']['after'][$ks1] = 0;
		# $kis['after']['cost_weight'] = 0;
		if (!empty($kis['after'])) { # after
			# $kis['after']['cost_weight'] = 0;
			$sql = '
				select
					rnal.rating_value,
					rnal.value as renewal_value
				from
					' . $config['mysql']['prefix'] . 'renewal rnal,
					' . $config['mysql']['prefix'] . 'point pt
				where
					rnal.point_id = pt.id and
					rnal.user_id = ' . (int)$ks1 . ' and
					pt.name != "end" and
					-- todo make sure cycle id is correct
					rnal.modified < ' . to_sql($cycle_restart['yyyy-mm-dd-1x']) . ' and
					rnal.modified >= ' . to_sql($cycle_restart['yyyy-mm-dd-2x'])
			;
			$result = mysql_query($sql) or die(mysql_error());
			while($row = mysql_fetch_assoc($result)) {
				$kisa = & $kis['after']; # alias
				$kisa['previous_average'] = $row['rating_value'];
				$kisa['member_cost'] = $row['renewal_value']; # $
				# todo after cost weight doesn't take into account the fraction of time
				$kisa['cost_weight'] = $kisa['member_cost'] / $channel[$kc1]['info']['after_cost'];
				$kisa['cost_weight']*= $kisa['time_weight'];
				$kisa['cost_weight_math'] = $kisa['member_cost'] . ' / ' . $channel[$kc1]['info']['before_cost'] . ' * ' . $kisa['time_weight'];
				if (!empty($kisa['member_cost']))
					$channel[$kc1]['member_cost']['after'][$ks1] = $kisa['member_cost'];
			}
		}
	}
	## weighted cost
	foreach ($channel[$kc1]['source_user_id'] as $ks1 => $vs1) {
		$kis = & $channel[$kc1]['source_user_id'][$ks1]; # alias
		$channel[$kc1]['computed_cost']['before'][$ks1] = 0;
		if (!empty($kis['before'])) { # before
			$kisb = & $kis['before']; # alias
			$kisb['computed_cost'] = $kisb['member_cost'] * $kisb['cost_weight']; # * $kisb['time_weight']; 
			$kisb['computed_cost_math'] = $kisb['member_cost'] . ' * ' . $kisb['cost_weight']; # . ' * ' . $kisb['time_weight']; 
			$channel[$kc1]['computed_cost']['before'][$ks1] = $kisb['computed_cost'];
		}
		$channel[$kc1]['computed_cost']['after'][$ks1] = 0;
		if (!empty($kis['after'])) { # after
			$kisa = & $kis['after']; # alias
			$kisa['computed_cost'] = $kisa['member_cost'] * $kisa['cost_weight']; # * $kisa['time_weight']; 
			$kisa['computed_cost_math'] = $kisa['member_cost'] . ' * ' . $kisa['cost_weight']; # . ' * ' . $kisa['time_weight']; 
			$channel[$kc1]['computed_cost']['after'][$ks1] = $kisa['computed_cost'];
		}
		if (1) { # combined
			# helper array ( but duplicates data )
			$channel[$kc1]['computed_cost']['combined'][$ks1] = $kis['before']['computed_cost'] + $kis['after']['computed_cost'];
		}
	}
	### compute weighted averages
	foreach ($channel[$kc1]['destination_user_id'] as $kd1 => $vd1) {
		$kid = & $channel[$kc1]['destination_user_id'][$kd1]; # alias
		if (!empty($kid['source_user_id_rating_average']))
		foreach ($kid['source_user_id_rating_average'] as $k1 => $v1) {
			$kis = & $channel[$kc1]['source_user_id'][$k1]; # alias
			$kisb = & $kisb; # alias
			$kisa = & $kis['after']; # alias
			# it isnt necessary to separate ratings into before and after for:
			# - count
			# - average
			# instead use ratings from the full cycle
			# ie) if my valid time during the payout period is 1 day my ratings for that entire cycycle would be factored into that one day ( not just the ratings I made on that 1 day)
			$kid['source_user_id_rating_weight'][$k1] =
				(
					$v1 *
					$kis['count_weight'] *
					$kisb['cost_weight'] *
					$kisb['time_weight']
				) +
				(
					$v1 *
					$kis['count_weight'] *
					$kisa['cost_weight'] *
					$kisa['time_weight']
				)
			;
			$kid['source_user_id_rating_weight_math'][$k1] = 
				' ( ' . 
					$v1 . ' * ' . 
					$kis['count_weight'] . ' * ' . 
					$kisb['cost_weight'] . ' * ' . 
					$kisb['time_weight'] .
				' )  + ' .
				' ( ' . 
					$v1 . ' * ' . 
					$kis['count_weight'] . ' * ' . 
					$kisa['cost_weight'] . ' * ' . 
					$kisa['time_weight'] .
				' ) '
			;

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
