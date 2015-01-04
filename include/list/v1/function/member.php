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

# description: member functions that will be used in both:
# - cycle cron
# - cycle process (if ever implemented)
# - renewal cron
# - renewal process

# changing cycle length does not permit skiping or covering multiple renewals
# the ratio into the current cycle when a renewal happens is factored in when renewing

# cycle/renewal
function get_day_difference($dt1, $dt2) {
	return abs((strtotime($dt1) - strtotime($dt2))/86400);
}
function get_datetime_add_day($dt1, $offset) {
	return date('Y-m-d H:i:s', strtotime($dt1) + (86400 * $offset));
}
function get_rating_calculation($dt1, $dt2, $user_id) {
	# todo: base on previous renewal
	$d1 = 0;
	return (double)$d1;
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
			cce.modified desc
	');
}

# renewal
function insert_renewal_future(& $cycle, & $renewal, $channel_parent_id, $user_id, $point_id) {
	global $prefix;
	# alias
	$fcycle = & $cycle['future'];
	$ccycle = & $cycle['current'];
	$pcycle = & $cycle['previous'];
	$frenewal = & $renewal['future'];
	$crenewal = & $renewal['current'];
	$prenewal = & $renewal['previous'];
	# future renewal
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
			rnal.start > now() and
			rnal.active = 1
	',1);
	echo '<hr>'; var_dump($i2);
	if (empty($i2)) {
		$frenewal['renewal_start'] = get_datetime_add_day($crenewal['renewal_start'], $ccycle['channel_offset']);
		$sql = '
			insert into
				' . $prefix . 'renewal
			set
				user_id = ' . (int)$user_id . ',
				start = '  . to_sql($frenewal['renewal_start']) . ',
				active = 1,
				cycle_id = ' . (int)$fcycle['cycle_id']
		;
		echo '<hr>' . $sql;
		mysql_query($sql) or die(mysql_error());
		$frenewal['renewal_id'] = mysql_insert_id();
		# todo make it so that the submitted point_id is used instead of always 2
		# point_id is not passed to this function!
		$sql = '
			insert into
				' . $prefix . 'renewage
			set
				point_id = 2,
				modified = now(),
				timeframe_id = 3,
				renewal_id = ' . (int)$frenewal['renewal_id']
		;
		echo '<hr>' . $sql;
		mysql_query($sql) or die(mysql_error());
		$sql = '
			insert into
				' . $prefix . 'gauge_renewal
			set
				renewal_id = ' . (int)$frenewal['renewal_id'] . ',
				rating_value = 0,
				renewal_value = ' . (double)$fcycle['channel_value']
		;
		echo '<hr>' . $sql;
		mysql_query($sql) or die(mysql_error());
		# todo grant payout based on:
		# see ascii picture at ~/include/list/v1/page/user_report.php
	}
}
function get_renewal_period_array(& $cycle, & $renewal, $user_id, $period) {
	# channel_parent_id probably is already part of the other array
	# get most recent renewal data
	global $prefix;
	switch($period) {
		case 'future':
		case 'current':
		case 'previous':
			if (!empty($cycle[$period]['cycle_start'])) {
				$sql = '
					select
						rnal.id as renewal_id,
						rnal.start as renewal_start,
						"0" as rating_value,
						"0" as renewal_value,
						rnae.point_id
					from
						' . $prefix . 'renewal rnal,
						' . $prefix . 'renewage rnae,
						' . $prefix . 'cycle cce,
						' . $prefix . 'channel cnl
					where
						rnal.id = rnae.renewal_id and
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
				# echo '<hr>'; echo $period; echo $sql;
				$result = mysql_query($sql) or die(mysql_error());
				while ($row = mysql_fetch_assoc($result)) {
					$renewal[$period] = $row;
				}
			}
		break;
	}
}
function get_renewal_array(& $cycle, & $renewal, $channel_parent_id, $user_id) {
	get_renewal_period_array($cycle, $renewal, $user_id, 'current');
	get_renewal_period_array($cycle, $renewal, $user_id, 'previous');
	get_renewal_period_array($cycle, $renewal, $user_id, 'future');
}

# cycle
function get_channel_data(& $cycle, $channel_parent_id, $period) {
	global $prefix;
	switch($period) {
		case 'future':
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
					cce.start > now() and
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
			id = ' . (int)$channel_parent_id . '
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
function insert_cycle_future(& $cycle, $channel_parent_id) {
	global $prefix;
	# alias
	$fcycle = & $cycle['future'];
	$ccycle = & $cycle['current'];
	$pcycle = & $cycle['previous'];
	$frenewal = & $renewal['future'];
	$crenewal = & $renewal['current'];
	$prenewal = & $renewal['previous'];

	# protect against array not already being set
	$fcycle['cycle_id'] = get_db_single_value('
			cce.id
		from
			' . $prefix . 'channel cnl,
			' . $prefix . 'cycle cce
		where
			cnl.id = cce.channel_id and
			channel_id = ' . (int)$channel_parent_id
	,1);
	
	# ensure future cycle exists in db
	if (empty($fcycle['cycle_id'])) {
		$fcycle['cycle_start'] = date('Y-m-d H:i:s', strtotime($ccycle['cycle_start']) + $ccycle['channel_offset'] * 86400);
		$sql = '
			insert into
				' . $prefix . 'cycle
			set
				start = ' . to_sql($fcycle['cycle_start']) . ',
				channel_id = ' . (int)$fcycle['channel_id'] . ',
				modified = now(),
				timeframe_id = 3,
				active = 1
		';
		$result = mysql_query($sql) or die(mysql_error());
		$cycle['future']['cycle_id'] = mysql_insert_id();
	}
	# future cycle now exists
}
function get_cycle_future_array(& $cycle, $channel_parent_id) {
	global $prefix;
	# alias
	$fcycle = & $cycle['future'];
	$ccycle = & $cycle['current'];
	$pcycle = & $cycle['previous'];
	$frenewal = & $renewal['future'];
	$crenewal = & $renewal['current'];
	$prenewal = & $renewal['previous'];
	# guarantee only and at least 1 possible future cycle
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
			cnl.id = ' . (int)$channel_parent_id . ' and
			cce.start > now() and
			cce.active = 1 and
			cnl.active = 1
		limit
			1
	';
	$result = mysql_query($sql) or die(mysql_error());
	while ($row = mysql_fetch_assoc($result)) {
		$cycle['future'] = $row;
	}
	get_channel_data($cycle, $channel_parent_id, 'future');
	insert_cycle_future($cycle, $channel_parent_id);
}
function get_cycle_current_array(& $cycle, $channel_parent_id) {
	global $prefix;
	# get 2 most recent non-future cycles
	# min cycles that can exist at this point?
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
			cnl.id = ' . (int)$channel_parent_id . ' and
			-- now could be the current cycle!
			cce.start <= now() and
			cce.active = 1 and
			cnl.active = 1
		order by
			cce.start desc
		limit
			2
	';
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
	get_channel_data($cycle, $channel_parent_id, 'current');
	get_channel_data($cycle, $channel_parent_id, 'previous');
}
function get_cycle_previous_array(& $cycle, $channel_parent_id) {
	# placeholder
	# bundled with get_cycle_current_array()
}
function get_cycle_array(& $cycle, $channel_parent_id) {
	# prerequire
	# - cycle when channel is created
	# - all previous cycles with no breaks!
	get_cycle_current_array($cycle, $channel_parent_id);
	get_cycle_previous_array($cycle, $channel_parent_id);
	get_cycle_future_array($cycle, $channel_parent_id);
}
