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

# Contents/Description: Get data for the drop down fields.
# Known Issues: Extra overhead is incurred at the cost of treating each field seperately. However this is desired such that we can have different options for: team_name, team_required_name, lock_team_name

# todo this is a work in progress - need to set this up so that ordering happens later after translations have been done to the array( 'name' => 'translation_name' ) values
# 2012-02-24 vaskoiii

# todo parent_tag_path is tricky (drop down options would be better to go by id)

# run after all options have been assigned and all translations have been done.
# we could sort before however that would require left outer join and we are trying to eliminate that.
function sort_option(& $option = null, & $key = null, & $translation = null) {
	# todo assign translated value to the option keys for sorting. 2012-02-22 vaskoiii
	if (!$option)
		global $option;
	if (!$key)
		global $key;
	if (!$translation)
		global $translation;
	if (!empty($option))
	foreach ($option as $k1 => $v1) {
		$s1 = str_replace(array('lock_', '_name'), array('', ''), $k1);
		switch ($k1) {
			case 'parent_tag_path':
			case 'parent_tag_name':
				# get the value here and sort
				foreach ($v1 as $k2 => $v2) {

					unset($option[$k1][$k2]);
					# all that should be left after sorting is to make things html safe
					#$option[$k1][$k2] = get_key('tag', $k2, 'translation_name', $fallback = $v2, $key);
					$option[$k1][$v2] = get_key('tag', $k2, 'translation_name', $fallback = $v2, $key);
				}
				asort($option[$k1]);
			break;
			case 'direction_name':
			case 'lock_range_name':
			case 'status_name':
			case 'page_name':
				foreach ($v1 as $k2 => $v2) {
					$option[$k1][$k2] = get_translation($s1, $v2);
				}
				asort($option[$k1]);
			break;
		}
	}
}


# only generate options for the specified fields 2012-02-15 vaskoiii
function add_option($option_name, & $option = null) {
	if (!$option)
		global $option;
	switch($option_name) { 
		case 'parent_tag_path':
		case 'parent_tag_name':
		case 'default_boolean_name':
		case 'dialect_name':
		case 'direction_name':
		case 'display_name':
		case 'grade_name':
		case 'group_name':
		case 'kind_name':
		case 'location_name': # not translating locations
		case 'lock_location_name':
		case 'lock_group_name':
		case 'lock_range_name': 
		case 'lock_team_name':
		case 'meritype_name':
		case 'minder_kind_name':
		case 'page_name':
		case 'phase_name':
		case 'range_name':
		case 'status_name':
		case 'decision_name':
		case 'team_name':
		case 'team_required_name':
		case 'teammate_name':
		case 'theme_name':
		case 'translation_kind_name':
			$option[$option_name] = array();
		break;
	}
}

