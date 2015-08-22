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

# description: compute the payout for users based on members to members only. (Do not compute all scores)

# not doing a full score list (ie. hidden scores) because only visible votes can be transparent and accountable
# also decentralization is important and hidden will not be practical if hidden and decentralized

# Notes
# - eligibility period does not get payout
# - eligibility is required after member expiration
# - payout is delayed for 1 cycle so members can review
# - autorenew script runs 1 day before member expiration
# - fees are not charged until autorenew script runs
# - fees are automatic from your available balance

# todo limit changes in channel length by a max of 10%

# Key
# s = start
# c = continue
# e = end
# n = autorenew once then end
# .5 = example ratio in the cycle where the member renews ( could be any ratio from 0 to 1 )
# a|b|c|d = variable cycle length

### Cycle Diagram #################################################
#                                                                 #
#                 -4d   -3c         -2b  -1a      0               #
#   timeline       |_____|___________|____|_______|               #
#   cycle 1              |___________|                            #
#   cycle 2                          |____|                       #
#   cycle 3                               |_______|               #
#                                                                 #
#   eligibility       |________|                                  #
#   payout 1                   |_____|                            #
#   payout 2                         |____|                       #
#   payout 3                              |_______|               #
#                                                                 #
#                    .5s      .5                                  #
#   member fee 1      |__|           .                            #
#   member fee 2         |_____|     .                            #
#                                                                 #
#                             .5c      .5                         #
#   member fee 1               |_____|    .                       #
#   member fee 2                     |__| .                       #
#                                                                 #
#                                      .5c   .5                   #
#   member fee 1                        |_|       .               #
#   member fee 2                          |___|   .               #
#                                                                 #
### Evaluating Payout Membership Renewal ##########################
#                                                                 #
#   nothing                                                       #
#   start                         |______ (after)                 #
#   continue                    __|______ (before|after)          #
#   end and start               ____|  |_ (before|ignore|after)   #
#   end                         ______|   (before)                #
#                                                                 #
###################################################################

# todo aahhhhh! get rid of this function in favor of get_cycle() with all the possible parameters
# todo also there are way to many calls to this function
# channel_origin_id is actually a more appropriate name than channel_parent_id
function get_latest_payout_cycle_id($channel_parent_id) {
	global $config;
	$a1 = array();
	get_channel_cycle_restart_array($a1, $channel_parent_id);
	return get_db_single_value('
		cce.id from
			' . $config['mysql']['prefix'] . 'cycle cce,
			' . $config['mysql']['prefix'] . 'channel cnl
		where
			cce.channel_id = cnl.id and
			cce.start = ' . to_sql($a1['cycle_offset'][2]['start']) . ' and
			cnl.parent_id = ' . (int)$channel_parent_id
	);
}

# variable
$data['user_report']['channel_list'] = array();

# alias
$channel_list = & $data['user_report']['channel_list'];

# get only the channel that was specified if applicable
$sql = '
	select
		cnl.id
	from
		' . $config['mysql']['prefix'] . 'channel cnl
	where
		cnl.id = cnl.parent_id
		' . (get_gp('channel_parent_id') ? ' and cnl.id = ' . (int)get_gp('channel_parent_id') : '') . '
	order by
		cnl.id asc
	limit 1
';
$result = mysql_query($sql) or die(mysql_error());
while ($row = mysql_fetch_assoc($result)) {
	$channel_list[$row['id']] = array();
}

