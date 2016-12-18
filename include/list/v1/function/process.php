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

# Contents/Description: process specific function (form input submission functions/handling)

# finally a real function for tagging 2012-03-10 vaskoiii
function auto_tag($parent_tag_path, $tag_translation_name, $dialect_id, $user_id = '') {
	global $config;
	if (!$user_id)
		$user_id = $config['autocreation_user_id'];

	# originally $parent_tag_path and $tag_translation_name were a single parameter so logic still follows that 2012-03-11 vaskoiii
	$tag_path = $parent_tag_path . $config['category_exploder'] . $tag_translation_name;
	# from here on this should compensate for the change in parameters (split up because it is less likely to get misused and more accurately labelled)

	$kind_id = 11; # hardcode for kind_name = 'tag';
	$prefix = & $config['mysql']['prefix'];

	$e1 = explode($config['category_exploder'], $tag_path);
	$tag_level = count($e1);
	$tag_name = array_pop($e1);

	# path does NOT start with <> ie)<>level1<>level2 even though it is an absolute path 2012-03-10 vaskoiii
	if ($tag_level > 1) {
		$parent_tag_path = implode($config['category_exploder'], $e1);

		$parent_tag_id = get_db_single_value('
				tag_id
			from
				' . $prefix . 'index_tag
			where
				tag_path = ' . to_sql($parent_tag_path)
		, 0);
	}
	else
		$parent_tag_id = $config['root_tag_id'];

	if ($tag_name) {
	if ($kind_id) {
	if ($parent_tag_id) {
	if ($dialect_id) {
	if ($user_id) {
		$tag_id = get_db_single_value('
				tr.kind_name_id
			FROM
				' . $prefix . 'translation tr,
				' . $prefix . 'tag ta
			WHERE
				tr.kind_name_id = ta.id AND
				tr.kind_id = ' . (int)$kind_id . ' AND
				tr.dialect_id = ' . (int)$dialect_id . ' AND
				ta.parent_id = ' . (int)$parent_tag_id . ' AND
				tr.name = ' . to_sql($tag_name)
		, 0);
		
		# no translation exists so maybe tag doesnt exist
		if (!$tag_id) {
			# todo ensure tag_name is always in english
			# todo make it check the translation table for existence as well 2012-03-06 vaskoiii
			$tag_id = get_db_single_value('
					id
				FROM
					' . $prefix . 'tag
				WHERE
					parent_id = ' . (int)$parent_tag_id . ' AND
					name = ' . to_sql($tag_name)
			, 0);
			
			if (!$tag_id) {
				$sql = '
					INSERT INTO
						' . $prefix . 'tag
					SET
						name = ' . to_sql($tag_name) . ',
						user_id = ' . (int)$user_id . ',
						parent_id = ' . (int)$parent_tag_id . ', 
						modified = CURRENT_TIMESTAMP,
						active = 1
				';
				$result = mysql_query($sql) or die(mysql_error());
				$tag_id = mysql_insert_id($config['mysql_resource']);
			}
	
			# update tag_id with: server_id, remote_id
			$sql = '
				update ts_tag
					' . $prefix . 'tag
				set
					server_id = ' . (int)$config['server_id'] . ',
					remote_id = ' . (int)$tag_id .  '
				where
					id = ' . (int)$tag_id
			;
			$result = mysql_query($sql) or die(mysql_error());
	
			# update index (may not be the most streamlined way but it works well) 2012-03-06 vaskoiii
			ts_recursive_tag($tag_id);
	
			# default = 1 if default not set
			$b1 = 2;
			$sql = '
				SELECT
					COUNT(tt.id) as countage
				FROM
					' . $prefix . 'translation tt
				WHERE
					dialect_id = ' . (int)$dialect_id . ' AND
					kind_id = ' . (int)$kind_id . ' AND
					kind_name_id = ' . (int)$tag_id . ' AND
					`default` = 1
			';
			$result = mysql_query($sql) or die(mysql_error());
			while($row = mysql_fetch_assoc($result))
				if ($row['countage'] == 0)
					$b1 = 1;
			$sql = '
				INSERT INTO
					' . $prefix . 'translation
				SET
					server_id = ' . (int)$config['server_id'] . ',
					remote_id = ' . (int)$tag_id . ',
					user_id = ' . (int)$user_id . ',
					kind_id = ' . (int)$kind_id . ',
					kind_name_id = ' . (int)$tag_id . ',
					dialect_id = ' . (int)$dialect_id . ',
					name = ' . to_sql($tag_name) . ',
					description = "!",
					modified = CURRENT_TIMESTAMP,
					`default` = ' . (int)$b1 . ',
					active = 1
			';
			$result = mysql_query($sql) or die(mysql_error());
		}
		return $tag_id;
	} } } } }
	return false;
}

