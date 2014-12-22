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
# - cron
# - manual edits

# changing cycle length does not permit skiping or covering multiple renewals
# the % into the current cycle when a renewal happens is factored in when renewing

##############
# cycle
# renewal
##############
function get_rating_calculation($dt1, $dt2, $user_id) {
	# base on previous renewal
	return (double)$d1;
}
function get_datetime_difference($dt1, $dt2) {
	# computed with
	# startpoint (renewal start)
	# midpoint (cycle start)
	return (string)$s1;
}
function get_renewcycle_array(& $cycle, & $array) {
}

##############
# renewal
##############
function get_renewal_future_array(& $cycle, & $renewal, $channel_parent_id, $user_id) {
	get_renewal_period_array($cycle, $renewal, $user_id, 'future');
}
function get_renewal_current_array(& $cycle, & $renewal, $user_id) {
	get_renewal_period_array($cycle, $renewal, $user_id, 'current');
}
function get_renewal_previous_array(& $cycle, & $renewal, $channel_parent_id, $user_id) {
	get_renewal_period_array($cycle, $renewal, $user_id, 'previous');
}
function get_renewal_period_array(& $cycle, & $renewal, $user_id, $period) {
	# channel_parent_id probably is already part of the other array
	# get most recent renewal data
	global $prefix;

	switch($period) {
		case 'current':
		case 'previous':
		case 'future':
			$sql = '
				select
					rnal.id as renewal_id,
					rnal.start as renewal_start,
					rnal.rating_value,
					rnal.value as renewal_value,
					rnae.point_id
				from
					' . $prefix . 'renewal rnal,
					' . $prefix . 'renewage rnae,
					' . $prefix . 'cycle cce,
					' . $prefix . 'channel cnl
				where
					rnae.renewal_id = rnal.cycle_id and
					cce.id = rnal.cycle_id and
					cnl.id = cce.channel_id and
					cnl.parent_id = ' . (int)$cycle[$period]['channel_parent_id'] . ' and
					rnal.user_id = ' . (int)$user_id . ' and
					rnal.start >= ' . to_sql($cycle[$period]['start']) . ' and
					rnal.start < ' . to_sql('date_add(' . $cycle[$period]['start'] . ', interval + ' . $cycle[$period]['offset'] . ' day') . ' and
					rnal.active = 1 and
					cce.active = 1 and
					cnl.active = 1
				order by
					rnal.start desc
				limit
					1
			';
			$result = mysql_query($sql) or die(mysql_error());
			while ($row = mysql_fetch_assoc($result)) {
				$renewal[$period] = $row;
			}
		break;
	}
}
function get_renewal_array(& $cycle, & $renewal, $channel_parent_id, $user_id) {
	# todo calculate differences in cycle/renewal later
	get_renewal_period_array($cycle, $renewal, $user_id, 'previous');
	get_renewal_period_array($cycle, $renewal, $user_id, 'current');
	get_renewal_period_array($cycle, $renewal, $user_id, 'future');
}

##############
# cycle
##############
function get_channel_data(& $cycle, $channel_parent_id, $period) {
	global $prefix;

	switch($period) {
		case 'future':
			$s1 = $cycle['current']['cycle_start'];
		break;
		case 'current':
			$s1 = $cycle['previous']['cycle_start'];
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
			modified < ' . to_sql($s1) . ' and 
			id = ' . (int)$channel_parent_id . '
		order by
			modified desc
		limit
			1
	';
	$result = mysql_query($sql) or die(mysql_error());
	while ($row = mysql_fetch_assoc($result)) {
		# overwriting should overwrite with same values if they exist
		$cycle[$period]['channel_id'] = $row['channel_id'];
		$cycle[$period]['channel_parent_id'] = $row['channel_parent_id'];
		$cycle[$period]['channel_value'] = $row['channel_value'];
		$cycle[$period]['channel_offset'] = $row['channel_offset'];

		# $cycle[$period]['cycle_start'] = date('Y-m-d', strtotime($cycle['current']['cycle_start']) + $row['channel_offset']*86400);
	}
}
function get_cycle_future_array(& $cycle, $channel_parent_id) {
	global $prefix;
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

	# ensure future cycle exists in db
	if (empty($cycle['future']['cycle_id'])) {
		$sql = '
			insert into
				' . $prefix . 'cycle
			set
				start = ' . to_sql($cycle['future']['cycle_start']) . ',
				channel_id = ' . (int)$cycle['future']['channel_id'] . ',
				active = 1
		';
		# $result = mysql_query($sql) or die(mysql_error());
		$cycle['future']['cycle_id'] = mysql_insert_id();
	}
	# future cycle now exists
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
			cce.start < now() and
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
	global $prefix;
	# prerequire
	# - cycle when channel is created
	# - all previous cycles with no breaks!
	get_cycle_current_array($cycle, $channel_parent_id);
	get_cycle_previous_array($cycle, $channel_parent_id);
	get_cycle_future_array($cycle, $channel_parent_id);
	# no need to return since data was passed as a reference
}
