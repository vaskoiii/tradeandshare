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

# Contents/Description: Generate secondary queries to help make the main search easier!

# $tranlation 
# - goes by [name] so it is human readable
# - intended to only retrieve data from the translation table

# $key 
# - goes by [id]
# - intended to retrieve a multitude of data

# NOTE: sometimes lookups may be unnecessary. However, in order to write less queries some unnecessary lookups may occur.

# easy get
function kk($type, $id, $select, $fallback = false, & $key = null) {
	if (!isset($key))
		global $key;
	return to_html(get_key($type, $id, $select, $fallback, $key));
}
function tt($type, $name, $select = 'translation_name', & $translation = null) {
	if (!isset($translation))
		global $translation;
	return to_html(get_translation($type, $name, $select, $translation));
}

# normal get
function get_key($type, $id, $select, $fallback = false, & $key = null) {
	global $config;
	if (!isset($key))
		global $key;

	#if (!$fallback)
	#	$fallback = $type . '_id: ' . $id;

	$s1 = $key[$type . '_id']['result'][$id][$select];
	if ($s1)
		return $s1;
	elseif($fallback) {


		if (str_match($config['reserved_prefix'], $fallback)) # assumes prefix match is same as suffix match 2012-04-28 vaskoiii
			return $fallback;
		else
			return $config['unabstracted_prefix'] . $fallback . $config['unabstracted_suffix'];
	}
}
function get_translation($type, $name, $select = 'translation_name', & $translation = null) {
	global $config;
	if (!isset($translation))
		global $translation;
	if (str_match($config['reserved_prefix'], $name) || str_match($config['reserved_suffix'], $name))
		return $name; # reserved names are NOT translatable
	elseif ($translation[$type . '_name']['result'][$name][$select])
		return ($translation[$type . '_name']['result'][$name][$select]);
	else
		return ($config['unabstracted_prefix'] . $name . $config['unabstracted_suffix']);
}

# isset
# used? 2012-04-10 vaskoiii
function isset_key($type, $id, $select, & $key = null) {
	if (!isset($key))
		global $key;
	if ($key[$type . '_id']['result'][$id][$select])
		return true;
	return false;
}
function isset_translation($type, $name, $select, & $translation = null) {
	if (!isset($translation))
		global $translation;
	if ($translation[$type . '_name']['result'][$name][$select]);
		return true;
	return false;
}

# add
function add_key($type, $id = false, $select = false, & $key = null) {
	if (!isset($key))
		global $key;
	if ($select)
		$key[$type . '_id']['select'][$select] = $select;
	if ($id)
		$key[$type . '_id']['search'][$id] = $id;
}
function add_translation($type, $name, $select = 'translation_name', & $translation = null) {
	if (!isset($translation))
		global $translation;
	switch($type) {
		case 'category':
		case 'tag':
			# we need to explode the entire path and add translations all the way down the line 2012-03-10 vaskoiii
			$translation['tag_path']['select'][$select] = $select;
			$translation['tag_path']['search'][$name] = $name;
		break;
		default:
			$translation[$type . '_name']['select'][$select] = $select;
			$translation[$type . '_name']['search'][$name] = $name;
		break;
	}
}



