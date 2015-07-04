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

# Contents/Description: The Behind the scenes engine stuff that makes listings go - based on parameters specified in the URL
# Note: References used frequently to help reduce wordiness

function prepare_engine(& $base, $type, $login_user_id) {
	global $x;
	global $data;
	global $config;

	# reduce wordiness
	$where = & $base['search']['where'];

	$s1 = get_main_table_alias($type);
	switch($type) {
		case 'user':
			$where[] = $s1 . '.id != ' . (int)$login_user_id;
		break;
		case 'offer':
		case 'transfer':
		case 'invited':
			$where[] = $s1 . '.source_user_id != ' . (int)$login_user_id;
		break;
		case 'feedback':
		case 'item':
		case 'login':
		case 'meripost':
		case 'metail':
		case 'minder':
		case 'vote':
		# =)
		case 'category':
		case 'tag':
			$where[] = $s1 . '.user_id != ' . (int)$config['autocreation_user_id'];
			$where[] = $s1 . '.user_id != ' . (int)$login_user_id;
		break;
		case 'teammate':
			$where[] = $s1 . '.user_id != ' . (int)$login_user_id;
			$where[] = get_secondary_table_alias($type) . '.name NOT LIKE ' . to_sql('<%');
		break;
		case 'jargon':
		case 'translation':
			$where[] = $s1 . '.user_id != ' . (int)$config['autocreation_user_id'];
			$where[] = $s1 . '.user_id != ' . (int)$login_user_id;
			$where[] = $s1 . '.name NOT LIKE ' . to_sql('<%');

		break;
		default:
			$where[] = $s1 . '.user_id != ' . (int)$login_user_id;
			$where[] = $s1 . '.name NOT LIKE ' . to_sql('<%');
		break;
	}
}

function start_engine( & $base, $type, $login_user_id, $uid_array = array(), $load = false) {
	# todo load seems like an extra parameter (previously called base by name ie) 'view' which gave us the extra load variable but required globals 2012-04-22 vaskoiii
	
	# reduce wordiness
	$where_x = & $base['search']['where_x'];

	if (!is_array($where_x))
		$where_x = array();

	search_lock($base, $type, $login_user_id);


	listing_engine($base, $type, $login_user_id);
	$t1 = get_main_table_alias($type);
	if (isset_gp($type . '_uid')) {
		$where_x[] = $t1 . '.id = ' . (int)get_gp($type . '_uid');
	}

	# ignore all other search criteria if $uid_array is set
	if(!empty($uid_array)) {
		$where_x = array(
			$t1 . '.id IN (' . implode(', ', $uid_array) . ')'
		);
	}

	# hmmm
	if (empty($uid_array))
		get_engine_result_total($base);

	if ($load == 'view')
		$sql = get_engine_result_listing_sql($base, 0, 'view');
	else
		$sql = get_engine_result_listing_sql($base);


	$base['result']['listing'] = array();
	$result = mysql_query($sql) or die(mysql_error());
	while ($row = mysql_fetch_assoc($result))
		$base['result']['listing'][] = $row;
		#todo use a more specific id and stop using the hardcode for $listing[0] 2012-04-10 vaskoiii
		#$base['result']['listing'][$type . '_' . $row[$type . '_id']] = $row;
}

function get_engine_result_listing_sql(& $base, $limit = false, $load = false) {
	global $config; # $config[$x['site']['t']]['result_amount_per_page'] && $config['debug']
	global $x; # $x['site']['t'] && $x['page']['name'] 

	# reduce wordiness
	$select = & $base['search']['select'];
	$from = & $base['search']['from'];
	$where = & $base['search']['where'];
	$where_x = & $base['search']['where_x'];
	$group_by = & $base['search']['group_by'];
	$order_by = & $base['search']['order_by'];


	if ($limit)
		$limit = (int)$limit;
	else
		$limit = $config[$x['site']['t']]['result_amount_per_page'];


	$i1 = get_gp('page');
	if (!$i1)
		$i1 = 1;

	$sql = '
		SELECT
			' . implode(', ', $select) . '
		FROM 
			' . implode(', ', $from) .  '
		WHERE
			' . (empty($where_x)
				? implode(' AND ', $where)
				: implode(' AND ', array_merge($where, $where_x))
		) . 
		($group_by 
			? ' GROUP BY ' . implode(', ', $group_by)
			: ''
		) . 
		($order_by
			? ' ORDER BY ' . implode(', ', $order_by)
			: ' ORDER BY modified DESC ' 
		) . '
		LIMIT '
			. ( $load == 'view'
				? '1'
				: (($i1 - 1) * $limit)  . ',' . $limit
			) . '
	';
	# todo fix page name comparison! 2012-02-16 vaskoiii
	if ($config['debug'] == 1 && $x['page']['name'] != 'feed_atom' && !str_match('_process', $x['page']['name']))
		echo '<hr />' . $sql . '<hr />';
	return $sql;
}

