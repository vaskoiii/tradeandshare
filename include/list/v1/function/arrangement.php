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

# Contents/Description: Retrieve parts that "may" be be better stored here than in a relational database.
# Ideally: Logic is minimal. (no [if] only [switch])
# todo: $id = false as a parameter? 2012-04-19 vaskoiii
# todo: parent_tag_path should actually be parent_tag_name

# layout/FOOTER
function get_action_footer_1($type = false, $name = false) {
	$array = array();
	switch($name) {
		case 'set':
			$array = array(
				'set' => '',
			);
		break;
		case 'recover':
		switch($type) {
			case 'login':
			case 'feed':
				$array = array(
					'recover' => '',
				);
			break;
		}
		break;
		default:
		switch ($type) {
			case 'message':
				$array = array(
					'send' => '',
				);
			break;
			default: # using genericly for: set, recover, edit, select, etc...
				$array = array(
					'add' => '',
					'edit' => '',
#					'recover',
#					'select',
#					'send',
#					'set',
#					'submit',
				);
			break;
		}
		break;
	}
	return $array;
}
function get_view_footer_1($type = false, $name = false) { } # placeholder
function get_search_footer_1($type = false, $name = false) { } # placeholder
function get_result_footer_1($type = false, $name = false) {

	switch($type) {
		case 'channel':
			$array = array(
				'remember' => '',
				'forget' => '',
			);
		break;
		case 'team':
		case 'location':
		case 'category':
			$array = array(
				'remember' => '',
				'forget' => '',
				'delete' => '',
			);
		break;
		case 'tag':
			$array = array(
				'merge' => '',
			);
		break;
		case 'contact':
		case 'feed':
		case 'group':
		case 'groupmate':
		case 'invite':
		case 'metail':
		case 'note':
		case 'offer':
		case 'teammate':
			$array = array(
				'delete' => '',
			);
		break;
		case 'item': 
		case 'transfer':
		case 'vote':
			$array = array(
				'delete' => '',
				'export' => '',
				'import' => '',
				'judge' => '',
			);
		break;
		case 'metail':
			$array = array(
				'memorize' => '',
			);
		break;
	}
	return $array;
}

# layout/FOOTER
function get_action_footer_2($type = false, $name = false) {
	$array = array();
	switch ($name) {
		case 'recover':
		switch($type) {
			default:
			case 'login':
			case 'feed':
				# do nothing
			break;
		}
		break;
		default:
		switch ($type) {
			case 'login':
			case 'feed':
				$array = array(
					#'edit_more' => '',
					'recover_' . $type => '',
				);
			break;
			default:
				$array = array(
					#'edit_more' => '',
				);
			break;
		}
	break;
	}
	return $array;
}
function get_view_footer_2($type = false, $name = false) {
	switch($type) {
		case 'contact':
		case 'user':
			$array = array(
				'edit_offer' => '',
			);
		break;
	}
	return $array;
}
function get_search_footer_2($type = false, $name = false) { } # placeholder
function get_result_footer_2($type = false, $name = false) {
	switch($type) {
		default:
			$array = array(
				'paging' => '',
			);
		break;
	}
	return $array;
}

# layout/FOOTER
function get_action_footer_3($type = false, $name = false) { } # placeholder
function get_view_footer_3($type = false, $name = false) { } # placeholder
function get_search_footer_3($type = false, $name = false) { } # placeholder
function get_result_footer_3($type = false, $name = false) { } # placeholder

