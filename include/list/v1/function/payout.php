<?
# author: vaskoiii
# description: separating out payout specific functions for membership

# description: compute the payout for users based on members to members only. (Do not compute all scores)

# not doing a full score list (ie. hidden scores) because only visible votes can be transparent and accountable
# also decentralization is important and hidden will not be practical if hidden and decentralized

# Notes
# - todo: eligibility period does not get payout
# - todo: eligibility is required after member expiration
# - payout is delayed for 1 cycle so members can review (3 most recent cycles are ineligble: future, current, previous)
# - autorenew script runs 1 day before member expiration
# - fees are not charged until autorenew script runs
# - fees are automatic from your available balance

# todo limit changes in channel length by a max %
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

function do_payout_computation(& $channel, $k1, $cycle_id = null) {
	# additional variables
	global $key;
	global $config;

	$kc1 = $k1;
	$vc1 = & $channel;
	# $cycle_id = $_GET['cycle_id']

	# convenience for display:
	add_key('channel', $k1, 'channel_name', $key);
	if (!empty($cycle_id)) {
		get_specific_channel_cycle_restart_array($channel, $k1, $cycle_id);
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
	if (!empty($cycle_id))
		if ($cycle_id > get_latest_payout_cycle_id($k1))
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
			if (!empty($cycle_id)) {
				$data['user_report']['info']['cycle_id'] = $cycle_id;
			}
			else if ($k1) {
				# should actually be parent_channel_id
				# assume latest if cycle is not specified
				$channel['info']['cycle_id'] = get_latest_payout_cycle_id(get_gp('channel_parent_id'));
			}
		}
	}

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
					);
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
					);
					$kis['before']['time_weight'] = (
						$kis['before']['member_time'] /
						$channel['info']['payout_length']
					);
					$kis['after']['member_time'] = (
						strtotime($cycle_offset[0]['start']) -
						strtotime($timeline['continue'])
					);
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
					);
					$kis['before']['time_weight'] = (
						$kis['before']['member_time'] /
						$channel['info']['payout_length']
					);
					$kis['after']['member_time'] = (
						strtotime($cycle_offset[0]['start']) -
						strtotime($timeline['start'])
					);
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
					);
					$kis['before']['time_weight'] = (
						$kis['before']['member_time'] /
						$channel['info']['payout_length']
					);
				} } }
			}
		}
	}
	# helper array
	$channel['computed_time']['lowest'] = 0;
	$lowest = & $channel['computed_time']['lowest'];
	if (!empty($channel['member_list']))
	foreach ($channel['member_list'] as $k1 => $v1) {
		# setup a combined time weight variable
		$kis = & $channel['source_user_id'][$k1];
		$kis['combined']['member_time'] = $kis['before']['member_time'] + $kis['after']['member_time'];
		$kis['combined']['time_weight'] = $kis['before']['time_weight'] + $kis['after']['time_weight'];
		if ($kis['combined']['time_weight'] > $lowest)
			$lowest = $kis['combined']['time_weight'];
		$channel['computed_time']['combined'][$k1] =  $kis['combined']['time_weight'];
	}
	if (!empty($channel['computed_time']['combinded']))
		arsort($channel['computed_time']['combined']);
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
		foreach ($kid['score_offset'] as $kd2 => $vd2) {
		if (!empty($vd2['score_average']))
		foreach ($vd2['score_average'] as $k1 => $v1) {
			$kis = & $channel['source_user_id'][$k1]; # alias
			$s_time_weight = & $channel['source_user_id'][$k1]['combined']['time_weight'];
			# score counts as long as it is made within the corresponding cycle
			# score weight is later factored in as a % of membership time under the corresponding cycle
			$s2d_net_count = & $kid['score_offset'][$kd2]['net_count'][$k1];
			$s_net_count = & $kis['score_offset'][$kd2]['net_count'];
			$kid['score_offset'][$kd2]['score_weight'][$k1] =
				( $s2d_net_count / $s_net_count )
				* $s_time_weight
				# destination user time already fatored in from get_score_channel_user_id_array()
			;
			$i1 = 0;
			$i2 = 0;
			if (!empty($vd2['like_count'][$k1]))
				$i1 = $vd2['like_count'][$k1];
			if (!empty($vd2['mark_count'][$k1]))
				$i2 = $vd2['mark_count'][$k1] - $i1;

			# todo better debug for when actually empty
			if (!empty($kid['score_offset'][$kd2]['score_weight_math'][$k1]))
				$kid['score_offset'][$kd2]['score_weight_math'][$k1] .= 
					'1 / ' . pow(2, $kd2) . ' | ' .
					'Like: ' . $i1 . ' | ' .
					'Dislike: ' . $i2 . ' | ' .
					'Net: ' . $vd2['net_count'][$k1] . ' | ' .
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
			# inject a 0 to prevent a warning
			if (empty($kid['aggregate']['this_mark_count'][$kd3]))
				$kid['aggregate']['this_mark_count'][$kd3] = 0;
			$kid['aggregate']['this_mark_count'][$kd3] += $vd3 / pow(2, $kd2);
		} } } }
		foreach ($kid['score_offset'] as $kd2 => $vd2) {
		if (!empty($vd2['like_count'])) {
		foreach ($vd2['like_count'] as $kd3 => $vd3) {
		if (!empty($vd3)) {
			# inject a 0 to prevent a warning
			if (empty($kid['aggregate']['this_like_count'][$kd3]))
				$kid['aggregate']['this_like_count'][$kd3] = 0;
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
			# inject a 0 to prevent a warning
			if (empty($kid['aggregate']['this_net_count'][$kd3]))
				$kid['aggregate']['this_net_count'][$kd3] = 0;
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
			# inject a 0 to prevent a warning
			if (empty($kis['aggregate']['all_net_count']))
				$kis['aggregate']['all_net_count'] = 0;
			$kis['aggregate']['all_net_count'] += $vd2['net_count'] / pow(2, $kd2);
		} }
	}
	### AGGREGATE compute weighted averages
	if (!empty($channel['destination_user_id']))
	foreach ($channel['destination_user_id'] as $kd1 => $vd1) {
		$kid = & $channel['destination_user_id'][$kd1]; # alias
		if (!empty($kid['aggregate']['this_mark_count']))
		foreach ($kid['aggregate']['this_mark_count'] as $k1 => $v1) {
			$s_time_weight = & $channel['source_user_id'][$k1]['combined']['time_weight'];
			$kis = & $channel['source_user_id'][$k1]; # alias
			# use scores from the full cycle to simplify logic (regardless of time in cycle)
			$s2d_this_net_count = & $kid['aggregate']['this_net_count'][$k1];
			$s_all_net_count = & $kis['aggregate']['all_net_count'];
			$kid['aggregate']['this_score_weight'][$k1] =
				( $s2d_this_net_count / $s_all_net_count )
				* $s_time_weight
			;
			$i1 = 0;
			$i2 = 0;
			if (!empty($kid['aggregate']['this_like_count'][$k1]))
				$i1 = $kid['aggregate']['this_like_count'][$k1];
			if (!empty($kid['aggregate']['this_mark_count'][$k1]))
				$i2 = $kid['aggregate']['this_mark_count'][$k1] - $i1;
			$kid['aggregate']['this_score_weight_math'][$k1] = 
				'Like: ' . $i1 . ' | ' .
				'Dislike: ' . $i2 . ' | ' .
				'Net: ' . $kid['aggregate']['this_net_count'][$k1] . ' | ' .
				'All-User Net: ' . $kis['aggregate']['all_net_count'] .  ' | ' .
				'Credit: ' . $kid['aggregate']['this_score_weight'][$k1] . ' | ' . 
				'Time: ' . ($s_time_weight)
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
				# prevent manipulation of the average
				# ie) if it is "cheaper" to mark members that have partial time
				$channela['weight_sum'][$kd1] += $d1;
				$channela['nmath'][$kd1] .= $d1 . ' + ';
			}
		}
		else {
			$channela['weight_sum'][$kd1] = 0;
		}
	}
	# liking 1 time or more automatically uses max points
	$equalizer = .5; # todo allow configurability from 0 to 100%
	if (!empty($channel['computed_weight']['aggregate']['weight_sum']))
		arsort($channel['computed_weight']['aggregate']['weight_sum']);
	$aggregate= & $channel['computed_weight']['aggregate'];
	$aggregate['info']['total'] = 0;
	if (!empty($aggregate['weight_sum']))
	foreach ($aggregate['weight_sum'] as $k1 => $v1) {
		$aggregate['info']['total'] += $v1;
	}
	if (1) {
		$aggregate['info']['time_count'] = 0;
		if (!empty($channel['member_list']))
		foreach ($channel['member_list'] as $k1 => $v1) {
			$kis = & $channel['source_user_id'][$k1];
			$aggregate['info']['time_count'] += $kis['before']['time_weight'];
			$aggregate['info']['time_count'] += $kis['after']['time_weight'];
		}
	}
	$aggregate['info']['average'] = 0;
	if (!empty($aggregate['info']['total']))
	if (!empty($aggregate['info']['time_count']))
		$aggregate['info']['average'] = $aggregate['info']['total'] / $aggregate['info']['time_count'];
	$aggregate['average_difference'] = array();
	if (!empty($aggregate['weight_sum']))
	foreach ($aggregate['weight_sum'] as $k1 => $v1) {
		$aggregate['average_difference'][$k1] = $v1 - $aggregate['info']['average'];
		# free points section (so that no user will be negative)
		$kis = & $channel['source_user_id'][$k1];
		$aggregate['weighted_credit_math'][$k1] = $kis['combined']['time_weight']*$equalizer . ' + ' . $v1;
		$aggregate['weighted_credit'][$k1] = $kis['combined']['time_weight']*$equalizer + $v1;
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
	if (!empty($channel['computed_weight']['aggregate']['weighted_credit']))
		arsort($channel['computed_weight']['aggregate']['weighted_credit']);
}

