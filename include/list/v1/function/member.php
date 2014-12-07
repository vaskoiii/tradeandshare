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

# functions used to avoid the issue where changing cycle length can skip or cover multiple renewals
# the % into the current cycle when a renewal happens is factored in when renewing
function get_renewal_to_cycle_length($user_id, $cycle_id) {
}
function get_renewal_to_cycle_cost($user_id, $cycle_id) {
}
function get_cycle_to_renewal_length($user_id, $cycle_id) {
}
function get_cycle_to_renewal_cost($user_id, $cycle_id) {
}

function get_renewal_array(& $cycle, & $renewal, $channel_parent_id, $user_id) {
	die('todo write get_renewal_array function');
}

function get_cycle_array(& $cycle, $channel_parent_id) {

	global $prefix;

	# prerequire
	# - cycle when channel is created
	# - all previous cycles with no breaks!

	# get 3 most recent non-future cycles
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
	if (!empty($cycle['current']['cycle_start'])) {
		$sql = '
			select
				id as channel_id,
				parent_id as channel_parent_id,
				value as channel_value,
				offset as channel_offset
			from
				' . $prefix . 'channel
			where
				modified < ' . to_sql($cycle['current']['cycle_start']) . ' and 
				id = ' . (int)$channel_parent_id . '
			order by
				modified desc
			limit
				1
		';
		$result = mysql_query($sql) or die(mysql_error());
		while ($row = mysql_fetch_assoc($result)) {
			# overwriting should overwrite with same values if they exist
			$cycle['future']['channel_id'] = $row['channel_id'];
			$cycle['future']['channel_parent_id'] = $row['channel_parent_id'];
			$cycle['future']['channel_value'] = $row['channel_value'];
			$cycle['future']['channel_offset'] = $row['channel_offset'];

			$cycle['future']['cycle_start'] = date('Y-m-d', strtotime($cycle['current']['cycle_start']) + $row['channel_offset']*86400);
		}
	}
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
		$result = mysql_query($sql) or die(mysql_error());
		$cycle['future']['cycle_id'] = mysql_insert_id();
	}
	# future cycle now exists
	
	# no need to return since data was passed as a reference
}

