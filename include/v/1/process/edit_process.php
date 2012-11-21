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

# Contents/Description: process ALL the different edits

# SEND EMAIL:
# offer, teammate
# -------------------------
# on add? YES
# on edit? YES
# if duplicate? NO

# keep at the top to easily turn on and off sections for testing
switch (get_gp('type')) {
	# rough
	case 'dialect':
		# intended to be added in future but not necessary now 2012-04-28 vaskoiii
	break;
	case 'minder':
		# requires memorizing the correct kind_name_name/kind_name_id - remeber/forget only requires 1 click 2012-04-20 vaskoiii
		# diabled intentionally
	break;
	# special
	case 'profile':
	case 'invite':
	# main
	case 'item':
	case 'transfer':
	case 'rating':
	case 'incident':
	case 'feedback':
	# contact
	case 'offer':
	case 'news':
	case 'contact':
	case 'note':
	case 'group':
	case 'groupmate':
	case 'feed':
	# people
	case 'team':
	case 'teammate':
	case 'user':
	case 'metail':
	# other
	case 'meritopic':
	case 'meripost':
	case 'location':
	# control:
	case 'translation':
	case 'jargon':
	case 'tag':
	case 'category':
		# briefly tested that adding things works 2012-03-07 vaskoiii
	break;
	default:
		die('needs testing');
	break;
}

# PRECHECK
# part of preview functionality. Not used but included so that browsers that focus on form fields when pressing tab have an easier time locating this ability.
# We could also pull the submit [add_more] to here too but whatever.
if (get_gp('reedit')) {
	// skip everything and go back to the edit screen
	$interpret['lookup']['id'] = get_gp('id'); // HaCk!
	process_success(tt('element', 'reedit_ready'));
}

// testing out if we can strip weird characters out of the input (mainly the enter key if accidentally submitted).
function trimmage($v) {
	return trim($v);
}

$process = array(); # Send to process
$interpret = array(); # Interpreted from $process
$interpret['lookup'] = array();

$process['form_info'] = get_action_header_1();
foreach($process['form_info'] as $k1 => $v1)
	$process['form_info'][$k1] = get_gp($k1);

$process['action_miscellaneous']['id'] = '';
if (isset_gp('id'))
	$process['action_miscellaneous']['id'] = get_gp('id');

$process['action_miscellaneous']['load'] = '';
if (isset_gp('load'))
	$process['action_miscellaneous']['id'] = get_gp('load');

$process['action_content_1']  = get_action_content_1($process['form_info']['type'], 'edit');
$process['action_content_2']  = get_action_content_2($process['form_info']['type'], 'edit');

# shortcuts
$id = & $process['action_miscellaneous']['id'];
$lookup = & $interpret['lookup'];
$action_content_1 = & $process['action_content_1'];
$action_content_2 = & $process['action_content_2'];
$prefix = & $config['mysql']['prefix'];
$type = & $process['form_info']['type'];
$message = & $interpret['message'];
$login_user_id = $_SESSION['login']['login_user_id'];

# todo fix unusual editing/overrides in the below switch 2012-02-02 vaskoiii
switch($process['form_info']['type']) {
	case 'feed':
		# only required on insert 2012-03-10 vaskoiii
		$lookup['feed_key'] = md5(uniqid(rand(), true));
	break;
	case 'invite':
		$lookup['invite_password'] = get_random_string();
	break;
	case 'user':
		# first registration separate file
		include('v/1/process/user_process.php');
		exit;
	break;
}