function get_payout_array(& $channel) {
	global $config;
	# Channel Total

	$channel['payout'] = array();
	$payout = & $channel['payout'];

	$channel['renewal']['total'] = 0;
	$channel['sponsor']['total'] = 0;
	$payout['total'] = 0;
	# renewal
	if (!empty($channel['computed_cost']['combined']))
		$channel['renewal']['total'] = array_sum($channel['computed_cost']['combined']);
	# sponsor
	if (!empty($channel['donate_value']['user_id']))
		$channel['sponsor']['total'] += array_sum($channel['donate_value']['user_id']);
	# combined
	$payout['total'] = $channel['renewal']['total'] + $channel['sponsor']['total'];

	$payout['hostfee'] = $config['hostfee_percent'] / 100 * $payout['total'];
	# % of remaining total
	$payout['missionfee'] = (
			$payout['total']
			-
			$payout['hostfee']
		) 
		*
		(int)$channel['info']['percent']
		*
		.01
	;
	$aggregate = & $channel['computed_weight']['aggregate'];
	$payout['remainder'] = $payout['total'] - $payout['hostfee'] -$payout['missionfee'];
	$payout['muliplier'] = 0;
	if (!empty($aggregate['weighted_credit']))
	if (array_sum($aggregate['weighted_credit']) != 0) {
		$payout['multiplier'] = $payout['remainder'] / array_sum($aggregate['weighted_credit']);
	}
	if (!empty($channel['destination_user_id']))
	foreach ($aggregate['weighted_credit'] as $kd1 => $vd1) {
		$kid = & $channel['destination_user_id'][$kd1]; # alias
		$payout['user_id'][$kd1] = round($payout['multiplier'] * $aggregate['weighted_credit'][$kd1], 2);
	}
}

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

