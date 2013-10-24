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

# Contents/Description: New user registration standalone
# Known Issues: Included from within edit_process.php

$process = array(); # Send to process
$interpret = array(); # Interpreted from $process
$interpret['lookup'] = array();
$interpret['message'] = array();

# get initial values
$process['form_info'] = get_action_header_1();
foreach($process['form_info'] as $k1 => $v1)
	$process['form_info'][$k1] = get_gp($k1);
$process['action_content_1']  = get_action_content_1($process['form_info']['type'], 'edit');
if ($process) {
foreach ($process as $k1 => $v1) {
if ($v1) {
foreach ($v1 as $k2 => $v2) {
switch ($k2) {
	case 'accept_usage_policy':
		$process[$k1][$k2] = get_boolean_gp('accept_usage_policy');
	break;
	default:
		$process[$k1][$k2] = get_gp($k2);
	break;
} } } } }

# shortcut
$prefix = & $config['mysql']['prefix'];
$lookup = & $interpret['lookup'];
$action_content_1 = & $process['action_content_1'];
$interpret['message'] = ''; # initialize to string
$message = & $interpret['message'];


# translation
process_data_translation('action_content_1');
if (empty($lookup['user_password']) && !empty($action_content_1['user_password_unencrypted_again']))
	$lookup['user_password'] = md5($action_content_1['user_password_unencrypted_again']); # super simple password encryption

# custom translation for invitation
$lookup['invite_id'] = get_db_single_value('
		id
	FROM
		' . $prefix . 'invite
	WHERE
		id = ' . (int)$action_content_1['invite_id']
, 0);

$lookup['invite_inactive'] = 1;
$lookup['invite_used'] = 1;
$lookup['invite_expired'] = 1;
$lookup['invite_invalid'] = 1; # 2 types
	$lookup['invite_unauthenticated'] = 1; # NOT [peer_authenticated]
	$lookup['invite_mismatched'] = 1; # NOT invite user/password mismatch