# SET RESPONSE
if ($process)
foreach ($process as $k1 => $v1)
if ($v1)
foreach ($v1 as $k2 => $v2)
switch ($k2) {
	case 'kind_name':
		switch($process['form_info']['type']) {
			case 'item':
			case 'transfer':
				$process[$k1][$k2] = 'tag';
			break;
			default:
				$process[$k1][$k2] = get_gp('kind_name');
			break;
		}
	break;
	case 'invite_password':
		# todo prompt for password when sending an invite. previous notes/logic has been removed in favor of this approach. 2012-02-02 vaskoiii
		#$lookup['invite_password'] = get_random_string();
	break;
	case 'user_name':
	case 'contact_name':
	case 'lock_user_name':
	case 'lock_contact_name':
		# ignore we will be splitting!
	break;
	case 'contact_user_mixed':
	case 'lock_contact_user_mixed':
		$s1 = '';
		if (str_match('lock_', $k2))
			$s1 = 'lock_';
		switch ($process['form_info']['type']) {
			case 'contact':
			case 'note':
			case 'groupmate':
				contact_user_mixed_split('action_content_1', $s1, 1);
			break;
			default:
				contact_user_mixed_split('action_content_1', $s1);
			break;
		}
	break;
	case 'login_user_name':
		$process[$k1][$k2] = $_SESSION['login']['login_user_name']; # Emergen C 2012-02-02 vaskoiii
	break;
	case 'notify_offer_received':
	case 'notify_teammate_received':
	case 'feature_lock':
	case 'feature_minnotify':
	case 'accept_usage_policy':
	case 'accept_friend':
	case 'accept_default':
		$process[$k1][$k2] = get_boolean_gp($k2);
	break;
	default:
		if (str_match('_description', $k2))
			$process[$k1][$k2] = trimmage(get_gp($k2));
		else
			$process[$k1][$k2] = get_gp($k2);
	break;
}

# TRANSLATIONS for messages
if ($process)
foreach ($process as $k1 => $v1)
if ($v1)
if ($v1 != 'form_info')
foreach($v1 as $k2 => $v2)
	add_translation('element', $k2);
add_translation('element', 'transaction_complete');
add_translation('element', 'email_sent');
add_translation('element', 'error_does_not_exist');
add_translation('element', 'error_does_exist');
# too early? gets called 2x?
do_translation($key, $translation, $_SESSION['dialect']['dialect_id'], $_SESSION['login']['login_user_id']);

# lookup
process_data_translation('action_content_1');
process_data_translation('action_content_2');

switch($type) {
	case 'profile':
		# also needs to be set for $type == user 2012-05-06 vaskoiii
		$lookup['user_password'] = encrypt_password($action_content_1['user_password_unencrypted']);
	break;
	case 'category':
	case 'tag':
		# $id is needed for the edit_sql.php 2012-03-12 vaskoiii
		# always an update on success
		$id = $lookup['tag_id'] = auto_tag($action_content_2['parent_tag_path'], $action_content_1['tag_translation_name'], $_SESSION['dialect']['dialect_id'], $_SESSION['login']['login_user_id']);
	break;
	case 'item':
	case 'transfer':
		$lookup['tag_id'] = auto_tag($action_content_2['parent_tag_path'], $action_content_1['tag_translation_name'], $_SESSION['dialect']['dialect_id'], $config['autocreation_user_id'] );
		#include($x['site']['i'] . 'inline/auto_tag.php');
	break;
}

