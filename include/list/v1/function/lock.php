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

# Contents/Description: Guide for how we would *usually* merge the tables together in the queries to produce the desired result.

# todo: Make a consistent convention for table aliases perhaps based on the join type

function search_lock(& $base, $type, $login_user_id) {
	# todo accept a parameter for the query string so we don't have to use these guys 2012-02-26 vaskoiii
	global $_GET, $_POST;
	global $config;

	# reduce wordiness
	$select = & $base['search']['select'];
	$from = & $base['search']['from'];
	$where = & $base['search']['where'];
	$where_x = & $base['search']['where_x'];
	$group_by = & $base['search']['group_by'];
	$prefix = $config['mysql']['prefix'];

	# USER &| CONTACT &| GROUP &| TEAM &| LOCATION
	if (get_gp('lock_user_id') || get_gp('lock_contact_id') || get_gp('lock_group_id') || get_gp('lock_team_id') || get_gp('lock_location_id')) {
		$default = false;
		switch($type) {
			case 'invited':
			case 'offer':
			case 'rating':
			case 'transfer':
				if (isset_gp('direction_id')) 
					$default = true;
				else {
					if ($type < 'user')
						$from[] = $prefix . 'index_' . $type . '_user luj';
					else
						$from[] = $prefix . 'index_user_' . $type . ' luj';
					$where[] = 'luj.' . $type . '_id = t1.id';
					$group_by[] = 'luj.' .$type . '_id';
					if (get_gp('lock_user_id'))
						$where[] = 'luj.user_id = ' . (int)get_gp('lock_user_id');
				}
			break;
			default:
				$default = true;
			break;
		}
		if ($default)
		if (get_gp('lock_user_id'))
			$where[] = 'u.id = ' . (int)get_gp('lock_user_id');
	}
	# CONTACT
	if (get_gp('lock_contact_id')) {
		$default = false;
		switch($type) {
			case 'invited':
			case 'offer':
			case 'rating':
			case 'transfer':
				if (isset_gp('direction_id'))
					$default = true;
				else {
					$from[] = $prefix . 'link_contact_user lcux';
					$where[] = 'lcux.user_id = luj.user_id';
					$where[] = 'lcux.contact_id = ' . (int)get_gp('lock_contact_id');
				}
			break;
			default:
				$default = true;
			break;
		}
		if ($default)
		if (get_gp('lock_contact_id'))
			/* # */ $where[] = 'c.id = ' . (int)get_gp('lock_contact_id');
	}
	# GROUP
	if (get_gp('lock_group_id')) {
		$default = false;
		switch($type) {
			case 'invited':
			case 'offer':
			case 'rating':
			case 'transfer':
				if (isset_gp('direction_id'))
					$default = true;
				else {
					# Prevent SQL from complaining about NOT unique table alias...
					if (!get_gp('lock_contact_id'))
						$from[] = $prefix . 'link_contact_user lcux';
					$from[] = $prefix . 'link_contact_group lcgx';
					$where[] = 'lcux.user_id = luj.user_id';
					$where[] = 'lcux.contact_id = lcgx.contact_id';
					$where[] = 'lcgx.group_id = ' . (int)get_gp('lock_group_id');
				}
			break;
			default:
				$default = true;
			break;
		}
		if ($default) {
			$from[] = $prefix . 'link_contact_group lcg';
				$where[] = 'lcg.active = 1';
			$from[] = $prefix . 'group g';
				$where[] = 'g.active = 1';
			$where[] = 'g.user_id = ' . (int)$login_user_id;
			switch($type) {
				case 'group':
					$where[] = 'u.id = g.user_id';
					$where[] = 'c.id = lcg.contact_id';
					$where[] = 'lcg.group_id = ' . (int)get_gp('lock_group_id');
				break;
				case 'groupmate':
					$from[] = $prefix . 'link_contact_group lcgxx';
					$where[] = 'lcgxx.contact_id = lcg.contact_id';
					$where[] = 'g.id = lcg.group_id';
					$where[] = 'c.id = lcg.contact_id';
					$where[] = 'lcgxx.group_id = ' . (int)get_gp('lock_group_id');
				break;
				default:
					$where[] = 'c.id = lcg.contact_id';
					$where[] = 'g.id = lcg.group_id';
					$where[] = 'g.id = ' . (int)get_gp('lock_group_id');
				break;
			}
		}
	}
	else {
		$b1 = false;
		switch($type) {
			case 'group':
				$b1 = true;
				$where[] = 'g.user_id = u.id';
			break;
			case 'groupmate':
				$b1 = true;
				$from[] = $prefix . 'link_contact_group lcg';
					$where[] = 'lcg.active = 1';
				$where[] = 'g.id = lcg.group_id';
				$where[] = 'c.id = lcg.contact_id';
			break;
		}
		if ($b1) {
			$from[] = $prefix . 'group g';
			$where[] = 'g.user_id = ' . (int)$login_user_id;
			$where[] = 'g.active = 1';
		}
	}
	# TEAM
	if (get_gp('lock_team_id')) {
		$default = false;
		switch($type) {
			case 'invited':
			case 'offer':
			case 'rating':
			case 'transfer':
				if (isset_gp('direction_id'))
					$default = true;
				else {
					$from[] = $prefix . 'link_team_user ltux';
					$where[] = 'ltux.user_id = luj.user_id';
					$where[] = 'ltux.team_id = ' . (int)get_gp('lock_team_id');
				}
			break;
			default:
				$default = true;
			break;
		}
		if ($default) {
			$from[] = $prefix . 'link_team_user ltu';
				$where[] = 'ltu.active = 1';
			$from[] = $prefix . 'team t';
				$where[] = 't.active = 1';
			switch($type) {
				case 'team':
					$where[] = 'u.id = t.user_id';
					$where[] = 'u.id = ltu.user_id';
					$where[] = 'ltu.team_id = ' . (int)get_gp('lock_team_id');
				break;
				case 'teammate':
					$where[] = 'ltu.team_id = t.id';
					$from[] = $prefix . 'link_team_user ltuxx';
					$where[] = 'ltuxx.user_id = ltu.user_id';
					$where[] = 'ltu.user_id = u.id';
					$where[] = 'ltuxx.team_id = ' . (int)get_gp('lock_team_id');
				break;
				default:
					$where[] = 'ltu.team_id = t.id';
					$where[] = 'ltu.user_id = u.id';
					$where[] = 't.id = ' . (int)get_gp('lock_team_id');
				break;
			}
		}
	}
	else {
		switch($type) {
			case 'team':
				$from[] = $prefix . 'team t';
				$where[] = 't.active = 1';
				$where[] = 't.user_id = u.id';
			break;
			case 'teammate':
				$from[] = $prefix . 'team t';
				$where[] = 't.active = 1';
				$from[] = $prefix . 'link_team_user ltu';
				$where[] = 'ltu.active = 1';
				$where[] = 'ltu.team_id = t.id';
				$where[] = 'ltu.user_id = u.id';
			break;
		}
	}
	# LOCATION
	if (get_gp('lock_location_id') && get_gp('lock_range_id')) {
		$sql = '
			SELECT
				latitude,
				longitude
			FROM
				' . $prefix . 'location lo
			WHERE
				id = ' . (int)get_gp('lock_location_id') . '
			LIMIT
				1
		';
		$result = mysql_query($sql) or die(mysql_error());
		while ($row = mysql_fetch_assoc($result)) {
			$base['extra']['location_latitude'] = $row['latitude'];
			$base['extra']['location_longitude'] = $row['longitude'];
		}
		$base['extra']['range_value'] = get_db_single_value('
				value
			FROM
				' . $prefix . 'range
			WHERE
				id = ' . (int)get_gp('lock_range_id')
		);
		$base['extra']['location_latitude_delta'] = get_latitude_range_in_miles($base['extra']['range_value']);
		$base['extra']['location_longitude_delta'] = get_longitude_range_in_miles($base['extra']['location_latitude'], $base['extra']['range_value']);
		$from[] = $prefix . 'location lo';
		$where[] = 'lo.active = 1';
		$where[] = 'lo.latitude <= ' . ($base['extra']['location_latitude'] + $base['extra']['location_latitude_delta']);
		$where[] = 'lo.latitude >= ' . ($base['extra']['location_latitude'] - $base['extra']['location_latitude_delta']);
		$where[] = 'lo.longitude <= ' . ($base['extra']['location_longitude'] + $base['extra']['location_longitude_delta']);
		$where[] = 'lo.longitude >= ' . ($base['extra']['location_longitude'] - $base['extra']['location_longitude_delta']);
		$default = false;
		switch($type) {
			case 'invited':
			case 'offer':
			case 'rating':
			case 'transfer':
				if (isset_gp('direction_id'))
					$default = true;
				else {
					$from[] = $prefix . 'user ux';
					$where[] = 'ux.id = luj.user_id';
					$where[] = 'ux.location_id  = lo.id';
				}
			break;
			case 'location':
				$where[] = 'u.id = lo.user_id';
			break;
			case 'locationmind':
			break;
			default:
				$default = true;
			break;
		}
		if ($default)
			$where[] = 'u.location_id  = lo.id';
	}
	else {
		$b1 = false;
		switch($type) {
			case 'user':
				$b1 = true;
				$where[] = 'lo.id = u.location_id';
			break;
			case 'location':
				$b1 = true;
				$where[] = 'lo.user_id = u.id';
			break;
			case 'locationmind':
				$b1 = true;
			break;
		}
		if ($b1) {
			$from[] = $prefix . 'location lo';
			$where[] = 'lo.active = 1';
		}
	}
	$base['adhere'] =  false;
	switch($type) {
		# 4 cases needing helper tables
		case 'score':
		case 'carry':
		case 'invited':
		case 'transfer':
		case 'offer':
		case 'rating':
			if (get_gp('lock_contact_id') || get_gp('lock_group_id')) {
				$from[] = $prefix . 'contact c';
				$from[] = $prefix . 'link_contact_user lcu';
			}
			if (get_gp('lock_contact_id') || get_gp('lock_group_id')) {
				$where[] = 'c.id = lcu.contact_id';
				$where[] = 'u.id = lcu.user_id';
			}
			$from[] = $prefix . 'user u';
				$where[] = 'u.active = 1';
			$from[] = $prefix . 'user u2';
				$where[] = 'u2.active = 1';
			if (get_gp('direction_id') == 1) { // TO
				$select[] = 'u2.id as source_user_id';
				$select[] = 'u2.name as source_user_name';
				$select[] = 'u.id as destination_user_id';
				$select[] = 'u.name as destination_user_name';
				$where[] = 'u2.id = t1.source_user_id';
				$where[] = 'u.id = t1.destination_user_id'; 
			}
			elseif (get_gp('direction_id') == 2) { // FROM
				$select[] = 'u.id as source_user_id';
				$select[] = 'u.name as source_user_name';
				$select[] = 'u2.id as destination_user_id';
				$select[] = 'u2.name as destination_user_name';
				$where[] = 'u.id = t1.source_user_id';
				$where[] = 'u2.id = t1.destination_user_id';
			}
			else {
				$select[] = 'u.id as source_user_id';
				$select[] = 'u.name as source_user_name';
				$select[] = 'u2.id as destination_user_id';
				$select[] = 'u2.name as destination_user_name';
				$where[] = 'u.id = t1.source_user_id';
				$where[] = 'u2.id = t1.destination_user_id';
			}
		break;
		case 'comment':
		case 'category':
		case 'cost':
		case 'cycle':
		case 'channel':
		# todo join to the renewal table to see what particular user was participating in what cycle
		# todo alternatively join to the channel table - cycles = children of channel
		case 'dialect':
		case 'feed':
		case 'feedback':
		case 'group':
		case 'incident':
		case 'invited':
		case 'item':
		case 'jargon':
		case 'location':
		case 'login':
		case 'membership':
		case 'meripost':
		case 'meritopic':
		case 'metail':
		case 'minder':
		case 'news':
		case 'renewal':
		case 'renewage':
		case 'tag':
		case 'team':
		case 'teammate':
		case 'transaction':
		case 'translation':
		case 'user':
		case 'vote':
			$base['adhere'] = 'user_join';
		break;
		case 'contact':
		case 'groupmate':
		case 'note':
			$base['adhere'] = 'contact_join';
		break;
	}
	switch($base['adhere']) {
		case 'user_join':
			$select[] = 'u.id as user_id';
			$select[] = 'u.name as user_name';
			$from[] = $prefix . 'user u';
				$where[] = 'u.active = 1';
			if (get_gp('lock_contact_id') || get_gp('lock_group_id')) {
				$from[] = $prefix . 'contact c';
				$from[] = $prefix . 'link_contact_user lcu';
			}
			if (get_gp('lock_contact_id') || get_gp('lock_group_id')) {
				$where[] = 'c.id = lcu.contact_id';
				$where[] = 'u.id = lcu.user_id';
			}
		break;
		case 'contact_join':
			$select[] = 'c.id as contact_id';
			$select[] = 'c.name AS contact_name';
			$from[] = $prefix . 'contact c';
				$where[] = 'c.active = 1';
			if ( get_gp('lock_user_id') || get_gp('lock_team_id') || (get_gp('lock_location_id') && get_gp('lock_range_id'))) {
				$from[] = $prefix . 'user u';
				$from[] = $prefix . 'link_contact_user lcu';
			}
			$where[] = 'c.user_id = ' . (int)$login_user_id;
			if ( get_gp('lock_user_id') || get_gp('lock_team_id') || (get_gp('lock_location_id') && get_gp('lock_range_id'))) {
				$where[] = 'c.id = lcu.contact_id';
				$where[] = 'u.id = lcu.user_id';
			}
		break;
	}
}
