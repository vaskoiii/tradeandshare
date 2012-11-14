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

# shortcuts defined in edit_process.php
# $id = & $process['action_miscellaneous']['id'];
# $lookup = & $interpret['lookup'];
# $action_content_1 = & $process['action_content_1'];
# $content_2 = & $process['action_content_2'];
# $prefix = & $config['mysql']['prefix'];
# $type = & $process['form_info']['type'];
# $message = & $interpret['message'];
# $login_user_id = $_SESSION['login']['login_user_id'];

switch($type) {
	case 'minder':
		# we should really just strip off the minder part in lookups and stuff and store backend as just kind_id 2012-04-11 vaskoiii
		$sql = '
			INSERT INTO
				' . $prefix . 'minder
			SET
				user_id = ' . (int)$login_user_id . ',
				kind_id = ' . (int)$lookup['minder_kind_id'] . ',
				kind_name_id = ' . (int)$action_content_1['kind_name_id'] . ',
				modified = CURRENT_TIMESTAMP,
				active = 1
		';
	break;
	case 'category':
	case 'tag':
		# other parts updated in auto_tag() 2012-03-10 vaskoiii
		if (!$id)
			die('id must be set for tag/category');
		$sql = '
			UPDATE 
				' . $prefix . 'tag
			SET
				user_id = ' . (int)$login_user_id . ',
				modified = CURRENT_TIMESTAMP,
				active = 1
			WHERE id = ' . (int)$id
		;
	break;
	case 'contact':
		$sql = '
			' . ($id ? 'UPDATE' : 'INSERT INTO') . '
				' . $prefix . 'contact 
			SET
				user_id = ' . (int)$login_user_id . ',
				name = ' . to_sql($action_content_1['contact_name']) . ',
				modified = CURRENT_TIMESTAMP,
				active = 1
			' . ($id ? 'WHERE id = ' . (int)$id : '') . '
		';
	break;
	case 'dialect':
		$sql = '
			' . ($id ? 'UPDATE ' : 'INSERT INTO ') . '
				' . $prefix . 'dialect
			SET
				name = ' . to_sql($action_content_1['dialect_name']) . ',
				description = ' . to_sql($action_content_1['dialect_description']) . ',
				user_id = ' . (int)$login_user_id . ',
				modified = CURRENT_TIMESTAMP,
				active = 1
			' . ($id ? ' WHERE id = ' . (int)$id : '')
		;
	break;
	case 'feedback':
		$sql = '
			' . ($id ? 'UPDATE' : 'INSERT INTO') . '
				' . $prefix . 'feedback 
			SET
				incident_id = ' . (int)$action_content_1['incident_id'] . ',
				user_id = ' . (int)$login_user_id . ',
				modified = CURRENT_TIMESTAMP,
				description = ' . to_sql($action_content_1['feedback_description']) . ',
				active = 1
			' . ($id ? 'WHERE id = ' . (int)$id : '') . '
		';
	break;
	case 'feed':
		// TODO bring out the query part to normal error checking sections...
		//dialect_id = ' . (int)$_SESSION['dialect']['dialect_id'] . '
		$sql = '
			' . ($id ? 'UPDATE' : 'INSERT INTO') . '
				' . $prefix . 'feed
			SET
				modified = CURRENT_TIMESTAMP,
				name = ' . to_sql($action_content_1['feed_name']) . ',
				active = 1,
				user_id = ' . (int)$login_user_id . ',
				dialect_id = ' . (int)$lookup['dialect_id'] . '
				' . ($id ? '' : ', `key` = ' . to_sql($lookup['feed_key'])) . '
				' . ($id ? '' : ', page_id = ' . (int)$lookup['page_id']) . '
				' . ($id ? '' : ', query = ' . to_sql($content_2['feed_query'])) . '
			' . ($id ? 'WHERE id = ' . (int)$id : '') . '
		';
	break;
	case 'group':
		$sql = '
			' . ($id ? 'UPDATE' : 'INSERT INTO') . '
				`' . $prefix . 'group` 
			SET
				name = ' . to_sql($action_content_1['group_name']) . ',
				description = ' . to_sql($action_content_1['group_description']) . ',
				user_id = ' . (int)$login_user_id . ',
				modified = CURRENT_TIMESTAMP,
				active = 1
			' . ($id ? 'WHERE id = ' . (int)$id : '') . '
		';
	break;
	case 'groupmate':
		$sql = '
			' . ($id ? 'UPDATE' : 'INSERT INTO') . '
				' . $prefix . 'link_contact_group
			SET
				contact_id = ' . to_sql($lookup['xor_contact_id']) . ',
				group_id = ' . to_sql($lookup['group_id']) . ',
				modified = CURRENT_TIMESTAMP,
				active = 1
			' . ($id ? 'WHERE id = ' . (int)$id : '') . '
		';
	break;
	case 'incident':
		$sql = '
			' . ($id ? 'UPDATE' : 'INSERT INTO') . '
				' . $prefix . 'incident 
			SET
				modified = CURRENT_TIMESTAMP,
				user_id = ' . (int)$login_user_id . ',
				name = ' . to_sql($action_content_1['incident_name']) . ',
				description = ' . to_sql($action_content_1['incident_description']) . ',
				phase_id = ' . (int)$lookup['phase_id'] . ',
				active = 1
			' . ($id ? 'WHERE id = ' . (int)$id : '') . '
		';
	break;
	case 'item':
		$sql = '
			' . ($id ? 'UPDATE' : 'INSERT INTO') . '
				' . $prefix . 'item
			SET
				team_id = ' . (int)$lookup['team_required_id'] . ',
				user_id = ' . (int)$login_user_id . ',
				modified = CURRENT_TIMESTAMP,
				tag_id = ' . (int)$lookup['tag_id'] . ',
				status_id = ' . (int)$lookup['status_id'] . ',
				description = ' . to_sql($action_content_1['item_description']) . ',
				active = 1
			' . ($id ? 'WHERE id = ' . (int)$id : '')
		;
	break;
	case 'transfer':
		// active bit handled differently (not set here).
		$sql = '
			' . ($id ? 'UPDATE' : 'INSERT INTO') . '
				' . $prefix . 'transfer
			SET
				source_user_id = ' . (int)$login_user_id . ',
				destination_user_id = ' . (int)$lookup['xor_user_id'] . ',
				modified = CURRENT_TIMESTAMP,
				tag_id = ' . to_sql($lookup['tag_id']) . ',
				team_id = ' . to_sql($lookup['team_required_id']) . ',
				description = ' . to_sql($action_content_1['transfer_description']) . ',
				active = 1
			' . ($id ? 'WHERE id = ' . (int)$id : '')
		;
	break;
	case 'invite':
		$sql = '
			' . ($id ? 'UPDATE' : 'INSERT INTO') . '
				' . $prefix . 'invite
			SET
				user_id = ' . (int)$login_user_id . ',
				email = ' . to_sql($action_content_1['invite_email']) . ',
				modified = CURRENT_TIMESTAMP
				' . ($id ? '' : ', 
						password = ' . to_sql($lookup['invite_password']) . ',
						used = 2,
						active = 1
					') . '
			' . ($id ? 'WHERE id = ' . (int)$id : '')
		;
	break;
	case 'location':
		$sql = '
			' . ($id ? 'UPDATE' : 'INSERT INTO') . '
				' . $prefix . 'location
			SET
				user_id = ' . (int)$login_user_id . ',
				latitude = 	' . to_sql($action_content_1['location_latitude']) . ',
				longitude = ' . to_sql($action_content_1['location_longitude']) . ',
				name = ' . to_sql($action_content_1['location_name']) . ',
				modified = CURRENT_TIMESTAMP,
				active = 1
			' . ($id ? 'WHERE id = ' . (int)$id : '')
		;
	break;
	case 'meripost':
		$sql = '
			' . ($id ? 'UPDATE' : 'INSERT INTO') . '
				' . $prefix . 'meripost
			SET
				modified = CURRENT_TIMESTAMP,
				user_id = ' . (int)$login_user_id . ',
				meritopic_id = ' . (int)$lookup['meritopic_id'] . ',
				meritype_id = ' . (int)$lookup['meritype_id'] . ',
				description = ' . to_sql($action_content_1['meripost_description']) . ',
				active = 1
			' . ($id ? 'WHERE id = ' . (int)$id : '')
		;
	break;
	case 'meritopic':
		$sql = '
			' . ($id ? 'UPDATE' : 'INSERT INTO') . '
				' . $prefix . 'meritopic
			SET
				modified = CURRENT_TIMESTAMP,
				user_id = ' . (int)$login_user_id . ',
				name = ' . to_sql($action_content_1['meritopic_name']) . ',
				description = ' . to_sql($action_content_1['meritopic_description']) . ',
				active = 1
			' . ($id ? 'WHERE id = ' . (int)$id : '')
		;
	break;
	case 'news':
		$sql = '
			' . ($id ? 'UPDATE' : 'INSERT INTO') . '
				' . $prefix . 'news 
			SET
				name = ' . to_sql($action_content_1['news_name']) . ',
				user_id = ' . (int)$login_user_id . ',
				team_id = ' . (int)$lookup['team_required_id'] . ',
				description = ' . to_sql($action_content_1['news_description']) . ',
				modified = CURRENT_TIMESTAMP,
				active = 1
			' . ($id ? 'WHERE id = ' . (int)$id : '') . '
		';
	break;
	case 'metail':
		$sql = '
			' . ($id ? 'UPDATE' : 'INSERT INTO') . '
				' . $prefix . 'metail 
			SET
				user_id = ' . (int)$login_user_id . ',
				team_id = ' . (int)$lookup['team_required_id'] . ',
				description = ' . to_sql($action_content_1['metail_description']) . ',
				modified = CURRENT_TIMESTAMP,
				active = 1
			' . ($id ? 'WHERE id = ' . (int)$id : '') . '
		';
	break;
	case 'note':
		$sql = '
			' . ($id ? 'UPDATE' : 'INSERT INTO') . ' 
				' . $prefix . 'note
			SET
				contact_id = ' . to_sql($lookup['xor_contact_id']) . ',
				description = ' . to_sql($action_content_1['note_description']) . ',
				modified = CURRENT_TIMESTAMP,
				active = 1
			'  . ($id ? ' WHERE id = ' . (int)$id : '') . '
		';
	break;
	case 'offer':
		// active bit handled differently (not set here).
		$sql = '
			' . ($id ? 'UPDATE' : 'INSERT INTO') . '
				' . $prefix . 'offer
			SET
				source_user_id = ' . (int)$login_user_id . ',
				destination_user_id = ' . (int)$lookup['xor_user_id'] . ',
				name = ' . to_sql($action_content_1['offer_name']) . ',
				description = ' . to_sql($action_content_1['offer_description']) . ',
				modified = CURRENT_TIMESTAMP
			' . ($id ? 'WHERE id = ' . (int)$id : '') . '
		';
	break;
	case 'rating':
		$sql = '
			' . ($id ? 'UPDATE' : 'INSERT INTO') . '
				' . $prefix . 'rating
			SET
				source_user_id = ' . (int)$login_user_id . ',
				destination_user_id = ' . (int)$lookup['xor_user_id'] . ',
				team_id = ' . (int)$lookup['team_required_id'] . ',
				modified = CURRENT_TIMESTAMP,
				grade_id = ' . to_sql($lookup['grade_id']) . ',
				description = ' . to_sql($action_content_1['rating_description']) . ',
				active = 1
			' . ($id ? 'WHERE id = ' . (int)$id : '') . '
		';
	break;
	case 'team':
		$sql = '
			' . ($id ? 'UPDATE' : 'INSERT INTO') . '
				' . $prefix . 'team 
			SET
				user_id = ' . (int)$login_user_id . ',
				name = ' . to_sql($action_content_1['team_name']) . ',
				description = ' . to_sql($action_content_1['team_description']) . ',
				modified = CURRENT_TIMESTAMP,
				active = 1
			' . ($id ? 'WHERE id = ' . (int)$id : '') . '
		';
	break;
	case 'teammate':
		$sql = '
			' . ($id ? 'UPDATE' : 'INSERT INTO') . '
				' . $prefix . 'link_team_user
			SET
				user_id = ' . (int)$lookup['xor_user_id'] . ',
				team_id = ' . to_sql($lookup['team_id']) . ',
				modified = CURRENT_TIMESTAMP,
				active = 1
			' . ($id ? 'WHERE id = ' . (int)$id : '') . '
		';
	break;
	case 'jargon':
		$i1 = $lookup['tag_id'];
		$i2 = 11; # (kind_id)
	#nobreak;
	case 'translation':
		if ($type != 'jargon') {
			$i1 = (int)$lookup['kind_name_id'];
			$i2 = (int)$lookup['translation_kind_id'];
		}
		$sql = '
			' . ($id ? 'UPDATE ' : 'INSERT INTO ') . '
				`' . $prefix . 'translation`
			SET
				kind_name_id = ' . (int)$i1 . ',
				remote_id = ' . (int)$i1 . ',
				kind_id = ' . (int)$i2 . ',

				server_id = ' . (int)$config['server_id'] . ',
				user_id = ' . (int)$login_user_id . ',
				dialect_id = ' . to_sql($lookup['dialect_id']) . ',
				name = ' . to_sql($action_content_1['translation_name']) . ',
				description = ' . to_sql($action_content_1['translation_description']) . ',
				modified = CURRENT_TIMESTAMP,
				`default` = ' . (int)$lookup['default_boolean_id'] . ',
				active = 1
			' . ($id ? ' WHERE id = ' . (int)$id : '')
		;
	break;
	case 'profile':
		# location_id = ' . (int)$config['main_location_id'] . ',
		$sql = '
			UPDATE '
				. $prefix . 'user
			SET
				name = ' . to_sql($action_content_1['login_user_name']) . ',
				' . ( 
					empty($action_content_1['user_password_unencrypted']) 
						? '' 
						: 'password = ' . to_sql($lookup['user_password']) . ', ' 
				) . '
				location_id = ' . (int)$lookup['location_id'] . ',
				email = ' . to_sql($action_content_1['user_email']) . ',
				modified = CURRENT_TIMESTAMP,
				active = 1
			WHERE id = ' . (int)$login_user_id
		;
	break;
}
