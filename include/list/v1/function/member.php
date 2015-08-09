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
# score_report.php

# todo make sure changing cycle length does not permit skiping or covering multiple renewals
# ie) the ratio into the current cycle when a renewal happens should be factored in when renewing

# todo show functions that need to be called before the current function is called - keyword: precall

function print_debug($s1) {
	echo "\n<hr>\n<pre>\n$s1\n</pre>\n";
}

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
			cce.start = ' . to_sql($a1['cycle_restart']['yyyy-mm-dd-3x']) . ' and
			cnl.parent_id = ' . (int)$channel_parent_id
	);
}

function get_specific_channel_cycle_restart_array(& $channel, $channel_parent_id, $cycle_id) {
	global $config;

	# get cycle_id array
	$sql = '
		select
			cce.id as cycle_id,
			cce.start as cycle_start
		from
			' . $config['mysql']['prefix'] . 'cycle cce,
			' . $config['mysql']['prefix'] . 'channel cnl
		where
			cce.channel_id = cnl.id and
			cnl.parent_id = ' . (int)$channel_parent_id . ' and
			cce.id <= ' . (int)$cycle_id . '
		order by
			cce.id desc
		limit
			3
	';
	$result = mysql_query($sql) or die(mysql_error());
	$i1 = 1;
	while ($row = mysql_fetch_assoc($result)) {
		$i1++;
		$channel['cycle_restart']['yyyy-mm-dd-' . $i1 . 'x'] = $row['cycle_start'];
	}

	$dt2 = $channel['cycle_restart']['yyyy-mm-dd-2x'];
	$dt3 = $channel['cycle_restart']['yyyy-mm-dd-3x'];

	# calculate from 2 cycles back to 3 cycles back
	$channel['cycle_restart']['length_2x_to_3x'] = abs((strtotime($dt2) - strtotime($dt3))/86400);

	# can not choose a current or future cycle
	$dtlast = $channel['cycle_restart']['yyyy-mm-dd-last'] = get_cycle_last_start($channel_parent_id, date('Y-m-d H:i:s'));
	if ($dt2 >= $dtlast)
		die('cycle not ready!');
}

function get_channel_cycle_restart_array(& $channel, $channel_parent_id) {
	# todo merge with get_specific_channel_cycle_restart_array() - required getting cycle id for $dt2 though
	$dt1 = $channel['cycle_restart']['yyyy-mm-dd-1x'] = get_cycle_last_start($channel_parent_id, date('Y-m-d H:i:s'));
	$dt2 = $channel['cycle_restart']['yyyy-mm-dd-2x'] = get_cycle_last_start($channel_parent_id, $dt1);
	$dt3 = $channel['cycle_restart']['yyyy-mm-dd-3x'] = get_cycle_last_start($channel_parent_id, $dt2);
	$channel['cycle_restart']['yyyy-mm-dd-4x'] = get_cycle_last_start($channel_parent_id, $dt3);
	# calculate from 2 cycles back to 3 cycles back
	$channel['cycle_restart']['length_2x_to_3x'] = abs((strtotime($dt2) - strtotime($dt3))/86400);
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
			rnal.start < ' . to_sql($channel['cycle_restart']['yyyy-mm-dd-2x']) . ' and
			rnal.start >= ' . to_sql($channel['cycle_restart']['yyyy-mm-dd-3x'])
	;
	$result = mysql_query($sql) or die(mysql_error());
	while ($row = mysql_fetch_assoc($result))
		$channel['member_list'][$row['user_id']] = $row['user_id'];
}