function get_engine_result_total(& $base) {
	global $config; # $config['debug']

	# reduce wordiness
	$from = & $base['search']['from'];
	$where = & $base['search']['where'];
	$where_x = & $base['search']['where_x'];
	$group_by = & $base['search']['group_by'];

	$debugger = false;
	if ($config['debug'] == 1)
		$debugger = true;

	$base['result']['total'] = get_db_single_value(
			($group_by 
				? 'COUNT(DISTINCT(' . implode(', ', $group_by) . '))'
				: 'COUNT(*) '
			) . '
		FROM
			' . implode(', ', $from) . '
		WHERE
			' . (empty($where_x)
				? implode(' AND ', $where)
				: implode(' AND ', array_merge($where, $where_x))
			)
	, $debugger);
}

function listing_engine(& $base, $type, $login_user_id, $dialect_id = 0) {

	# todo always pass in dialect_id (only optional for transitional period) 2012-03-09 vaskoiii
	if (!$dialect_id) # already global...
		$dialect_id = $_SESSION['dialect']['dialect_id'];

	# & $base = containing array name ie) $data['view']

	# todo: pass $_SESSION['dialect']['dialect_id'] in this as a parameter somehow: maybe & $session 2012-03-05 vaskoiii

	global $config;
	global $_GET;
	global $_POST;

	# common aliases used - reduces wordiness on logic - 2012-02-16 vaskoiii
	$select = & $base['search']['select'];
	$from = & $base['search']['from'];
	$where = & $base['search']['where'];
	$where_x = & $base['search']['where_x'];
	$group_by = & $base['search']['group_by'];

	$prefix = $config['mysql']['prefix'];

	# TEAM REQUIRED switch
	switch($type) {
		case 'item':
		case 'metail':
		case 'news':
		case 'transfer':
		case 'vote':
			$select[] = 't1.team_id AS team_required_id';
			if (isset_gp('team_required_id'))
				$where_x[] = 't1.team_id = ' . (int)get_gp('team_required_id');

			$select[] = 't2.name as team_required_name';
			$from[] = $prefix . 'team t2';
			$where[] = 't2.id = t1.team_id';
				$from[] = $prefix . 'link_team_user ltu2';
				$where[] = 'ltu2.active = 1';
				$where[] = 'ltu2.team_id = t1.team_id';
				$where[] = 'ltu2.user_id = ' . (int)$login_user_id;
		break;
	}
	# MAIN switch
	switch($type) {
		case 'category':
			$from[] = $prefix . 'link_tag l_t';
			$where[] = 't1.id = l_t.tag_id';
		case 'tag':
			$select[] = 't1.id as tag_id';
			$select[] = 't1.modified';
			$select[] = 't1.active';
			$select[] = 't1.parent_id';

			$select[] = 'i_t.tag_path';
			$select[] = 't1.name as tag_name';




			# have to get the parent_tag_path parameter
			$select[] = 't1.parent_id as parent_tag_id';
			# todo: fix needing the parent_tag_path because of the way it is retrieved with the $option 2012-12-29 vaskoiii
			$select[] = 'i_t2.tag_path AS parent_tag_name';
			$select[] = 'i_t2.tag_path AS parent_tag_path';
			$from[] = $prefix . 'index_tag i_t2';
			$where[] = 't1.parent_id = i_t2.tag_id';




			$from[] = $prefix . 'index_tag i_t';
			$where[] = 't1.id = i_t.tag_id';
			$where[] = 't1.user_id = u.id';

			$group_by[] = 't1.id';

			$from[] = $prefix . 'tag t1';
			if (isset_gp('keyword')) {
				$from[] = $prefix . 'translation tt1';
				$where[] = 't1.id = tt1.kind_name_id';
				$where[] = 'tt1.kind_id = 11'; # 11 hardcode for [tag]
				$where[] = 'tt1.dialect_id = ' . (int)$dialect_id;
				$where_x[] = '(
					tt1.name LIKE ' . to_sql('%' . from_url(get_gp('keyword')) . '%') . ' OR
					tt1.description LIKE ' . to_sql('%' . from_url(get_gp('keyword')) . '%') . 
				')';
			}
			if (isset_gp('parent_tag_id')) 
				$where_x[] = 't1.parent_id = ' . to_sql(get_gp('parent_id'));
		break;
		case 'contact':
			$select[] = 'c.user_id';
			$select[] = 'c.modified';
			if (isset_gp('keyword'))
				$where_x[] = '(
					c.name LIKE ' . to_sql('%' . get_gp('keyword') . '%') . '
				)';
			// not contact_uid thought they achieve the same result
			if (isset_gp('contact_id'))
				$where_x[] = 'c.id = ' . (int)get_gp('contact_id');
		break;
		case 'dialect':
			$select[] = 't1.id as dialect_id';
			$select[] = 't1.description as dialect_description';
			$select[] = 't1.modified';
			$select[] = 't1.active';
			$select[] = 't1.name as dialect_name';
			$from[] = $prefix . 'dialect t1';
			$where[] = 't1.active = 1';
			$where[] = 't1.user_id = u.id';
			if (isset_gp('dialect_id'))
				$where_x[] = 't1.id = ' . to_sql(get_gp('dialect_id'));
			if (isset_gp('keyword'))
				$where_x[] = '(
					t1.name LIKE ' . to_sql('%' . get_gp('keyword') . '%') . ' OR
					t1.description LIKE ' . to_sql('%' . get_gp('keyword') . '%') . '
				)';
		break;
		case 'feed':
			$select[] = 't1.key as feed_key';
			$select[] = 't1.id as feed_id';
			$select[] = 'dd.name as dialect_name';
			$select[] = 't1.enabled';
			$select[] = 'pp.name as page_name';
			$select[] = 't1.name as feed_name';
			$select[] = 't1.query';
			$select[] = 't1.user_id';
			$select[] = 't1.modified';
			$from[] = $prefix . 'dialect dd';
			$from[] = $prefix . 'feed t1';
			$from[] = $prefix . 'page pp';
			$where[] = 't1.page_id = pp.id';
			$where[] = 't1.user_id = u.id';
			$where[] = 'dd.id = t1.dialect_id';
			#$where[] = 't1.user_id = ' . (int)$login_user_id; # uncomment to see only your feeds 2012-04-04 vaskoiii
			$where[] = 't1.active = 1';
			if (isset_gp('keyword'))
				$where_x[] = '(
					t1.name LIKE ' . to_sql('%' . get_gp('keyword') . '%') . '
				)';
		break;
		case 'feedback':
			$select[] = 't1.id AS feedback_id';
			$select[] = 't1.incident_id';
			$select[] = 'inc.name AS incident_name';
			$select[] = 't1.description AS feedback_description';
			$select[] = 't1.modified';
			$from[] = $prefix . 'feedback t1';
			$from[] = $prefix . 'incident inc';
			$where[] = 't1.incident_id = inc.id';
			$where[] = 't1.user_id = u.id';
			$where[] = 't1.active = 1';
			if (isset_gp('keyword'))
				$where_x[] = '(
					inc.name LIKE ' . to_sql('%' . get_gp('keyword') . '%') . ' OR
					t1.description LIKE ' . to_sql('%' . get_gp('keyword') . '%') . '
				)';
			if (isset_gp('incident_id'))
				$where_x[] = 't1.incident_id = ' . to_sql(get_gp('incident_id'));
		break;
		case 'group':
			$select[] = 'g.id AS group_id';
			$select[] = 'g.name as group_name';
			$select[] = 'g.description AS group_description';
			$select[] = 'g.modified';
			$select[] = 'g.active';
			if (isset_gp('keyword')) {
				$where_x[] = '(
						g.name LIKE ' . to_sql('%' . get_gp('keyword') . '%') . ' OR 
						g.description LIKE ' . to_sql('%' . get_gp('keyword') . '%') . '
					)
				';
			}
			if (isset_gp('group_id'))
				$where_x[] = 'g.id = ' . to_sql(get_gp('group_id'));
			$group_by[] = 'g.id';
		break;
		case 'groupmate':
			$select[] = 'lcg.id as groupmate_id';
			$select[] = 'g.id as group_id';
			$select[] = 'g.user_id as user_id';
			$select[] = 'g.name as group_name';
			$select[] = 'lcg.modified';
			$where[] = 'lcg.active = 1';
			if (isset_gp('keyword')) {
				$where_x[] = '(
					c.name LIKE ' . to_sql('%' . get_gp('keyword') . '%') . ' OR 
					g.name LIKE ' . to_sql('%' . get_gp('keyword') . '%') . '
				)';
			}
			if (isset_gp('group_id'))
				$where_x[] = 'g.id = ' . to_sql(get_gp('group_id'));
		break;
		case 'incident':
			$select[] = 't1.id AS incident_id';
			$select[] = 't1.name AS incident_name';
			$select[] = 'in_s.name AS phase_name';
			$select[] = 't1.description AS incident_description';
			$select[] = 't1.modified';
			$from[] = $prefix . 'phase in_s';
			$from[] = $prefix . 'incident t1';
			$where[] = 't1.phase_id = in_s.id';
			$where[] = 't1.active = 1';
			$where[] = 't1.user_id = u.id';
			if (isset_gp('keyword'))
				$where_x[] = '(
					t1.id LIKE ' . to_sql('%' . get_gp('keyword') . '%') . ' OR
					t1.name LIKE ' . to_sql('%' . get_gp('keyword') . '%') . ' OR
					t1.description LIKE ' . to_sql('%' . get_gp('keyword') . '%') . '
				)';
			if (isset_gp('incident_id'))
				$where_x[] = 't1.id = ' . (int)get_gp('incident_id');
			if (isset_gp('phase_id'))
				$where_x[] = 't1.phase_id = ' . (int)get_gp('phase_id');
		break;
		case 'invited':
			$select[] = 't1.id AS invited_id';
			$select[] = 't1.invite_id';
 			$select[] = 't1.source_user_id';
 			$select[] = 't1.destination_user_id';
			$select[] = 't1.modified';
			$from[] = $prefix . 'invited t1';
			$where[] = 't1.active = 1';
		break;
		case 'item':
		case 'transfer':
		case 'vote':
			if (empty($group_by)) # conditional added because sometimes we get 2x group by clauses for some reason. 2012-04-12 vaskoiii
				$group_by[] = 't1.id'; // Allows us to search ALL translations not just the default one and still not show several result sets!
			# get translated tag name from key
			# get translated dialect from key
			$select[] = 't1.id AS item_id';
			$select[] = 't1.description AS ' . $type . '_description';
			$select[] = 't1.modified';

			# we can get everything from this guy
			$select[] = 'a.parent_id AS parent_tag_id';
			$select[] = 'a.id AS tag_id';
			$select[] = 'a.name AS tag_name';

			$select[] = 'i_t.tag_path AS parent_tag_path';
			$from[] = $prefix . 'index_tag i_t';

			$where[] = 'a.parent_id = i_t.tag_id';

			$where[] = 't1.tag_id = a.id';
			$where[] = 't1.active = 1';

			$from[] = $prefix . 'tag a';
			# keyword
			if (isset_gp('keyword')) {
				$from[] = $prefix . 'translation tt1';
				$where[] = 'a.id = tt1.kind_name_id';
				$where[] = 'tt1.kind_id = 11'; # 11 = hardcode for [tag]
				$where[] = 'tt1.dialect_id = ' . (int)$dialect_id;
				$where_x[] = '(
					tt1.name LIKE ' . to_sql('%' . from_url(get_gp('keyword')) . '%') . ' OR
					t1.description LIKE ' . to_sql('%' . from_url(get_gp('keyword')) . '%') . 
				')';
			}
			if (isset_gp($type . '_id'))
				$where_x[] = 't1.id = ' . (int)get_gp($type . '_id');
			if (isset_gp('parent_tag_id'))
				$where_x[] = 'a.parent_id = ' . (int)get_gp('parent_tag_id');
			switch($type) {
				case 'transfer':
					$select[] = 't1.id as transfer_id';
					$from[] = $prefix . 'transfer t1';
				break;
				case 'vote':
					# get translated decision name from key
					$select[] = 't1.id as vote_id';
					$select[] = 'i_s.id AS decision_name';
					$select[] = 'i_s.name AS decision_name';
					$from[] = $prefix . 'vote t1';
					$from[] = $prefix . 'decision i_s';
					$where[] = 't1.user_id = u.id';
					$where[] = 't1.decision_id = i_s.id';
					if (isset_gp('decision_id'))
						$where_x[] = 't1.decision_id = ' . (int)get_gp('decision_id');
				break;
				case 'item':
					# get translated status name from key
					$select[] = 't1.id as item_id';
					$select[] = 'i_s.id AS status_id';
					$select[] = 'i_s.name AS status_name';
					$from[] = $prefix . 'item t1';
					$from[] = $prefix . 'status i_s';
					$where[] = 't1.user_id = u.id';
					$where[] = 't1.status_id = i_s.id';
					if (isset_gp('status_id'))
						$where_x[] = 't1.status_id = ' . (int)get_gp('status_id');
				break;
			}
		break;
		case 'channel':
			$select[] = 't1.id as channel_id';
			$select[] = 't1.user_id as user_id';
			$select[] = 't1.name as channel_name';
			$select[] = 't1.description as channel_description';
			$select[] = 't1.value AS channel_value';
			$select[] = 't1.offset AS channel_offset';
			$select[] = 'tfe.id AS timeframe_id';
			$select[] = 'tfe.name AS timeframe_name';
			# channel description is intentionally not translated because it will cause divergence
			$select[] = 't1.modified';
			$from[] = $prefix . 'channel t1';
			$from[] = $prefix . 'timeframe tfe';
			$where[] = 'u.id = t1.user_id';
			$where[] = 'tfe.id = t1.timeframe_id';
			$where[] = '1'; # implode(' , ', $where) stops complaining
			if (isset_gp('keyword')) {
				$where_x[] = '(
					t1.name LIKE ' . to_sql('%' . from_url(get_gp('keyword')) . '%') . 
				')';
			}
			if (isset_gp('channel_parent_id'))
				$where_x[] = 't1.parent_id = ' . to_sql(get_gp('channel_parent_id'));
		break;
		case 'location':
			$select[] = 'lo.id as location_id';
			$select[] = 'lo.longitude AS location_longitude';
			$select[] = 'lo.latitude AS location_latitude';
			$select[] = 'lo.name as location_name';
			$select[] = 'lo.modified';
			if (isset_gp('keyword')) {
				$where_x[] = '(
					lo.name LIKE ' . to_sql('%' . from_url(get_gp('keyword')) . '%') . 
				')';
			}
			if (isset_gp('location_id'))
				$where_x[] = 'lo.id = ' . to_sql(get_gp('location_id'));
		break;
		case 'login':
			$select[] = 't1.id as login_id';
			$select[] = 't1.`when` as modified';
			$from[] = $prefix . 'login t1';
			$where[] = 'u.id = t1.user_id';
			if (isset_gp('keyword'))
				$where_x[] = 'u.name LIKE ' . to_sql('%' . get_gp('keyword') . '%');
		break;
		case 'meripost':
			// get meritype name translation from key
			$select[] = 't1.id AS meripost_id';
			$select[] = 't1.meritype_id';
			$select[] = 'in_s.name as meritype_name';
			$select[] = 't1.meritopic_id';
			$select[] = 'mt.name AS meritopic_name';
			$select[] = 't1.description AS meripost_description';
			$select[] = 't1.modified';
			$from[] = $prefix . 'meritopic mt';
			$from[] = $prefix . 'meripost t1';
			$from[] = $prefix . 'meritype in_s';
			$where[] = 'mt.id = t1.meritopic_id';
			$where[] = 't1.meritype_id = in_s.id';
			$where[] = 't1.active = 1';
			$where[] = 't1.user_id = u.id';
			if (isset_gp('keyword'))
				$where_x[] = '(
					t1.description LIKE ' . to_sql('%' . get_gp('keyword') . '%') . '
				)';
			if (isset_gp('meritopic_id'))
				$where_x[] = 't1.meritopic_id = ' . (int)get_gp('meritopic_id');
		break;
		case 'meritopic':
			$select[] = 't1.id AS meritopic_id';
			$select[] = 't1.name AS meritopic_name';
			$select[] = 't1.description AS meritopic_description';
			$select[] = 't1.modified';
			$from[] = $prefix . 'meritopic t1';
			$where[] = 't1.user_id = u.id';
			$where[] = 't1.active = 1';
			if (isset_gp('keyword'))
				$where_x[] = '(
					t1.description LIKE ' . to_sql('%' . get_gp('keyword') . '%') . '
				)';
			if (isset_gp('meritopic_id'))
				$where_x[] = 't1.id = ' . (int)get_gp('meritopic_id');
		break;
		case 'news':
			$select[] = 't1.id AS news_id';
			$select[] = 't1.name AS news_name';
			$select[] = 't1.description AS news_description';
			$select[] = 't1.modified';
			$from[] = $prefix . 'news t1';
			$where[] = 't1.user_id = u.id';
			$where[] = 't1.active = 1';
			if (isset_gp('keyword'))
				$where_x[] = '(
					t1.name LIKE ' . to_sql('%' . get_gp('keyword') . '%') . ' OR
					t1.description LIKE ' . to_sql('%' . get_gp('keyword') . '%') . '
				)';
		break;
		case 'metail':
			$select[] = 't1.id AS metail_id';
			$select[] = 't1.description AS metail_description';
			$select[] = 't1.modified';
			$from[] = $prefix . 'metail t1';
			$where[] = 't1.user_id = u.id';
			$where[] = 't1.active = 1';
			if (isset_gp('keyword'))
				$where_x[] = '(
					t1.description LIKE ' . to_sql('%' . get_gp('keyword') . '%') . '
				)';
		break;
		case 'note':
			$select[] = 't1.id AS note_id';
			$select[] = 'c.id as contact_id';
			$select[] = 'c.user_id AS user_id';
			$select[] = 't1.description as note_description';
			$select[] = 't1.modified';
			$select[] = 'c.name as contact_name';
			$from[] = $prefix . 'note t1';
			$where[] = 'c.id = t1.contact_id';
			$where[] = 't1.active = 1';
			// NOT searching for user or contact name here because the way the database is built. Use lock_contact_user_mixed to search here instead.
			if (isset_gp('keyword')) {
				$where_x[] = '(
					t1.description LIKE ' . to_sql('%' . get_gp('keyword') . '%') . '
				)';
			}
		break;
		case 'offer':
			$select[] = 't1.id AS offer_id';
			$select[] = 't1.name AS offer_name';
			$select[] = 't1.description as offer_description';
			$select[] = 't1.modified';
			$from[] = $prefix . 'offer t1';
			$from[] = $prefix . 'active_offer_user a1';
			$where[] = 'a1.offer_id = t1.id';
			$where[] = 'a1.user_id = ' . (int)$login_user_id;
			// TODO: Fix OR statement!!! We can fix using the index tables.
			$where[] = '(
				t1.source_user_id = ' . (int)$login_user_id . ' OR
				t1.destination_user_id = ' . (int)$login_user_id . '
			)';
			if (isset_gp('keyword'))
				$where_x[] = '(
					t1.description LIKE ' . to_sql('%' . get_gp('keyword') . '%') . '
				)';
		break;