# layout/CONTENT
function get_action_content_1($type = false, $name = false) {
	$array = array();
	switch($name) {
		case 'set':
			switch($type) {
				case 'lock':
					$array = array(
						'lock_contact_user_mixed' => '',
						'lock_user_name' => '',
						'lock_contact_name' => '',
						'lock_group_name' => '',
						'lock_team_name' => '',
						'lock_location_name' => '',
						'lock_range_name' => '',
					);
				break;
				case 'login':
					$array = array(
						'login_user_name' => '',
						'login_user_password_unencrypted' => '',
						'remember_login' => '',
						'login_request_uri' => '', # redirect
					);
				break;
				case 'dialect':
					$array = array(
						'dialect_name' => '',
					);
				break;
				case 'theme':
					$array = array(
						'theme_name' => '',
						'background_theme_name' => '',
						'launcher_theme_name' => '',
					);
				break;
				case 'display':
					$array = array(
						'display_name' => '',
					);
				break;
				case 'load':
					$array = array(
						'load_javascript' => '',
					);
				break;
				case 'config':
					# todo find reason entry exists in db though we really only use config_report. 2012-02-06 vaskoiii
				break;
			}
		break;
		case 'recover':
			switch($type) {
				case 'login':
					$array = array(
						'login_user_name' => '',
					);
				break;
				case 'feed':
					# hmmm doesn't show anything but the ?id=XXX in the url content is populated. 2012-02-06 vaskoiii
				break;
			}
		break;
		case 'edit':
		default:
			switch($type) {
				case 'channel':
					$array = array(
						# 'user_name' => '',
						'channel_name' => '',
						'channel_offset' => '',
						'channel_value' => '',
						'channel_percent' => '',
						'channel_description' => '',
					);
				break;
				case 'renewal':
				case 'renewage':
					$array = array(
						'channel_name' => '',
						'point_name' => '',
					);
				break;
				# case 'cycle':
				# 	cycles are created indirectly by renewals/channels
				# break;
				case 'invite':
					$array = array(
						'invite_email' => '',
					);
				break;
				case 'vote':
					$array = array(
						'tag_translation_name' => '',
						'vote_description' => '',
						'decision_name' => '',
					);
				break;
				case 'item':
					$array = array(
						'tag_translation_name' => '',
						'item_description' => '',
						'status_name' => '',
					);
				break;
				case 'dialect':
					$array = array(
						'new_dialect_name' => '', # ONLY sessionable listing addable by users? 2012-04-12 vaskoiii
						'dialect_description' => '',
					);
				break;
				case 'news':
					$array = array(
						'news_name' => '',
						'news_description' => '',
					);
				break;
				case 'rating':
					$array = array(
						'contact_user_mixed' => '',
						'channel_name' => '',
						'user_name' => '',
						'contact_name' => '',
						'grade_name' => '',
						'rating_description' => '',
					);
				break;
				case 'metail':
					$array = array(
						'metail_description' => '',
					);
				break;
				case 'visit':
				break;
				case 'incident':
					$array = array(
						'incident_name' => '',
						'phase_name' => '',
						'incident_description' => '',
					);
				break;
				case 'feedback':
					$array = array(
						'incident_id' => '',
						'feedback_description' => '',
					);
				break;
				case 'offer':
					$array = array(
						'contact_user_mixed' => '',
						'user_name' => '',
						'contact_name' => '',
						'offer_name' => '',
						'offer_description' => '',
					);
				break;
				case 'transfer':
					$array = array(
						'contact_user_mixed' => '',
						'user_name' => '',
						'contact_name' => '',
						'tag_translation_name' => '',
						'transfer_description' => '',
					);
				break;
				case 'contact':
					$array = array(
						'contact_user_mixed' => '',
						# order switch
						'contact_name' => '',
						'user_name' => '',
						'accept_friend' => '',
					);
				break;
				case 'note':
					$array = array(
						'contact_user_mixed' => '',
						'user_name' => '',
						'contact_name' => '',
						'note_description' => '',
					);
				break;
				case 'group':
					$array = array(
						'group_name' => '',
						'group_description' => '',
					);
				break;
				case 'groupmate':
					$array = array(
						'group_name' => '',
						'contact_user_mixed' => '',
						'user_name' => '',
						'contact_name' => '',
					);
				break;
				case 'feed':
					$array = array(
						'feed_name' => '',
					);
				break;
				case 'team':
					$array = array(
						'team_name' => '',
						'team_description' => '',
					);
				break;
				case 'teammate':
					$array = array(
						'team_name' => '',
						'contact_user_mixed' => '',
						'user_name' => '',
						'contact_name' => '',
					);
				break;
				case 'location':
					$array = array(
						'location_name' => '',
						'location_latitude' => '',
						'location_longitude' => '',
					);
				break;
				case 'user':
					$array = array(
						'login_user_name' => '',
						'user_password_unencrypted' => '',
						'user_password_unencrypted_again' => '',
						'user_email' => '',
						'invite_id' => '',
						'invite_user_name' => '',
						'invite_password' => '',
						'accept_usage_policy' => '',
					);
				break;
				case 'profile':
					$array = array(
						'login_user_name' => '',
						'user_password_unencrypted' => '',
						'user_password_unencrypted_again' => '',
						'user_email' => '',
						'location_name' => '',
						'notify_offer_received' => '',
						'notify_teammate_received' => '',
						'feature_lock' => '',
						'feature_minnotify' => '',
						'accept_usage_policy' => '',
						'face_file' => '',
						//'face_md5' => '',
						//'face_extension' => '',
						'pubkey_value' => '',

					);
				break;
				case 'invited':
				break;
				case 'category':
				case 'tag':
					$array = array(
						'tag_translation_name' => '',
					);
				break;
				case 'minder':
					$array = array(
						'minder_kind_name' => '',
						'kind_name_id' => '',
					);
				break;
				case 'carry': # placeholder
					$array = array(
						# todo design so that end user doesn't have to do anything
						# todo diminishing weight
						# carry_value = 1/2 of number of likes/dislikes from previous cycle
					);
				break;
				case 'comment':
				case 'score':
					$array = array(
						# done by responding directly to the corresponding item
					);
				break;
				case 'jargon':
					$array = array(
						'tag_path' => '',
						'dialect_name' => '',
						'translation_name' => '',
						'translation_description' => '',
						'default_boolean_name' => '',
					);
				break;
				case 'translation':
					$array = array(
						'translation_kind_name' => '',
						'kind_name_name' => '', # prefixed with translation_ to go along with translation_kind_name 2012-03-10 vaskoiii
						'dialect_name' => '',
						'translation_name' => '',
						'translation_description' => '',
						'default_boolean_name' => '',
					);
				break;
				case 'meritopic':
					$array = array(
						'meritopic_name' => '',
						'meritopic_description' => '',
					);
				break;
				case 'meripost':
					$array = array(
						'meritopic_id' => '',
						'meritype_name' => '',
						'meripost_description' => '',
					);
				break;
			}
		break;
	}
	return $array;
}
function get_view_content_1($type) { } # placeholder
function get_search_content_1($type) {
	switch($type) {
		case 'vote':
			$array = array(
				'decision_name' => '',
				'parent_tag_path' => '',
				# 'parent_tag_name' => '',
				'team_required_name' => '',
			);
		break;
		case 'item':
			$array = array(
				'status_name' => '',
				'parent_tag_path' => '',
				# 'parent_tag_name' => '',
				'team_required_name' => '',
			);
		break;
		case 'news':
			$array = array(
				'team_required_name' => '',
			);
		break;
		case 'rating':
			$array = array(
				'channel_name' => '',
				'grade_name' => '',
				'direction_name' => '',
				);
		break;
		case 'metail':
			$array = array(
				'team_required_name' => '',
			);
		break;
		case 'visit':
		break;
		case 'incident':
			$array = array(
				'phase_name' => '',
			);
		break;
		case 'feedback':
			$array = array(
				'incident_id' => '',
			);
		break;
		case 'offer':
			$array = array(
				'direction_name' => '',
			);
		break;
		case 'transfer':
			$array = array(
				'direction_name' => '',
				'parent_tag_path' => '',
				# 'parent_tag_name' => '',
				'team_required_name' => '',
			);
		break;
		case 'contact':
		break;
		case 'note':
		break;
		case 'group':
			$array = array(
				'group_name' => '',
			);
		break;
		case 'groupmate':
			$array = array(
				'group_name' => '',
			);
		break;
		case 'feed':
		break;
		case 'team':
			$array = array(
				'team_name' => '',
			);
		break;
		case 'teammate':
			$array = array(
				'team_name' => '',
			);
		break;
		case 'channel':
			$array = array(
				'channel_name' => '',
			);
		break;
		case 'location':
			$array = array(
				'location_name' => '',
			);
		break;
		case 'user':
			$array = array(
				'location_name' => '',
			);
		break;
		case 'profile':
		break;
		case 'invited':
			$array = array(
				'direction_name' => '',
			);
		break;
		case 'category':
			$array = array(
				'parent_tag_path' => '',
				# 'parent_tag_name' => '',
				'tag_name' => '',
			);
		break;
		case 'tag':
			$array = array(
				'parent_tag_path' => '',
				# 'parent_tag_name' => '',
			);
		break;
		case 'minder':
			$array = array(
				'minder_kind_name' => '',
			);
		break;
		case 'carry':
			$array = array();
		break;
		case 'comment':
		case 'score':
			$array = array(
				'kind_name' => '', # todo make sure search is working
				# 'kind_id' => '', # not sure if anyone would ever search with kind_id
			);
		break;
		case 'jargon':
			$array = array(
				'tag_path' => '',
				'kind_name_name' => '',
				'dialect_name' => '',
			);
		break;
		case 'translation':
			$array = array(
				'translation_kind_name' => '',
				'kind_name_name' => '',
				'dialect_name' => '',
			);
		break;
		case 'meritopic':
			$array = array(
				'meritopic_id' => '',
			);
		break;
		case 'meripost':
			$array = array(
				'meritopic_id' => '',
				'meritype_name' => '',
			);
		break;
	}
	return $array;
}
function get_result_content_1($type = false, $name = false) { } # placeholder