foreach($channel_list as $k1 => $v1) {
	# alias
	$channel = & $data['user_report']['channel_list'][$k1];
	# convenience for display:
	add_key('channel', $k1, 'channel_name', $key);
	if ($_GET['cycle_id']) {
		get_specific_channel_cycle_restart_array($channel, $k1, $_GET['cycle_id']);
		# temp variable placeholder
		$channel['cycle_restart'] = get_deprecated_channel_cycle_restart_array($channel['cycle_offset'], $channel['cycle_restart']);
	}
	else {
		get_channel_cycle_restart_array($channel, $k1, get_latest_payout_cycle_id($k1));
		# temp variable placeholder
		$channel['cycle_restart'] = get_deprecated_channel_cycle_restart_array($channel['cycle_offset'], $channel['cycle_restart']);
	}

	# alias
	$cycle_restart = & $channel['cycle_restart'];
	$cycle_offset = & $channel['cycle_offset'];

	# todo die if this cycle id is before the latest
	$b1 = 1;
	if ($_GET['cycle_id'])
		if ($_GET['cycle_id'] > get_latest_payout_cycle_id($k1))
			$b1 = 2;

	# if (empty($channel['cycle_offset'][1]['start'])) {
	if ($b1 == 2) {
		$data['user_report']['premature_channel_list'][$k1] = $channel;
		$data['user_report']['premature_channel_list'][$k1] = $channel;
		$data['user_report']['premature_channel_list'][$k1]['info']['channel_parent_id'] = $k1;
		unset($channel); # too early to evaluate
	}
	else {
		# cost has to have had the price set 1 month previous to be valid
		# todo indicate noncurrent prices on display of cost_list
		if (1) { # before
			$sql = '
				select
					cnl.name,
					cnl.percent,
					' . (int)$channel['info']['payout_length'] . ' as time,
					cnl.value as before_cost
				from
					' . $config['mysql']['prefix'] . 'channel cnl
				where
					cnl.parent_id = ' . (int)$k1 . ' and
					cnl.modified <= ' . to_sql($cycle_offset[1]['start']) . ' and
					1
				order by
					cnl.modified desc
				limit
					1 
			';
			$result = mysql_query($sql) or die(mysql_error());
			while ($row = mysql_fetch_assoc($result))
				$channel['info'] = array_merge($channel['info'], $row);
		}
		if (1) { # after
			$channel['info']['after_cost'] = get_db_single_value('
					cnl.value as after_cost
				from
					' . $config['mysql']['prefix'] . 'channel cnl
				where
					cnl.parent_id = ' . (int)$k1 . ' and
					cnl.modified <= ' . to_sql($cycle_offset[0]['start']) . '
				order by
					cnl.modified desc
			');
			get_channel_member_list_array($channel, $k1);
		}
		# what cycle
		if (1) {
			if ($_GET['cycle_id']) {
				$data['user_report']['info']['cycle_id'] = $_GET['cycle_id'];
			}
			else if ($k1) {
				# should actually be parent_channel_id
				# assume latest if cycle is not specified
				$channel['info']['cycle_id'] = get_latest_payout_cycle_id(get_gp('channel_parent_id'));
			}
		}

		$channel['member_time']['before'] = array();
		$channel['member_time']['after'] = array();
		# $channel[$k1]['average_weight_sum'] = array();
		# display after debug math by commenting out
		# $channel['weighted_credit'] = array();
	}
}
foreach ($channel_list as $kc1 => $vc1) {
	$channel  = & $data['user_report']['channel_list'][$kc1];

	# update alias
	$cycle_restart = & $channel['cycle_restart'];
	$cycle_offset = & $channel['cycle_offset'];

	# prepare additional computation arrays
	if (!empty($vc1['member_list']))
	foreach ($vc1['member_list'] as $k1 => $v1) {
		# convenience for display
		add_key('user', $k1, 'user_name', $key);

		$channel['destination_user_id'][$k1] = array();
		$channel['source_user_id'][$k1] = array();
	}

	initialize_score_channel_user_id_array($channel);
	if (!empty($channel['destination_user_id']))
	foreach ($channel['destination_user_id'] as $kd1 => $vd1) {
		# $kc1 = $channel_parent_id;
		# $kd1 = $destination_user_id;
		get_score_channel_user_id_array($channel, $kc1, $kd1);
	}

	# time with the current membership period?

	if (!empty($channel['source_user_id']))
	foreach ($channel['source_user_id'] as $ks1 => $vs1) {
		$kis = & $channel['source_user_id'][$ks1]; # alias
		if (1) {
			$channel['member_time']['before'][$ks1] = 0;
			$channel['member_time']['after'][$ks1] = 0;
			# $kis['after']['time_weight'] = 0;
			$sql = '
				select
					rnal.point_id,
					pt.name as point_name,
					rnal.start
				from
					' . $config['mysql']['prefix'] . 'renewal rnal,
					' . $config['mysql']['prefix'] . 'point pt
				where
					rnal.point_id = pt.id and
					rnal.user_id = ' . (int)$ks1 . ' and
					rnal.start < ' . to_sql($cycle_offset[0]['start']) . ' and
					rnal.start >=' . to_sql($cycle_offset[1]['start']) . '
				order by
					rnal.start asc
			';
			$result = mysql_query($sql) or die(mysql_error());
			while($row = mysql_fetch_assoc($result)) {
				$kis['timeline'][$row['point_name']] = $row['start'];
			}
			$timeline = & $kis['timeline'];
			if (1) {
				# start
				if (!empty($timeline['start'])) {
				if ( empty($timeline['continue'])) {
				if ( empty($timeline['end'])) {
					# should be no before
					# there is no after time for the 3 required conditions
					$kis['before']['member_time'] = 0;
					$kis['before']['time_weight'] = 0;
					$channel['member_time']['after'][$ks1] = $kis['before']['member_time'];
					$kis['after']['member_time'] = (
						strtotime($cycle_offset[0]['start'])
						-
						strtotime($timeline['start'])
					)/86400 ;
					$kis['after']['time_weight'] = $kis['after']['member_time'] / $channel['info']['payout_length'];
					$channel['member_time']['before'][$ks1] = $kis['after']['member_time'];
				} } }
				# continue
				if ( empty($timeline['start'])) {
				if (!empty($timeline['continue'])) {
				if ( empty($timeline['end'])) {
					$kis['before']['member_time'] = (
						strtotime($timeline['continue'])
						-
						strtotime($cycle_offset[1]['start'])
					) / 86400 ;
					$kis['before']['time_weight'] = $kis['before']['member_time'] / $channel['info']['payout_length'];
					
					$channel['member_time']['before'][$ks1] = $kis['before']['member_time'];
					$kis['after']['member_time'] = (
						strtotime($cycle_offset[0]['start'])
						-
						strtotime($timeline['continue'])
					) / 86400 ;
					$kis['after']['time_weight'] = $kis['after']['member_time'] / $channel['info']['payout_length'];
					$channel['member_time']['after'][$ks1] = $kis['after']['member_time'];
				} } }
				# end and start
				if (!empty($timeline['start'])) {
				if ( empty($timeline['continue'])) {
				if (!empty($timeline['end'])) {
					$kis['before']['member_time'] = (
						strtotime($timeline['end'])
						-
						strtotime($cycle_offset[1]['start'])
					) / 86400 ;
					$kis['before']['time_weight'] = $kis['before']['member_time'] / $channel['info']['payout_length'];
					$channel['member_time']['before'][$ks1] = $kis['before']['member_time'];
					$kis['after']['member_time'] = (
						strtotime($cycle_offset[0]['start'])
						-
						strtotime($timeline['start'])
					) / 86400 ;
					$kis['after']['time_weight'] = $kis['after']['member_time'] / $channel['info']['payout_length'];
					$channel['member_time']['after'][$ks1] = $kis['after']['member_time'];
				} } }
				# end
				if ( empty($timeline['start'])) {
				if ( empty($timeline['continue'])) {
				if (!empty($timeline['end'])) {
					$kis['before']['member_time'] = (
						strtotime($timeline['end'])
						-
						strtotime($cycle_offest[1]['start'])
					)/86400 ;
					$kis['before']['time_weight'] = $kis['before']['member_time'] / $channel['info']['payout_length'];
					$channel['member_time']['before'][$ks1] = $kis['before']['member_time'];
					# there is no after time for the 3 required conditions
					$kis['after']['member_time'] = 0;
					$kis['after']['time_weight'] = 0;
					$channel['member_time']['after'][$ks1] = $kis['before']['member_time'];
				} } }
			}
		}
	}
	# membership will always be the same price for everyone 
	# incentive for good behavior comes indirectly in the payout
	# incentive to be a member may come indirectly from a sponsorship though membership will still be full price
	# dramatically simplifies calculations
	if (1) {
	## weighted cost
	if (!empty($channel['source_user_id']))
	foreach ($channel['source_user_id'] as $ks1 => $vs1) {
		$kis = & $channel['source_user_id'][$ks1]; # alias
		$channel['computed_cost']['before'][$ks1] = 0;
		if (!empty($kis['before'])) { # before
			$kisb = & $kis['before']; # alias
			$kisb['computed_cost'] = $channel['info']['before_cost']  * $kisb['time_weight']; 
			$kisb['computed_cost_math'] = $channel['info']['after_cost'] . ' * ' . $kisb['time_weight']; 
			$channel['computed_cost']['before'][$ks1] = $kisb['computed_cost'];
		}
		$channel['computed_cost']['after'][$ks1] = 0;
		if (!empty($kis['after'])) { # after
			$kisa = & $kis['after']; # alias
			$kisa['computed_cost'] = $channel['info']['after_cost'] * $kisa['time_weight']; 
			$kisa['computed_cost_math'] = $channel['info']['after_cost'] . ' * ' . $kisa['time_weight']; 
			$channel['computed_cost']['after'][$ks1] = $kisa['computed_cost'];
		}
		if (1) { # combined
			# helper array ( but duplicates data )
			$channel['computed_cost']['combined'][$ks1] = $kis['before']['computed_cost'] + $kis['after']['computed_cost'];
		}
	}
	}
	### compute weighted averages
	if (!empty($channel['destination_user_id']))
	foreach ($channel['destination_user_id'] as $kd1 => $vd1) {
		$kid = & $channel['destination_user_id'][$kd1]; # alias

		if (!empty($kid['source_user_id_score_average']))
		foreach ($kid['source_user_id_score_average'] as $k1 => $v1) {
			$kis = & $channel['source_user_id'][$k1]; # alias
			$kisb = & $kis['before']; # alias
			$kisa = & $kis['after']; # alias
			# it isnt necessary to separate scores into before and after for:
			# - count
			# - average
			# instead use scores from the full cycle
			# ie) if my valid time during the payout period is 1 day my scores for that entire cycycle would be factored into that one day ( not just the scores I made on that 1 day)
			$s111 = ($kid['source_user_id_score_count'][$k1] / $kis['user_score_count']);
			# $s111 = ($kid['source_user_id_score_like_count'][$k1] / $kis['user_score_like_count']);
			$kid['source_user_id_score_weight'][$k1] =
				(
					(
						$s111
					)
					*
					$kisb['time_weight']
				) +
				(
					(
						$s111
					) *
					$kisa['time_weight']
				)
			;
			$il1 = 0;
			$il2 = 0;
			if (!empty($kid['source_user_id_score_like_count'][$k1]))
				$il1 = $kid['source_user_id_score_like_count'][$k1];
			if (!empty($kid['source_user_id_score_count'][$k1]))
				$il2 = $kid['source_user_id_score_count'][$k1] - $kid['source_user_id_score_like_count'][$k1];
			$kid['source_user_id_score_weight_math_before'][$k1] = 
				'This-User Like: ' . $il1 . ' | ' .
				'This-User Dislike: ' . $il2 . ' | ' .
				'All-User Score: ' . $kis['user_score_count'] . ' | ' .
				'Average: ' . $kid['source_user_id_score_average'][$k1] . ' | ' .
				'Time: ' . ($kisa['time_weight'] + $kisb['time_weight'])
			;
		}
	}
	### OFFSET compute weighted averages
	if (!empty($channel['destination_user_id']))
	foreach ($channel['destination_user_id'] as $kd1 => $vd1) {
		$kid = & $channel['destination_user_id'][$kd1]; # alias

		foreach ($kid['score_offset'] as $kd2 => $vd2) {
		if (!empty($vd2['score_average']))
		foreach ($vd2['score_average'] as $k1 => $v1) {
			$kis = & $channel['source_user_id'][$k1]; # alias
			$kisb = & $kis['before']; # alias
			$kisa = & $kis['after']; # alias
			# score counts as long as it is made within the corresponding cycle
			# score weight is later factored in as a % of membership time under the corresponding cycle
			$i1t = $kid['score_offset'][$kd2]['mark_count'][$k1];
			$i2t = $kis['score_offset'][$kd2]['mark_count'];
			$s111 = $i1t / $i2t;
			$kid['score_offset'][$kd2]['score_weight'][$k1] =
				($s111 * $kisb['time_weight'])
				+
				($s111 * $kisa['time_weight'])
			;
			$il1 = 0;
			$il2 = 0;
			if (!empty($vd2['like_count'][$k1]))
				$il1 = $vd2['like_count'][$k1];
			if (!empty($vd2['mark_count'][$k1]))
				$il2 = $vd2['mark_count'][$k1] - $vd2['like_count'][$k1];
			$kid['score_offset'][$kd2]['score_math'][$k1] = 
				'( ' . $il1 . ' - ' .  $il2 . ' ) / ' .  $kis['score_offset'][$kd2]['mark_count'] .
				' = ' . ( $il1 - $il2 )  / $kis['score_offset'][$kd2]['mark_count'] .
				' | Average: ' .  $vd2['score_average'][$k1] .
				' | Time: ' . ($kisa['time_weight'] + $kisb['time_weight'])
			;
		} }
	}
	# average_weight_sum && weighted_credit
	if (!empty($channel['destination_user_id']))
	foreach ($channel['destination_user_id'] as $kd1 => $vd1) {
		$kid = & $channel['destination_user_id'][$kd1]; # alias
		if (!empty($kid['source_user_id_score_weight'])) {
			$channel['dmath'][$kd1] = ' 0 '; # oh no!
			$channel['average_weight_sum_denominator'][$kd1] = 0; # oh no!
			$channel['nmath'][$kd1] = ' 0 ';
			$channel['average_weight_sum_numerator'][$kd1] = 0;
			foreach ($kid['source_user_id_score_average'] as $k11 => $v11) {
				$channel['nmath'][$kd1] .= ' += ( ( ( ' . $kid['source_user_id_score_average'][$k11] . ' + 1) / 2) * ' .  $kid['source_user_id_score_weight'][$k11] . ') '; 
				$channel['average_weight_sum_numerator'][$kd1] += (
					(
						(
							$kid['source_user_id_score_average'][$k11]
							+
							1
						)
						/
						2
					)
					*
					$kid['source_user_id_score_weight'][$k11]
				); 
				$channel['dmath'][$kd1] .= ' += ' . $kid['source_user_id_score_weight'][$k11];
				$channel['average_weight_sum_denominator'][$kd1] += (
					$kid['source_user_id_score_weight'][$k11]
				);
			}
			# system score (reinforce user scores)
			$channel['nmath'][$kd1] .= '| system += ( ( ' .  $channel['average_weight_sum_numerator'][$kd1] . ' / ' .  $channel['average_weight_sum_denominator'][$kd1] . ') * 1 )';
			$channel['average_weight_sum_numerator'][$kd1] += (
				$channel['average_weight_sum_numerator'][$kd1]
				/
				$channel['average_weight_sum_denominator'][$kd1]
			);
		}
		else {
			# system score (assume half credit)
			$channel['nmath'][$kd1] = '| system: += ( ( .5 / 1 ) * 1 )';
			$channel['average_weight_sum_numerator'][$kd1] = .5;
			# placeholders for readability on debugging
			$channel['dmath'][$kd1] = '0 += 1 ';
			$channel['average_weight_sum_denominator'][$kd1] = 1;
		}
		# $channel['average_weight_sum_denominator'] no longer needed
		$channel['weighted_credit_math'][$kd1] = '(' .
			$channel['average_weight_sum_numerator'][$kd1]
		. ')'
		. '* ( ' .
			$channel['source_user_id'][$kd1]['before']['time_weight'] . ' + ' . 
			$channel['source_user_id'][$kd1]['after']['time_weight']
		. ')';
		$channel['weighted_credit'][$kd1] = (
			$channel['average_weight_sum_numerator'][$kd1]
		)
		* (
			$channel['source_user_id'][$kd1]['before']['time_weight'] +
			$channel['source_user_id'][$kd1]['after']['time_weight']
		);
	}
	# OFFSET average_weight_sum && weighted_credit
	if (!empty($channel['destination_user_id']))
	foreach ($channel['destination_user_id'] as $kd1 => $vd1) {
		$kid = & $channel['destination_user_id'][$kd1]; # alias
		if (0)
		foreach ($kid['score_offset'] as $kd2 => $vd2) {

			$cso = & $channel['computed_weight']['score_offset'][$kd2];




			$cso['average_sum'][$kd1] = 0;
			if (!empty($vd2['score_weight'])) {
			foreach ($vd2['score_average'] as $k11 => $v11) {
				$cso['average_sum'][$kd1] += (
					(
						(
							$vd2['score_average'][$k11]
							+ 1 # (prevent negative scoring)
						)
						/ 2
					)
					*
					$vd2['score_weight'][$k11]
				);
			} }
		}

		foreach ($kid['score_offset'] as $kd2 => $vd2) {
			$cso = & $channel['computed_weight']['score_offset'][$kd2];

			$cso['dmath'][$kd1] = 0; # oh no!
			$cso['system_divisor'][$kd1] = 0; # oh no!
			$cso['nmath'][$kd1] = 0;
			$cso['average_weight_sum'][$kd1] = 0;

			# $cso['nmath'][$kd1] = array();
			# $cso['average_weight_sum'][$kd1] = 0;
			if (!empty($vd2['score_weight'])) {
			foreach ($vd2['score_average'] as $k11 => $v11) {
				$cso['nmath'][$kd1] .= ' += ( ( ( ' . $vd2['score_average'][$k11] . ' + 1) / 2) * ' .  $vd2['score_weight'][$k11] . ') '; 
				$cso['average_weight_sum'][$kd1] += (
					(
						(
							$vd2['score_average'][$k11]
							+
							1 # (prevent negative scoring)
						)
						/
						2
					)
					*
					$vd2['score_weight'][$k11]
				); 
				$cso['dmath'][$kd1] .= ' += ' . $vd2['score_weight'][$k11];
				$cso['system_divisor'][$kd1] += (
					$vd2['score_weight'][$k11]
				);
			}
			# system score (reinforce user scores)
			$cso['nmath'][$kd1] .= '| system += ( ( ' .  $cso['average_weight_sum'][$kd1] . ' / ' .  $cso['system_divisor'][$kd1] . ') * 1 )';
			$cso['average_weight_sum'][$kd1] += (
				$cso['average_weight_sum'][$kd1]
				/
				$cso['system_divisor'][$kd1]
			);
		}
		else {
			# system score (assume half credit)
			$cso['nmath'][$kd1] = '| system: += ( ( .5 / 1 ) * 1 )';
			$cso['average_weight_sum'][$kd1] = .5;
			# placeholders for readability on debugging
			$cso['dmath'][$kd1] = '0 += 1 ';
			$cso['system_divisor'][$kd1] = 1;
		}
		# $channel['average_weight_sum_denominator'] no longer needed
		$cso['weighted_credit_math'][$kd1] = '(' .
			$cso['average_weight_sum'][$kd1]
		. ')'
		. '* ( ' .
			$channel['source_user_id'][$kd1]['before']['time_weight'] . ' + ' . 
			$channel['source_user_id'][$kd1]['after']['time_weight']
		. ')';
		$cso['weighted_credit'][$kd1] = (
			$cso['average_weight_sum'][$kd1]
		)
		* (
			$channel['source_user_id'][$kd1]['before']['time_weight'] +
			$channel['source_user_id'][$kd1]['after']['time_weight']
		);
	}


	}
	# OFSSET final computation on scores
	if  (0)
	if (!empty($channel['member_list']))
	foreach ($channel['member_list'] as $k1 => $v1) {
		# todo initialize
		$channel['computed_weight']['carry_sum'][$k1] = 0;
		$carry_sum = & $channel['computed_weight']['carry_sum'][$k1];
		foreach ($channel['computed_weight']['score_offset'] as $k2 => $v2) {
			$carry_sum += $v2['average_sum'][$k1] / pow(2, $k2);
		}
	}

	# todo add in system rating
	# looking back things seem to work out right but the math is hard to follow

}
