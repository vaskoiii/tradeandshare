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

# description:
# member functions that will be used in both:
# periodic_renewal.php
# process_renewal.php
# (including cycle handling)

# also contains functions used in:
# cycle_report.php

# todo make sure changing cycle length does not permit skiping or covering multiple renewals
# ie) the ratio into the current cycle when a renewal happens should be factored in when renewing

# todo show functions that need to be called before the current function is called - keyword: precall

function print_debug($s1) {
	echo "\n<hr>\n<pre>\n$s1\n</pre>\n";
}

function get_channel_cycle_restart_array(& $channel, $channel_parent_id, $cycle_id = null) {
	get_specific_channel_cycle_restart_array($channel, $channel_parent_id, $cycle_id);
}
function get_deprecated_channel_cycle_restart_array($a1, $a3) {
	# temp solution so that everything doesn't break immediately
	$a2 = array();
	if (!empty($a1))
	foreach ($a1 as $k1 => $v1)
		$a2['yyyy-mm-dd-' . ($k1 + 2) . 'x'] = $v1['start'];
	$a2['length_2x_to_3x'] = $a3['length_2x_to_3x'];
	$a2['yyyy-mm-dd-last'] = $a3['yyyy-mm-dd-last'];
	return $a2;
}
function get_specific_channel_cycle_restart_array(& $channel, $channel_parent_id, $cycle_id = null) {
	global $config;

	if (empty($cycle_id)) {
		# assume most recent cycle from now()
		$where = ' cce.start <= ' . to_sql(date('Y-m-d H:i:s'));
	}
	else {
		$where = ' cce.id <= ' . (int)$cycle_id;
	}

	# get cycle_id array
	$sql = '
		select
			cce.id as cycle_id,
			cce.point_id as cycle_point_id,
			cce.start as cycle_start
		from
			' . $config['mysql']['prefix'] . 'cycle cce,
			' . $config['mysql']['prefix'] . 'channel cnl
		where
			cce.channel_id = cnl.id and
			cnl.parent_id = ' . (int)$channel_parent_id . ' and
			' . $where . '
		order by
			cce.id desc
		limit
			6
	';
	$result = mysql_query($sql) or die(mysql_error());
	# todo fix $i1
	# member_report needs 0 but payout needs 1?
	$i1 = 1;
	$b1 = 1; # dont count cycles that are not connected
	while ($row = mysql_fetch_assoc($result)) {
		$i1++;
		$channel['cycle_restart']['yyyy-mm-dd-' . $i1 . 'x'] = $row['cycle_start'];
		if ($b1 == 1) {
			switch ($row['cycle_point_id']) {
				case '1':
				case '2':
				case '4':
					$channel['cycle_offset'][$i1 - 2]['start'] = $row['cycle_start'];
					$channel['cycle_offset'][$i1 - 2]['id'] = $row['cycle_id'];
					$channel['cycle_offset'][$i1 - 2]['point_id'] = $row['cycle_point_id'];
				break;
				case '3':
					$b1 = 2;
				break;
			}
		}
	}
	if (!empty($channel['cycle_restart'])) {
		$dt2 = $channel['cycle_restart']['yyyy-mm-dd-2x'];
		$dt3 = $channel['cycle_restart']['yyyy-mm-dd-3x'];

		# calculate from 2 cycles back to 3 cycles back
		$channel['cycle_restart']['length_2x_to_3x'] = abs((strtotime($dt2) - strtotime($dt3))/86400);
		$channel['info']['payout_length'] = abs((strtotime($dt2) - strtotime($dt3))/86400);

		# can not choose a current or future cycle
		$dtlast = $channel['cycle_restart']['yyyy-mm-dd-last'] = get_cycle_last_start($channel_parent_id, date('Y-m-d H:i:s'));
		$dtlast = $channel['info']['start_last'] = get_cycle_last_start($channel_parent_id, date('Y-m-d H:i:s'));
	}
}

