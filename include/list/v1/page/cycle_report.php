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
# todo there may be a lot unused vars floating around that are not needed

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

# number of previous cycles to carry over
# this is a hardcode =(
$config['cycle_carry'] = 3;
# limit set at 8 ie) 2^8 = 512 as a safety but should be less than 3 or less

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
					cnl.user_id,
					u.name as user_name,
					cnl.percent,
					' . (int)$channel['info']['payout_length'] . ' as time,
					cnl.value as before_cost
				from
					' . $config['mysql']['prefix'] . 'channel cnl,
					' . $config['mysql']['prefix'] . 'user u
				where
					u.id = cnl.user_id and
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

	initialize_score_channel_user_id_array($channel, $config['cycle_carry']);
	if (!empty($channel['destination_user_id']))
	foreach ($channel['destination_user_id'] as $kd1 => $vd1) {
		# $kc1 = $channel_parent_id;
		# $kd1 = $destination_user_id;
		get_score_channel_user_id_array($channel, $kc1, $kd1, $config['cycle_carry']);
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
						strtotime($cycle_offset[1]['start'])
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

			# trying out the net count
			$i1t = $kid['score_offset'][$kd2]['net_count'][$k1];
			$i2t = $kis['score_offset'][$kd2]['net_count'];

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
			$kid['score_offset'][$kd2]['score_weight_math'][$k1] .= 
				'1/' . pow(2, $kd2) . ' | ' .
				'This-User Like: ' . $il1 . ' | ' .
				'This-User Dislike: ' . $il2 . ' | ' .
				'All-User Net: ' . $kis['score_offset'][$kd2]['net_count']
			;
		} }
	}
	# get the 100% max total with the carry with a cycle carry of 3
	# ( not the 100% + 50% + 25% + 12.5% = 187.5% )
	# AGGREGATE get precalculations
	if (!empty($channel['destination_user_id']))
	foreach ($channel['destination_user_id'] as $kd1 => $vd1) {
		$kid = & $channel['destination_user_id'][$kd1]; # alias
		# set placeholders
		$kid['aggregate']['this_mark_count'] = array();
		$kid['aggregate']['this_like_count'] = array();
		$kid['aggregate']['this_dislike_count'] = array();
		# todo get this_net_count
		$kid['aggregate']['this_net_count'] = array();
		foreach ($kid['score_offset'] as $kd2 => $vd2) {
		foreach ($vd2['mark_count'] as $kd3 => $vd3) {
		if (!empty($vd3)) {
			$kid['aggregate']['this_mark_count'][$kd3] += $vd3 / pow(2, $kd2);
		} } }
		foreach ($kid['score_offset'] as $kd2 => $vd2) {
		if (!empty($vd2['like_count'])) {
		foreach ($vd2['like_count'] as $kd3 => $vd3) {
		if (!empty($vd3)) {
			$kid['aggregate']['this_like_count'][$kd3] += $vd3 / pow(2, $kd2);
		} } } }
		foreach ($kid['score_offset'] as $kd2 => $vd2) {
		if (!empty($vd2['dislike_count'])) {
		foreach ($vd2['dislike_count'] as $kd3 => $vd3) {
		if (!empty($vd3)) {
			$kid['aggregate']['this_dislike_count'][$kd3] += $vd3 / pow(2, $kd2);
		} } } }
		foreach ($kid['score_offset'] as $kd2 => $vd2) {
		if (!empty($vd2['net_count'])) {
		foreach ($vd2['net_count'] as $kd3 => $vd3) {
		if (!empty($vd3)) {
			$kid['aggregate']['this_net_count'][$kd3] += $vd3 / pow(2, $kd2);
		} } } }
	}
	if (!empty($channel['source_user_id']))
	foreach ($channel['source_user_id'] as $kd1 => $vd1) {
		$kis = & $channel['source_user_id'][$kd1]; # alias
		$kis['aggregate']['all_mark_count'] = 0; # todo done!
		foreach ($kis['score_offset'] as $kd2 => $vd2) {
		if (!empty($vd2['mark_count'])) {
			$kis['aggregate']['all_mark_count'] += $vd2['mark_count'] / pow(2, $kd2);
			
		} }
		foreach ($kis['score_offset'] as $kd2 => $vd2) {
		if (!empty($vd2['net_count'])) {
			$kis['aggregate']['all_net_count'] += $vd2['net_count'] / pow(2, $kd2);
			
		} }
	}
	### AGGREGATE compute weighted averages
	if (!empty($channel['destination_user_id']))
	foreach ($channel['destination_user_id'] as $kd1 => $vd1) {
		$kid = & $channel['destination_user_id'][$kd1]; # alias

		if (!empty($kid['aggregate']['this_mark_count']))
		foreach ($kid['aggregate']['this_mark_count'] as $k1 => $v1) {
			$kis = & $channel['source_user_id'][$k1]; # alias
			$kisb = & $kis['before']; # alias
			$kisa = & $kis['after']; # alias
			# it isnt necessary to separate scores into before and after for:
			# - count
			# - average
			# instead use scores from the full cycle
			# ie) if my valid time during the payout period is 1 day my scores for that entire cycycle would be factored into that one day ( not just the scores I made on that 1 day)
			
			# previous 
			# $s111 = ($kid['aggregate']['this_mark_count'][$k1] / $kis['aggregate']['all_mark_count']);

			# needs to have a sign as + or minus
			$s111 = ($kid['aggregate']['this_net_count'][$k1] / $kis['aggregate']['all_net_count']);

			$kid['aggregate']['this_score_weight'][$k1] =
				( $s111 * $kisb['time_weight']) +
				( $s111 * $kisa['time_weight'])
			;
			$il1 = 0;
			$il2 = 0;
			if (!empty($kid['aggregate']['this_like_count'][$k1]))
				$il1 = $kid['aggregate']['this_like_count'][$k1];
			if (!empty($kid['aggregate']['this_mark_count'][$k1]))
				$il2 = $kid['aggregate']['this_mark_count'][$k1] - $kid['aggregate']['this_like_count'][$k1];
			$kid['aggregate']['this_score_weight_math'][$k1] = 
				'This-User Like: ' . $il1 . ' | ' .
				'This-User Dislike: ' . $il2 . ' | ' .
				# 'All-User Score: ' . $kis['aggregate']['all_mark_count'] . ' | ' .
				'All-User Net: ' . $kis['aggregate']['all_net_count'] .  ' | ' .
				# Net1
				# todo
				'Credit: ' . $kid['aggregate']['this_score_weight'][$k1] . ' | ' . 
				'Time: ' . ($kisa['time_weight'] + $kisb['time_weight'])
			;
		}
	}
	# AGGREGATE average_weight_sum && weighted_credit
	if (!empty($channel['destination_user_id']))
	foreach ($channel['destination_user_id'] as $kd1 => $vd1) {
		$kid = & $channel['destination_user_id'][$kd1]; # alias
		$kida = & $kid['aggregate']; # alias
		$channela = & $channel['computed_weight']['aggregate'];
		if (!empty($kida['this_score_weight'])) {
			$channela['nmath'][$kd1] = '';
			$channela['average_weight_sum'][$kd1] = 0;
			foreach ($kida['this_mark_count'] as $k11 => $v11) {
				$channela['nmath'][$kd1] .= 
					$kida['this_score_weight'][$k11] .
					' + '
				;
				# last part should be the addition of the half_span
				$channela['average_weight_sum'][$kd1] +=
					$kida['this_score_weight'][$k11]
				; 
			}
		}
		else {
			$channela['nmath'][$kd1] = '';
			$channela['average_weight_sum'][$kd1] = 0;
		}
	}
	# OFFSET average_weight_sum && weighted_credit
	if (!empty($channel['destination_user_id']))
	foreach ($channel['destination_user_id'] as $kd1 => $vd1) {
		$kid = & $channel['destination_user_id'][$kd1]; # alias
		foreach ($kid['score_offset'] as $kd2 => $vd2) {
			$cso = & $channel['computed_weight']['score_offset'][$kd2];
			$cso['nmath'][$kd1] = 0;
			$cso['average_weight_sum'][$kd1] = 0;
			if (!empty($vd2['score_weight'])) {
				if(0)
				foreach ($vd2['score_average'] as $k11 => $v11) {
					$cso['nmath'][$kd1] .= ' += ' .
						$vd2['score_average'][$k11] .  ' * ' . 
						$vd2['score_weight'][$k11]
					;
					$cso['average_weight_sum'][$kd1] +=
						$vd2['score_average'][$k11] *
						$vd2['score_weight'][$k11]
					; 
				}
			}
			$cso['weighted_credit_math'][$kd1] =
				$cso['average_weight_sum'][$kd1] . 
				' * ( ' .
					$channel['source_user_id'][$kd1]['before']['time_weight'] . ' + ' . 
					$channel['source_user_id'][$kd1]['after']['time_weight'] . 
				' ) ';
			$cso['weighted_credit'][$kd1] =
				$cso['average_weight_sum'][$kd1]
				* (
					$channel['source_user_id'][$kd1]['before']['time_weight'] +
					$channel['source_user_id'][$kd1]['after']['time_weight']
				)
			;
		}
	}
	# OFSSET final computation on scores
	if (!empty($channel['member_list']))
	foreach ($channel['member_list'] as $k1 => $v1) {
		$channel['computed_weight']['carry_sum']['nmath'][$k1] = '';
		$channel['computed_weight']['carry_sum']['average_weight_sum'][$k1] = 0;
		$carry_sum = & $channel['computed_weight']['carry_sum'];
		foreach ($channel['computed_weight']['score_offset'] as $k2 => $v2) {
			$carry_sum['average_weight_sum'][$k1] += $v2['average_weight_sum'][$k1] / pow(2, $k2);
			$carry_sum['nmath'][$k1] .=  ' + ' . $v2['average_weight_sum'][$k1] / pow(2, $k2);
		}
	}
}