# cycle and sponsor hybrid functions
function get_cyspon_left_value(& $sponsora, $cycle_start) {
	# todo possible to make less abstract? ie) maybe next_renewal_start - cycle_start
	# will fail if pre_cycle_renewal is manually changed in the database ie. crafting test data
	$i1 =
		strtotime($sponsora['start'])
		+
		( $sponsora['donate_offset'])
		-
		strtotime($cycle_start)
	;
	$d1 = ( $i1 / ($sponsora['donate_offset']) ) * $sponsora['donate_value'];
	# todo enforce:
	# if ($d1 < 0)
	# 	die('negative sponsor not allowed');
	return $d1;
}
function get_cyspon_right_value(& $sponsora, $cycle_end) {
	# todo check to make sure cycle length  matches
	$i1 = 
		strtotime($cycle_end)
		-
		strtotime($sponsora['start'])
	;
	return ( $i1 / ($sponsora['donate_offset']) ) * $sponsora['donate_value'];
}
function get_left_cyspon_array($start, $user_id) {
	global $config;
	$a1 = array();
	$sql = '
		select
			"left" as overflow,
			dne.id as donate_id,
			dne.offset as donate_offset,
			dne.value as donate_value,
			ssr.id as sponsor_id,
			ssr.point_id as point_id,
			ssr.start	
		from
			' . $config['mysql']['prefix'] . 'donate dne,
			' . $config['mysql']['prefix'] . 'sponsor ssr
		where
			dne.id = ssr.donate_id and
			ssr.start < ' . to_sql($start) . ' and
			dne.user_id = ' . (int)$user_id . '
		order by
			ssr.start desc
		limit
			1
	';
	# echo $sql;
	$result = mysql_query($sql) or die(mysql_error());
	while ($row = mysql_fetch_assoc($result)) {
		$a1[$row['sponsor_id']] = $row;
	}
	return $a1;
}
function get_right_cyspon_id($end, $user_id) {
	global $config;
	$a1 = array();
	$sql = '
		select
			ssr.id as sponsor_id,
			ssr.point_id
		from
			' . $config['mysql']['prefix'] . 'donate dne,
			' . $config['mysql']['prefix'] . 'sponsor ssr
		where
			dne.id = ssr.donate_id and
			ssr.start < ' . to_sql($end) . ' and
			dne.user_id = ' . (int)$user_id . '
		order by
			ssr.start desc
		limit
			1
	';
	# echo $sql;
	$result = mysql_query($sql) or die(mysql_error());
	while ($row = mysql_fetch_assoc($result))
		$a1 = $row;
	if (!empty($a1))
	if ($a1['point_id'] != 3) # could also mean that there is bad data in the db
		return $a1['sponsor_id'];
	return false;
}