function get_score_channel_destination_user_id_array(& $channel, $channel_parent_id, $destination_user_id) {
	# precall
	# - get_channel_cycle_restart_array() || get_specifig_channel_cycle_restart_array ???
	# - get_member_list_array() ???
	global $config;
	$kid = & $channel['destination_user_id'][$destination_user_id];
	foreach ($channel['member_list'] as $k1 => $v1)
		$kid['source_user_id_score_count'][$v1] = 0;
	foreach ($channel['member_list'] as $k1 => $v1) {
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
				se.source_user_id = ' . (int)$v1 . ' and 
				se.source_user_id in (' . implode(', ', $channel['member_list']) . ') and
				se.destination_user_id = ' . (int)($destination_user_id) . ' and
				se.modified < ' . to_sql($channel['cycle_restart']['yyyy-mm-dd-2x']) . ' and
				se.modified >= ' . to_sql($channel['cycle_restart']['yyyy-mm-dd-3x']) . ' and
				se.active = 1
		';
		# $kid['source_user_id_score_like_count'][$row['source_user_id']] = 0;
		$result = mysql_query($sql . ' and se.mark_id = 1 ') or die(mysql_error());
		while ($row = mysql_fetch_assoc($result))
			if (!empty($row['count'])) {
				$kid['source_user_id_score_like_count'][$row['source_user_id']] = $row['count'];
				$kid['source_user_id_score_count'][$row['source_user_id']] += $row['count'];
			}
		# $kid['source_user_id_score_dislike_count'][$row['source_user_id']] = 0;
		$result = mysql_query($sql . ' and se.mark_id = 2 ') or die(mysql_error());
		while ($row = mysql_fetch_assoc($result))
			if (!empty($row['count'])) {
				$kid['source_user_id_score_dislike_count'][$row['source_user_id']] = $row['count'];
				$kid['source_user_id_score_count'][$row['source_user_id']] += $row['count'];
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
}

# not yet a shared function
function get_score_channel_source_user_id_array(& $channel, $channel_parent_id, $source_user_id) {
	# precall ???
	global $config;
	$kis = & $channel['source_user_id'][$source_user_id];
	$kis['user_score_count'] = 0;
	$kis['user_score_like_count'] = 0;
	$kis['user_score_dislike_count'] = 0;
	$sql = '
		select
			count(*) count
		FROM
			' . $config['mysql']['prefix'] . 'score
		WHERE
			destination_user_id in (' . implode(', ', $channel['member_list']) . ') and
			source_user_id != destination_user_id and
			source_user_id = ' . (int)$source_user_id . ' and 
			active = 1
	';
	$result = mysql_query($sql . ' and mark_id = 1 ') or die(mysql_error());
	while ($row = mysql_fetch_assoc($result)) {
		if ($row['count']) {
			$kis['user_score_like_count'] = $row['count'];
			$kis['user_score_count'] += $row['count'];
		}
	}
	$result = mysql_query($sql . ' and mark_id = 2 ') or die(mysql_error());
	while ($row = mysql_fetch_assoc($result)) {
		if ($row['count']) {
			$kis['user_score_dislike_count'] = $row['count'];
			$kis['user_score_count'] += $row['count'];
		}
	}
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
# unused function but wondering if it may simplify logic
function get_renewal_last_start($cycle_id, $user_id, $datetime) {
	global $config;
	return get_db_single_value('
		rnal.start from
			' . $config['mysql']['prefix'] . 'cycle cce,
			' . $config['mysql']['prefix'] . 'renewal rnal
		where
			cce.id = rnal.cycle_id and
			rnal.user_id = ' . (int)$user_id . ' and
			cce.start < ' . to_sql($datetime) . ' and
			active = 1
		order by
			rnal.start desc
	');
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
		# see ascii picture at ~/include/list/v1/page/score_report.php
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
						rnal.start desc
					limit
						1
				';
				if ($config['debug'] == 1) {
					print_debug($sql);
				}
				$result = mysql_query($sql) or die(mysql_error());
				while ($row = mysql_fetch_assoc($result)) {
					$renewal[$period] = $row;
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
	# see ascii picture at ~/include/list/v1/page/score_report.php
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
			modified desc
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
			cce.start desc
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
		order by
			cce.start desc
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
			cce.start desc
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