function get_channel_member_list_array(& $channel, $channel_parent_id) {
	global $config;
	$sql = '
		select
			rnal.user_id
		from
			' . $config['mysql']['prefix'] . 'renewal rnal,
			' . $config['mysql']['prefix'] . 'cycle cce,
			' . $config['mysql']['prefix'] . 'channel cnl
		where
			rnal.cycle_id = cce.id and
			cnl.id = cce.channel_id and
			cnl.parent_id = ' . (int)$channel_parent_id . ' and
			rnal.start < ' . to_sql($channel['cycle_offset'][0]['start']) . ' and
			rnal.start >= ' . to_sql($channel['cycle_offset'][1]['start']) . ' and
			rnal.active = 1
	';
	$result = mysql_query($sql) or die(mysql_error());
	while ($row = mysql_fetch_assoc($result))
		$channel['member_list'][$row['user_id']] = $row['user_id'];
}

# will run a lot!
function get_score_count($source_user_id, $destination_user_id, $mark_id, $start, $end) {
	global $config;
	# todo allow team specific scores
	# cycle factored in with se.modified
	$sql = '
		select
			source_user_id,
			count(se.mark_id) as count
		FROM
			' . $config['mysql']['prefix'] . 'score se
		WHERE
			se.source_user_id != se.destination_user_id and
			se.source_user_id = ' . (int)$source_user_id . ' and 
			se.destination_user_id = ' . (int)$destination_user_id . ' and
			se.mark_id = ' . (int)$mark_id . ' and
			se.modified >= ' . to_sql($start) . ' and
			se.modified < ' . to_sql($end) . ' and
			se.active = 1
	';
	# $kid['source_user_id_score_like_count'][$row['source_user_id']] = 0;
	$result = mysql_query($sql) or die(mysql_error());
	while ($row = mysql_fetch_assoc($result))
		return $row['count'];
	return 0;
}

function initialize_score_channel_user_id_array(& $channel, $cycle_carry = 3) {
	# precall
	# - $channel << get_channel_member_list()
	# be careful when using the channel alias because sometimes it references channel_list and not a single channel

	$a1 = array();
	for ($i1 = 0; $i1 <= $cycle_carry && $i1 <= 8; $i1++) {
		$a1[$i1] = $i1;
	}

	if (!empty($channel['member_list']))
	foreach ($channel['member_list'] as $k1 => $v1) {
		foreach ($channel['member_list'] as $k2 => $v2) {
			$kid = & $channel['destination_user_id'][$k1];
			$kid['source_user_id_score_count'][$k2] = 0;
			$kid['aggregate'] = array(); # placement for debug only
			# set later but only if non zero result
			# $kid['source_user_id_score_like_count'][$k2] = 0;
			# $kid['source_user_id_score_dislike_count'][$k2] = 0;
			foreach ($a1 as $k3 => $v3) {
				$kid['score_offset'][$k3]['mark_count'][$k2] = 0;
			}
		}
		$kis = & $channel['source_user_id'][$k1];
		$kis['aggregate'] = array(); # placement for debug only
		foreach ($a1 as $k3 => $v3) {
			$kis['score_offset'][$k3]['mark_count'] = 0;
			$kis['score_offset'][$k3]['like_count'] = 0;
			$kis['score_offset'][$k3]['dislike_count'] = 0;
			$kis['score_offset'][$k3]['net_count'] = 0;
		}
	}
}

