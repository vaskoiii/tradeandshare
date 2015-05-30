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

add_translation('element', 'error_field_missing');
add_translation('element', 'transaction_complete');
add_translation('element', 'email_sent');
do_translation($key, $translation, $_SESSION['dialect']['dialect_id'], $login_user_id);

$process = array(); # Sent to process
$interpret = array(); # Interpreted from $process
$interpret['lookup'] = array();

# Contents/Description: Do specified action on selection
# 2013-10 only using a 1-listing selection with the following 3 action cases below
# TODO: consider commenting out unused logic 2013-10-10 vaskoiii

$action = get_gp('action');
$type = get_gp('list_name');
switch(get_gp('action')) {
	case 'delete': # delete teammate: new logic for vote_list 2013-10-11 vaskoiii
		switch($type) {
			case 'channel':
			case 'team':
				# $interpret['message'] = 'not allowed';
				process_failure($type . ' ' . $action . ' case not yet implemented - aborted');
				exit;
			break;
		}
	break;
	case 'like':
	case 'dislike':
	case 'comment':
	case 'remember':
	case 'forget':
	break;
	default:
		# $interpret['message'] = 'not allowed';
		die($type . ' ' . $action . ' action case not tested');
	break;
}

# shortcuts
$login_user_id = $_SESSION['login']['login_user_id'];
$lookup = & $interpret['lookup'];
$prefix = & $config['mysql']['prefix'];

$email_sent = false; // NOT $interpret['email']['sent'] = false; ??

$process['miscellaneous']['action'] = get_gp('action');
$process['miscellaneous']['list_name'] = get_gp('list_name');
$process['miscellaneous']['row'] = array_unique(get_gp('row')); // removes duplicates (only happens with bad data)

function get_author_only_team_id($user_name) {
	global $config;
	$prefix = & $config['mysql']['prefix'];
	$author_only_team_id_from_link_team_user = false;
	$user_id = get_db_single_value('
		 	id
		FROM
			' . $prefix . 'user
		WHERE
			name = ' . to_sql($user_name)
	);
	if (!$user_id)
		die('error_user_NOT_found!'); # should not happen.
	$author_only_team_id_from_team = get_db_single_value('
			id
		FROM
			' . $prefix . 'team
		where
			name = ' . to_sql('<' . $user_name . '>')
	);
	if (!$author_only_team_id_from_team) {
		$sql = '
			INSERT INTO
				' . $prefix . 'team
			SET
				user_id = ' . (int)$user_id . ',
				modified = CURRENT_TIMESTAMP,
				name = ' . to_sql('<' . $user_name . '>') . ',
				description = ' . to_sql('<' . $user_name . '>') . ',
				active = 1
		';
		$result = mysql_query($sql) or die(mysql_error());
		$author_only_team_id_from_team = mysql_insert_id();
	}
	$author_only_team_id_from_link_team_user = get_db_single_value('
			team_id
		FROM
			' . $prefix . 'link_team_user
		WHERE
			team_id = ' . (int)$author_only_team_id_from_team
	);
	if (!$author_only_team_id_from_link_team_user) {
		$sql = '
			INSERT INTO
				' . $prefix . 'link_team_user
			SET
				user_id = ' . (int)$user_id . ',
				team_id = ' . (int)$author_only_team_id_from_team . ',
				modified = CURRENT_TIMESTAMP,
				active = 1
		';
		$result = mysql_query($sql) or die(mysql_error());
		$author_only_team_id_from_link_team_user = mysql_insert_id();
	}
	if (!$author_only_team_id_from_link_team_user)
		die('author_only_team_id_from_link_team_user not found!'); // shouldn't happen
	return $author_only_team_id_from_link_team_user;
}

switch($process['miscellaneous']['action']) {
	case 'export':
		contact_user_mixed_split();
	break;
	case 'judge':
	case 'import':
		$process['team_required_name'] = get_gp('team_required_name');
	break;
	case 'comment':
		$process['comment_description'] = get_gp('comment_description');
	break;
}