// OPTIONS
# structure:
# $option = array(
# 	'select_name' => array(
# 		'option_id' => 'option_value',
# 	),
# );
# probably we dont even need option_id but it is a more descriptive array key than just a sequential number. 2012-02-06 vaskoiii
function do_option(& $option, & $key = null, & $translation = null) {
	global $config;
	if (!$key)
		global $key;
	if (!$translation)
		global $translation;

	if (!empty($option))
	foreach($option as $k1 => $v1) {
		unset($sql);
		switch($k1) { 
			case 'parent_tag_name':
			case 'parent_tag_path':
				$sql = '
					SELECT
						i_ta.tag_path as name,
						i_ta.tag_id as id
					FROM
						' . $config['mysql']['prefix'] . 'minder lt,
						' . $config['mysql']['prefix'] . 'kind ki,
						' . $config['mysql']['prefix'] . 'index_tag i_ta
					WHERE
						ki.name = "tag" AND
						ki.id = lt.kind_id AND
						i_ta.tag_id = lt.kind_name_id AND
						lt.user_id = ' . to_sql($_SESSION['login']['login_user_id']) . ' 
				';
				#if ($s2)
				#	$sql .= ' OR gt.id = ' . (int)$s2;
			break;

			# there are actually 2 kinds
			case 'kind_name':
			case 'translation_kind_name':
			case 'minder_kind_name':
				$a1 = array();
				if ($k1 == 'translation_kind_name')
					$a1[] = 'gt.name != "tag"';
				if ($k1 != 'kind_name')
					$a1[] = 'gt.' . str_replace('_kind_name', '', $k1) . ' = 1';

				$sql = '
					SELECT
						gt.name
					FROM
						' . $config['mysql']['prefix'] . 'kind gt
					' . (!empty($a1)
						? ' where ' . implode(' and ', $a1)
						: ''
					)
				;
			break;
			case 'default_boolean_name':
				$sql = '
					SELECT
						gt.name
					FROM
						' . $config['mysql']['prefix'] . 'boolean gt
				';
			break;
			case 'theme_name':
				$s1 = str_replace(array('_required', '_qualified', '_name', 'lock_'), array('', '', '', ''), $k1);
				$sql = '
					SELECT
						gt.name
					FROM
						' . $config['mysql']['prefix'] . $s1 . ' gt
					where
						active = 1
				';
			break;
			case 'decision_name':
			case 'direction_name':
			case 'display_name':
			case 'grade_name':
			case 'lock_range_name':
			case 'meritype_name':
			case 'page_name':
			case 'phase_name':
			case 'range_name':
			case 'status_name':
				$s1 = str_replace(array('_required', '_qualified', '_name', 'lock_'), array('', '', '', ''), $k1);
				$sql = '
					SELECT
						gt.name
					FROM
						' . $config['mysql']['prefix'] . $s1 . ' gt
					' . (
						$k1 == 'page_name'
							? ' WHERE (gt.name LIKE "%\_list" OR gt.name = "top_report") '
							: ''
					) . '
				';
			break;
			case 'dialect_name':
				$sql = '
					SELECT
						name
					FROM
						`' . $config['mysql']['prefix'] . str_replace(array('_required', 'lock_', '_name'), array('', '', ''), $k1) . '`
					WHERE
						active = 1
				';
			break;
			case 'lock_group_name':
			case 'group_name':
				$sql = '
					SELECT
						name
					FROM
						' . $config['mysql']['prefix'] . str_replace(array('_name', 'lock_'), array('', ''), $k1) . '
					WHERE
						active = 1 AND
						user_id = ' . to_sql($_SESSION['login']['login_user_id']) . ' 
					ORDER BY
						name ASC
				';
			break;
			case 'location_name':
			case 'lock_location_name':
			case 'team_name':
			case 'lock_team_name':
			case 'team_required_name'; 
				$s1 = str_replace(array('_required', '_name', 'lock_'), array('', '', '',), $k1);
				$s2 = get_gp(str_replace('_name', '_id', $k1));
				$sql = '
					SELECT
						gt.name
					FROM
						' . $config['mysql']['prefix'] . 'minder lt,
						' . $config['mysql']['prefix'] . 'kind ki,
						' . $config['mysql']['prefix'] . $s1 . ' gt
					WHERE
						ki.id = lt.kind_id AND
						ki.name = ' . to_sql($s1) . ' AND
						gt.id = lt.kind_name_id AND
						lt.user_id = ' . to_sql($_SESSION['login']['login_user_id']) . ' 
				';
				if ($s2)
					$sql .= ' OR gt.id = ' . (int)$s2;
				$sql .= ' order by name asc';
			break;
		}
		if (isset($sql)) {
			$result = mysql_query($sql) or die(mysql_error());
			while ($row = mysql_fetch_assoc($result)) {
				if ($k1 == 'parent_tag_path')
					$option[$k1][$row['id']] = $row['name']; # originally id is used but is later overwritten 2012-04-22 vaskoiii
				elseif ($k1 == 'parent_tag_name')
					$option[$k1][$row['id']] = $row['name']; # originally id is used but is later overwritten 2012-04-22 vaskoiii
				else
					$option[$k1][$row['name']] = $row['name'];
				switch($k1) {

					case 'parent_tag_path':
					case 'parent_tag_name':
						# special case using id 2012-04-28 vaskoiii
						add_key('tag', $row['id'], 'translation_name', $key);
					break;
					case 'translation_kind_name':
					case 'minder_kind_name':
						add_translation('kind', $row['name']);
					break;
					case 'decision_name':
					case 'direction_name':
					case 'display_name':
					case 'grade_name':
					case 'kind_name':
					case 'lock_location_name':
					case 'lock_range_name':
					case 'meritype_name':
					case 'page_name':
					case 'phase_name':
					case 'range_name':
					case 'status_name':
					case 'theme_name':
						add_translation(str_replace(array('lock_', '_name'), array('', ''), $k1), $row['name']);
					break;
				}
			}
		}
	}
}