# layout/CONTENT
# todo in the future we may want to use this 2012-02-02 vaskoiii
# more
function get_action_content_2($type, $name = false) {
	$array = array();
	switch($name) {
		case 'recover':
		switch($type) {
			case 'feed':
			case 'login':
				# new style should header you back to the form (no preview) 2012-04-19 vaskoiii
			break;
		}
		break;
		default:
		switch($type) {
			case 'item':
			case 'transfer':
			case 'vote':
				$array = array(
					'parent_tag_path' => '',
					# 'parent_tag_name' => '',
					'team_required_name' => '',
				);
			break;
			case 'category':
			case 'tag':
				$array = array(
					'parent_tag_path' => '',
					# 'parent_tag_name' => '',
				);
			break;
			case 'news':
				$array = array(
					'team_required_name' => '',
				);
			break;
			case 'rating':
				$array = array(
					'team_required_name' => '',
				);
			break;
			case 'metail':
				$array = array(
					'team_required_name' => '',
				);
			break;
			case 'feed':
				$array = array(
					'page_name' => '',
					'feed_query' => '',
					'dialect_name' => '',
				);
			break;
		}
		break;
	}
	return $array;
}
function get_view_content_2($type = false, $name = false) { } # placeholder
function get_search_content_2($type = false, $name = false) {
	switch($type) {
		default:
			$array = array(
				'lock_contact_user_mixed' => '',
				'lock_user_name' => '',
				'lock_contact_name' => '',
				'lock_team_name' => '',
				'lock_location_name' => '',
				'lock_range_name' => '',
			);
		break;
	}
	return $array;
}
function get_result_content_2($type = false, $name = false) { } # placeholder

# layout/CONTENT
function get_edit_content_3($type = false, $name = false) { } # placeholder
function get_view_content_3($type = false, $name = false) { } # placeholder
function get_search_content_3($type = false, $name = false) { } # placeholder
function get_result_content_3($type = false, $name = false) { } # placeholder

# layout/HEADER
function get_action_header_1($type = false, $name = false) {
	$array = array();
	switch($type) {
		default:
			$array = array(
				'x' => '',
				'q' => '',
				'load' => '',
				'type' => '',
				'id' => '',
			);
		break;
	}
	return $array;
}
function get_view_header_1($type = false, $name = false) { } # placeholder
function get_search_header_1($type = false, $name = false) {
	$array = array();
	switch($type) {
		default:
			$array = array(
				'x' => '',
				'q' => '',
				'load' => '',
				'type' => '',
			);
	}
	return $array;
}
function get_result_header_1($type = false, $name = false) {
	$array = array();
	switch($type) {
		default:
			$array = array(
				'x' => '',
				'q' => '',
				'load' => '',
				'type' => '',
			);
	}
	return $array;
}