# should happen before get_translation
# at this point all we should have to do is cycle through the key values and interpret them...
# todo previously each key was based on the login_id in the specified $data[$container] because we dont always use the same login id for search criteria. ie) for emails we need a separate means of getting the login_user_id of the email recipient. having a shared key could be problematic for this. 2012-02-16 vaskoiii
function do_key(& $key, & $translation, $dialect_id = 0, $login_user_id = 0) {

	global $config;

	# todo make $dialect_id a required parameter 2012-03-09 vaskoiii
	if (!$dialect_id) # already global because session.
		$dialect_id = $_SESSION['dialect']['dialect_id'];

	// GET Translation Category Description
	// GET Translation Tag Description

	$prefix = & $config['mysql']['prefix'];

	# dont allow category 

	# new style using select array
	if ($key)
	foreach ($key as $k1 => $v1) {
	foreach ($v1['select'] as $k2 => $v2) {
	switch ($k2) {
		# kinds
		case 'tag_path':
		switch($k1) {
			case 'tag_id':
				$sql = '
					SELECT
						tag_id as kind_name_id,
						tag_path
					FROM
						' . $prefix . 'index_tag
					WHERE
						tag_id IN (' . implode(',', $key[$k1]['search']) . ')
				';
				$result = mysql_query($sql) or die(mysql_error());
				while ($row = mysql_fetch_assoc($result)) {
					$key[$k1]['result'][$row['kind_name_id']]['tag_path'] = $row['tag_path'];
				}
			break;
		}
		break;
		case 'known':
		if (!empty($v1['search'])) {
		switch($k1) {
			case 'location_id':
			case 'tag_id':
			case 'team_id':
			# todo only find teams from the selection 2012-04-20 vaskoiii
			# ie) id IN(' . implode(', ', $v1['search']) . ') and
			$sql = '
				SELECT
					lul.kind_name_id
				FROM
					' . $prefix . 'minder lul,
					' . $prefix . 'kind ki
				WHERE
					ki.name = ' . to_sql(str_replace('_id', '', $k1)) . ' AND
					ki.id = lul.kind_id AND
					lul.user_id = ' . (int)$login_user_id
			;
			
			$result = mysql_query($sql) or die(mysql_error());
			while ($row = mysql_fetch_assoc($result)) {
				$key[$k1]['result'][$row['kind_name_id']]['link'] = 'yes'; // {known}
			}
			break;
		} }
		break;
		case 'team_owner':
		if (!empty($v1['search'])) {
		switch($k1) {
			case 'team_id':
				// TODO we probably don't need to do this on every page. switch($listing_type)
				$sql = '
					SELECT
						user_id AS team_owner_user_id,
						id AS team_id
					FROM
						' . $prefix . 'team
					WHERE
						id IN (' . implode(',', $key[$k1]['search']) . ')
				';
				$result = mysql_query($sql) or die(mysql_error());
				while ($row = mysql_fetch_assoc($result))
					$key[$k1]['result'][$row[$k1]]['team_owner_user_id'] = $row['team_owner_user_id'];
			break;
		} }
		break;
		case 'user_count': # child of location
		case 'groupmate_count':
		case 'metail_count':
		case 'feedback_count':
		case 'note_count':
		case 'teammate_count':
		case 'meripost_count':
		if (!empty($v1['search'])) {
		switch($k1) {
			case 'location_id':
			case 'group_id':
			case 'user_id':
			case 'contact_id':
			case 'meritopic_id':
			case 'incident_id':
			case 'team_id':
				$e1 = explode('_', $k1);
				$s1 = get_child_listing_type($e1[0]);
				if ($v1['select'][$s1 . '_count']) {
				foreach ($v1['search'] AS $k2 => $v2) {
					$sql = '
						SELECT
							COUNT(id) AS count
						FROM
							' . $prefix . get_table_name($s1) . ' n
						WHERE
							n.' . $k1 . ' = ' . (int)$k2 . ' AND
							n.active = 1
					';
					$result = mysql_query($sql) or die(mysql_error());
					while ($row = mysql_fetch_assoc($result))
						$key[$k1]['result'][$k2][get_child_listing_type($e1[0]) . '_count'] = $row['count'];
				} }
			break;
		} }
		break;
		case 'translation_name':
		case 'translation_description': # added so it is more clear both are retrieved 2014-07-06 vaskoiii
		if (!empty($v1['search'])) {
		switch($k1) {
			case 'element':
			#case 'tag':
			#case 'category':
			default:
				$s1 = str_replace('_id', '', $k1); # get from $key['whatever_id']

				if ($s1 == 'category')
					$s1 = 'tag';

				# implode only safe if numbers only! 2012-03-04 vaskoiii
				# todo cycle through and typecast everything to (int) and check if it matches what it was previous before typecasting 2012-04-24 vaskoiii
				# ie) foreach ($v1['search'] as $k2 => $v2)
				if ($k1 == $s1 . '_id') {
					$sql = '
						select
							tra.kind_name_id as id,
							tra.name,
							tra.description
						from
							' . $prefix . 'translation tra,
							' . $prefix . 'kind ki
						where
							tra.kind_id = ki.id and
							ki.name = ' . to_sql($s1) . ' and
							tra.kind_name_id IN(' . implode(', ', $v1['search']) . ')
					';
					$result = mysql_query($sql) or die(mysql_error());
					while ($row = mysql_fetch_assoc($result)) {
						$key[$s1 . '_id']['result'][$row['id']][$k2] = $row['name'];
						$key[$s1 . '_id']['result'][$row['id']]['translation_description'] = $row['description'];
						add_translation($s1, $row['name']);
					}
				}
			break;
		} }
		break;
		case 'boolean_name':
		case 'direction_name':
		case 'display_name':
		case 'element_name':
		case 'channel_name':
		case 'grade_name':
		case 'decision_name':
		case 'kind_name':
		case 'location_name': # minder
		case 'meritype_name':
		case 'page_name':
		case 'phase_name':
		case 'range_name':
		case 'status_name':
		case 'tag_name': # minder
		case 'team_name': # minder
		case 'theme_name':
			$s1 = str_replace('_name', '', $k2);

			# implode only safe if numbers only! 2012-03-04 vaskoiii
			# todo cycle through and typecast everything to (int) and check if it matches what it was previous before typecasting 2012-04-24 vaskoiii
			if ($k1 == $s1 . '_id') {
				$sql = '
					select
						id,
						name
					from
						' . $prefix . $s1 . '
					where
						id IN(' . implode(', ', $v1['search']) . ')
				'; # implode only safe if numbers only! 2012-03-04 vaskoiii
				$result = mysql_query($sql) or die(mysql_error());
				while ($row = mysql_fetch_assoc($result)) {
					$key[$s1 . '_id']['result'][$row['id']][$k2] = $row['name'];
					add_translation($s1, $row['name']);
				}
			}
		break;
	} } }


			/*
	if (!empty($key))
	foreach($key as $k1 => $v1)
	if (!empty($v1['search']))
	switch($k1) {
		case 'kind_name_id':
			// we have to check here if the kind id is tag. if it is we need to add translations.
			# or not... 2012-02-06 vaskoiii
			# if ($data[$c]['result']['listing']['kind_name'] == 'tag')
			#;
		break;
		case 'tag_id':
		case 'parent_tag_id': # why? this lookup should be easy! 2012-02-06 vaskoiii
			#foreach ($v1['search'] as $k2 => $v2)
				# only place this function is used to call by 'id'
				# also, this is the reason there is the $translation['kind_name_id'] 2012-02-07 vaskoiii
				# echo '<hr>' . $k1 . ' = ' . $k2; 

				#add_translation(str_replace('_id', '', $k1), $k2, 'id');

				# instead of doing this by adding these things again to the $translation array we should just have the translation work on $key['tag_id'] when it fires as a special case. likewise get_translation would need to grab from the key.
			
			echo $sql = '
				SELECT
					tt.kind_name_id,
					tt.description
				FROM
					' . $config['mysql']['prefix'] . 'translation tt,
					' . $config['mysql']['prefix'] . 'kind kk
				WHERE
					kk.id = tt.kind_id AND
					tt.dialect_id = ' . (int)$dialect_id . ' AND
					kk.name = ' . to_sql(preg_replace('/\_id/', '', $k1)) . ' AND
					tt.kind_name_id IN (' . implode(',', $key[$k1]['search']) . ')
			';
			$result = mysql_query($sql) or die(mysql_error());
			while ($row = mysql_fetch_assoc($result))
				$key[$k1]['result'][$row['kind_name_id']]['description'] = $row['description'];
		break;
	}
			*/

	# Always get: contact (user)
	if (!empty($key)) {
	foreach($key as $k1 => $v1) {
	if (!empty($v1['search'])) {
	switch($k1) {
		case 'contact_id':
			$sql = '
				SELECT
					lcu.contact_id,
					lcu.user_id,
					u.name as user_name
				FROM
					' . $prefix . 'user u,
					' . $prefix . 'contact c,
					' . $prefix . 'link_contact_user lcu
				WHERE
					lcu.user_id = u.id AND
					lcu.contact_id = c.id AND
					c.user_id = ' . (int)$login_user_id . ' AND
					lcu.contact_id IN (' . implode(', ', $key[$k1]['search']) . ') AND
					c.active = 1
			';
			$result = mysql_query($sql) or die(mysql_error());
			while ($row = mysql_fetch_assoc($result)) {
				$key[$k1]['result'][$row['contact_id']]['user_id'] = $row['user_id'];
				$key[$k1]['result'][$row['contact_id']]['user_name'] = $row['user_name'];
			}
		break;
		case 'user_id':
			# 2011-08-10 Used to get team owner user name.
			$sql = '
				SELECT
					id as user_id,
					name as user_name
				FROM
					' . $prefix . 'user
				WHERE
					id IN (' . implode(', ', $key[$k1]['search']) . ')
			';
			$result = mysql_query($sql) or die(mysql_error());
			while ($row = mysql_fetch_assoc($result)) {
				$key[$k1]['result'][$row['user_id']]['user_name'] = $row['user_name']; // Needed for team_owner
			}
	
			$sql = '
				SELECT
					lcu.contact_id,
					lcu.user_id,
					c.name AS contact_name,
					u.name AS user_name
				FROM
					' . $prefix . 'user u,
					' . $prefix . 'contact c,
					' . $prefix . 'link_contact_user lcu
				WHERE
					lcu.user_id = u.id AND
					lcu.contact_id = c.id AND
					c.user_id = ' . (int)$login_user_id . ' AND
					lcu.user_id IN (' . implode(', ', $key[$k1]['search']) . ') AND
					c.active = 1
			';
			$result = mysql_query($sql) or die(mysql_error());
			while ($row = mysql_fetch_assoc($result)) {
				$key[$k1]['result'][$row['user_id']]['contact_id'] = $row['contact_id'];
				$key[$k1]['result'][$row['user_id']]['contact_name'] = $row['contact_name'];
			}
		break;
	} } } }

	# does this even happen anymore kind_name_id? should be able to get that actual type id from kind_id and kind_name_id 2012-02-08
	/*
	if (!empty($key['kind_name_id']))
	foreach($key['kind_name_id'] as $k1 => $v1)
	if (!empty($v1))
	switch($k1) {
		default:
		break; # test to see if it is still used
		#case 'parent_tag_id':
		case 'container_id':
		case 'direction_id':
		case 'display_id':
		case 'kind_id':
		case 'grade_id':
		case 'meritype_id':
		case 'page_id':
		case 'phase_id':
		case 'range_id':
		case 'status_id':
		case 'theme_id':
			$s1 = str_replace('_id', '', $k1);
			$sql = '
				SELECT
					id,
					name
				FROM
					' . $prefix . $s1 . '
				WHERE
					id IN (' . implode(', ', $key['kind_name_id'][$k1]['search']) . ')
			';
			$result = mysql_query($sql) or die(mysql_error());
			while ($row = mysql_fetch_assoc($result)) 
				$key['kind_name_id'][$k1]['result'][ $row['id' ]][$s1 . '_name'] = $row['name']; 
		break;
	}
	*/
}