function process_variables() {
	// I would actually rather get all the process variables here...
	// We should be able to do this throught a query... unfortunately we will have to change all the generic_search_process files to page_name_search_process...
	// or we can submit ANOTHER variable with every form (which sucks) this might be the easiest thing under the current implementation.. but I don't like it because if the id isn't sent... it totaly messes up the error checking... hmmm... maybe just check it it is set...
	// or we can write a crazy query to find out what search elements are included on this page...
	
	// These should all be set in the db for search_content_box
	
	global $_GET, $_POST;
	global $process;
	global $interpret;
	global $config;
	global $x;
	
	// 1 get all process variables
	// requires us to know the page name! and be able to retrieve the link_arrangement_element for whatever page we are on.
	// should be done by first getting the current page id in the db... then from the page id... find out what elements are tied to it...
	// this may be a pain because all the search pages generically link to generic_search_process... so we will have to insert crazy amounts of page ids in the db...
	
	// the best thing about this is that it will prevent the necessity for having a switch statement with a bunch of values... this is kind of a pain...
	$sql = '';
	
	// 2 set all process variables;
	foreach($whatever as $k1 => $v1) {
		switch($k1) {
			default:
				$process[$k1] = get_gp($k1);
			break;
		}
	}
}

# function get_name_from_id($var, $value, $login_user_id) {
# 	return  $name
# }

# function get_id_from_name($var, $value, $login_user_id) {
# 	return id
# }

