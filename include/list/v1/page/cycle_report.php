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
	$channel = & $data['user_report']['channel_list'][$k1]; # alias
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
	$cycle_restart = & $channel['cycle_restart']; # alias
	$cycle_offset = & $channel['cycle_offset']; # alias
	# todo die if this cycle id is before the latest
	$b1 = 1;
	if ($_GET['cycle_id'])
		if ($_GET['cycle_id'] > get_latest_payout_cycle_id($k1))
			$b1 = 2;
	if ($b1 == 2) {
		$data['user_report']['premature_channel_list'][$k1] = $channel;
		$data['user_report']['premature_channel_list'][$k1]['info']['channel_parent_id'] = $k1;
		unset($channel); # too early to evaluate
	}
	else {
		# cost has to have had the price set 1 cycle previous to be valid
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
	}
}
if (1) {
	# todo minimum member count for computation = 3 (otherwise all get equal payout)
	# how to treat 3 users where one is only in the cycle for 50% of the time?
	if ((count($channel['member_list'])) < 3)
		die('all payout will be distributed equally until there are at least 3 members');
	$channel['info']['equalizer'] = 1/(count($channel['member_list']) - 2);
}
foreach ($channel_list as $kc1 => $vc1) {
	$channel  = & $data['user_report']['channel_list'][$kc1];
	$cycle_restart = & $channel['cycle_restart']; # alias
	$cycle_offset = & $channel['cycle_offset']; # alias
	# prepare additional computation arrays
	if (!empty($vc1['member_list']))
	foreach ($vc1['member_list'] as $k1 => $v1) {
		# convenience for display
		add_key('user', $k1, 'user_name', $key);
		$channel['destination_user_id'][$k1] = array();
		$channel['source_user_id'][$k1] = array();
	}
	initialize_score_channel_user_id_array($channel, $config['cycle_carry']);
	# time with the current membership period?
	if (!empty($channel['source_user_id']))
	foreach ($channel['source_user_id'] as $ks1 => $vs1) {
		$kis = & $channel['source_user_id'][$ks1]; # alias
		if (1) {
			$sql = '
				select
					rnal.point_id,
					pt.name as point_name,
					rnal.start
				from
					' . $config['mysql']['prefix'] . 'point pt,
					' . $config['mysql']['prefix'] . 'renewal rnal,
					' . $config['mysql']['prefix'] . 'cycle cce,
					' . $config['mysql']['prefix'] . 'channel cnl
				where
					pt.id = rnal.point_id and
					cce.id = rnal.cycle_id and
					cnl.id = cce.channel_id and
					cnl.parent_id = ' . (int)$kc1 . ' and
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
			$kis['before']['member_time'] = 0;
			$kis['before']['time_weight'] = 0;
			$kis['after']['member_time'] = 0;
			$kis['after']['time_weight'] = 0;
			if (1) {
				# start
				if (!empty($timeline['start'])) {
				if ( empty($timeline['continue'])) {
				if ( empty($timeline['end'])) {
					$kis['after']['member_time'] = (
						strtotime($cycle_offset[0]['start']) -
						strtotime($timeline['start'])
					)/86400 ;
					$kis['after']['time_weight'] = (
						$kis['after']['member_time'] /
						$channel['info']['payout_length']
					);
				} } }
				# continue
				if ( empty($timeline['start'])) {
				if (!empty($timeline['continue'])) {
				if ( empty($timeline['end'])) {
					$kis['before']['member_time'] = (
						strtotime($timeline['continue']) -
						strtotime($cycle_offset[1]['start'])
					) / 86400 ;
					$kis['before']['time_weight'] = (
						$kis['before']['member_time'] /
						$channel['info']['payout_length']
					);
					$kis['after']['member_time'] = (
						strtotime($cycle_offset[0]['start']) -
						strtotime($timeline['continue'])
					) / 86400 ;
					$kis['after']['time_weight'] = (
						$kis['after']['member_time'] /
						$channel['info']['payout_length']
					);
				} } }
				# end and start
				if (!empty($timeline['start'])) {
				if ( empty($timeline['continue'])) {
				if (!empty($timeline['end'])) {
					$kis['before']['member_time'] = (
						strtotime($timeline['end']) -
						strtotime($cycle_offset[1]['start'])
					) / 86400 ;
					$kis['before']['time_weight'] = (
						$kis['before']['member_time'] /
						$channel['info']['payout_length']
					);
					$kis['after']['member_time'] = (
						strtotime($cycle_offset[0]['start']) -
						strtotime($timeline['start'])
					) / 86400 ;
					$kis['after']['time_weight'] = (
						$kis['after']['member_time'] /
						$channel['info']['payout_length']
					);
				} } }
				# end
				if ( empty($timeline['start'])) {
				if ( empty($timeline['continue'])) {
				if (!empty($timeline['end'])) {
					$kis['before']['member_time'] = (
						strtotime($timeline['end']) -
						strtotime($cycle_offset[1]['start'])
					)/86400 ;
					$kis['before']['time_weight'] = (
						$kis['before']['member_time'] /
						$channel['info']['payout_length']
					);
				} } }
			}
		}
	}
	# moved down because member time is important when computing score
	if (!empty($channel['destination_user_id']))
	foreach ($channel['destination_user_id'] as $kd1 => $vd1) {
		# $kc1 = $channel_parent_id;
		# $kd1 = $destination_user_id;
		get_score_channel_user_id_array($channel, $kc1, $kd1, $config['cycle_carry']);
		# major cleanup
		unset_if_empty_in_array($channel['destination_user_id'][$kd1]['source_user_id_score_count']);
		for ($i1 = 0; $i1 <= $config['cycle_carry']; $i1++) {
			unset_if_empty_in_array($channel['destination_user_id'][$kd1]['score_offset'][$i1]['mark_count']);
			unset_if_empty_in_array($channel['destination_user_id'][$kd1]['score_offset'][$i1]['net_count']);
			unset_if_empty_in_array($channel['destination_user_id'][$kd1]['score_offset'][$i1]);
		}
	}
	if (!empty($channel['destination_user_id']))
	foreach ($channel['destination_user_id'] as $kd1 => $vd1) {
		for ($i1 = 0; $i1 <= $config['cycle_carry']; $i1++) {
			unset_if_empty_in_array($channel['source_user_id'][$kd1]['score_offset'][$i1]);
		}
	}
	# membership will always be the same price for everyone 
	# incentive for good behavior comes indirectly in the payout
	# incentive to be a member may come indirectly from a sponsorship though membership will still be full price
	# dramatically simplifies calculations
	## weighted cost
	if (!empty($channel['source_user_id'])) {
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
	} }
	### OFFSET compute weighted averages
	if (!empty($channel['destination_user_id']))
	foreach ($channel['destination_user_id'] as $kd1 => $vd1) {
		$kid = & $channel['destination_user_id'][$kd1]; # alias
		$kidt = & $channel['source_user_id'][$kd1]; # alias
		$kidtb = & $kidt['before']['time_weight']; # alias
		$kidta = & $kidt['after']['time_weight']; # alias
		foreach ($kid['score_offset'] as $kd2 => $vd2) {
		if (!empty($vd2['score_average']))
		foreach ($vd2['score_average'] as $k1 => $v1) {
			$kis = & $channel['source_user_id'][$k1]; # alias
			$kisb = & $kis['before']; # alias
			$kisa = & $kis['after']; # alias
			# score counts as long as it is made within the corresponding cycle
			# score weight is later factored in as a % of membership time under the corresponding cycle
			$i1t = $kid['score_offset'][$kd2]['net_count'][$k1];
			$i2t = $kis['score_offset'][$kd2]['net_count'];
			$s111 = $i1t / $i2t;
			$kid['score_offset'][$kd2]['score_weight'][$k1] = (
				($s111 * $kisb['time_weight'])
				+
				($s111 * $kisa['time_weight'])
			)
			* (
				# scale needed at every point and must happen before the average
				$kidta
				+
				$kidtb
			);
			$il1 = 0;
			$il2 = 0;
			if (!empty($vd2['like_count'][$k1]))
				$il1 = $vd2['like_count'][$k1];
			if (!empty($vd2['mark_count'][$k1]))
				$il2 = $vd2['mark_count'][$k1] - $vd2['like_count'][$k1];
			$il3 = $vd2['net_count'][$k1];
			$kid['score_offset'][$kd2]['score_weight_math'][$k1] .= 
				($kidta + $kidtb) .
				' / ' . pow(2, $kd2) . ' | ' .
				'Like: ' . $il1 . ' | ' .
				'Dislike: ' . $il2 . ' | ' .
				'Net: ' . $il3 . ' | ' .
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
		$kid['aggregate']['this_net_count'] = array();
		foreach ($kid['score_offset'] as $kd2 => $vd2) {
		if (!empty($vd2['mark_count'])) {
		foreach ($vd2['mark_count'] as $kd3 => $vd3) {
		if (!empty($vd3)) {
			$kid['aggregate']['this_mark_count'][$kd3] += $vd3 / pow(2, $kd2);
		} } } }
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
		$kis['aggregate']['all_mark_count'] = 0;
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
			$il3 = $kid['aggregate']['this_net_count'][$k1];
			$kid['aggregate']['this_score_weight_math'][$k1] = 
				'Like: ' . $il1 . ' | ' .
				'Dislike: ' . $il2 . ' | ' .
				'Net: ' . $il3 . ' | ' .
				'All-User Net: ' . $kis['aggregate']['all_net_count'] .  ' | ' .
				'Credit: ' . $kid['aggregate']['this_score_weight'][$k1] . ' | ' . 
				'Time: ' . ($kisa['time_weight'] + $kisb['time_weight'])
			;
		}
	}
	# AGGREGATE weight_sum && weighted_credit
	if (!empty($channel['destination_user_id']))
	foreach ($channel['destination_user_id'] as $kd1 => $vd1) {
		$kid = & $channel['destination_user_id'][$kd1]; # alias
		$kida = & $kid['aggregate']; # alias
		$channela = & $channel['computed_weight']['aggregate'];
		if (!empty($kida['this_score_weight'])) {
			$channela['nmath'][$kd1] = '';
			$channela['weight_sum'][$kd1] = 0;
			foreach ($kida['this_mark_count'] as $k11 => $v11) {
				$d1 = $kida['this_score_weight'][$k11];
				$d2 = $channel['info']['equalizer'];
				# prevent manipulation of the average
				# ie) if it is "cheaper" to mark members that have partial time
				# todo verify if needing to scale $d1 or $d2 by time
				# todo enforce max payout as: (% of time in cycle) * (max payout)
				# - users lose marking credit if not marking users with time adding up to 1 cycle?
				# - needs marking credit used calculation (to be factored in when scaling Delta)?
				$kist = & $channel['source_user_id'][$k11];
				$kista = & $kist['after']['time_weight'];
				$kistb = & $kist['before']['time_weight'];
				$d1 *= ($kista + $kistb);
				$channela['nmath'][$kd1] .= $d1 .  ' + *&Delta; + ';
				$channela['weight_sum'][$kd1] += $d1 + ( $d2 * $d1 ); 
			}
		}
		else {
			$channela['nmath'][$kd1] = '';
			$channela['weight_sum'][$kd1] = 0;
		}
	}
}
# equalizer
# liking 1 time or more automatically uses max points
$equalizer = & $channel['info']['equalizer'];
foreach ($channel['source_user_id'] as $k1 => $v1) {
	if (!empty($v1['aggregate']['all_net_count'])) {
		$d1 = (
			$v1['before']['time_weight'] +
			$v1['after']['time_weight']
		);
		$channel['computed_weight']['aggregate']['nmath'][$k1] .= ' (&Delta; * ' . $d1 . ') ';
		$channel['computed_weight']['aggregate']['weight_sum'][$k1] += ( $equalizer * $d1 );
	}
	else
		$channel['computed_weight']['aggregate']['nmath'][$k1] .= ' 0 ';
	$channel['computed_weight']['aggregate']['nmath'][$k1] .= ' <br /> ';
}
arsort($channel['computed_weight']['aggregate']['weight_sum']);
$aggregate= & $channel['computed_weight']['aggregate'];
# account for time in cycle
$aggregate['weight_sum_timed'] = array();
foreach ($aggregate['weight_sum'] as $k1 => $v1) {
	$aggregate['weight_sum_timed'][$k1] = $v1; 
	$aggregate['weight_sum_timed'][$k1] *= (
		$channel['source_user_id'][$k1]['before']['time_weight'] +
		$channel['source_user_id'][$k1]['after']['time_weight']
	);
}
$aggregate['info']['total'] = 0;
foreach ($aggregate['weight_sum_timed'] as $k1 => $v1) {
	$aggregate['info']['total'] += $v1;
}
if (1) {
	$aggregate['info']['time_count'] = 0;
	foreach ($channel['member_list'] as $k1 => $v1) {
		$kis = & $channel['source_user_id'][$k1];
		$aggregate['info']['time_count'] += $kis['before']['time_weight'];
		$aggregate['info']['time_count'] += $kis['after']['time_weight'];
	}
}
$aggregate['info']['average'] = $aggregate['info']['total'] / $aggregate['info']['time_count'];
$aggregate['average_difference'] = array();
# weight_sum or weight_sum_timed? ( time is now compensated for below so weight_sum_timed may not be needed)
foreach ($aggregate['weight_sum'] as $k1 => $v1)
	$aggregate['average_difference'][$k1] = $v1 - $aggregate['info']['average'];