# if we haven't logged in yet the user id will not be possible to get...
function do_translation(& $key, & $translation, $dialect_id = 0, $login_user_id = 0 ) {

	if (!$dialect_id)
		$dialect_id = $_SESSION['dialect']['dialect_id'];
	global $config;

	# new style: use select 2012-03-05 vaskoiii
	# translation should be only:
	# - translation_name
	# - translation_description

	$prefix = & $config['mysql']['prefix'];


	if (!empty($translation))
	foreach ($translation as $k1 => $v1) {
	switch($k1) {
		case 'load_name':
		break;
		case 'kind_name_id': # todo we should not have any id fields here 2012-02-06 vaskoiii
		case 'tag_path': # todo shouldnt happen 2012-03-10 vaskoiii
		break;
		# use lookups from link_tag
		case 'tag_name': 
			# tricky because we have to get every single translation down the whole path!

			# add in missing category paths to $translation['tag_name']['search']

			# get all translations...

			# this function works well
			#print_tag('1<>2<>3<>4');
			#exit;

			$a1 = array();
			foreach ($translation[$k1]['search'] as $k2 => $v2) {
				$a1[$k2] = to_sql($v2); # make mysql safe output 2012-02-24 vaskoiii
			}

			$b1 = isset($v1['select']['translation_name']);
			$b2 = isset($v1['select']['translation_description']);
			$sql = '
				select
					ww.name as tag_name,
					' . ($b1 ? 'tt.name as translation_name' : '')
					. ($b1 && $b2 ? ', ' : '')
					. ($b2 ? 'tt.description as translation_description' : '')
				. ' from
					' . $prefix . 'translation tt,
					' . $prefix . 'tag ww,
					' . $prefix . 'kind kk
				where
					ww.id = tt.kind_name_id AND
					kk.id = tt.kind_id AND
					tt.default = 1 AND

					tt.dialect_id = ' . (int)$dialect_id . ' and

					kk.name = "tag" AND
					ww.name IN (' . implode(', ', $a1) . ')
			';
			$result = mysql_query($sql) or die(mysql_error());
			while ($row = mysql_fetch_assoc($result)) {
				$r1 = & $translation['tag_name']['result'][$row['tag_name']];
				if ($b1)
					$r1['translation_name'] = $row['translation_name'];
				if ($b2)
					$r1['translation_description'] = $row['translation_description'];
			}
			# echo '<pre>'; print_r($translation['tag_name']); echo '</pre>'; exit;
		break;
		default:
			// we don't use translation_description on every page so this could be streamlined.
			// for now translation_description is fine until we notice a performance hit.
			$a1 = array();
			foreach ($translation[$k1]['search'] as $k2 => $v2) {
				$a1[$k2] = to_sql($v2); # make mysql safe output 2012-02-24 vaskoiii
			}

			$sql = '
				SELECT
					tt.name as translation_name,
					tt.description as translation_description,
					ww.name as ' . to_sql($k1) . ' 
				FROM
					' . $prefix . 'translation tt,
					' . $prefix . 'kind kk,
					' . $prefix . str_replace('_name', '', $k1) . ' ww
				WHERE
					tt.kind_name_id = ww.id AND
					kk.id = tt.kind_id AND
					tt.default = 1 AND

					tt.dialect_id = ' . (int)$dialect_id . ' and

					kk.name = ' . to_sql(str_replace('_name', '', $k1)) . ' AND
					ww.name IN (' . implode(', ', $a1) . ')
			';
			$result = mysql_query($sql) or die(mysql_error());

			//if ($config['debug'] == 1)
			//	echo '<hr />' . $sql;
			while ($row = mysql_fetch_assoc($result)) {
				$translation[$k1]['result'][$row[$k1]]['translation_name'] = $row['translation_name'];
				$translation[$k1]['result'][$row[$k1]]['translation_description'] = $row['translation_description'];
			}
		break;
	} }

	# SPECIAL_CASE_TAG_ID
	if (!empty($key['tag_id'])) {
		$sql = '
			SELECT
				tt.name as translation_name,
				tt.description as translation_description,
				ww.id as tag_id 
			FROM
				' . $prefix . 'translation tt,
				' . $prefix . 'kind kk,
				' . $prefix . 'tag ww
			WHERE
				tt.kind_name_id = ww.id AND
				kk.id = tt.kind_id AND
				tt.default = 1 AND

					tt.dialect_id = ' . (int)$dialect_id . ' and

				kk.name = "tag" AND
				ww.id IN ("' . implode('", "', $key['tag_id']['search']) . '")
		';
		$result = mysql_query($sql) or die(mysql_error());
		while ($row = mysql_fetch_assoc($result)) {
			$key['tag_id']['result'][$row['tag_id']]['translation_name'] = $row['translation_name'];
			$key['tag_id']['result'][$row['tag_id']]['translation_description'] = $row['translation_description'];
		}
	}
}