# sort
arsort($channel['computed_weight']['aggregate']['average_weight_sum']);

# scale
$aggregate= & $channel['computed_weight']['aggregate'];
$aggregate['scale'] = array();
$scale = & $aggregate['scale'];
$scale['max'] = max($aggregate['average_weight_sum']);
$scale['min'] = min($aggregate['average_weight_sum']);
$scale['half_span'] = $scale['max'];
if (abs($scale['max']) < abs($scale['min']))
	$scale['half_span'] = $scale['min'];
# half span
# .5 for the range 0 to 1
# .5 for the range -1 to 0
# 1 for the range -1 to 1
# member_count - 1 is the maximum half_span
# half_span also serves as a normalizer such that as half_span increases so does the equlity effect (freebie for everyone is half_span so that people are never negative)

# economics big trade-off is equality vs efficiency
foreach($aggregate['average_weight_sum'] as $k1 => $v1) {
	# >0 (efficiency)
	if ($v1 > 0) {
		# >1 (unbalanced economy)
		# slope = half_span
		if ($scale['half_span'] > 1) {
			$aggregate['weighted_credit'][$k1] = 
				$scale['half_span'] +
				(
					$aggregate['average_weight_sum'][$k1] *
					$scale['half_span']
				)
			;
			$aggregate['weighted_credit_math'][$k1] = 
				'unbalanced efficiency:  ( ' . $aggregate['average_weight_sum'][$k1] . ' * ' . $scale['half_span'] . ' ) + ' .
				$scale['half_span']
			;
		}
		# <=1 (balanced economy)
		# slope = 1/half_span
		else {
			$aggregate['weighted_credit'][$k1] = 
				$scale['half_span'] +
				(
					$aggregate['average_weight_sum'][$k1] /
					$scale['half_span']
				)
			;
			$aggregate['weighted_credit_math'][$k1] = 
				'balanced efficiency:  ( ' . $aggregate['average_weight_sum'][$k1] . ' / ' . $scale['half_span'] . ' ) + ' .
				$scale['half_span']
			;
		}
	}
	# <=0 (equality)
	# slope = 1
	else {
		$aggregate['weighted_credit'][$k1] = 
			$aggregate['average_weight_sum'][$k1] +
			$scale['half_span']
		;
		$aggregate['weighted_credit_math'][$k1] = 
			' equality: ' . $aggregate['average_weight_sum'][$k1] . ' + ' . $scale['half_span']
		;
	}
	# time in cycle fix
	$aggregate['weighted_credit'][$k1] *= (
		$channel['source_user_id'][$k1]['before']['time_weight'] +
		$channel['source_user_id'][$k1]['after']['time_weight']
	);
	$aggregate['weighted_credit_math'][$k1] .= ' * ( ' .
		$channel['source_user_id'][$k1]['before']['time_weight'] . ' + ' .
		$channel['source_user_id'][$k1]['after']['time_weight'] .
	' ) ';
}