# TABLE TRANSLATION
# not in php_database.php
# todo Use the interpret value in SQL queries when possible
$s1 = $process['miscellaneous']['list_name'];
switch($process['miscellaneous']['action']) {
	case 'disable': # unused 2012-03-27 vaskoiii
		switch($s1) {
			case 'feed':
				$interpret['table'] = $s1;
			break;
		}
	break;
	case 'delete':
		switch($s1) {
			case 'category':
				$interpret['table'] = 'link_tag';
			break;
			case 'teammate':
				$interpret['table'] = 'link_team_user';
			break;
			case 'groupmate':
				$interpret['table'] = 'link_contact_group';
			break;
			case 'offer':
				$interpret['table'] = 'active_' . $s1 . '_user';
			break;
			case 'contact':
			case 'feed':
			case 'feedback':
			case 'group':
			case 'incident':
			case 'invite':
			case 'item':
			case 'location':
			case 'metail':
			case 'news':
			case 'note':
			case 'rating':
			case 'transfer':
			case 'vote':
				$interpret['table'] = $s1;
			break;
			default:
				# Useless logic? vaskoiii 2013-10-10
				$process['miscellaneous']['list_name'] = '<NOT FOUND!>'; 
			break;
		}
	break;
	case 'forget':
		# selecting a table name here is not necessary and table name for minder is variable
	break;
}