function get_score_channel_user_id_array(& $channel, $channel_parent_id, $destination_user_id, $cycle_carry = 3) {
	# precall
	# - <channel_parent_id>
	# - get_channel_cycle_restart_array() || get_specifig_channel_cycle_restart_array()
	# - initialize_score_channel_user_id_array()
	global $config;
	$kid = & $channel['destination_user_id'][$destination_user_id];
	$cycle_offset = & $channel['cycle_offset'];
	# hardcode for mark
	$a1 = array(
		1 => 1,
		2 => 2,
	);
	# todo make sure get_cycle_last_start() isnt running here
	$a2 = array();
	for ($i1 = 0; $i1 <= $cycle_carry && $i1 <= 8; $i1++) {
		$a2[$i1] = array(
			'end' => $cycle_offset[$i1]['start'],
			'start' => $cycle_offset[$i1+1]['start'],
		);
	}
	foreach ($channel['member_list'] as $k1 => $v1) {
		# reset alias for this loop
		$kis = & $channel['source_user_id'][$k1];
		foreach ($a1 as $k11 => $v11) {
		foreach ($a2 as $k12 => $v12) {
			$i1 = 0;
			if ($k1 != $destination_user_id) {
				$i1 = get_score_count(
					$k1,
					$destination_user_id,
					$k11,
					$v12['start'],
					$v12['end']
				);
			}
			if (!empty($i1)) {
				if (1) {
					# scale by destination user time
					# so part time users will be less likely to receive higher payout
					# (loses the ability to see the integer amount of likes)
					$kist = & $channel['source_user_id'][$destination_user_id];
					$kista = & $kist['after']['time_weight'];
					$kistb = & $kist['before']['time_weight'];
					$i1 *= ($kista + $kistb);
				}
				switch ($k11) {
					case 1:
						$kid['score_offset'][$k12]['like_count'][$k1] = $i1;
						$kis['score_offset'][$k12]['like_count'] += $i1;
					break;
					case 2:
						$kid['score_offset'][$k12]['dislike_count'][$k1] = $i1;
						$kis['score_offset'][$k12]['dislike_count'] += $i1;
					break;
				}
				$kid['score_offset'][$k12]['mark_count'][$k1] += $i1;
				$kis['score_offset'][$k12]['mark_count'] += $i1;
			}
		} }
		foreach ($kis['score_offset'] as $k3 => $v3) {
			# $kis['score_offset'][$k3]['net_count'] = abs($v3['like_count'] - $v3['dislike_count']);
			$kis['score_offset'][$k3]['net_count'] = ($v3['like_count'] - $v3['dislike_count']);
			# todo calculating kid needed 1st?
			# todo don't allow less than 0 on payout (probably ok in other computations)
			# todo test
			# if ($kis['score_offset'][$k3]['net_count'] < 0)
			# 	$kis['score_offset'][$k3]['net_count'] = 0;
		}
	}
	foreach ($kid['source_user_id_score_count'] as $k1 => $v1) {
	if (!empty($v1)) {
		$kid['source_user_id_score_sum'][$k1] = (
			$kid['source_user_id_score_like_count'][$k1]
			-
			$kid['source_user_id_score_dislike_count'][$k1]
		);
		$kid['source_user_id_score_average'][$k1] = (
			$kid['source_user_id_score_sum'][$k1]
			/
			$kid['source_user_id_score_count'][$k1]
		);
	} }
	foreach($kid['score_offset'] as $k2 => $v2) {
	if (!empty($v2)) {
		foreach($v2['mark_count'] as $k3 => $v3) {
		$kid['score_offset'][$k2]['net_count'][$k3] = abs($v2['like_count'][$k3] - $v2['dislike_count'][$k3]);
		# don't allow less than 0 on payout (probably ok in other computations)
		if ($kid['score_offset'][$k2]['net_count'][$k3] < 0)
			$kid['score_offset'][$k2]['net_count'][$k3] = 0;
		if (!empty($v3)) {
			$kid['score_offset'][$k2]['score_sum'][$k3] = (
				$v2['like_count'][$k3]
				-
				$v2['dislike_count'][$k3]
			);
			$kid['score_offset'][$k2]['score_sum'][$k3];
			$kid['score_offset'][$k2]['score_average'][$k3] = (
				$kid['score_offset'][$k2]['score_sum'][$k3]
				/
				$v2['mark_count'][$k3]
			);
		} }
	} }
}

function unset_if_empty_in_array(& $a1) {
	foreach ($a1 as $k1 => $v1)
		if (empty($v1))
			unset($a1[$k1]);
}

# cycle/renewal
function get_single_channel_parent_id($type, $id) {
	global $config;
	global $prefix;
	$sql = '
			cnl.parent_id
		from
			' . $prefix . 'channel cnl,
			' . $prefix . 'cycle cce
		where
			cce.channel_id = cnl.id and
	';
	switch ($type) {
		case 'channel': # placeholder
			$sql .= 'cnl.id = ' . (int)$id;
		break;
		case 'cycle':
			$sql .= 'cce.id = ' . (int)$id;
		break;
	}
	$i1 = get_db_single_value($sql,0);
	return $i1;
}
function get_run_datetime_array() {
	# get a solid run datetime for the running of this script 
	# pecision = day (perhaps can increase precision in the next)
	# intended to be used with cron
	$i1 = strtotime(date('Y-m-d'));
	$i2 = 86400;
	return array(
		'previous' => date('Y-m-d H:i:s', $i1 - $i2),
		'current' => date('Y-m-d H:i:s', $i1),
		'next' => date('Y-m-d H:i:s', $i1 + $i2),
	);
}
function get_day_difference($dt1, $dt2) {
	return abs((strtotime($dt1) - strtotime($dt2))/86400);
}
function get_datetime_add_day($dt1, $offset) {
	return date('Y-m-d H:i:s', strtotime($dt1) + (86400 * $offset));
}
function get_cycle_next_start($ccycle_start, $cchannel_offset) {
	return date('Y-m-d H:i:s', strtotime($ccycle_start) + ($cchannel_offset * 86400));
}