# kind of a listing_engine.php function
# helper for populating $key and $translation after retrieving the result set
function listing_key_translation(& $key, & $translation, & $base, $type, $login_user_id) {
	global $config;

	# switch is significant because it will compensate for missing search criteria from listing_engine() 2012-02-29 vaskoiii
	switch($type) {
		case 'contact':
			if ($base['result']['listing'])
			foreach ($base['result']['listing'] as $k1 => $v1)
			foreach ($v1 as $k2 => $v2) {
				switch($k2) {
					case 'contact_id':
						add_key('contact', $v2, 'user_id', $key);
						add_key('contact', $v2, 'user_name', $key);
					break;
				}
			}
		break;
		case 'minder':


			if ($base['result']['listing'])
			foreach ($base['result']['listing'] as $k1 => $v1)
			foreach ($v1 as $k2 => $v2) {
				switch($k2) {
					case 'user_id':
						add_key('user', $v2, 'contact_id', $key);
						add_key('user', $v2, 'contact_name', $key);
					break;
					case 'kind_name_id':
						$s1 = $base['result']['listing'][$k1]['kind_name'];
						if ($s1)
							add_key($s1, $v2, $s1 . '_name', $key);
					break;
				}
			}
		break;
		default:

			if ($base['result']['listing'])
			foreach ($base['result']['listing'] as $k1 => $v1)
			foreach ($v1 as $k2 => $v2) {
				switch($k2) {
					case 'user_id':
					case 'destination_user_id':
					case 'source_user_id':
					case 'team_owner_user_id':
						if ($v2) {
							add_key('user', $v2, 'contact_id', $key); # offer_list
							add_key('user', $v2, 'contact_name', $key); # offer_list
						}
					break;
				}
			}
			// GET TRANSLATION
			if ($base['result']['listing'])
			foreach($base['result']['listing'] as $k1 => $v1) 
			foreach($v1 as $k2 => $v2) {
				switch($k2) {
					case 'status_name':
					case 'tag_name':
					case 'dialect_name':
					case 'channel_name':
					case 'grade_name':
					case 'phase_name':
					case 'decision_name':
	
					case 'parent_tag_name':
						$s1 = str_replace('_name', '', $k2);
						if ($s1 == 'parent_tag')
							$s1 = 'tag';
						if ($v2)
							add_translation($s1, $v2, 'translation_name', $translation);
					break;
				}
			}

			# not sure where this will come in handy...
			# foreach ($data[$c]['result']['listing'] as $k1 => $v1) {
			# 	if ($v1['kind_name'] && $v1['kind_name_id'] )
			# 		add_key($v1['kind_name'], $v1['kind_name_id'], 'select');
			# }
		break;
	}
}