function process_data_translation($container) {

	# todo too many globals - in this case however, it isnt an issue 2012-02-26 vaskoiii 
	# just make sure NOT to call the process_ functions from anywhere else!!!! only from form process files
	# suggested improvement:
	# process_data_translation(& $input, & $output, $type, $login_user_id);

	global $process;
	global $interpret;
	global $config; # $prefix
	global $x; # $x['page']['name']
	global $_SESSION;

	# shortcuts
	$login_user_id = & $_SESSION['login']['login_user_id'];
	$prefix = & $config['mysql']['prefix'];
	$input = & $process[$container];
	$output = & $interpret['lookup'];

	if (!empty($input))
	foreach($input as $k1 => $v1) {
	#echo '<hr>' . $k1 . ' - ' . $v1 . '<br>';
	if ($v1) {
	switch($k1) {
		# data translation
		case 'channel_offset':
			# input is in days (need to convert)
			$output[$k1] = $input[$k1] * 86400;
		break;
		case 'channel_parent_name':
			$output['channel_parent_id'] = get_db_single_value('
				id from
					' . $prefix . 'channel
				where
					id = parent_id and
					name = ' . to_sql($input[$k1])
			);
		break;
		case 'tag_path':
			# complicated lookup
			# if tag_path is given parent_tag_path should NOT be given.
			$output['tag_id'] = get_db_single_value('
					tag_id
				from
					' . $prefix . 'index_tag
				where
					tag_path = ' . to_sql($input[$k1])
			, 0);

			#$e1 = explode($config['category_exploder'], $input[$k1]);
			#$output['tag_level'] = count($e1);
			#$output['tag_name'] = array_pop($e1);

			## does not start with <>level1<>level2 even though it is an absolute path 2012-03-10 vaskoiii
			#if ($output['tag_level'] > 1) {
			#	$output['parent_tag_path'] = implode($config['category_exploder'], $e1);
	
			#	$output['parent_tag_id'] = get_db_single_value('
			#			tag_id
			#		from
			#			' . $prefix . 'index_tag
			#		where
			#			tag_path = ' . to_sql($output['parent_tag_path'])
			#	, 0);
			#}
			#else
			#	$output['parent_tag_id'] = $config['root_tag_id'];
		#nobreak;
		case 'parent_tag_path':
			$output[str_replace('_path', '_id', $k1)] = get_db_single_value('
					tag_id
				FROM
					' . $prefix . 'index_tag
				WHERE
					tag_path = ' . to_sql($input[$k1]) . '
			', 0);
			#echo '<pre>'; print_r($output); echo '</pre>'; exit;
		break;
		case 'id':
		case 'edit_id':
			// used in edit pages but we don't need a translation here.
		break;
		case 'action':
		case 'list_name':
		case 'row':
			// used in result selection processing but don't need translations here.
		break;
		// BOOLEAN checkbox
		case 'enabled';
		case 'notify_offer_received':
		case 'notify_teammate_received':
		case 'feature_lock':
		case 'feature_minnotify':
			// booleans don't need error checking with get_boolean_gp() used in generic_edit_process
			$output[$k1] = $input[$k1];
		break;

		case 'incident_id':
		case 'meritopic_id':
			$output[$k1] = get_db_single_value('
					id
				FROM
					' . $prefix . str_replace('_id', '', $k1) . '
				WHERE
					id = ' . to_sql($input[$k1])
			);
		break;

		case 'default_boolean_name':
		case 'invite_user_name':
		case 'login_user_name':
		case 'lock_user_name':
		case 'lock_location_name':
		case 'lock_team_name':
		//case 'theme_name': // May have to move this to the translated section in the future...
		case 'element_name':
			$output[str_replace('_name', '_id', $k1)] = get_db_single_value('
					id
				FROM
					' . $prefix . str_replace(
						array('default_', 'invite_', 'login_', 'lock_', '_name', '_parent', '_required'), 
						array('', '', '', '', '', '', ''), 
						$k1
					) . '
				WHERE
					name = ' . to_sql($input[$k1]) . ' 
			');
		break;
		case 'translation_kind_name':
		case 'minder_kind_name':
		case 'kind_name':
			$s1 = str_replace('_name', '', $k1);
			$output[$s1 . '_id'] = get_db_single_value('
					id
				FROM
					' . $prefix . 'kind
				WHERE
					name = ' . to_sql($input[$k1]) . ' 
			', 0);
		break;
		case 'kind_name_name':
			// make sure $input['kind_name'] is validated otherwise it can be sql injection!
			$t1 = 'kind';
			if ($input['translation_kind_name'])
				$t1 = 'translation_kind';
			if ($input['minder_kind_name'])
				$t1 = 'minder_kind';

			// I think we need to make sure that the kind name is specified here too like in the other section...
			if (!$input[$t1 . '_name']) {
			if (isset_gp($t1 . '_id'))  {
				$input[$t1 . '_name'] = get_db_single_value('
						gt.`name`
					FROM
						`' . $prefix . 'kind` gt
					WHERE
						gt.id = ' . to_sql(get_gp($t1 . '_id'))
				, 0);
			} }

			if ($input[$t1 . '_name'] && $output[$t1 . '_id'])
				$output['kind_name_id'] = get_db_single_value('
						id
					FROM
						`' . mysql_real_escape_string($prefix . $input[$t1 . '_name']) . '`
					WHERE
						name LIKE ' . to_sql($input[$k1]) . '
				', 0);

		break;

		case 'tag_name':
		case 'parent_tag_name': // Obtained from category name but still needs to be checked...
			#do nothing?
		break;

		case 'dialect_name':
			$output[str_replace('_name', '_code', $k1)] = get_db_single_value('
					code
				FROM
					' . $prefix . 'dialect
				WHERE
					name = ' . to_sql($input[$k1]) . ' AND
					active = 1
			', 0);
		# nobreak;
		case 'team_name':
		case 'team_required_name':
		case 'location_name':
			$output[str_replace('_name', '_id', $k1)] = get_db_single_value('
					id
				FROM
					' . $prefix . str_replace(array('_name', 'parent_', '_required'), array('', '', ''), $k1) . '
				WHERE
					name = ' . to_sql($input[$k1]) . ' AND
					active = 1
			', 0);
		break;
		# user_name and contact_name are XOR on many pages!
		# eventually we could change the logic to ONLY deal with xor_user_name or xor_contact_name
		case 'user_name':
			if ($input['user_name'])
				$output['user_id'] = get_db_single_value('
						id
					FROM
						' . $prefix . 'user
					WHERE
						name LIKE ' . to_sql($input['user_name'])
				);
			#$interpret['message'] = 'check for xor_user_name';
			#echo $process['form_info']['type'];
			#echo '<pre>'; print_r($x) ; echo '</pre>'; exit;
			# todo bring out into another function - non straightforward data_translation 2012-02-26 vaskoiii
		
			if ($x['page']['name'] == 'edit_process') {
			switch($process['form_info']['type']) {
			case 'offer':
			case 'transfer':
			case 'teammate':
				if ($output['user_id']) {
					$output['xor_user_name'] = $input['user_name'];
					$output['xor_user_id'] = $output['user_id'];
				}
				elseif (!$input['user_name'] && get_gp('contact_name')) {
					$sql = '
						SELECT
							u.name,
							u.id
						FROM
							' . $prefix . 'contact c,
							' . $prefix . 'link_contact_user luc,
							' . $prefix . 'user u
						WHERE
							c.id = luc.contact_id AND
							u.id = luc.user_id AND
							c.user_id = ' . (int)$login_user_id . ' AND
							c.name = ' . to_sql(get_gp('contact_name')) . '
						LIMIT
							1
					';
					$result = mysql_query($sql) or die(mysql_error());
					while ($row = mysql_fetch_assoc($result)) {
						$output['xor_user_name'] = $row['name'];
						$output['xor_user_id'] = $row['id'];
					}
				}
			break;
			} }
			
		break;
		case 'contact_name':
			if ($input['contact_name'])
				$output['contact_id'] = get_db_single_value('
						id
					FROM
						' . $prefix . 'contact
					WHERE
						user_id = ' . (int)$login_user_id . ' AND
						name = ' . to_sql($input['contact_name']) . ' AND
						active = 1'
				);

			# todo bring out into another function - non straightforward data_translation 2012-02-26 vaskoiii
			if ($x['page']['name'] == 'edit_process')
			switch($process['form_info']['type']) {
				case 'note':
				case 'groupmate':
					if ($output['contact_id']) {
						$output['xor_contact_id'] = $output['contact_id'];
						$output['xor_contact_name'] = $input['contact_name'];
					}
					elseif (!$input['contact_name'] && get_gp('user_name')) {
						$sql = '
							SELECT
								c.id
								c.name
							FROM
								' . $prefix . 'contact c,
								' . $prefix . 'link_contact_user luc,
								' . $prefix . 'user u
							WHERE
								c.id = luc.contact_id AND
								u.id = luc.user_id AND
								c.user_id = ' . (int)$login_user_id . ' AND
								u.name = ' . to_sql(get_gp('user_name')) . '
							LIMIT
								1
						';
						$result = mysql_query($sql) or die(mysql_error());
						while ($row = mysql_fetch_assoc($result)) {
							$output['xor_contact_name'] = $row['name'];
							$output['xor_contact_id'] = $row['id'];
						}
					}
				break;
			}
		break;

		case 'lock_contact_name':
		case 'lock_group_name':
		case 'group_name':
		if ($input[$k1])
			$output[str_replace('_name', '_id', $k1)] = get_db_single_value('
					id
				FROM
					' . $prefix . str_replace(array('lock_', '_name'), array('', ''), $k1) . '
				WHERE
					user_id = ' . (int)$login_user_id . ' AND
					name = ' . to_sql($input[$k1]) . ' AND
					active = 1'
			);
		break;
		case 'background_theme_name': // May have to move this to the translated section in the future...
		case 'launcher_theme_name': // May have to move this to the translated section in the future...
		case 'theme_name': // May have to move this to the translated section in the future...
		if ($input[$k1])
			$output[str_replace('_name', '_id', $k1)] = get_db_single_value('
					gt.id
				FROM
					' . $prefix . 'theme gt
				WHERE
					gt.name = ' . to_sql($input[$k1])
			);
		break;
		case 'decision_name':
		case 'direction_name':
		case 'display_name':
		case 'channel_name': # err channel_parent_name?
		case 'grade_name':
		case 'lock_range_name':
		case 'meritype_name':
		case 'page_name':
		case 'phase_name':
		case 'range_name':
		case 'status_name':
		if ($input[$k1])
			$output[str_replace('_name', '_id', $k1)] = get_db_single_value('
					gt.id
				FROM
					' . $prefix . str_replace(array('lock_', '_name'), array('', ''), $k1) . ' gt
				WHERE
					gt.name = ' . to_sql($input[$k1])
			);
		break;
		# are these uid parts even necessary? 2012-03-10 vaskoiii
		case 'jargon_uid':
			$output[$k1] = get_db_single_value('
					id
				FROM
					' . $prefix . 'translation
				WHERE
					id = ' . (int)($v1)
			);
		break;
		case 'groupmate_uid':
			$output[$k1] = get_db_single_value('
					id
				FROM
					' . $prefix . 'link_contact_group
				WHERE
					id = ' . (int)($v1)
			);
		break;
		case 'teammate_uid':
			$output[$k1] = get_db_single_value('
					id
				FROM
					' . $prefix . 'link_team_user
				WHERE
					id = ' . (int)($v1)
			);
		break;
		default:
		if (str_match('_uid', $k1)) { # allow checking for non-existent ids
		switch ($k1) {
			case 'category_uid':
			$output[$k1] = get_db_single_value('
					tag_id
				FROM
					' . $prefix . 'link_tag
				WHERE
					tag_id = ' . (int)($v1)
			, 0);
			break;
			default:
			$output[$k1] = get_db_single_value('
					id
				FROM
					' . $prefix . get_table_name(str_replace('_uid', '', $k1)) . '
				WHERE
					id = ' . (int)($v1)
			, 0);
			break;
		} }
		break;
	} } }
}

function process_does_not_exist($container) {
	global $process;
	global $interpret;
	global $x;
	global $config;

	if($interpret['message'])
		return $interpret['message'];

	$does_not_exist = false;

	# shortcut
	$lookup = & $interpret['lookup'];

	if(!empty($process[$container]))
	foreach($process[$container] as $k1 => $v1) {
	if ($v1) {
		if (str_match('_uid', $k1)) {
			if (isset_gp($v1) && !$lookup[$k1])
				$does_not_exist = $k1;
		}
		else {
		switch($k1) {

			// TODO... kind_id here?
			// TODO... kind_name_id here?
			case 'incident_id':
			case 'meritopic_id':
				if (!$lookup[$k1] && !(str_match(get_gp('listing_type'), $k1)))
					$does_not_exist = $k1;
			break;
			case 'user_password':
			case 'user_password_unencrypted':
				// Do Nothing
			break;

			#case 'tag_name': // this is included on the item_edit screen but SHOULD be assigned an id before this function is called.
			//case 'login_user_name':
			case 'decision_name':
			case 'dialect_name':
			case 'direction_name':
			case 'display_name':
			case 'element_name':
			case 'channel_name':
			case 'grade_name':
			case 'invite_user_name':
			case 'location_name':
			case 'lock_contact_name':
			case 'lock_group_name':
			case 'lock_location_name':
			case 'lock_range_name':
			case 'lock_team_name':
			case 'lock_user_name':
			case 'meritype_name':
			case 'phase_name':
			case 'status_name':
			case 'team_name':
			case 'team_required_name':
			case 'theme_name':
				if (!$lookup[str_replace('_name', '_id', $k1)]) {
				if (!str_match(get_gp('listing_type'), $k1)) {
					$does_not_exist = $k1;
				} }
			break;
			case 'user_name':
			case 'contact_name';
				if ($x['page']['name'] == 'edit_process') {
				switch($process['form_info']['type']) {
					case 'offer':
					case 'transfer':
					case 'teammate':
						if (!$lookup['xor_user_id'])
							$does_not_exist = 'user_name';
					break;
					case 'note':
					case 'groupmate':
						if (!$lookup['xor_contact_id'])
							$does_not_exist = 'contact_name';
					break;
					default:
						# necessary? 2012-03-09 vaskoiii
						$s1 = str_replace('_name', '', $k1);
						if (!$interpret['lookup'][$s1 . '_id'])
						if ($process['form_info']['type'] != $s1)
							$does_not_exist = $k1;
					break;
				} }
			break;
			# default: case intentionally omitted so we dont have to deal with %_description stuff
		} }
	} }
	if ($does_not_exist)
		$interpret['message'] = tt('element', $does_not_exist) . ' : ' . tt('element', 'error_does_not_exist');
}

# todo do not allow an optional field 2012-03-06
function process_field_missing($container = 'edit_content_1') {
	global $process;
	global $interpret;
	global $x;
	global $config;

	if($interpret['message'])
		return $interpret['message'];
	$field_missing = false;

	# shortcut
	$arrangement = & $process[$container];
	$message = & $interpret['message'];

	$b1 = 2;
	if ($x['name'] == 'edit_process') # does: include('user_edit.php');
		$b1 = 1;
	if ($x['name'] == 'user_process') # special process file
		$b1 = 1;
	if ($b1)
	switch($process['form_info']['type']) {
		case 'user':
			if ($arrangement['accept_usage_policy'] != 1)
				$field_missing = 'accept_usage_policy';
		break;
	}
	unset($b1);

	if ($field_missing)
		$message = tt('element', $field_missing) . ' : ' . tt('element', 'error_field_missing');

	if ($arrangement) {
	foreach($arrangement as $k1 => $v1) {
	if (!$message) {
		if (preg_match('/^todo_/', $k1)) {
			;
		}
	else switch($k1) {
		case 'channel_percent': # dont error if 0
		case 'face_file':
		# placeholder: case 'point_id': 
		case 'invite_id': # handled in user_process.php (sometimes needed sometimes not)
		case 'accept_default':
		case 'accept_usage_policy':
		case 'accept_friend':
		case 'redirect':
		case 'id':
		case 'edit_id':
		case 'feed_query':
			# Do Nothing
		break;
		case 'user_password':
		case 'user_password_unencrypted':
		case 'user_password_unencrypted_again':
			# does not get checked if edit_process
			if ($x['name'] != 'edit_process') {
			if ($process['form_info']['type'] != 'profile') {
			if (!$arrangement[$k1]) {
				$message = tt('element', $k1) . ' : ' . tt('element', 'error_field_missing');
			} } }
		break;
		# Will run through 2x
		case 'user_name':
		case 'contact_name':
			if (
				$x['name'] == 'edit_process' || 
				$x['name'] == 'portal_process'
			) {
			switch($process['form_info']['type']) {
				case 'contact':
					#echo '<hr>' . $message . $k1 . ':'; die('dead');
					# must have contact name set. dont unset!
					if (!$arrangement['contact_name'])
						$message = tt('element', 'contact_name') . ' : ' . tt('element', 'error_field_missing');
				break;
				case 'offer':
				case 'transfer':
				case 'teammate':
					if (!$arrangement['user_name'])
					if (!$arrangement['contact_name'])
						$message = tt('element', 'user_name') . ' XOR ' . tt('element', 'contact_name') . $config['spacer'] . tt('element', 'error_field_missing');
				break;
				case 'note':
				case 'groupmate':
					if (!$arrangement['user_name'])
					if (!$arrangement['contact_name'])
						$message = tt('element', 'contact_name') . ' XOR ' . tt('element', 'user_name') . $config['spacer'] . tt('element', 'error_field_missing');	
				break;
				default:
					if (!$arrangement[$k1])
						$message = tt('element', $k1) . ' : ' . tt('element', 'error_field_missing');
				break;
			} }
		break;
		case 'row':
			if (empty($arrangement['row']))
				$message = tt('element', 'error_field_missing');
		break;
		case 'contact_user_mixed':
		case 'lock_contact_user_mixed':
		break;
		default:
			if (!$v1)
				$message = tt('element', $k1) . ' : ' . tt('element', 'error_field_missing');
		break;
	} } } }
}

function process_does_exist() {
	// Also, if certain items exist it resets them to active and puts an error message!
	// get potential inactive items! <<  ???

	global $process;
	global $interpret;
	global $x;
	global $config;

	# shortcut
	$type = & $process['form_info']['type'];
	$message = & $interpret['message'];
	$prefix = & $config['mysql']['prefix'];
	$action_content_1 = & $process['action_content_1'];
	$id = & $process['form_info']['id'];
	$lookup = & $interpret['lookup'];

	if($message)
		return $message;

	# get special values!
	# i_ for "inactive"
	if ($lookup['xor_contact_name'])
		$lookup['i_xor_contact_id'] = get_db_single_value('
				id
			FROM
				' . $prefix . 'contact
			WHERE
				user_id = ' . (int)$_SESSION['login']['login_user_id'] . ' AND
				name = ' . to_sql($lookup['i_xor_contact_name']) . ' AND
				active != 1
		');
	if ($action_content_1['contact_name'])
		$lookup['i_contact_id'] = get_db_single_value('
				id
			FROM
				' . $prefix . 'contact
			WHERE
				user_id = ' . (int)$_SESSION['login']['login_user_id'] . ' AND
				name = ' . to_sql($action_content_1['contact_name']) . ' AND
				active != 1
		');
	if ($lookup['xor_user_name'])
		$lookup['i_xor_user_id'] = get_db_single_value('
				id
			FROM
				' . $prefix . 'user
			WHERE
				name = ' . to_sql($lookup['xor_user_name']) . ' AND
				active != 1
		');
	// there are currently no inactive users so we don't have to worry about a message like that for EVERY PAGE THAT HAS USER_NAME ON IT!!! easy way is just to say that the user does not exist. which should be evident in process_data_translation() already.
	if ($action_content_1['user_name'])
		$lookup['i_user_id'] = get_db_single_value('
				id
			FROM
				' . $prefix . 'user
			WHERE
				name = ' . to_sql($action_content_1['user_name']) . ' AND
				active != 1
		');
	if ($action_content_1['login_user_name'])
		$lookup['i_login_user_id'] = get_db_single_value('
				id
			FROM
				' . $prefix . 'user
			WHERE
				name = ' . to_sql($action_content_1['login_user_name']) . ' AND
				active != 1
		', 0);
	if ($action_content_1['group_name'])
		$lookup['i_group_id'] = get_db_single_value('
				id
			FROM
				`' . $prefix . 'group`
			WHERE
				name = ' . to_sql($action_content_1['group_name']) . ' AND
				active != 1
		');
	if ($action_content_1['team_name'])
		$lookup['i_team_id'] = get_db_single_value('
				id
			FROM
				' . $prefix . 'team
			WHERE
				name = ' . to_sql($action_content_1['team_name']) . ' AND
				active != 1
		');
	# todo are we getting everything? probably lots of time is needed to settle into the new format 2012-03-10 vaskoiii
	if ($action_content_2['team_name'])
		$lookup['i_team_id'] = get_db_single_value('
				id
			FROM
				' . $prefix . 'team
			WHERE
				name = ' . to_sql($action_content_2['team_name']) . ' AND
				active != 1
		');

	// Does process_does_not_exist() take care of inactive users? and just say that that user does_not exist? Does process_data_translation() ??
	$lookup['existence'] = array();
	$does_exist = false;
	$did_exist = false;
	if (!$message) {
	switch($type) {
		case 'contact':
			// NO 2 contacts can have the same user name!!!
			if (get_db_single_value('
					1
				FROM
					' . $prefix . 'link_contact_user luc,
					' . $prefix . 'contact c
				WHERE
					luc.contact_id = c.id AND
					luc.user_id = ' . (int)$lookup['user_id'] . ' AND
					c.user_id = ' . (int)$_SESSION['login']['login_user_id'] . ' AND
					c.id != ' . (int)$id . ' AND
					c.active = 1
			')) {
				$does_exist = true;
				$message = tt('element', 'user_name') . $config['spacer'] . tt('element', 'already_used');
			}
			// does_exist is ambiguous
			// contact_name?
			// user_name?
			// both? (we won't give 2 problems at the same time on an error message.)
			else {
				if ($lookup['contact_id'] && $lookup['contact_id'] != get_gp('id')) {
					$does_exist = true;
				}
				elseif($lookup['i_contact_id']) {
					// does additional work to set the contact to active!
					// contact does NOT require a user id!
					if (1) {
						$did_exist = true;
						$lookup['id'] = $lookup['i_contact_id'];
						$sql = '
							UPDATE
								' . $prefix . 'contact
							SET
								modified = CURRENT_TIMESTAMP, 
								active = 1
							WHERE
								user_id = ' . (int)$_SESSION['login']['login_user_id'] . ' AND
								id = ' . (int)$lookup['i_contact_id'] . '
							LIMIT
								1
						';
						$result = mysql_query($sql) OR die(mysql_error());

						// We need to update indexes too!!!
						$sql = '
							DELETE FROM
								' . $prefix . 'link_contact_user
							WHERE
								contact_id = ' . (int)$lookup['i_contact_id']
						;
						$result = mysql_query($sql) or die(mysql_error());
						if ($lookup['user_id']) {
							$sql = '
								INSERT INTO
									' . $prefix . 'link_contact_user
								SET
									user_id = ' . (int)$lookup['user_id'] . ',
									contact_id = ' . (int)$lookup['i_contact_id']
							;
							$result = mysql_query($sql) or die(mysql_error());
						}
					}
				}
			}
		break;
		case 'translation':
			if (get_db_single_value('
					id
				FROM
					' . $prefix . 'translation
				WHERE
					dialect_id = ' . (int)$lookup['dialect_id'] . ' AND
					kind_id = ' . (int)$lookup['kind_id'] . ' AND
					kind_name_id = ' . (int)$lookup['kind_name_id'] . ' AND
					name = ' . to_sql($action_content_1['translation_name']) . ' AND
					id != ' . (int)$id
			, 0))
				$does_exist = true;
		break;
		case 'group':
			if ($lookup['group_id'])
				$does_exist = true;
			elseif ($lookup['i_group_id']) {
				$did_exist = true;
				$lookup['id'] = $lookup['i_group_id'];
				$sql = '
					UPDATE
						`' . $prefix . 'group`
					SET
						active = 1,
						modified = CURRENT_TIMESTAMP,
						description = ' . to_sql($action_content_1['group_description']) . '
					WHERE
						user_id = ' . (int)$_SESSION['login']['login_user_id'] . ' AND
						id = ' . (int)$lookup['i_group_id'] . '
					LIMIT
						1
				';
				$result = mysql_query($sql) OR die(mysql_error());
			}
		break;
		case 'groupmate':
			$sql = '
				SELECT
					lcg.id,
					lcg.active
				FROM 
					' . $prefix . 'link_contact_group lcg,
					' . $prefix . 'group g
				WHERE
					lcg.group_id = g.id AND
					lcg.contact_id = ' . (int)$lookup['xor_contact_id'] . ' AND
					lcg.group_id = ' . (int)$lookup['group_id'] . ' AND
					g.user_id = ' . (int)$_SESSION['login']['login_user_id'] . ' AND
					lcg.id != ' . (int)$id . '
				LIMIT
					1
					
			';
			$result = mysql_query($sql) OR die(mysql_error());
			while ($row = mysql_fetch_assoc($result)) {
				$lookup['existence']['id'] = $row['id'];
				$lookup['existence']['active'] = $row['active'];
			}
			switch($lookup['existence']['active']) {
				case '1':
					$does_exist = true;
				break;
				case '2':
					if ($lookup['contact_id'] && $lookup['group_id']) {
						$did_exist = true;
						$lookup['id'] = $lookup['existence']['id'];
						$sql = '
							UPDATE
								' . $prefix . 'link_contact_group
							SET
								modified = CURRENT_TIMESTAMP, 
								active = 1
							WHERE
								id = ' . (int)$lookup['existence']['id'] . '
							LIMIT
								1
						';
						$result = mysql_query($sql) OR die(mysql_error());
					}
				break;
			}
		break;
		case 'team':
			if ($lookup['team_id']) {
			if ($type == 'team') {
			if ($x['name'] == 'edit_process') {
			if ($lookup['team_id'] != get_gp('id')) {
				$does_exist = true;
			} } } }
			elseif ($lookup['i_team_id']) {
				$did_exist = true;
				$lookup['id'] = $lookup['team_id'];
				$sql = '
					UPDATE
						' . $prefix . 'team
					SET
						active = 1,
						modified = CURRENT_TIMESTAMP,
						description = ' . to_sql($action_content_1['team_description']) . '
					WHERE
						user_id = ' . (int)$_SESSION['login']['login_user_id'] . ' AND
						id = ' . (int)$lookup['i_team_id'] . '
					LIMIT
						1
				';
				$result = mysql_query($sql) OR die(mysql_error());
			}
		break;
		case 'teammate':
			$sql = '
				SELECT
					ltu.id,
					ltu.active
				FROM
					' . $prefix . 'link_team_user ltu,
					' . $prefix . 'team t
				WHERE
					ltu.team_id = t.id AND
					t.user_id = ' . (int)$_SESSION['login']['login_user_id'] . ' AND
					ltu.user_id = ' . (int)$lookup['xor_user_id'] . ' AND
					ltu.team_id = ' . (int)$lookup['team_id'] . ' AND
					ltu.id != ' . (int)$id . '
				LIMIT
					1
			';
			$result = mysql_query($sql) OR die(mysql_error());
			while ($row = mysql_fetch_assoc($result)) {
				$lookup['existence']['id'] = $row['id'];
				$lookup['existence']['active'] = $row['active'];
			}
			switch($lookup['existence']['active']) {
				case '1':
					$does_exist = true;
				break;
				case '2':
					if ($lookup['user_id'] && $lookup['team_id']) {
						$did_exist = true;
						$lookup['id'] = $lookup['existence']['id'];
						$sql = '
							UPDATE
								' . $prefix . 'link_team_user
							SET
								modified = CURRENT_TIMESTAMP, 
								active = 1
							WHERE
								id = ' . (int)$lookup['existence']['id'] . '
							LIMIT
								1
						';
						$result = mysql_query($sql) OR die(mysql_error());
					}
				break;
			}
		break;
		case 'user':
			if ($lookup['login_user_id'])
				$does_exist = true;
			# there are no inactive users 2013-10-23
			# only way to inactivate users is to manually do it in the database
			# todo once inactive users are supported the logic for TS will have to be rechecked
			elseif ($lookup['i_login_user_id'])
				$did_exist = true;
		break;
		case 'profile':
			//hmmmm
			if ($lookup['login_user_id'])
				if ($lookup['login_user_id'] != $_SESSION['login']['login_user_id'])
					$does_exist = true;
			elseif ($lookup['i_login_user_id'])
				$did_exist = true;
				
		break;
		default:
			if ($lookup[$type . '_id'])
				if ($id != $lookup[$type . '_id']) // evaluates to false if $id is set
					$does_exist = true;
		break;
	} }
	if (!$message) {
	if ($did_exist) {
	if ($x['name'] == 'edit_process' || $x['name'] == 'user_process') {
		switch($type) {
			case 'teammate':
				#todo test this case 2012-04-10 vaskoiii
				# Also in process/edit_process.php
				# Also in function/process.php
				$email_sent = false;
				if (0) # todo uncomment when the email link makes sense 2012-05-01 vaskoiii
				if (!$lookup['same_data_update'] && ($lookup['xor_user_id'] != $_SESSION['login']['login_user_id'])) {
					#$email_array = get_user_email_array($lookup['xor_user_id'], 'notify_teammate_received');

					$tsmail = array();
					$tsmail['data']['list'] = array();
					$tsmail['_SESSION']['login']['login_user_id'] = $lookup['xor_user_id'];
					$tsmail['x']['load']['list']['type'] = $type;
					$tsmail['data']['search']['response']['search_miscellaneous'] = get_user_email_array($lookup['xor_user_id'], 'notify_teammate_received');
					if ($tsmail['data']['search']['response']['search_miscellaneous']['notify_' . $type . '_received']) {
						$tsmail['data']['search']['response']['search_miscellaneous']['email_boundaray'] = get_email_boundary();
						$tsmail['data']['search']['response']['search_miscellaneous']['id'] = $lookup['id'];

						start_engine(
							$tsmail['data']['list'],
							$tsmail['x']['load']['list']['type'],
							$tsmail['_SESSION']['login']['login_user_id'],
							array($tsmail['data']['search']['response']['search_miscellaneous']['id'])
						);
						listing_key_translation(
							$tsmail['key'],
							$tsmail['translation'],
							$tsmail['data']['list'],
							$tsmail['x']['load']['list']['type'],
							$tsmail['_SESSION']['login']['login_user_id']
						);

						# SEND EMAIL!
						$to = $tsmail['data']['search']['response']['search_miscellaneous']['email'];
						$subject = get_tsmail_subject($tsmail);
						$body = get_tsmail_body($tsmail);
						$header = get_tsmail_header($tsmail);
						# parent {} was never enabled?
						if ($config['email_enable'] == 1)
						$email_sent = mail( $to, $subject, $body, $header);
					}
				}

			break;
			case 'profile':
			case 'user':
				if (!$message) {
					# allows custom message for this function() ONLY!
					$message = tt('element', 'login_user_name') . $config['spacer'] . tt('element', 'error_did_exist');
				}
			break;
		}
		# success already? 2012-03-10 vaskoiii
		# special case because teammate already existed.
		process_success(tt('element', 'transaction_complete') . $config['spacer'] . tt('element', 'did_exist') 
			. ($email_sent ? $config['spacer'] . tt('element', 'email_sent') : '') );
	} } }

	if (!$message) {
	if ($does_exist) {
	# dont forget the special user_process page
	if ($x['name'] == 'edit_process' || $x['name'] == 'user_process') {
	switch($type) {
		# case 'channel':
		# 	# channel is kept as a log
		# 	# modifications have a special set of rules and can only happen after 2 future ending cycles
		# 	# the updating process is custom
		# 	# placeholder (separate for now)
		# break;
		case 'group':
			# no reason to not be able to update if it exists
			# todo deal with change the name of a group to a group that already existed but was deleted
			# currently adds another group with the submitted info and you have to delete the one you were editing after
			# 2012-10-16 vaskoiii
		break;
		case 'profile':
		case 'user':
			if (!$message) // allows custom message for this function() ONLY!
				$message = tt('element', 'login_user_name') . $config['spacer'] . tt('element', 'error_does_exist');
		break;
		case 'tag':
		#	if (!$message) 
		#		$message =  tt('element', 'tag_path') . ' : ' . tt('element', 'error_does_exist');
		break;
		default:
			if (!$message) // allows custom message for this function() ONLY!
				$message = tt('element', $type . '_name') . $config['spacer'] . tt('element', 'error_does_exist');
		break;
	} } } }
}

function process_failure($message, $location = false) {
	global $process;
	global $interpret;
	global $x;
	global $q;
	if ($message) {
		unset($_SESSION['process']);
		unset($_SESSION['interpret']);

		$_SESSION['interpret']['message'] = $message;
		$_SESSION['interpret']['failure'] = true;

		$_SESSION['process'] = array();

		if ($process) {
		foreach($process as $k1 => $v1) {
		if (!empty($v1)) {
		foreach($v1 as $k2 => $v2) {
		switch($k2) {
			case 'id':
			case 'edit_id':
			case 'user_password':
			# case 'invite_password':
				# Do Nothing
			break;
			case 'feed_name':
				# TODO find out why we need this case! if error on editing a feed we have problems without this
				$_SESSION['process'][$k1][$k2] = $process[$k1][$k2];
			break;
			default:
				if ($v2)
					$_SESSION['process'][$k1][$k2] = $process[$k1][$k2];
			break;
		} } } } }

		$load = $process['form_info']['load'];

		$_SESSION['interpret']['preload']['focus'] = $load;
		$_SESSION['interpret']['preload']['expand'] = array($load);

		$a1 = array(
			'focus' => $load,
			'preview' => array(''),
			'expand' => array($load),
		);

		$location = ($location ? $location : $x['..']) . ffm(http_build_query($a1), -1);

		header_debug($location);
		header('location: ' . $location);
		exit;
	}
}

function process_success($message,  $location = false, $amod = array()) {
	global $process;
	global $x;
	global $q;
	global $interpret;

	unset($_SESSION['process']);
	unset($_SESSION['interpret']);
	# allow not needing a reply message
	if ($message) {
		$_SESSION['interpret']['success'] = true;
		$_SESSION['interpret']['message'] = $message;
	}

	if ($location) {
		header_debug($location);
		header('location: ' . $location);
		exit;
	}
	else {
		$s1 = $process['form_info']['load'];
		$_SESSION['interpret']['preload']['focus'] = $s1;
		$interpret['preload']['focus'] = 'action';

		$a1 = array();
		$a1[$s1 . '_id'] = '';
		# unset if any of the following were set to reduce wordiness 2012-04-05 vaskoiii
		$a1['focus'] = '';
		$a1['preview'] = array(''); 
		$a1['expand'] = array('');


		if (!empty($amod))
			$a1 = array_merge($amod, $a1);

		# profile_set naming would eliminate the need for this extra part
		switch($process['form_info']['type']) {
			case 'profile':
			case 'invite':
				$location = $x['..'] . ffm(http_build_query($a1), -1);
			break;
			default:
				$location = str_replace('_edit', '_list', $x['..']) . ffm(http_build_query($a1), -1);
			break;
		}
		header_debug($location);
		header('location: ' . $location);
		exit;
	}
}