function get_cycle_last_start($channel_parent_id, $datetime) {
	global $config;
	return get_db_single_value('
			cce.start
		from
			' . $config['mysql']['prefix'] . 'channel cnl,
			' . $config['mysql']['prefix'] . 'cycle cce
		where
			cnl.id = cce.channel_id and
			cce.start < ' . to_sql($datetime) . ' and
			cnl.parent_id = ' . (int)$channel_parent_id . '
		order by
			cce.id desc
	');
}

# renewal
function insert_renewal_next(& $cycle, & $renewal, $channel_parent_id, $user_id, $point_id, $datetime) {
	# precall
	# - get_renewal_next_data()

	global $config;
	global $prefix;
	if ($config['debug'] == 1) {
		echo '<pre>'; print_r($cycle); echo '</pre>';
	}
	# alias
	$ncycle = & $cycle['next'];
	$ccycle = & $cycle['current'];
	$pcycle = & $cycle['previous'];
	$nrenewal = & $renewal['next'];
	$crenewal = & $renewal['current'];
	$prenewal = & $renewal['previous'];
	# next renewal
	$i2 = 0;
	$i2 = get_db_single_value('
			rnal.id
		from
			' . $prefix . 'channel cnl,
			' . $prefix . 'cycle cce,
			' . $prefix . 'renewal rnal
		where
			cnl.id = cce.channel_id and
			cce.id = rnal.cycle_id and
			cnl.parent_id = ' . (int)$channel_parent_id . ' and
			rnal.user_id = ' . (int)$user_id . ' and
			rnal.start > ' . to_sql($datetime) . ' and
			rnal.active = 1
	',0);
	if ($config['debug'] == 1)
		print_debug($i2);
	if (empty($i2)) {
		# account for the ratio into the next cycle
		# make sure get_renewal_next_data() already ran and set $nrenewal['renewal_start']
		$sql = '
			insert into
				' . $prefix . 'renewal
			set
				user_id = ' . (int)$user_id . ',
				start = '  . to_sql($nrenewal['renewal_start']) . ',
				point_id = ' . (int)$point_id . ',
				timeframe_id = 3,
				modified = now(),
				active = 1,
				cycle_id = ' . (int)$ncycle['cycle_id']
		;
		if ($config['debug'] == 1)
			print_debug($sql);
		if ($config['write_protect'] != 1)
			mysql_query($sql) or die(mysql_error());

		$i1 = mysql_insert_id();
		# todo placeholder to insert carry over score

		# todo grant payout based on:
		# see ascii picture at ~/include/list/v1/page/cycle_report.php
	}
}
function get_renewal_period_array(& $cycle, & $renewal, $user_id, $period) {
	global $config;
	global $prefix;
	# channel_parent_id probably is already part of the other array
	# get most recent renewal data

	if ($config['debug'] == 1)
		print_debug($period);
	# echo '<pre>'; print_r($cycle[$period]); echo '</pre>';

	switch($period) {
		case 'next':
		case 'current':
		case 'previous':
			if (!empty($cycle[$period]['cycle_start'])) {
				$sql = '
					select
						rnal.id as renewal_id,
						rnal.start as renewal_start,
						rnal.point_id
					from
						' . $prefix . 'renewal rnal,
						' . $prefix . 'cycle cce,
						' . $prefix . 'channel cnl
					where
						cce.id = rnal.cycle_id and
						cnl.id = cce.channel_id and
						cnl.parent_id = ' . (int)$cycle[$period]['channel_parent_id'] . ' and
						rnal.user_id = ' . (int)$user_id . ' and
						rnal.start >= ' . to_sql($cycle[$period]['cycle_start']) . ' and
						rnal.start < date_add(' . to_sql($cycle[$period]['cycle_start']) . ', interval + ' . (int)$cycle[$period]['channel_offset'] . ' day) and
						rnal.active = 1 and
						cce.active = 1 and
						cnl.active = 1
					order by
						rnal.id desc
					limit
						1
				';
				if ($config['debug'] == 1) {
					print_debug($sql);
				}
				$result = mysql_query($sql) or die(mysql_error());
				while ($row = mysql_fetch_assoc($result)) {
					$renewal[$period]['renewal_id'] = $row['renewal_id'];
					$renewal[$period]['renewal_start'] = $row['renewal_start'];
					$renewal[$period]['point_id'] = $row['point_id'];
				}
			}
		break;
	}
}
function get_renewal_next_data(& $cycle, & $renewal) {

	# alias
	$ncycle = & $cycle['next'];
	$ccycle = & $cycle['current'];
	$pcycle = & $cycle['previous'];
	$nrenewal = & $renewal['next'];
	$crenewal = & $renewal['current'];
	$prenewal = & $renewal['previous'];
	# r2c
	# todo calclulate score
	# todo factor in score with value

	# logic repeated from insert_cycle_next();
	if (empty($ncycle['cycle_start']))
		$ncycle['cycle_start'] = get_cycle_next_start($ccycle['cycle_start'], $ccycle['channel_offset']);

	$nrenewal['r2c_day'] = get_day_difference($crenewal['renewal_start'], $ncycle['cycle_start']);
	$nrenewal['r2c_ratio'] = 1 - ($nrenewal['r2c_day'] / $ccycle['channel_offset']);
	$nrenewal['r2c_score'] = 0;
	$nrenewal['r2c_renewal'] = $nrenewal['r2c_ratio'] * $ccycle['channel_value'];
	# c2r
	# todo calclulate score
	# todo factor in score with value
	$nrenewal['c2r_day'] = abs($nrenewal['r2c_day'] - $ncycle['channel_offset']);
	$nrenewal['c2r_ratio'] = 1 - $nrenewal['r2c_ratio'];
	$nrenewal['c2r_score'] = 0;
	$nrenewal['c2r_renewal'] = $nrenewal['c2r_ratio'] * $ncycle['channel_value'];
	# misc
	$nrenewal['renewal_start'] = get_datetime_add_day($ncycle['cycle_start'], $nrenewal['r2c_ratio'] * $ncycle['channel_offset']);
	
	# todo placeholder to insert carry over score

	# todo grant payout based on:
	# see ascii picture at ~/include/list/v1/page/cycle_report.php
}
function get_renewal_array(& $cycle, & $renewal, $channel_parent_id, $user_id) {
	get_renewal_period_array($cycle, $renewal, $user_id, 'current');
	get_renewal_period_array($cycle, $renewal, $user_id, 'previous');
	get_renewal_period_array($cycle, $renewal, $user_id, 'next');
}

# cycle
function get_channel_data(& $cycle, $channel_parent_id, $period, $datetime) {
	global $config;
	global $prefix;
	switch($period) {
		case 'next':
			$s1 = $cycle['current']['cycle_start'];
		break;
		case 'current':
			# new channel!
			$s1 = $cycle['current']['cycle_start'];
			if (!empty($cycle['previous']['cycle_start']))
				$s1 = $cycle['previous']['cycle_start'];
		break;
		case 'previous':
			# no data array that far back to grab from
			$s1 = get_db_single_value('
					cce.start as cycle_start
				from
					' . $prefix . 'cycle cce,
					' . $prefix . 'channel cnl
				where
					cnl.id = cce.channel_id and
					cnl.id = ' . (int)$channel_parent_id . ' and
					cce.start > ' . to_sql($datetime) . ' and
					cce.active = 1 and
					cnl.active = 1
			');
		break;
	}
	$sql = '
		select
			id as channel_id,
			parent_id as channel_parent_id,
			value as channel_value,
			offset as channel_offset
		from
			' . $prefix . 'channel
		where
			modified <= ' . to_sql($s1) . ' and 
			parent_id = ' . (int)$channel_parent_id . '
		order by
			id desc
		limit
			1
	';
	$result = mysql_query($sql) or die(mysql_error());
	while ($row = mysql_fetch_assoc($result)) {
		# overwrite with same values if they exist
		$cycle[$period]['channel_id'] = $row['channel_id'];
		$cycle[$period]['channel_parent_id'] = $row['channel_parent_id'];
		$cycle[$period]['channel_value'] = $row['channel_value'];
		$cycle[$period]['channel_offset'] = $row['channel_offset'];
	}
}
function insert_cycle_next(& $cycle, $channel_parent_id, $datetime) {
	global $config;
	global $prefix;

	# alias
	$ccycle = & $cycle['current'];

	# protect against array not already being set
	$ncycle_id = get_db_single_value('
			cce.id
		from
			' . $prefix . 'channel cnl,
			' . $prefix . 'cycle cce
		where
			cnl.id = cce.channel_id and
			cce.start > ' . to_sql($datetime) . ' and
			cnl.parent_id = ' . (int)$channel_parent_id
	,0);

	# ensure next cycle exists in db
	if (empty($ncycle_id)) {

		if (empty($ccycle['cycle_id']))
			get_cycle_current_array($cycle, $channel_parent_id, $datetime);

		# ahead of time calculation
		# repeated from get_renewal_next_data()
		$ncycle_start = date('Y-m-d H:i:s', strtotime($ccycle['cycle_start']) + $ccycle['channel_offset'] * 86400);
		$ncycle_start = get_cycle_next_start($ccycle['cycle_start'], $ccycle['channel_offset']);
		
		$sql = '
			insert into
				' . $prefix . 'cycle
			set
				start = ' . to_sql($ncycle_start) . ',
				channel_id = ' . (int)$channel_parent_id . ',
				modified = now(),
				point_id = 2,
				timeframe_id = 3,
				active = 1
		';
		if ($config['debug'] == 1)
			print_debug($sql);
		if ($config['write_protect'] != 1)
			mysql_query($sql) or die(mysql_error());
	}
}
function get_cycle_next_array(& $cycle, $channel_parent_id, $datetime) {
	global $config;
	global $prefix;

	# alias
	$ncycle = & $cycle['next'];
	$ccycle = & $cycle['current'];
	$pcycle = & $cycle['previous'];

	$sql = '
		select
			cce.id as cycle_id,
			cce.start as cycle_start,
			cnl.id as channel_id,
			cnl.parent_id as channel_parent_id,
			cnl.modified as channel_modified
		from
			' . $prefix . 'cycle cce,
			' . $prefix . 'channel cnl
		where
			cnl.id = cce.channel_id and
			cnl.parent_id = ' . (int)$channel_parent_id . ' and
			cce.start > ' . to_sql($datetime) . ' and
			cce.active = 1 and
			cnl.active = 1
		limit
			1
	';
	$result = mysql_query($sql) or die(mysql_error());
	while ($row = mysql_fetch_assoc($result)) {
		$cycle['next'] = $row;
	}
	get_channel_data($cycle, $channel_parent_id, 'next', $datetime);
}
function get_cycle_current_array(& $cycle, $channel_parent_id, $datetime) {
	global $config;
	global $prefix;
	# does a cycle have to exist for this function?
	$sql = '
		select
			cce.id as cycle_id,
			cce.start as cycle_start,
			cnl.id as channel_id,
			cnl.parent_id as channel_parent_id,
			cnl.modified as channel_modified
		from
			' . $prefix . 'cycle cce,
			' . $prefix . 'channel cnl
		where
			cnl.id = cce.channel_id and
			cnl.parent_id = ' . (int)$channel_parent_id . ' and
			cce.start <= ' . to_sql($datetime) . ' and
			cce.active = 1 and
			cnl.active = 1
		order by
			cce.id desc
		limit
			2
	';
	# -- now could be the current cycle!
	if ($config['debug'] == 1)
		print_debug($sql);
	$i1 = 1;
	$result = mysql_query($sql) or die(mysql_error());
	while ($row = mysql_fetch_assoc($result)) {
		switch ($i1) {
			case '1':
				# if brand new team does this contain the channel data for inserting a renewal
				$cycle['current'] = $row;
			break;
			case '2':
				# key for current channel data when inseting renewal
				$cycle['previous'] = $row;
			break;
		}
		$i1++;
	}
	get_channel_data($cycle, $channel_parent_id, 'current', $datetime);
	get_channel_data($cycle, $channel_parent_id, 'previous', $datetime);
}
function get_cycle_previous_array(& $cycle, $channel_parent_id, $datetime) {
	# placeholder
	# bundled with get_cycle_current_array()
}

# todo placeholder
function get_cycle($inequality, $key, $value) {
	# metaphor
	# - current
	# - previous
	# - next
	switch ($shift) {
		case '<=':
		case '<':
		case '>':
			# todo build query
		break;
	}
	switch ($from) {
		case 'datetime':
		case 'cycle_id':
		case 'renewal_id':
		break;
	}
}
function get_renewal($shift, $from, $value) {
	# todo same as get_cycle()
}

function get_cycle_array(& $cycle, $channel_parent_id, $datetime) {
	# prerequire
	# - cycle when channel is created
	# - all previous cycles with no breaks!
	get_cycle_current_array($cycle, $channel_parent_id, $datetime);
	get_cycle_previous_array($cycle, $channel_parent_id, $datetime);
	get_cycle_next_array($cycle, $channel_parent_id, $datetime);
}

#######################
# renewal_process.php #
#######################

# first ever cycle
function is_cycle_start($channel_parent_id) {
	global $config;
	global $prefix;
	$i1 = get_db_single_value('
			cce.point_id
		from
			' . $prefix . 'channel cnl,
			' . $prefix . 'cycle cce
		where
			cnl.id = cce.channel_id and
			cnl.parent_id = ' . (int)$channel_parent_id . ' and
			cce.point_id != 3
		order by
			cce.id desc
	',0);
	switch($i1) {
		case '1': # should not happen
		case '2':
		case '4':
			return 0;
		break;
		case '3': # cycle has ended previously ended and needs restart
		default: # no result 
			return 1;
		break;
	}
}
function insert_cycle_start($channel_parent_id) {
	global $config;
	global $prefix;
	# if the cycle already ended use the most recent channel data
	$i1 = get_db_single_value('
			id
		from
			' . $prefix . 'channel
		where
			parent_id = ' . (int)$channel_parent_id . '
		order by
			id desc
	');
	if (empty($i1))
		$i1 = $channel_parent_id;
	# intention is to not be concerened with when a cycle first starts
	$sql = '
		insert into
			' . $prefix . 'cycle
		set
			channel_id = ' . (int)$i1 . ',
			point_id = 1,
			timeframe_id = 2,
			start = now(),
			modified = now(),
			active = 1
	';
	if ($config['debug'] == 1)
		print_debug($sql);
	if ($config['write_protect'] != 1)
		mysql_query($sql) or die(mysql_error());
	# previous end cycle is no longer current
	$i2 = get_db_single_value('
			cce.id
		from
			' . $prefix . 'cycle cce,
			' . $prefix . 'channel cnl
		where
			cce.channel_id = cnl.id and
			cce.point_id = 3
		order by
			cce.id desc
	');
	if (!empty($i2)) {
		$sql = '
			update
				' . $prefix . 'cycle
			set
				timeframe_id = 1
			where
				id = ' . (int)$i2
		;
		if ($config['debug'] == 1)
			print_debug($sql);
		if ($config['write_protect'] != 1)
			mysql_query($sql) or die(mysql_error());
	}
}
# first ever renewal
# setting a future cycle renewal start date is not yet permitted
# to enable perhaps store future renewal dates in a separate table
function is_renewal_start($cycle_id, $user_id) {
	global $config;
	global $prefix;
	$i1 = (get_db_single_value('
			rnal.point_id
		from
			' . $prefix . 'renewal rnal
		where
			rnal.cycle_id = ' . (int)$cycle_id . ' and
			rnal.user_id = ' . (int)$user_id . ' and
			rnal.active = 1
	',0));
	switch($i1) {
		case '1':
		case '2':
		case '4':
			return 0;
		break;
		case '3':
		default:
			return 1;
		break;
	}
}
function insert_renewal_start($cycle_id, $user_id) {
	global $config;
	global $prefix;
	$sql = '
		insert into
			' . $prefix . 'renewal
		set
			user_id = ' . (int)$user_id . ',
			start = now(),
			point_id = 1,
			timeframe_id = 2,
			modified = now(),
			active = 1,
			cycle_id = ' . (int)$cycle_id
	;
	if ($config['debug'] == 1)
		print_debug($sql);
	if ($config['write_protect'] != 1)
		mysql_query($sql) or die(mysql_error());

	$i1 = mysql_insert_id();
	# todo placeholder to insert carry over score
}