/*

 SELECT u.id as source_user_id, u.name as source_user_name, u2.id as destination_user_id, u2.name as destination_user_name, t1.team_id AS team_required_id, t2.name as team_required_name, t1.id AS item_id, t1.description AS transfer_description, i_s.id AS status_id, i_s.name AS status_name, t1.modified, a.parent_id AS parent_tag_id, a.id AS tag_id, a.name AS tag_name, i_t.tag_path AS parent_tag_path, t1.id as transfer_id 

FROM ts_user u, ts_user u2, ts_team t2, ts_link_team_user ltu2, ts_index_tag i_t, ts_status i_s, ts_tag a, ts_transfer t1 

WHERE u.active = 1 AND u2.active = 1 AND u.id = t1.source_user_id AND u2.id = t1.destination_user_id AND t2.id = t1.team_id AND ltu2.active = 1 AND ltu2.team_id = t1.team_id AND ltu2.user_id = 132 AND a.parent_id = i_t.tag_id AND t1.tag_id = a.id AND t1.status_id = i_s.id AND t1.active = 1 AND ( t1.source_user_id = 132 OR t1.destination_user_id = 132 ) GROUP BY t1.id ORDER BY modified DESC LIMIT 0,10 


*/


		case 'team':
			$select[] = 't.id AS team_id';
			$select[] = 't.name AS team_name';
			$select[] = 't.description as team_description';
			$select[] = 't.modified';
			if (isset_gp('keyword')) {
				$where_x[] = '(
					t.name LIKE ' . to_sql('%' . get_gp('keyword') . '%') . ' OR 
					t.description LIKE ' . to_sql('%' . get_gp('keyword') . '%') . '
				)';
			}
			if (isset_gp('team_id'))
				$where_x[] = 't.id = ' . to_sql(get_gp('team_id'));
			$group_by[] = 't.id';
		break;
		case 'teammate':
			$select[] = 'ltu.id as teammate_id';
			$select[] = 'ltu.modified';
			$select[] = 't.id as team_id';
			$select[] = 't.name as team_name';
			$select[] = 't.user_id as team_owner_user_id';
			if (isset_gp('keyword')) {
				$where_x[] = '(
					t.name LIKE ' . to_sql('%' . get_gp('keyword') . '%') . '
				)';
			}
			if (isset_gp('team_id'))
				$where_x[] = 't.id = ' . to_sql(get_gp('team_id'));
		break;
		# placeholders
		case 'channelifer': # todo channelifer is to channel as category is to tag
		break;
		case 'membership': # todo membership is to renew as category is to tag (combine into renewal?)
			$select[] = 't1.id AS membership_id';
			$select[] = 'cnl.id as channel_id';
			$select[] = 'cnl.name as channel_name';
			# membership duration taken from config
			$select[] = 't1.modified';
			$from[] = $prefix . 'membership t1';
				$where[] = 't1.active = 1';
			$from[] = $prefix . 'channel cnl';
			$where[] = 't1.user_id = u.id';
			$where[] = 't1.channel_id = cnl.id';
			if (isset_gp('keyword'))
				$where_x[] = '(
					u.name LIKE ' . to_sql('%' . get_gp('keyword') . '%') . '
				)';
		break;
		case 'cycle': # log
			$select[] = 't1.id AS cycle_id';
			$select[] = 't1.modified';
			$select[] = 'cnl.id as channel_id';
			$select[] = 'cnl.name as channel_name';
			$select[] = 'tfe.id AS timeframe_id';
			$select[] = 'tfe.name AS timeframe_name';
			$select[] = 't1.start AS cycle_start';
			$from[] = $prefix . 'cycle t1';
			$from[] = $prefix . 'timeframe tfe';
				$where[] = 't1.active = 1';
			# join to renewal and there may be nobody
			# $from[] = $prefix . 'renewal rnwl';
			# $where[] = 'rnwl.user_id = u.id';
			# $where[] = 'rnwl.cycle_id = t1.id';
			# join to channel and you will only get owners
			$from[] = $prefix . 'channel cnl';
			$where[] = 'cnl.user_id = u.id';
			$where[] = 'cnl.id = t1.channel_id';
			$where[] = 'tfe.id = t1.timeframe_id';
			# both
			if (isset_gp('keyword'))
				$where_x[] = '(
					u.name LIKE ' . to_sql('%' . get_gp('keyword') . '%') . '
				)';
			if (isset_gp('channel_parent_id'))
				$where_x[] = 'cnl.parent_id = ' . (int)get_gp('channel_parent_id');
				
		break;
		case 'renewage': # log
			$select[] = 'rnae.id as renewage_id';
			$select[] = 't1.id AS renewal_id';
			$select[] = 'cnl.id as channel_id';
			$select[] = 't1.cycle_id';
			$select[] = 'rnae.point_id';
			$select[] = 'pt.name as point_name';
			$select[] = 'cnl.name as channel_name';
			$select[] = 't1.start as renewal_start';
			$select[] = 'tfe.id AS timeframe_id';
			$select[] = 'tfe.name AS timeframe_name';

			# membership duration taken from config
			$select[] = 'rnae.modified';
			$from[] = $prefix . 'renewal t1';
			$from[] = $prefix . 'renewage rnae';
				$where[] = 't1.active = 1';
			$from[] = $prefix . 'gauge_renewal ge_rnal';
			$from[] = $prefix . 'channel cnl';
			$from[] = $prefix . 'cycle cce';
			$from[] = $prefix . 'timeframe tfe';
			$from[] = $prefix . 'point pt';
			$where[] = 'ge_rnal.renewal_id = t1.id';
			$where[] = 'tfe.id = rnae.timeframe_id';
			$where[] = 'pt.id = rnae.point_id';
			$where[] = 't1.id = rnae.renewal_id';
			$where[] = 't1.user_id = u.id';
			$where[] = 't1.cycle_id = cce.id';
			$where[] = 'cce.channel_id = cnl.id';
			
			if (isset_gp('keyword'))
				$where_x[] = '(
					u.name LIKE ' . to_sql('%' . get_gp('keyword') . '%') . '
				)';
		break;
		case 'renewal': # log
			# todo deprecate renewal_list as renewal is incomplete without renewage
			$select[] = 't1.id AS renewal_id';
			$select[] = 'cnl.id as channel_id';
			$select[] = 't1.cycle_id';
			$select[] = 'rnae.point_id';
			$select[] = 'cnl.name as channel_name';
			$select[] = 't1.start as renewal_start';
			$select[] = 'tfe.id AS timeframe_id';
			$select[] = 'tfe.name AS timeframe_name';

			# membership duration taken from config
			$select[] = 'rnae.modified';
			$from[] = $prefix . 'renewal t1';
			$from[] = $prefix . 'renewage rnae';
				$where[] = 't1.active = 1';
			$from[] = $prefix . 'gauge_renewal ge_rnal';
			$from[] = $prefix . 'channel cnl';
			$from[] = $prefix . 'cycle cce';
			$from[] = $prefix . 'timeframe tfe';
			$where[] = 'ge_rnal.renewal_id = t1.id';
			$where[] = 'tfe.id = rnae.timeframe_id';
			$where[] = 't1.id = rnae.renewal_id';
			$where[] = 't1.user_id = u.id';
			$where[] = 't1.cycle_id = cce.id';
			$where[] = 'cce.channel_id = cnl.id';
			# different
			$where[] = 'rnae.timeframe_id = 2';
			
			if (isset_gp('keyword'))
				$where_x[] = '(
					u.name LIKE ' . to_sql('%' . get_gp('keyword') . '%') . '
				)';
		break;
		case 'cost': # log
			$select[] = 't1.id AS cost_id';
			$select[] = 'cnl.id as channel_id';
			$select[] = 'cnl.name as channel_name';
			$select[] = 't1.value as cost_value';

			# membership duration taken from config
			$select[] = 't1.modified';
			$from[] = $prefix . 'cost t1';
				$where[] = 't1.active = 1';
			$from[] = $prefix . 'channel cnl';
			$where[] = 't1.user_id = u.id';
			$where[] = 't1.channel_id = cnl.id';
			if (isset_gp('keyword'))
				$where_x[] = '(
					u.name LIKE ' . to_sql('%' . get_gp('keyword') . '%') . '
				)';
		break;
		case 'transaction': # log
			$select[] = 't1.id AS transaction_id';
			$select[] = 't1.class_id';
			$select[] = 't1.class_name_id';
			$select[] = 't1.value as transaction_value';

			# membership duration taken from config
			$select[] = 't1.modified';
			$from[] = $prefix . 'transaction t1';
				$where[] = 't1.active = 1';
			# should probably work like translation to get the class data by iid
			$where[] = 't1.user_id = u.id';
			if (isset_gp('keyword'))
				$where_x[] = '(
					u.name LIKE ' . to_sql('%' . get_gp('keyword') . '%') . '
				)';
		break;
		case 'minder': 
			$select[] = 't1.id as minder_id';
			$select[] = 't1.kind_id';
			$select[] = 'ki.name as kind_name';
			$select[] = 't1.kind_name_id';
			$select[] = 't1.modified';
			$from[] = $prefix . 'minder t1';
			$from[] = $prefix . 'kind ki';
			$where[] = 't1.kind_id = ki.id';
			$where[] = 't1.user_id = u.id';
			if (isset_gp('minder_kind_id'))
				$where_x[] = 't1.kind_id = ' . (int)get_gp('minder_kind_id');
		break;
		# todo inject this into one of the other lists? ie) score?
		case 'carry':
			$select[] = 't1.id as carry_id';
			$select[] = 't1.cycle_id';
			$select[] = 't1.source_user_id';
			$select[] = 't1.destination_user_id';
			$select[] = 't1.score_value';
			$select[] = 't1.value as carry_value';
			$select[] = 't1.modified';
			$select[] = 't1.active';
			$from[] = $prefix . 'carry t1';
			$where[] = '(
				t1.source_user_id = ' . (int)$login_user_id . ' OR
				t1.destination_user_id = ' . (int)$login_user_id . '
			)';
			$where[] = 't1.active = 1';
			if (get_gp('keyword')) {
				$where_x[] = '(
					u.name LIKE ' . to_sql('%' . get_gp('keyword') . '%') . '
				)';
			}
		break;
		case 'comment':
			$select[] = 't1.id AS comment_id';
			$select[] = 't1.kind_id AS kind_id';
			$select[] = 'k.name as kind_name';
			$select[] = 't1.kind_name_id AS kind_name_id';
			$select[] = 't1.description as comment_description';
			$select[] = 't1.modified';
			$select[] = 't1.active';
			$from[] = $prefix . 'comment t1';
			$from[] = $prefix . 'kind k';
			$where[] = 't1.user_id = u.id';
			$where[] = 't1.active = 1';
			$where[] = 't1.kind_id = k.id';
			if (isset_gp('kind_name_id'))
				$where_x[] = 't1.kind_name_id = ' . (int)get_gp('kind_name_id');
			if (get_gp('keyword')) {
				$where_x[] = '(
					t1.name LIKE ' . to_sql('%' . get_gp('keyword') . '%') . '
				)';
			}
		break;
		case 'score':
			$select[] = 't1.kind_id AS kind_id';
			$select[] = 't1.id AS score_id';
			$select[] = 't1.mark_id';
			$select[] = 't1.kind_name_id AS kind_name_id';
			$select[] = 't1.modified';
			$select[] = 't1.active';
			$select[] = 'k.name as kind_name';
			$from[] = $prefix . 'score t1';
			$from[] = $prefix . 'kind k';
			$where[] = '(
				t1.source_user_id = ' . (int)$login_user_id . ' OR
				t1.destination_user_id = ' . (int)$login_user_id . '
			)';
			$where[] = 't1.active = 1';
			$where[] = 't1.kind_id = k.id';
			if (isset_gp('lock_user_id')) 
				$where[] = '(
					t1.source_user_id = ' . (int)get_gp('lock_user_id') . ' OR
					t1.destination_user_id = ' . (int)$get_gp('lock_user_id') . '
				)';
			if (isset_gp('score_kind_id'))
				$where_x[] = 'k.id = ' . (int)get_gp('score_kind_id');
			if (isset_gp('kind_name_id'))
				$where_x[] = 't1.kind_name_id = ' . (int)get_gp('kind_name_id');
			if (get_gp('keyword')) {
				# todo better keyword searching
				$where_x[] = '(
					u.name LIKE ' . to_sql('%' . get_gp('keyword') . '%') . ' or
					u2.name LIKE ' . to_sql('%' . get_gp('keyword') . '%') . '
				)';
			}
		break;
		case 'jargon':
			// we do NOT keyword by:
			// cat.name
			// tag.name
			// instead these are intended to be queried as specific elements (not by keyword).
			$where[] = 'k.name = "tag"';
			$select[] = 'i_t.tag_path';


			#$from[] = $prefix . 'index_tag i_t

			#$from[] = $prefix . 'tag tag';
			$from[] = $prefix . 'index_tag i_t';
			$where[] = 'i_t.tag_id = t1.kind_name_id';
			$select[] = 't1.id AS jargon_id'; # same as translation_id below but needed for jargon edit 2012-06-04 vaskoiii


			#$where[] = 't1.kind_name_id = tag.id'; // this is not known for generic translations below because we don't know what table to join to.
		case 'translation':
			//$select[] = ' AS kind_name_name';
			// difficult!
			if ($type == 'translation') {
				$where[] = 'k.name != "tag"';
			}
			$select[] = 't1.default AS default_boolean_id';
			$select[] = 'bb.name AS default_boolean_name';
			$select[] = 't1.kind_id AS kind_id';
			$select[] = 't1.id AS translation_id';
			$select[] = 't1.kind_name_id AS kind_name_id';
			$select[] = 't1.dialect_id';
			$select[] = 't1.name AS translation_name';
			$select[] = 't1.description AS translation_description';
			$select[] = 't1.modified';
			$select[] = 't1.active';
			$select[] = 'd.name as dialect_name';
			$select[] = 'k.name as kind_name';
			#$select[] = 'k.name as kind_name';
			$from[] = $prefix . 'boolean bb';
			$from[] = $prefix . 'translation t1';
			$from[] = $prefix . 'dialect d';
			$from[] = $prefix . 'kind k';
			$where[] = 't1.user_id = u.id';
			$where[] = 't1.active = 1';
			$where[] = 't1.dialect_id = d.id';
			$where[] = 't1.kind_id = k.id';
			$where[] = 'bb.id = t1.default';
			if (isset_gp('translation_kind_id'))
				$where_x[] = 'k.id = ' . (int)get_gp('translation_kind_id');
			if (isset_gp('kind_name_id'))
				$where_x[] = 't1.kind_name_id = ' . (int)get_gp('kind_name_id');
			if (isset_gp('dialect_id'))
				$where_x[] = 't1.dialect_id = ' . (int)get_gp('dialect_id');
			if (get_gp('keyword')) {
				$where_x[] = '(
					t1.name LIKE ' . to_sql('%' . get_gp('keyword') . '%') . '
				)';
			}
			# tricky part is we have to add on the translation_kind_name_name its crazy! because we don't really know the translation_kind_name beforehand...
		break;
		case 'user':
			$select[] = 'u.modified';
			$select[] = 'lo.id AS location_id';
			$select[] = 'lo.latitude AS location_latitude';
			$select[] = 'lo.longitude AS location_longitude';
			$select[] = 'lo.name as location_name';
			if (isset_gp('keyword'))
				$where_x[] = '(
					u.name LIKE ' . to_sql('%' . get_gp('keyword') . '%') . '
				)';
			if (isset_gp('location_id'))
				$where_x[] = 'lo.id = ' . to_sql(get_gp('location_id'));
		break;
	}
	# global $data; echo '<pre>'; print_r($data['result']); echo '</pre>';
}