# ROW TRANSLATION
# This part should NOT lead to ANY error messages unless there is bad data!
$interpret['row'] = array(); // contains only ids of rows that can have the corresponding action performed!!!
$s1 = $process['miscellaneous']['list_name'];
switch($process['miscellaneous']['action']) {
	case 'memorize':
		switch($s1) {
		case 'metail':
		# you have access and a contact with the corresponding username
		$sql = '
			SELECT
				me.id
			FROM
				' . $prefix . $s1 . ' me,
				' . $prefix . 'link_team_user ltu,
				' . $prefix . 'link_contact_user lcu,
				' . $prefix . 'contact co
			WHERE
				me.id IN (' . implode(',', $process['miscellaneous']['row']) .') AND
				ltu.team_id = me.team_id AND 
				me.user_id = lcu.user_id AND
				co.id =  lcu.contact_id AND
				ltu.user_id = ' . (int)$login_user_id . ' AND
				co.user_id = ' . (int)$login_user_id
		;
		break;
		}
		$result = mysql_query($sql) or die(mysql_error());
		while ($row = mysql_fetch_assoc($result))
			$interpret['row'][] = $row['id'];
	break;
	case 'disable':
		switch($s1) {
			case 'feed':
			$sql = '
				SELECT
					id
				FROM
					`' . $prefix . $s1 . '`
				WHERE
					id IN (' . implode(',', $process['miscellaneous']['row']) .') AND
					user_id = ' . (int)$login_user_id
			;
			break;
		}
		$result = mysql_query($sql) or die(mysql_error());
		while ($row = mysql_fetch_assoc($result))
			$interpret['row'][] = $row['id'];
	break;
	break; #double break? 2012-03-27 vaskoiii
	case 'delete':
		switch($s1) {
			# Validates from orignial table not the active_table_user table
			case 'offer':
			case 'transfer':
			$sql = '
				SELECT
					id
				FROM
					' . $prefix . $s1 . '
				WHERE
					id IN (' . implode(',', $process['miscellaneous']['row']) .') AND
					(
						source_user_id = ' . (int)$login_user_id . ' OR
						destination_user_id = ' . (int)$login_user_id . '
					)
			';
			break;
			case 'rating':
			$sql = '
				SELECT
					id
				FROM
					' . $prefix . $s1 . '
				WHERE
					id IN (' . implode(',', $process['miscellaneous']['row']) .') AND
					source_user_id = ' . (int)$login_user_id
			;
			break;
			case 'teammate':
			$sql = '
				SELECT
					lt1.id
				FROM
					' . $prefix . $interpret['table'] . ' lt1,
					`' . $prefix . str_replace('mate', '', $s1) . '` tt
				WHERE
					lt1.' . preg_replace('/mate/', '', $s1) . '_id = tt.id AND
					lt1.id IN (' . implode(',', $process['miscellaneous']['row']) .') AND
					tt.id != ' . (int)$config['everyone_team_id'] . ' AND
					tt.user_id = ' . (int)$login_user_id
			;
			break;
			case 'groupmate':
			$sql = '
				SELECT
					lug.id
				FROM
					' . $prefix . 'link_contact_group lug,
					`' . $prefix . 'group` g
				WHERE
					lug.group_id = g.id AND
					lug.id IN (' . implode(',', $process['miscellaneous']['row']) .') AND
					g.user_id = ' . (int)$login_user_id
			;
			break;
			case 'note':
			$sql = '
				SELECT
					cc.id
				FROM
					' . $prefix . $s1 . ' cc,
					' . $prefix . 'contact c
				WHERE
					c.id = cc.contact_id AND
					cc.id IN (' . implode(',', $process['miscellaneous']['row']) .') AND
					c.user_id = ' . (int)$login_user_id
			;
			break;
			# case 'incident':
			# case 'feedback':
			# case 'location':
			case 'category':
			# anyone can delete
			$sql = '
				SELECT
					tag_id as id
				FROM
					`' . $prefix . $interpret['table'] . '`
				WHERE
					tag_id IN (' . implode(',', $process['miscellaneous']['row']) . ')'
			;
			break;
			case 'contact':
			case 'feed':
			case 'group':
			case 'invite':
			case 'item':
			case 'metail':
			case 'news':
			case 'vote':
			$sql = '
				SELECT
					id
				FROM
					`' . $prefix . $interpret['table'] . '`
				WHERE
					id IN (' . implode(',', $process['miscellaneous']['row']) .') AND
					user_id = ' . (int)$login_user_id
			;
			break;
		}
		$result = mysql_query($sql) or die(mysql_error());
		while ($row = mysql_fetch_assoc($result))
			$interpret['row'][] = $row['id'];
	break;
	case 'export':
	case 'import':
	case 'judge':
		switch($s1) {
			case 'vote':
			case 'item':
				# private stuff can still be imported by hacking?
				# TODO: allow selection of visable items only...
				$sql = '
					SELECT
						id
					FROM 
						' . $prefix . $s1 . '
					WHERE
						id IN (' . implode(',', $process['miscellaneous']['row']) .')
				';
			break;
			case 'transfer':
				$sql = '
					SELECT
						id
					FROM 
						' . $prefix . 'transfer
					WHERE
						id IN (' . implode(',', $process['miscellaneous']['row']) .') AND
						(
							source_user_id = ' . (int)$login_user_id . ' OR
							destination_user_id = ' . (int)$login_user_id . '
						)
				';
				$result = mysql_query($sql) or die(mysql_error());
				while ($row = mysql_fetch_assoc($result)) 
					$interpret['row'][] = $row['id']; 
			break;
			default:
			break;
		}
		$result = mysql_query($sql) or die(mysql_error());
		while ($row = mysql_fetch_assoc($result)) 
			$interpret['row'][] = $row['id'];
	break;
	case 'remember':
	case 'forget':
		switch($s1) {
			case 'category':
				die('category should be referred to as tag');
			break;
			case 'tag':
			case 'team':
			case 'location':
				$lookup['kind_id'] = get_db_single_value('
						mi.kind_id
					FROM
						' . $prefix . 'minder mi,
						' . $prefix . 'kind kk
					WHERE
						mi.kind_id = kk.id AND
						kk.name = ' . to_sql($s1) . '
				');
				if ($lookup['kind_id']) {
					# rows should actually be selecting the team | tag | location name (not the minder anything)
					$sql = ' 
						SELECT
							id
						FROM
							`' . mysql_real_escape_string($prefix . $s1) . '`
						WHERE
							id IN (' . implode(', ', $process['miscellaneous']['row']) . ')
					';
					$result = mysql_query($sql) or die(mysql_error());
					while ($row = mysql_fetch_assoc($result)) 
						$interpret['row'][] = $row['id'];
				}
			break;
		}
	break;
	case 'like':
	case 'dislike':
	case 'comment':
		# todo fix cheating
		$interpret['row'][] = $process['miscellaneous']['row'][0];
	break;
}

# REGULAR TRANSLATION
process_data_translation('miscellaneous');

# ERROR CHECKING
if (count($process['miscellaneous']['row']) != count($interpret['row']))
	$interpret['message'] = tt('element', 'error_invalid_selection');

# We have to check and make sure that you are on the team
# If importing make sure the user is the owner of the imported item.
switch($process['miscellaneous']['action']) {
	case 'remember':
	case 'forget':
		if (!$lookup['kind_id'])
			$interpret['message'] = tt('element', 'error') . ' : ' . tt('element', 'kind_name');
	break;
	case 'import':
		if ($lookup['team_required_id']) {
			if (!get_db_single_value('
					user_id
				FROM
					' . $prefix . 'link_team_user
				WHERE
					team_id = ' . (int)$lookup['team_required_id'] . ' AND
					user_id = ' . (int)$login_user_id . ' AND
					active = 1
			'))
				$interpret['message'] = tt('element', 'error') . ' : ' . tt('element', 'error_not_on_team');
		}
	break;
}

process_field_missing();
process_does_not_exist('miscellaneous');

# It will be easier to just error if something has children =)
if (!$interpret['message']) {
switch($process['miscellaneous']['list_name']) {
	case 'contact':
		if (get_db_single_value('
				id
			FROM
				' . $prefix . 'note
			WHERE
				contact_id IN (' . implode($process['miscellaneous']['row']) . ') AND
				active = 1
		'))
			$interpret['message'] = tt('element', 'error_has_children');
	break;
	case 'group':
		if (get_db_single_value('
				id
			FROM
				' . $prefix . 'link_contact_group
			WHERE
				group_id IN (' . implode($process['miscellaneous']['row']) . ') AND
				active = 1
		'))
			$interpret['message'] = tt('element', 'error_has_children');
	break;
	case 'teammate':
		# REGEXP "[^a-z0-9A-Z]"
		# restrict this now!
		if (get_db_single_value('
				te.id,
				te.name
			FROM
				' . $prefix . 'team te,
				' . $prefix . 'link_team_user tm
			WHERE
				te.id = tm.team_id AND
				tm.id IN (' . implode(', ', $process['miscellaneous']['row']) . ') AND
				te.name LIKE "%<%" AND
				te.name NOT LIKE "%<*%"
		'))
			$interpret['message'] = tt('element', 'error') . ' : ' . tt('element', 'error_uneditable') . ' : ' . tt('element', 'team_name');
	break;
} }

# don't allow a selection of more than 1 for teammates
if (!$interpret['message']) {
switch($process['miscellaneous']['action']) {
	case 'delete':
		switch($process['miscellaneous']['list_name']) {
			case 'teammate':
				if (count($process['miscellaneous']['row']) > 1)
					$interpret['message'] = tt('element', 'error') . ' : ' . tt('element', $process['miscellaneous']['list_name'] . '_list') . ' : ' . tt('element', 'selection_limit') . ' : 1';
					# TODO add translations for selection_limit
					# imposed because this is a very intensive change for the indexes...
			break;
		}
	break;
} }

# FAILURE
# process_failure() Can't really use this because it populates:
# $_SESSION['process']['container']['search_content_box']['element'] = array();
# $interpret['message'] = 'Testing!!!';

if ($interpret['message']) {
	# different from other process_failure because of the selection... which I guess why we don't use it right now...
	$_SESSION['process']['selection'] = array();
	foreach($process['miscellaneous']['row'] as $k1 => $v1)
		$_SESSION['process']['selection'][$v1] = $v1;
	$_SESSION['process']['message'] = $interpret['message'];
	$s1 = $x['..'] . get_q_query($x['level'] - 1, get_q_query_modified($x['level'] - 1, $q['parsed'][$x['level'] - 1]));
	header_debug($s1);
	header('location: ' . $s1);
	exit;
}

# AUTHOR ONLY TEAM ID
switch($process['miscellaneous']['action']) {
	case 'disable': // Not used yet...
	case 'delete':
	case 'import':
		$interpret['selection_action_process']['author_only_team_id'] = get_author_only_team_id($_SESSION['login']['login_user_name']);
	break;
}

# DO IT
switch($process['miscellaneous']['action']) {
	case 'memorize':
		# there is already a check above but since this is potentially super sensitive data we should double check.
		if (!empty($interpret['row']))
		foreach($interpret['row'] as $k1 => $v1) {
			# perhaps we should limit this to one contact at a time... you could import all the contacts in the system at once with this method by sending a big array.
			# get contact_id
			# and description
			$sql = '
				SELECT
					co.id as contact_id,
					me.description as metail_description
				FROM
					' . $prefix . 'metail me,
					' . $prefix . 'link_contact_user lcu,
					' . $prefix . 'contact co
				WHERE
					me.id = ' . (int)$v1 . ' AND
					me.user_id = lcu.user_id AND
					lcu.contact_id = co.id AND
					co.user_id = ' . (int)$login_user_id . ' AND 
					me.id = ' . (int)$v1 . '
				LIMIT
					1
			';
			$result = mysql_query($sql) or die(mysql_error());
			while ($row = mysql_fetch_assoc($result)) {
				$interpret['selection_action_process']['metail_fun']['contact_id'] = $row['contact_id'];
				$interpret['selection_action_process']['metail_fun']['metail_description'] = $row['metail_description'];
			}
			
			# repeat of generic_edit process...
			$sql = '
				INSERT INTO
					' . $prefix . 'note
				SET
					contact_id = ' . (int)$interpret['selection_action_process']['metail_fun']['contact_id'] . ',
					description = ' . to_sql($interpret['selection_action_process']['metail_fun']['metail_description']) . ',
					modified = CURRENT_TIMESTAMP,
					active = 1
				' . ($process['id'] ? 'WHERE id = ' . (int)$process['id'] : '') . '
			';	
			$result = mysql_query($sql) or die(mysql_error());
		}
	break;
	case 'disable':
		$sql = '
			UPDATE
				`' . $prefix . $interpret['table'] . '`
			SET
				modified = CURRENT_TIMESTAMP,
				enabled = 2
			WHERE
				id IN (' . implode(',', $interpret['row']) .')
		';
		$result = mysql_query($sql) or die(mysql_error());
	break;
	case 'delete':
		# BEFORE DELETE
		# teammate has to update ALL intra-team listings as well the teammate
		switch($process['miscellaneous']['list_name']) {
			case 'teammate':
				# Actually we need to get TODO the author only teams for the user that is being removed!
				# yeah this part is not correct... 
				# GET removed team_id array
				# GET removed user_id array
				$sql = '
					SELECT
						team_id,
						user_id
					FROM
						' . $prefix . 'link_team_user
					WHERE
						id IN (' . implode(',', $interpret['row']) .')
				';
				$result = mysql_query($sql) or die(mysql_error());
				while ($row = mysql_fetch_assoc($result)) {
					$interpret['selection_action_process']['team_id'][$row['team_id']] = $row['team_id']; 
					$interpret['selection_action_process']['user_id'][$row['user_id']] = $row['user_id'];
				}
				# MOVE all of the removed user's vote/item/news/metail/tranfer/rating from the corresponding team(s) and puts it in the <user_name> team for the corresponding (removed) user
				foreach ($interpret['selection_action_process']['user_id'] as $k1 => $v1) {
					$interpret['selection_action_process']['foreach']['user_name'] = get_db_single_value('
							name
						FROM
							' .$prefix . 'user
						WHERE
							id = ' . (int)$v1
					);
					if (!$interpret['selection_action_process']['foreach']['user_name'])
						die("!data['selection_action_process']['foreach']['user_name']");
					$interpret['selection_action_process']['foreach']['author_only_team_id'] = get_author_only_team_id($interpret['selection_action_process']['foreach']['user_name']);
					# don't update the timestamp... we don't want to alert users that they were booted from the team unnecessarily.  Ehhh.. or do we...  lets say no for now.
					# item
					$sql = '
						UPDATE
							' . $prefix . 'item
						SET
							team_id = ' . (int)$interpret['selection_action_process']['foreach']['author_only_team_id'] . '
						WHERE
							user_id = ' . (int)$v1 . ' AND
							team_id IN (' . implode(',', $interpret['selection_action_process']['team_id']) . ') AND
							active = 1
					';
					$result = mysql_query($sql) or die(mysql_error());
					# transfer
					$sql = '
						UPDATE
							' . $prefix . 'transfer
						SET
							team_id = ' . (int)$interpret['selection_action_process']['foreach']['author_only_team_id'] . '
						WHERE
							source_user_id = ' . (int)$v1 . ' AND
							team_id IN (' . implode(',', $interpret['selection_action_process']['team_id']) . ') AND
							active = 1
					';
					$result = mysql_query($sql) or die(mysql_error());
					# vote
					$sql = '
						UPDATE
							' . $prefix . 'vote
						SET
							team_id = ' . (int)$interpret['selection_action_process']['foreach']['author_only_team_id'] . '
						WHERE
							user_id = ' . (int)$v1 . ' AND
							team_id IN (' . implode(',', $interpret['selection_action_process']['team_id']) . ') AND
							active = 1
					';
					$result = mysql_query($sql) or die(mysql_error());
					# news
					$sql = '
						UPDATE
							' . $prefix . 'news
						SET
							team_id = ' . (int)$interpret['selection_action_process']['foreach']['author_only_team_id'] . '
						WHERE
							user_id = ' . (int)$v1 . ' AND
							team_id IN (' . implode(',', $interpret['selection_action_process']['team_id']) . ') AND
							active = 1
					';
					$result = mysql_query($sql) or die(mysql_error());
					# metail
					$sql = '
						UPDATE
							' . $prefix . 'metail
						SET
							team_id = ' . (int)$interpret['selection_action_process']['foreach']['author_only_team_id'] . '
						WHERE
							user_id = ' . (int)$v1 . ' AND
							team_id IN (' . implode(',', $interpret['selection_action_process']['team_id']) . ') AND
							active = 1
					';
					$result = mysql_query($sql) or die(mysql_error());
					# rating
					$sql = '
						UPDATE
							' . $prefix . 'rating
						SET
							team_id = ' . (int)$interpret['selection_action_process']['foreach']['author_only_team_id'] . '
						WHERE
							source_user_id = ' . (int)$v1 . ' AND
							team_id IN (' . implode(',', $interpret['selection_action_process']['team_id']) . ') AND
							active = 1
					';
					$result = mysql_query($sql) or die(mysql_error());
				}
			break;
		}

		# DELETE
		switch($process['miscellaneous']['list_name']) {
			case 'offer':
				$sql = '
					DELETE
						t0
					FROM
						' . $prefix . $interpret['table'] . ' t0
					WHERE
						user_id = ' . (int)$login_user_id . ' AND
						' . $process['miscellaneous']['list_name'] . '_id IN (' . implode(',', $interpret['row']) .')
				';
				$result = mysql_query($sql) or die(mysql_error());
			break;
			case 'category':
				$sql = '
					DELETE FROM
						`' . $prefix . $interpret['table'] . '`
					WHERE
						tag_id IN (' . implode(',', $interpret['row']) .')
				';
				$result = mysql_query($sql) or die(mysql_error());
				# also update the tag
				$interpret['table'] = 'tag';
			# nobreak;
			default:
				$sql = '
					UPDATE
						`' . $prefix . $interpret['table'] . '`
					SET
						modified = CURRENT_TIMESTAMP,
						active = 2
					WHERE
						id IN (' . implode(',', $interpret['row']) .')
				';
				$result = mysql_query($sql) or die(mysql_error());
			break;
		}
	break;
	case 'export':
		# untested logic just setup for future guidance 2012-11-11 vaskoiii
		switch($process['miscellaneous']['list_name']) {
			case 'item':
			case 'transfer':
			case 'vote':
				# TODO: use start_engine() so we don't need the repeat sql.
				$sql = '
					SELECT
						gt.tag_id,
						gt.description,
						ca.tag_path as parent_tag_path,
						ta.name as tag_name,
					FROM
						' . $prefix . $process['miscellaneous']['list_name'] . ' gt,
						' . $prefix . 'tag ta,
						' . $prefix . 'link_tag l_t,
					WHERE
						gt.id IN (' . implode(', ', $interpret['row']) . ') AND
						ta.id = gt.tag_id AND
						ta.parent_id = l_t.id AND
				';
				$result = mysql_query($sql) or die(mysql_error());
				while ($row = mysql_fetch_assoc($result))
					$interpret['port_array'][] = $row;
				foreach ($interpret['port_array'] as $k1 => $v1) {
					$sql = '
						INSERT INTO
							' . $prefix . 'transfer
						SET
							source_user_id = ' . (int)$login_user_id . ',
							destination_user_id = ' . (int)$interpret['user_id'] . ',
							modified = CURRENT_TIMESTAMP,
							tag_id = ' . (int)$v1['tag_id'] . ',
							description = ' . to_sql($v1['description']) . '
					';
					$result = mysql_query($sql) or die(mysql_error());
					$interpret['transfer_id'] = mysql_insert_id($config['mysql_resource']);
					index_entry(
						'transfer',
						$interpret['transfer_id'],
						$login_user_id,
						$interpret['user_id'],
						'index'
					);
				}
			break;
		}
	break;
	case 'import':
	switch($process['miscellaneous']['list_name']) {
		case 'item':
		case 'transfer':
		case 'vote':
			# TODO: use start_engine() so we don't need the repeat sql.

			# st.status_name seems unnecessary 2013-10-10 vaskoiii
			$sql = '
				SELECT
					gt.tag_id,
					gt.status_id,
					gt.description,
					ca.tag_path as parent_tag_path,
					ta.name as tag_name,
					st.name as status_name
				FROM
					' . $prefix . $process['miscellaneous']['list_name'] . ' gt,
					' . $prefix . 'tag ta,
					' . $prefix . 'link_tag l_t,
					' . $prefix . 'status st
				WHERE
					gt.id IN (' . implode(', ', $interpret['row']) . ') AND
					ta.id = gt.tag_id AND
					ta.parent_id = l_t.id AND
					gt.status_id = st.id AND
					gt.status_id = st.id
			';
			$result = mysql_query($sql) or die(mysql_error());
			while ($row = mysql_fetch_assoc($result))
				$interpret['port_array'][] = $row;
			foreach ($interpret['port_array'] as $k1 => $v1) {
				$sql = '
					INSERT INTO
						' . $prefix . 'item
					SET
						user_id = ' . (int)$login_user_id . ',
						modified = CURRENT_TIMESTAMP,
						tag_id = ' . (int)$v1['tag_id'] . ',
						status_id = ' . (int)$v1['status_id'] . ',
						team_id = ' . (int)$lookup['team_required_id'] . ',
						description = ' . to_sql($v1['description']) . ',
						active = 1
				';
				$result = mysql_query($sql) or die(mysql_error());
				# no need to update indexes for items ie) no source_user_id no destination_user_id just user_id
			}
		break;
	}
	break;
	case 'judge':
		# placeholder for to judge selection
	break;
	case 'remember':
	case 'forget':
	switch($process['miscellaneous']['list_name']) {
		case 'category':
			die('special case again category which should be referred to as tag');
		break;
		case 'tag':
		case 'location':
		case 'team':
			// we need to make a unique pair on minder with kind_id and kind_name_id
			$sql = '
				DELETE FROM
					' . $prefix . 'minder
				WHERE
					kind_id = ' . (int)$lookup['kind_id'] . ' AND
					user_id = ' . (int)$login_user_id . ' AND
					kind_name_id IN (' . implode(', ', $interpret['row']) . ')'
			;
			$result = mysql_query($sql) or die(mysql_error());
			switch($process['miscellaneous']['action']) {
			case 'remember':
			foreach($interpret['row'] as $k1 => $v1) {
				$sql = '
					INSERT INTO
						' . $prefix . 'minder
					SET
						user_id = ' . (int)$login_user_id . ',
						kind_id = ' . (int)$lookup['kind_id'] . ',
						kind_name_id = ' . (int)$v1 . ',
						modified = CURRENT_TIMESTAMP,
						active = 1
				';
				$result = mysql_query($sql) or die(mysql_error());
			}
			break;
			}
		break;
	}
	break;
	case 'like':
	case 'dislike':
	case 'comment':
		# todo integrate with the way selection action is intended instead of forcing everything in this case ie) sql queries
		# todo better deal with the die() errors
		$i1 = 1;
		if ($process['miscellaneous']['action'] == 'dislike')
			$i1 = 2;
		$i2 = get_db_single_value('
			id from
				' . $prefix . 'kind
			where
				name = ' . to_sql($process['miscellaneous']['list_name'])
		);
		if (empty($i2))
			die('kind not found');

		$i3 = 0;
		# have to know whether we are getting:
		# user_id
		# source_user_id
		$result = 0;
		$sql = '
			select 
				user_id
			from
				' . $prefix . $process['miscellaneous']['list_name'] . '
			where
				id = ' . (int)$process['miscellaneous']['row'][0] . '
			limit
				1
		';
		$result = @mysql_query($sql); # dont print error message
		if (!empty($result)) {
			while ($row = mysql_fetch_assoc($result))
				$i3 = $row['user_id'];
		}
		if (empty($i3)) {
			$sql = '
				select 
					source_user_id
				from
					' . $prefix . $process['miscellaneous']['list_name'] . '
				where
					id = ' . (int)$process['miscellaneous']['row'][0] . '
				limit
					1
			';
			$result = @mysql_query($sql); # dont print error message
			if (!empty($result)) {
				while ($row = mysql_fetch_assoc($result))
					$i3 = $row['source_user_id'];
			}
		}
		if (empty($i3))
			die('unable to get score user id');

		if (sizeof(get_gp('row')) != 1)
			die('only 1 like/dislike/comment at a time');
		switch($action) {
			case 'like':
			case 'dislike':
				$sql = '
					insert into
						' . $prefix . 'score
					set
						source_user_id = ' . (int)$login_user_id . ',
						destination_user_id = ' . (int)$i3 . ',
						mark_id = ' . (int)$i1 . ',
						kind_id = ' . (int)$i2 . ',
						kind_name_id = ' . (int)$process['miscellaneous']['row'][0] . ',
						modified = now(),
						active = 1
				';
			break;
			case 'comment':
				#todo there is no error checking on comments here?
				$sql = '
					insert into
						' . $prefix . 'comment
					set
						user_id = ' . (int)$login_user_id . ',
						kind_id = ' . (int)$i2 . ',
						kind_name_id = ' . (int)$process['miscellaneous']['row'][0] . ',
						description = ' . to_sql($process['comment_description']) . ',
						modified = now(),
						active = 1
				';
			break;
		}
		$result = mysql_query($sql) or die(mysql_error());
	break;
}

#process_success(tt('element', 'transaction_complete') . ($email_sent ? ' : ' . tt('element', 'email_sent') : ''), $x['..'] . get_q_query($x['level'] - 1, get_q_query_modified($x['level'] - 1, $q['parsed'][$x['level'] - 1])));
process_success(
	tt('element', 'transaction_complete') . (
	$email_sent 
		? ' : ' . tt('element', 'email_sent') 
		: ''
	), 
	str_replace('_edit', '_list', $x['..']) . ffm('expand%5B0%5D=', -1)
);