foreach ($aggregate['average_difference'] as $k1 => $v1) {
	$kist = & $channel['source_user_id'][$k1];
	$kista = & $kist['after']['time_weight'];
	$kistb = & $kist['before']['time_weight'];
	$stabilizer = 1 + $equalizer;
	$stabilizer *= ($kista + $kistb); 
	$stabilizer *= (
		$channel['computed_weight']['aggregate']['info']['time_count']
		/
		count($channel['member_list'])
	);
	$stabilizer_math = '(1 + &Delta;)(' . ($kista + $kistb) . ')(' .
		$channel['computed_weight']['aggregate']['info']['time_count'] . '
		/ ' . 
		count($channel['member_list']) . '
	)';
	# freebie for everyone (so there is no negative credit)
	$aggregate['weighted_credit_math'][$k1] = ' ( ( ' . $stabilizer_math . ' ) + ' . $v1 . '(' . ($kista + $kistb) . ') ) ';
	$aggregate['weighted_credit'][$k1] = $stabilizer + ($v1 * ($kista + $kistb));
}
$aggregate['info']['lowest'] = 0;
$aggregate['info']['highest'] = 0;
foreach ($aggregate['average_difference'] as $k1 => $v1) {
	if ($v1 < $aggregate['info']['lowest'])
		$aggregate['info']['lowest'] = $v1;
	if ($v1 > $aggregate['info']['highest'])
		$aggregate['info']['highest'] = $v1;
}
# adjusted by time weights
arsort($channel['computed_weight']['aggregate']['weighted_credit']);
# echo '<pre>'; print_r($channel); echo '</pre>'; exit;