function key_list_listing(& $listing, $load, & $x = null, & $key = null) {
	if (!$x)
		global $x;
	if (!$key)
		global $key;

	# shortcut
	$name = & $x['load'][$load]['name'];
	$type = & $x['load'][$load]['type'];

	switch($type) {
		case 'teammate':
			add_key('team', 0, 'team_owner', $key);
		break;
		case 'vote':
		case 'transfer': # added vaskoiii 2013-10-10
		case 'item':
			add_key('tag', 0, 'translation_name', $key);
		break;
		case 'minder':
			add_key($listing['kind_name'], 0, 'translation_name', $key);
			add_key($listing['kind_name'], 0, 'tag_path', $key);
		break;
		case 'user':
		case 'contact':
		case 'incident':
		case 'meritopic':
		case 'location':
		case 'group':
		case 'team':
			# todo get 2x child counts for user and contact ie) contact (user) - X (X) 2012-04-18 vaskoiii
			add_key($type, 0, get_child_listing_type($type) . '_count', $key);
		break;
	}

	if (!empty($listing)) {
	foreach ($listing as $k1 => $v1) {
	switch ($k1) {
		case 'kind_name':
			add_key($v1, $listing['kind_name_id'], $v1 . '_name' , $key);
			switch($v1) {
				#case 'category': # shouldnt happen
				case 'tag':
				case 'team':
				case 'location':
					add_key($v1, $listing['kind_name_id'], 'known' , $key);
				break;
			}
		break;
		case 'tag_name':
			add_key('tag', $listing['tag_id'], 'translation_name', $key);
			add_key('tag', $listing['tag_id'], 'translation_description', $key);
		break;

		case 'parent_tag_id':
			add_key('tag', $listing[$k1], 'translation_name', $key);
		break;
		case 'team_id':
		case 'location_id':
		case 'tag_id':
			$s1 = str_replace('_id', '', $k1);
			add_key($s1, $listing[$k1], 'known', $key);
		#nobreak;
		case 'meritopic_id':
		case 'incident_id':
		case 'group_id':
			$s1 = str_replace('_id', '', $k1);
			add_key($s1, $listing[$k1], get_child_listing_type($s1) . '_count', $key);
		break;
	} } }
} 