# more error checking
switch($type) {
	case 'category':
	case 'tag':
		if ($process['form_info']['id'])
			$message = tt('element', 'error') . ' : ' . tt('element', 'only_mergeable');
		if (!$lookup['tag_id'])
			$message = tt('element', 'error') . ' : ' . tt('element', 'tag_path');
		# simplify logic by requiring parent_tag_path 2012-03-10 vaskoiii
		# if ($lookup['tag_level'] > 1) {
		#if (!$lookup['parent_tag_id']) {
		#	$message = tt('element', 'parent_tag_path') . ' : ' . tt('element', 'error_does_not_exist');
		#} }
	break;
	case 'jargon':
		$i1 = $lookup['tag_id'];
		$i2 = 11; # kind_name = 'tag';
		if (!$i1)
			$message = tt('element', 'tag_path') . ' : ' . tt('element', 'error_does_not_exist');
	#nobreak;
	case 'translation':
		if ($type != 'jargon') {
			$i1 = $lookup['kind_name_id'];
			$i2 = $lookup['translation_kind_id'];
			if (!$i1 || !$i2)
				$message = tt('element', 'translation_kind_name') . ' + ' . tt('element', 'kind_name_name') . ' : ' . tt('element', 'error_does_not_exist');
		}

		if ($lookup['default_boolean_id'] == 2) {
			# must be true if there are no other entries
			$sql = '
				SELECT
					COUNT(id) as countage
				FROM
					' . $prefix . 'translation
				WHERE
					kind_name_id = ' . (int)$i1 . ' AND
					kind_id = ' . (int)$i2 . ' AND
					`default` = 1
			';
			$result = mysql_query($sql) or die(mysql_error());
			while($row = mysql_fetch_assoc($result)) {
				if ($row['countage'] == 0)
					$message = 'error_needs_to_be_default';
			
			}
		}
	break;
	case 'feed':
		if (!str_match('_list', $action_content_2['page_name']))
			$message = tt('element', 'error') . ' : ' . tt('element', 'invalid') . ' : ' . tt('element', 'page_name');
		if (get_db_single_value('
				id
			FROM
				' . $prefix . 'feed
			WHERE
				user_id = ' . (int)$login_user_id . ' AND
				name = ' . to_sql($action_content_1['feed_name']) . ' AND
				`active` = 1 AND
				id != ' . (int)get_gp('id') . '
		'))
			$message = tt('element', 'error') . ' : ' . tt('element', 'feed_name') . ' : ' . tt('element', 'error_does_exist'); 
	break;
	case 'rating':
	case 'offer':
	case 'transfer':
	case 'teammate':
	case 'note':
	case 'groupmate':
		if ($action_content_1['user_name'] && $action_content_1['contact_name']) {
			if (!get_db_single_value('
					1
				FROM
					' . $prefix . 'link_contact_user lcu
				WHERE
					lcu.user_id = ' . (int)$lookup['user_id'] . ' AND
					lcu.contact_id = ' . (int)$lookup['contact_id']
			))
				$message = tt('element', 'error') . $config['spacer'] . tt('element', 'user_name') . ' != ' . tt('element', 'contact_name');
		}
	break;
	case 'contact':
		if ($action_content_1['accept_friend'] == 1) {
		if (!$action_content_1['user_name']) {
			$message = tt('element', 'error') . ' : ' . tt('element', 'accept_friend') . ' + ' . tt('element', 'user_name');
		} }
	break;
	case 'profile':
		if ($action_content_1['accept_usage_policy'] != 1)
			$message = tt('element', 'accept_usage_policy') . ' : ' . tt('element', 'field_missing');
	break;
}


# process_check_existence();
process_field_missing('action_content_1');
#die('passed field missing');
process_does_not_exist('action_content_1');
process_does_exist('action_content_1');

# maybe has action_content_2
process_field_missing('action_content_2');
process_does_not_exist('action_content_2');
process_does_exist('action_content_2');


$s1 = '/[' . preg_quote($config['reserved_prefix'] . $config['reserved_suffix'], '/') . ']/'; # regular expression
$s2 = '';

# edit process so always edited things
switch($type) {
	case 'teammate':
		# allow adding teams to <*user> 2012-04-26 vaskoiii
	break;
	case 'jargon':
		if (preg_match($s1, $action_content_1['tag_translation_name']))
			$s2 = 'tag_translation_name';
	break;
	case 'contact':
	case 'dialect':
	case 'group':
	case 'news':
	case 'team':
	case 'translation':
	case 'user':
	default:
		if (preg_match($s1, $action_content_1[$type . '_name']))
			$s2 = $type;
	break;
}
if ($s2)
	$message = tt('element', 'error') . ' : ' . tt('element', 'reserved_character') . ' : <> : ' . tt('element', $s2);

# todo make process_check_access()
if (!$message) {
	switch($type) {
		// ALLOW ONLY AUTHOR CHANGES...
		case 'note':
			if ($id && !get_db_single_value('
					t1.id
				FROM
					' . $prefix . $type . ' t1,
					' . $prefix . 'contact c
				WHERE
					t1.contact_id = c.id AND
					c.user_id = ' . (int)$login_user_id  . ' AND
					t1.id = ' . (int)$id
			))
				$message = tt('element', 'error_access_denied');
		break;
		case 'groupmate':
			if ($id && !get_db_single_value('
					g.id
				FROM
					' . $prefix . 'link_contact_group t1,
					' . $prefix . 'group g
				WHERE
					t1.group_id = g.id AND
					g.user_id = ' . (int)$login_user_id  . ' AND
					t1.id = ' . (int)$id
			))
				$message = tt('element', 'error_access_denied');
		break;

		case 'contact':
		case 'group':
		case 'meritopic':
		case 'feed':
		case 'feedback':
		case 'incident':
		case 'item':
		case 'news':
		case 'metail':
			if ($id && !get_db_single_value('
					id
				FROM
					' . $prefix . $type . '
				WHERE
					user_id = ' . (int)$login_user_id  . ' AND
					id = ' . (int)$id
			))
				$message = tt('element', 'error_access_denied');
		break;
		case 'offer':
		case 'rating':
		case 'transfer':
			if ($id && !get_db_single_value('
					id
				FROM
					' . $prefix . $type . '
				WHERE
					source_user_id = ' . (int)$login_user_id  . ' AND
					id = ' . (int)$id
			))
				$message = tt('element', 'error_access_denied');
		break;
		case 'team':
			// Anyone !!!! can make
			// Author only can modify
			if ($id && (!get_db_single_value('
					id
				FROM
					' . $prefix . 'team
				WHERE
					user_id = ' . (int)$login_user_id . ' AND
					id = ' . (int)($lookup['team_id'])
			)))
				$message = tt('element', 'error_access_denied');
		break;
		case 'teammate':
			// Author only can make
			// Author only can modify
			if (!get_db_single_value('
					id
				FROM
					' . $prefix . 'team
				WHERE
					user_id = ' . (int)$login_user_id . ' AND
					id = ' . to_sql($lookup['team_id'])
			))
				$message = tt('element', 'error_access_denied');
		break;
		case 'profile':
			if (!$login_user_id)
				$message = tt('element', 'error_access_denied');
		break;
	}
}

// PREVENT ORPHANS
// Have to also check on selection_action_process for [export] and [import]
// [export] [team_required_id] = $config['everybody_team_id'];
// [import] [team_required_id] = <author_only_team_id>;

if (!$message) {
	switch($type) {
		case 'item':
		case 'news':
		case 'metail':
			if (!get_db_single_value('
					user_id
				FROM
					' . $prefix . 'link_team_user
				WHERE
					team_id = ' . (int)$lookup['team_required_id'] . ' AND
					user_id = ' . (int)$login_user_id . ' AND
					active = 1
			'))
				$message = tt('element', 'error') . ' : ' . tt('element', 'not_on_team');
			
		break;
	}
}


// TRICKY ERROR CHECKING
if (!$message) {
	switch($type) {
		case 'minder':
			# todo error checking here sucks.
			if (!$lookup['minder_kind_id'])
				$message = 'error on minder 1';
			# todo tag_path should be the input instead of kind_name_id
			if (!$action_content_1['kind_name_id'])
				$message = 'error on minder 2';
			if (!get_db_single_value('
					id
				from
					' . $prefix . mysql_real_escape_string($action_content_1['minder_kind_name']) . '
				where
					id = ' . (int)$action_content_1['kind_name_id']
			))
				$message = 'error on minder 3';
			if (get_db_single_value('
					id
				from
					' . $prefix . mysql_real_escape_string($action_content_1['minder_kind_name']) . '
				where
					id = ' . (int)$action_content_1['kind_name_id'] . ' and
					user_id = ' . (int)$login_user_id
			))
				$message = 'error on minder 4';
		break;
		# case 'jargon': already not possible to change translation_kind_name 2012-03-10 vaskoiii
		case 'translation':
			# Do NOT allow changing kind_id once it is set
			if (get_gp('id'))
				if (!get_db_single_value('
						t.id
					FROM
						' . $prefix . 'translation t,
						' . $prefix . 'kind k
					WHERE
						k.id = t.kind_id AND
						k.name = ' . to_sql($action_content_1['translation_kind_name']) . ' AND
						t.id = ' . (int)get_gp('id') . '
				'))
					$message = tt('element', 'translation_kind_name') . ' : ' . tt('element', 'uneditable');
		break;
		case 'offer':
			if (get_gp('id'))
				$message = tt('element', 'error') . ' : ' . tt('element', 'uneditable');
	
		break;
		case 'invite':
			if (!preg_match("/.*@.*\..*/", $action_content_1['invite_email']) | preg_match("/(<|>)/", $action_content_1['invite_email'])) 
				$message = tt('element', 'invite_email') . ' : ' . tt('element', 'error_invalid_entry');
			if (get_gp('id'))
				$message = tt('element', 'error') . ' : ' . tt('element', 'uneditable');
		break;
		case 'team':
			// Make < and > illegal for teams... they will denote reserved keywords...
			// we should make an alias field for team name so that we can use all characters for team names...
			if (preg_match("/[<>]/", $action_content_1['team_name']))
				$message = tt('element', 'error'). ' : ' . tt('element', 'team_name') . ' : <>';
			elseif ($lookup['team_id'] == $config['everyone_team_id'])
				$message = tt('element', 'error') . ' : ' . tt('element', 'uneditable') . ' : ' . tt('element', 'team_name');
			elseif ($id == $config['everyone_team_id'])
				$message = tt('element', 'error') . ' : ' . tt('element', 'uneditable') . ' : ' . tt('element', 'team_name');

		break;
		case 'teammate':
			# todo would be easier if |root| was the owner of noneditable teams
			// [<|*|>] team
			// [<0-9a-zA-Z>] team
			if ($lookup['team_id'] == $config['everyone_team_id'])
				$message = tt('element', 'error') . ' : ' . tt('element', 'uneditable') . ' : ' . tt('element', 'team_name');
			elseif (preg_match("/[<>]/", $action_content_1['team_name'])) {
			if (!str_match('<*', $action_content_1['team_name'])) {
				$message = tt('element', 'error') . ' : ' . tt('element', 'uneditable') . ' : ' . tt('element', 'team_name');
			} }
		break;
		case 'location':
			if ($action_content_1['location_id'] == $config['main_location_id'])
				$message = tt('element', 'error') . ' : ' . tt('element', 'uneditable') . ' : ' . tt('element', 'location_name');
			elseif (!is_numeric($action_content_1['location_latitude']))
				$message = tt('element', 'location_latitude') . ' : ' . tt('element', 'error_not_numeric');
			elseif (!is_numeric($action_content_1['location_longitude']))
				$message = tt('element', 'location_longitude') . ' : ' . tt('element', 'error_not_numeric');
		break;
		case 'profile':
			if ($action_content_1['user_password_unencrypted'] != $action_content_1['user_password_unencrypted_again']) 
				$message = tt('element', 'user_password') . ' : ' . tt('element', 'error_mismatch');
			elseif (!preg_match("/.*@.*\..*/", $action_content_1['user_email']) | preg_match("/(<|>)/", $action_content_1['user_email'])) 
				$message = tt('element', 'user_email') . ' : ' . tt('element', 'error_invalid_entry');
			elseif (!$lookup['location_id'])
				$message = tt('element', 'location_id') . ' : ' . tt('element', 'error_does_not_exist');
			elseif (preg_match("/[^a-z0-9]/i",$action_content_1['login_user_name'])) {
				if ($action_content_1['login_user_name'] != '|root|')
					$message = tt('element', 'login_user_name') . ' : ' . tt('element', 'error_invalid_entry');
			}
		break;
	}
}

# DO IT
process_failure($message);

#  main update/insert

$sql = '';
include('v/1/inline/edit_sql.php'); # not sure the best place but file is too big 2012-02-26 vaskoiii
if (!empty($sql));
	$result = mysql_query($sql) or die(mysql_error());

$id # Prefer more "qualified" names ie) 'tag_id' instead of 'id' 2012-03-10 vaskoiii
	? $lookup['id'] = $lookup[$type . '_id'] = $id
	: $lookup['id'] = $lookup[$type . '_id'] = mysql_insert_id($config['mysql_resource']);

switch($type) {
	case 'category':
		$sql = '
			delete from
				' . $prefix . 'link_tag
			where
				tag_id = ' . (int)$id
		;
		$result = mysql_query($sql) or die(mysql_error());
		$sql = '
			insert into
				' . $prefix . 'link_tag
			set
				tag_id = ' . (int)$id
		;
		$result = mysql_query($sql) or die(mysql_error());
	break;
	case 'tag':
		#$sql = '
		#	UPDATE
		#		' . $prefix . 'tag
		#	SET
		#		server_id = ' . (int)$config['server_id'] . ',
		#		remote_id = ' . (int)$lookup['kind_name_id'] . '
		#	WHERE
		#		id = ' . (int)$lookup['tag_id']
		#;
		#$result = mysql_query($sql) or die(mysql_error());

		## update the index!
		#ts_recursive_tag($lookup['tag_id']);
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
		if ($lookup['default_boolean_id'] == 1) {
			# if setting to true we have to set stuff up later to make sure there is only 1 default.
			$sql = '
				UPDATE
					' . $prefix . 'translation
				SET
					`default` = 2
				WHERE
					kind_name_id = ' . (int)$i1 . ' AND
					kind_id = ' . (int)$i2 . ' AND
					dialect_id = ' . (int)$lookup['dialect_id'] . ' AND
					id != ' . (int)$lookup[$type . '_id']
			;
			$result = mysql_query($sql) or die(mysql_error());
		}
	break;
	case 'profile':
		$sql = '
			UPDATE '
				. $prefix . 'user_more um
			SET
				notify_offer_received = ' . (int)$lookup['notify_offer_received'] . ',
				notify_teammate_received = ' . (int)$lookup['notify_teammate_received'] . ',
				feature_lock = ' . (int)$lookup['feature_lock'] . ',
				feature_minnotify = ' . (int)$lookup['feature_minnotify'] . '
			WHERE id = ' . (int)$login_user_id
		;
		$result = mysql_query($sql) or die(mysql_error());

		$_SESSION['feature']['feature_lock'] = (int)$lookup['feature_lock'];
		$_SESSION['feature']['feature_minnotify'] = (int)$lookup['feature_minnotify'];
	break;
	case 'invite':
		# EMAIL (Do Minnotify Email Only)
		# Sending to users that are NOT yet part of the system...
		# If users wish to do a more special invitation it can be done with a personal email or personal contact and optionally refer to the invitation mailer.
		# Invitation is kept in a minimal display so it will be easier to maintain and will work on the widest range of sofware/hardware.
		# todo integrate these variables into $tsmail 2012-04-10 vaskoiii
		$email_sent = false;
		$email_subject = $config['title_prefix'] . 'Invite Link' . $config['spacer'] . 'Valid 1 Week ';
		$email_body = 'https://' . $_SERVER['HTTP_HOST'] . '/user_edit/?invite_user_id=' . to_url($login_user_id) . '&invite_password=' . to_url($lookup['invite_password']);

		$tsmail = array();
		$tsmail['data']['search']['response']['search_miscellaneous']['feature_minnotify'] = 1;
		$tsmail['data']['search']['response']['search_miscellaneous']['email_boundary'] = false;

		$email_sent = mail(
			$action_content_1['invite_email'],
			$email_subject,
			$email_body,
			get_tsmail_header($tsmail)
		);
		process_success(tt('element', 'transaction_complete') . ($email_sent ? ' : ' . tt('element', 'email_sent') : ''));
	break;
	case 'feed':
		// dont send email unless it is the first add or a request to resend
		#todo test this case 2012-04-10 vaskoiii
		if (!get_gp('id')) {
			$email_sent = false;
			#$email_array = get_user_email_array($login_user_id);
			$tsmail = array();
			$tsmail['search']['response']['search_miscellaneous'] = get_user_email_array($login_user_id);
			$email_subject = $config['title_prefix'] . 'Feed Recover Link: ' . ucfirst(str_replace('_list', '', $lookup['page_name']));
			$email_body = 'https://' . $_SERVER['HTTP_HOST'] . '/feed_atom/?' . 'set_feed_id=' . (int)$lookup['feed_id'] . '&set_feed_key=' . to_url($lookup['feed_key']);
			$email_sent = mail(
				$tsmail['search']['response']['search_miscellaneous']['email'],
				$email_subject,
				$email_body,
				get_tsmail_header($tsmail)
			);
		}
	break;
	case 'teammate':
		#todo test this case 2012-04-10 vaskoiii
		# also in function/process.php
		$email_sent = false;
		if (0) # todo uncomment when the email link makes sense 2012-05-01 vaskoiii
		if (!$lookup['same_data_update'] && $lookup['xor_user_id'] != $login_user_id) {
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

				// SEND EMAIL!
				$to = $tsmail['data']['search']['response']['search_miscellaneous']['email'];
				$subject = get_tsmail_subject($tsmail);
				$body = get_tsmail_body($tsmail);
				$header = get_tsmail_header($tsmail);
				$email_sent = mail( $to, $subject, $body, $header);
			}
		}
	break;
	case 'rating':
	case 'transfer':
		# disable notification email (recipient can not always see it)
		index_entry(
			$type,
			$lookup[$type . '_id'],
			$login_user_id,
			$lookup['user_id'],
			'index'
		);
	break;
	case 'offer':
		index_entry(
			$type,
			$lookup[$type . '_id'],
			$login_user_id,
			$lookup['user_id'],
			'active'
		);
		index_entry(
			$type,
			$lookup[$type . '_id'],
			$login_user_id,
			$lookup['user_id'],
			'index'
		);

		// EMAIL (Full/Minimal Notification)
		# todo form a data structure similar to the normal one used for the logged in user
		$tsmail = array();
		$tsmail['x'] = array();
		$tsmail['_SESSION'] = array();
		$tsmail['data'] = array();
		$tsmail['key'] = array();
		$tsmail['translation'] = array();

		$tsmail['data']['list'] = array();

		$tsmail['_SESSION']['login']['login_user_id'] = $lookup['user_id'];
		$tsmail['data']['search']['response']['search_miscellaneous'] = get_user_email_array($lookup['user_id'], 'notify_' . $type . '_received');
		$tsmail['data']['search']['response']['search_miscellaneous']['id'] = $lookup[$type . '_id'];
		$tsmail['data']['search']['response']['search_miscellaneous']['email_boundary'] = get_email_boundary();
		$tsmail['x']['load']['list']['type'] = $type;

		$email_sent = false;
		if (!$lookup['same_data_update'] && $lookup['user_id'] != $login_user_id) {
			if ($tsmail['data']['search']['response']['search_miscellaneous']['notify_' . $type . '_received']) {
				switch($type) {
					case 'offer':
					# case 'transfer':
						start_engine(
							$tsmail['data']['list'],
							#$type,
							$tsmail['x']['load']['list']['type'],
							$tsmail['_SESSION']['login']['login_user_id'],
							#array($lookup[$type . '_id'])
							array($tsmail['data']['search']['response']['search_miscellaneous']['id'])
						);
						listing_key_translation(
							$tsmail['key'],
							$tsmail['translation'],
							$tsmail['data']['list'],
							$tsmail['x']['load']['list']['type'],
							$tsmail['_SESSION']['login']['login_user_id']
						);
						# already added by listing_key_translation();
						#add_key('user', $tsmail['_SESSION']['login']['login_user_id'], 'contact_name', $tsmail['key']);
						#add_key('user', $tsmail['_SESSION']['login']['login_user_id'], 'contact_id', $tsmail['key']);
					break;
				}

				add_translation('element', 'add_more', 'translation_name', $tsmail['translation']);
				add_translation('element', 'more', 'translation_name', $tsmail['translation']);
				add_translation('element', 'edit', 'translation_name', $tsmail['translation']);
				add_translation('element', 'view', 'translation_name', $tsmail['translation']);
				add_translation('page', $tsmail['x']['load']['list']['type'] . '_list', 'translation_name', $tsmail['translation']);

				do_key($tsmail['key'], $tsmail['translation'], $_SESSION['dialect']['dialect_id'], $tsmail['_SESSION']['login']['login_user_id']);
				# todo separate email creation into engine and template like the rest of the site (do_translation() may happen too often) 2012-04-06 vaskoii
				do_translation($tsmail['key'], $tsmail['translation'], $_SESSION['dialect']['dialect_id'], $tsmail['_SESSION']['login']['login_user_id']);

				# SEND EMAIL!
				$to = $tsmail['data']['search']['response']['search_miscellaneous']['email'];
				$subject = get_tsmail_subject($tsmail);
				$body = get_tsmail_body($tsmail);
				$header = get_tsmail_header($tsmail);
				$email_sent = mail( $to, $subject, $body, $header);
			}
		}
	break;
	case 'contact':
		$sql = '
			DELETE FROM
				' . $prefix . 'link_contact_user
			WHERE
				contact_id = ' . to_sql($lookup['contact_id'])
		;
		$result = mysql_query($sql) or die(mysql_error());
		if ($lookup['user_id']) {
			$sql = '
				INSERT INTO
					' . $prefix . 'link_contact_user
				SET
					user_id = ' . (int)$lookup['user_id'] . ',
					contact_id = ' . to_sql($lookup['contact_id'])
			;
			$result = mysql_query($sql) or die(mysql_error());
		}

		# todo make function auto_friend() 2012-04-05 vaskoiii
		if ($action_content_1['accept_friend'] == 1) {
			# only old users will need most of this logic as new users now are added to this team automatically.
			# todo some logic could be eliminated if we can gaurantee that all users have the *user team 2012-04-06 vaskoiii
			$s1 = '<*' . $_SESSION['login']['login_user_name'] .  '>';

			$i1 = false;
			# check if team exists
			$i1 = get_db_single_value('
					id
				from
					' . $prefix . 'team
				where
					name = ' . to_sql($s1)
			, 0);

			# cant have inactive *user teams so maybe ok...
			# =)

			# if not add *user team
			if (!$i1) {
				$sql = '
					INSERT INTO
						' . $prefix . 'team 
					SET
						user_id = ' . (int)$login_user_id . ',
						name = ' . to_sql($s1) . ',
						description = ' . to_sql($s1) . ',
						modified = CURRENT_TIMESTAMP,
						active = 1
				';
				$result = mysql_query($sql) or debug_die(mysql_error());
				$i1 = mysql_insert_id($config['mysql_resource']);

				# also add YOU to the team or you wont be able to see items posted to this team
				$sql = '
					INSERT INTO
						' . $prefix . 'link_team_user
					SET
						team_id = ' . (int)$i1 . ',
						user_id = ' . (int)$login_user_id . ',
						modified = CURRENT_TIMESTAMP,
						active = 1
				';
				$result = mysql_query($sql) or debug_die(mysql_error());

				# also add this team to minder
				# 16 is the hardcode for teams
				$sql = '
					INSERT INTO
						' . $config['mysql']['prefix'] . 'minder
					SET
						user_id = ' . (int)$login_user_id . ',
						kind_id = 16,
						kind_name_id = ' . (int)$i1 . ',
						modified = CURRENT_TIMESTAMP,
						active = 1
				';
				$result = mysql_query($sql) or debug_die(mysql_error());
			}

			# check if user is on team
			$i2 = false;
			$i2 = get_db_single_value('
					id
				from
					' . $prefix . 'link_team_user
				where
					team_id = ' . (int)$i1 . ' and
					user_id = ' . (int)$lookup['user_id']
			, 0);

			# if not add to team
			if (!$i2) {
				$sql = '
					INSERT INTO
						' . $prefix . 'link_team_user
					SET
						team_id = ' . (int)$i1 . ',
						user_id = ' . (int)$lookup['user_id'] . ',
						modified = CURRENT_TIMESTAMP,
						active = 1
				';
				$result = mysql_query($sql) or debug_die(mysql_error());
			}
			else { # user is on team
			# activate teammate just in case was inactive
				$sql = '
					update
						' . $prefix . 'link_team_user
					set
						active = 1
					where
						user_id = ' . (int)$lookup['user_id'] . ',
						team_id = ' . (int)$i1 . '
					limit
						1
				';
				$result = mysql_query($sql) or debug_die(mysql_error());
			}

		}
		else {
			# check if user is on team

			# if is then remove from team
		}
	break;
}

process_success(tt('element', 'transaction_complete') . ($email_sent ? ' : ' . tt('element', 'email_sent') : ''));