if ($lookup['invite_id']) {
	if (get_db_single_value('
			1
		FROM
			' . $prefix . 'invite
		WHERE
			id = ' . $lookup['invite_id'] . ' AND
			active = 1
	', 0))
		$lookup['invite_inactive'] = 2;
	if (get_db_single_value('
			1
		FROM
			' . $prefix . 'invite
		WHERE
			id = ' . $lookup['invite_id'] . ' AND
			used = 2
	', 0))
		$lookup['invite_used'] = 2;
	if (get_db_single_value('
			1
		FROM
			' . $prefix . 'invite
		WHERE
			id = ' . (int)$lookup['invite_id'] . ' AND
			user_id = ' . (int)$lookup['invite_user_id'] . ' AND
			password = ' . to_sql($action_content_1['invite_password']) . '
	', 0)) {
		$lookup['invite_invalid'] = 2;
		$lookup['invite_mismatched'] = 2;
	}
	if (get_db_single_value('
			id
		FROM
			' . $prefix . 'invite
		WHERE
			id = ' . (int)$lookup['invite_id'] . ' AND
			modified >= DATE_SUB(CURRENT_TIMESTAMP, INTERVAL 7 DAY)
	', 0))
		$lookup['invite_expired'] = 2;
}
# peer_authenticated (alternate signup method)
if ($_SESSION['login']['login_user_id']) {
if ($_SESSION['login']['login_user_name'] == $action_content_1['invite_user_name']) {
if ($action_content_1['invite_password'] == 'peer_authenticated') { # hardcode [peer_authenticated]
	$lookup['invite_invalid'] = 2;
	$lookup['invite_unauthenticated'] = 2;
} } }

# error

# more relevant error messages should be at the top
# dont make users waste time fixing error messages 1 by 1 if they wont even be able to register in the end
if (!$message) {
	# missing field
	if (!$action_content_1['invite_id'] && $lookup['invite_unauthenticated'] == 1)
		$message = tt('element', 'invite_id') . ' : ' . tt('element', 'field_missing');
	elseif (!$action_content_1['invite_user_name'])
		$message = tt('element', 'invite_user_name') . ' : ' . tt('element', 'field_missing');
	elseif (!$action_content_1['invite_password'])
		$message = tt('element', 'invite_password') . ' : ' . tt('element', 'field_missing');
}
if (!$message) {
if ($lookup['invite_unauthenticated'] == 1) {
	# invite errors
	if (!$lookup['invite_id'])
		$message = tt('element', 'invite_id') . ' : ' . tt('element', 'error_does_not_exist');
	elseif ($lookup['invite_inactive'] == 1)
		$message = tt('element', 'invite_id') . ' : ' . tt('element', 'error_invite_inactive');
	elseif ($lookup['invite_used'] == 1)
		$message = tt('element', 'invite') . ' : ' . tt('element', 'error_used_entry');
	elseif ($lookup['invite_expired'] == 1)
		$message = tt('element', 'invite') . ' : ' . tt('element', 'error_expired_entry');
} }

if (!$message) {
	if ($lookup['invite_invalid'] == 1) {
		$message = tt('element', 'invite') . ' : ' . tt('element', 'error_invalid_entry');
		if ($lookup['invite_unauthenticated'] == 1)
			$message .= ' | ' . tt('element', 'error_unauthenticated_entry');
		if ($lookup['invite_mismatched'] == 1)
			$message .= ' | ' . tt('element', 'error_mismatched_entry');
	}
}



# functions with global variable scope
process_field_missing('action_content_1');
process_does_not_exist('action_content_1');
process_does_exist('action_content_1');

# check these difficult cases after
if (!$message) {
	if (preg_match("/[^a-z0-9]/i",$action_content_1['login_user_name']))
		$message = tt('element', 'login_user_name') . ' : ' . tt('element', 'error_invalid_entry');
	elseif (preg_match("/[<>]/", $action_content_1['login_user_name']))
		$message = tt('element', 'error') . ' : ' . tt('element', 'login_user_name') . ' : ' . tt('element', 'reserved_character') . ' : <>';
	elseif ($action_content_1['user_password_unencrypted'] != $action_content_1['user_password_unencrypted_again']) 
		$message = tt('element', 'user_password') . ' : ' . tt('element', 'error_mismatch');
	elseif (!preg_match("/.*@.*\..*/", $action_content_1['user_email']) | preg_match("/(<|>)/", $action_content_1['user_email'])) 
		$message = tt('element', 'user_email') . ' : ' . tt('element', 'error_invalid_entry');
}

# failure
process_failure($message);

# create an invite on the fly
# note: an extra invite will be generated if the random password generated for an invite = [peer_authenticated]
# todo: make a check when generating the password that it is not equal to peer_authenticated
# only 8 character passwords or so are currently generated so it will never equal 'peer_authenticated'
if ($lookup['invite_unauthenticated'] == 2) {
	$sql = '
		INSERT INTO
			' . $config['mysql']['prefix'] . 'invite
		SET
			user_id = ' . (int)$_SESSION['login']['login_user_id'] . ',
			email = ' . to_sql($action_content_1['user_email']) . ',
			modified = CURRENT_TIMESTAMP,
			password = "peer_authenticated",
			used = 1,
			active = 1
	';
	$result = mysql_query($sql) or die(mysql_error());
	$lookup['invite_id'] = mysql_insert_id($config['mysql_resource']);
}

# main_add_edit
$sql = '
	INSERT INTO
		' . $config['mysql']['prefix'] . 'user
	SET
		name = ' . to_sql($action_content_1['login_user_name']) . ',
		location_id = ' . (int)$config['main_location_id'] . ',
		password = ' . to_sql($lookup['user_password']) . ', 
		email = ' . to_sql($action_content_1['user_email']) . ',
		modified = CURRENT_TIMESTAMP,
		active = 1
';
$result = mysql_query($sql) or die(mysql_error());

$lookup['login_user_id'] = mysql_insert_id($config['mysql_resource']);

# MORE (AFTER INITIAL INSERT)
$lookup['user_more_id'] = $lookup['login_user_id'];

# DEFAULTS
$_SESSION['feature']['feature_lock'] = 2;
$_SESSION['feature']['feature_minnotify'] = 2;

$sql = '
	INSERT INTO' . '
		' . $config['mysql']['prefix'] . 'user_more
	SET
		id = ' . (int)$lookup['user_more_id'] . ',
		notify_offer_received = 1,
		notify_teammate_received = 1,
		feature_lock = ' . (int)$_SESSION['feature']['feature_lock'] . ',
		feature_minnotify = ' . (int)$_SESSION['feature']['feature_minnotify'] . '
';
$result = mysql_query($sql) or die(mysql_error());

# INVITE
$sql = '
	UPDATE
		' . $config['mysql']['prefix'] . 'invite
	SET
		modified = CURRENT_TIMESTAMP,
		used = 1
	WHERE
		id = ' . (int)$lookup['invite_id'] . '
	LIMIT
		1
';
$result = mysql_query($sql) or die(mysql_error());

# INVITED
$sql = '
	INSERT INTO
		' . $config['mysql']['prefix'] . 'invited
	SET
		invite_id = ' . (int)$lookup['invite_id'] . ',
		source_user_id = ' . $lookup['invite_user_id'] . ',
		destination_user_id = ' . $lookup['login_user_id'] . ',
		modified = CURRENT_TIMESTAMP,
		active = 1
';
$result = mysql_query($sql) or die(mysql_error());
$lookup['invited_id'] = mysql_insert_id($config['mysql_resource']);

index_entry(
	'invited',
	$lookup['invited_id'],
	$lookup['invite_user_id'],
	$lookup['login_user_id']
);

# MIND - tag (kind_id HARDCODE)
$sql = '
	INSERT INTO
		' . $config['mysql']['prefix'] . 'minder
	SET
		user_id = ' . (int)$lookup['login_user_id'] . ',
		kind_id = 11,
		kind_name_id = ' . (int)$config['root_tag_id'] . ',
		modified = CURRENT_TIMESTAMP,
		active = 1
';
$result = mysql_query($sql) or die(mysql_error());

# MIND - location (kind_id HARDCODE)
$sql = '
	INSERT INTO
		' . $config['mysql']['prefix'] . 'minder
	SET
		user_id = ' . (int)$lookup['login_user_id'] . ',
		kind_id = 15,
		kind_name_id = ' . (int)$config['main_location_id'] . ',
		modified = CURRENT_TIMESTAMP,
		active = 1
';
$result = mysql_query($sql) or die(mysql_error());

# MIND - <*> team (kind_id HARDCODE)
$sql = '
	INSERT INTO
		' . $config['mysql']['prefix'] . 'minder
	SET
		user_id = ' . (int)$lookup['login_user_id'] . ',
		kind_id = 16,
		kind_name_id = ' . (int)$config['everyone_team_id'] . ',
		modified = CURRENT_TIMESTAMP,
		active = 1
';
$result = mysql_query($sql) or die(mysql_error());

# OFFER
$sql = '
	INSERT INTO
		' . $config['mysql']['prefix'] . 'offer
	SET
		source_user_id = ' . (int)$config['autocreation_user_id'] . ',
		destination_user_id = ' . (int)$lookup['login_user_id'] . ',
		modified = CURRENT_TIMESTAMP,
		name = ' . to_sql(tt('element', 'welcome')) . ',
		description = ' . to_sql(tt('element', 'welcome_to_trade_and_share')) . '
';
$result = mysql_query($sql) or die(mysql_error());
$lookup['offer_id'] = mysql_insert_id($config['mysql_resource']);

index_entry(
	'offer',
	$lookup['offer_id'],
	$config['autocreation_user_id'],
	$lookup['login_user_id'],
	'active'
);

index_entry(
	'offer',
	$lookup['offer_id'],
	$config['autocreation_user_id'],
	$lookup['login_user_id']
);

# <*> team
$sql = '
	INSERT INTO
		' . $config['mysql']['prefix'] . 'link_team_user
	SET
		user_id = ' . (int)$lookup['login_user_id'] . ',
		team_id = ' . (int)$config['everyone_team_id'] . ',
		modified = CURRENT_TIMESTAMP,
		active = 1
';
$result = mysql_query($sql) or die(mysql_error());
# <*author> team
$sql = '
	INSERT INTO
		' . $config['mysql']['prefix'] . 'team
	SET
		user_id = ' . (int)$lookup['login_user_id'] . ',
		modified = CURRENT_TIMESTAMP,
		name = ' . to_sql('<*' . $action_content_1['login_user_name'] . '>') . ',
		description = ' . to_sql('<*' . $action_content_1['login_user_name'] . '>') . ',
		active = 1
';
$result = mysql_query($sql) or die(mysql_error());
$lookup['everyone_author_team_id'] = mysql_insert_id();
$sql = '
	INSERT INTO
		' . $config['mysql']['prefix'] . 'link_team_user
	SET
		user_id = ' . (int)$lookup['login_user_id'] . ',
		team_id = ' . (int)$lookup['everyone_author_team_id'] . ',
		modified = CURRENT_TIMESTAMP,
		active = 1
';
$result = mysql_query($sql) or die(mysql_error());
$sql = '
	INSERT INTO
		' . $config['mysql']['prefix'] . 'minder
	SET
		user_id = ' . (int)$lookup['login_user_id'] . ',
		kind_id = 16,
		kind_name_id = ' . (int)$lookup['everyone_author_team_id'] . ',
		modified = CURRENT_TIMESTAMP,
		active = 1
';
$result = mysql_query($sql) or die(mysql_error());
# <author> team
$sql = '
	INSERT INTO
		' . $config['mysql']['prefix'] . 'team
	SET
		user_id = ' . (int)$lookup['login_user_id'] . ',
		modified = CURRENT_TIMESTAMP,
		name = ' . to_sql('<' . $action_content_1['login_user_name'] . '>') . ',
		description = ' . to_sql('<' . $action_content_1['login_user_name'] . '>') . ',
		active = 1
';
$result = mysql_query($sql) or die(mysql_error());
$lookup['author_only_team_id'] = mysql_insert_id();
$sql = '
	INSERT INTO
		' . $config['mysql']['prefix'] . 'link_team_user
	SET
		user_id = ' . (int)$lookup['login_user_id'] . ',
		team_id = ' . (int)$lookup['author_only_team_id'] . ',
		modified = CURRENT_TIMESTAMP,
		active = 1
';
$result = mysql_query($sql) or die(mysql_error());
$sql = '
	INSERT INTO
		' . $config['mysql']['prefix'] . 'minder
	SET
		user_id = ' . (int)$lookup['login_user_id'] . ',
		kind_id = 16,
		kind_name_id = ' . (int)$lookup['author_only_team_id'] . ',
		modified = CURRENT_TIMESTAMP,
		active = 1
';
$result = mysql_query($sql) or die(mysql_error());

# LOGIN
$sql = '
	INSERT INTO
		' . $config['mysql']['prefix'] . 'login
	SET
		user_id = ' . (int)$lookup['login_user_id'] . ',
		`when` = CURRENT_TIMESTAMP
';
$result = mysql_query($sql) or die(mysql_error());

$_SESSION['login']['login_user_id'] = $lookup['login_user_id'];
$_SESSION['login']['login_user_name'] = $action_content_1['login_user_name'];

process_success(tt('element', 'transaction_complete'), $config['start_page']);
exit;
