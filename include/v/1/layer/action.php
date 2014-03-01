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

# Contents/Description: Prototype for setting up the new edit content box. In the future will be a floating box that can be manipulated and reset with javascript...

# Note: action is going to be the umbrella for:
# - set
# - recover
# - edit
# and any other "quick action" box that displays focused data
# Warning: even though this file has the generic name of action_content.php it is only so that we do not have to create several different files. However, use the naming conventions as if we were using several different files depending on what is being worked on! ie) edit_content.php, set_content.php, recover_content.php, etc...

add_translation('boolean', 'false');
add_translation('boolean', 'true');
add_translation('element', 'add');
add_translation('element', 'add_' . get_child_listing_type($x['load']['action']['type']));
add_translation('element', 'add_feed');
add_translation('element', 'add_feedback');
add_translation('element', 'add_more');
add_translation('element', 'add_note');
add_translation('element', 'asearch_off');
add_translation('element', 'asearch_on');
add_translation('element', 'edit');
add_translation('element', 'error');
add_translation('element', 'extra');
add_translation('element', 'find_' . $x['load']['action_type']);
add_translation('element', 'find_' . get_gp('child'));
add_translation('element', 'less');
add_translation('element', 'more');
add_translation('element', 'new_form');
add_translation('element', 'recover');
add_translation('element', 'reedit');
add_translation('element', 'search');
add_translation('element', 'search_' . $x['load']['action']['type']);
add_translation('element', 'send');
add_translation('element', 'send_more');
add_translation('element', 'set');
add_translation('element', 'set_again');
add_translation('element', 'submit');
add_translation('element', 'translate');
add_translation('element', 'unset');
add_translation('kind', 'tag');

if ($x['part'][0])
	add_translation('element', $x['part'][0] . '_uid');
if ($x['load']['action']['type']) {
	add_translation('page', $x['load']['action']['type'] . '_list');
	add_translation('page', $x['load']['action']['type'] . '_edit');
}

$data['action']['inquiry']['todo'] = 'inquiry = "in" & response = "out"';

$s1 = $x['part'][1];
$s2 = $x['part'][0];
if ($x['load']['action']['type'])
	$s1 = $x['load']['action']['type'];
if ($x['load']['action']['name'])
	$s2 = $x['load']['action']['name'];

# shortcut (be careful it is global)
$action_content_1 = & $data['action']['response']['action_content_1'];
$action_content_2 = & $data['action']['response']['action_content_2'];

$action_content_1 = get_action_content_1($s1, $s2);
$action_content_2 = get_action_content_2($s1, $s2);

$b1 = 1;

# GET DATABASE VALUES
if ($b1 == 1) {
if (!$_SESSION['interpret']['failure']) {
if (!$_SESSION['process']['form_info']['load'] == 'action') {
if (
	$x['load']['action']['id']
	|| (get_gp('action_tag_id')) # mod to accomodate import/export/judge 2012-12-10 vaskoiii
) {
	$b1 = 2;
	if (get_gp('action_tag_id')) {
		start_engine($data['action'], 'tag', $_SESSION['login']['login_user_id'], array(get_gp('action_tag_id')), 'view');
	}
	else
		start_engine($data['action'], $x['load']['action']['type'], $_SESSION['login']['login_user_id'], array($x['load']['action']['id']), 'view');

	listing_key_translation($key, $translation, $data['action'], get_gp('edit_type'), $_SESSION['login']['login_user_id']);

	$action_listing = & $data['action']['result']['listing'][0];

	if (!empty($data['action']['response']))
	foreach ($data['action']['response'] as $k1 => $v1)
	foreach ($v1 as $k2 => $v2)
		$data['action']['response'][$k1][$k2] = $action_listing[$k2];
		# todo we have to figure out how to fix this guy
		# for now leave the the following intact so we don't have to change variable names too ridiculously ATM:
		# $data['action']['search']
		# $data['action']['result']

	if ($action_listing['tag_id']) { 
		add_key('tag', $action_listing['tag_id'], 'tag_name', $key);
		# may be better to just populate the fieled right away in which case we dont need the above line though it is much more wordy? 2012-03-27 vaskoiii
		$action_content_1['tag_translation_name'] = get_immediate_tag_translation_name($action_listing['tag_id'], $_SESSION['dialect']['dialect_id']);
	}

	# get the rest of contact_user_mixed
	$action_content_1['user_name'] = $action_listing['user_name'];
	if ($action_content_1['user_name'] == '') {
		$action_content_1['user_name'] = $action_listing['destination_user_name'];
	}
	
	if ($action_content_1['contact_name'])
		add_key('contact', $action_listing['contact_id'], 'user_name', $key);
} } } }

# Dont propogate user_name on transfer 2013-05-06 vaskoiii
# todo: integrate this more smoothly
if (get_gp('action_tag_id') && $x['part']['0'] == 'transfer') {
	# edit doesnt need to worry about lock_ values
	$action_content_1['user_name'] = '';
	$action_content_1['contact_name'] = '';
	$action_content_1['contact_user_mixed'] = '';
	$action_listing['user_name'] = '';
	$action_listing['user_id'] = '';
}

if ($b1 == 1) {
if ($x['page']['name'] == 'profile_edit') {
	$sql = '
			u.name as login_user_name,
			"" as user_password_unencrypted,
			"" as user_password_unencrypted_again,
			u.email as user_email,
			lo.name as location_name,
			um.notify_offer_received,
			um.notify_teammate_received,
			um.feature_lock,
			um.feature_minnotify' .
			($_SESSION['process']['failure'] == 1 
				? ', ' . get_boolean_gp('accept_usage_policy') . ' as accept_usage_policy'
				: ', 1 as accept_usage_policy'
			) . '
		FROM
			' . $config['mysql']['prefix'] . 'user u,
			' . $config['mysql']['prefix'] . 'user_more um,
			' . $config['mysql']['prefix'] . 'location lo
		WHERE
			u.id = um.id AND
			u.location_id = lo.id AND
			u.id = ' . (int)$_SESSION['login']['login_user_id'] 
	;
	if (isset($sql)) {
		if ($config['debug'] == 1)
			echo '<hr />SELECT ' . $sql . ' LIMIT 1<hr />';
		$result = mysql_query('SELECT ' . $sql . ' LIMIT 1') or die(mysql_error());
		while ($row = mysql_fetch_assoc($result))
			$data['action']['response']['action_content_1'] = $row;
	}
} }

if ($b1 == 1) {
	# dont do this if fast or quick is set (these reuse action_content_x) thinking about renaming to just (content_x)
	# 2014-03-01 vaskoiii
	if (!(
		isset($_SESSION['process']['form_info']['load']) && 
		$_SESSION['process']['form_info']['load'] == 'fast'
	)) {
		load_response('action_content_1', $action_content_1, $_SESSION['login']['login_user_id']);
		load_response('action_content_2', $action_content_2, $_SESSION['login']['login_user_id']);
	}
}

# hack 2012-04-09 vaskoiii
# todo integrate with load_response
if ($x['load']['action']['type'] == 'feed') {
	if (get_gp('page_name'))
	$action_content_2['page_name'] = get_gp('page_name');
	if (get_gp('feed_query'))
	$action_content_2['feed_query'] = get_gp('feed_query');
}
elseif ($x['load']['action']['type'] == 'user') {
	if (!$action_content_1['invite_password']) {
	if ($_SESSION['login']['login_user_id']) {
		$action_content_1['invite_password'] = 'peer_authenticated';
	} }
}

# INJECT AUTO VALUES (special invitation case)
if ($_SESSION['login']['login_user_name']) {
if ($x['load']['action']['name'] == 'edit') {
if ($x['load']['action']['type'] == 'user') {
	if (!$action_content_1['invite_user_name'])
		$action_content_1['invite_user_name'] = $_SESSION['login']['login_user_name'];
	if (!$action_content_1['invite_user_name'])
		$action_content_1['invite_password'] = 'peer_authenticated';
} } }

# needed to get kind_name_name
if (!empty($action_content_1)) {
foreach($action_content_1 as $k1 => $v1) {
switch($s1 . '_' . $s2) {
	# $_SESSION set (unlinkable in url)
	case 'lock_set':
		if ($_SESSION['proces']['interpret']['failure'] != 1)
			$action_content_1[$k1] = $_SESSION['lock'][$k1];
	break;
	case 'jargon_edit':
	case 'translation_edit':
		if ($_SESSION['process']['interpret']['failure'] != 1) {
		if ($k1 == 'kind_name_name') {
			if ($action_listing['kind_name_id'])
				add_key($action_listing['kind_name'], $action_listing['kind_name_id' ], 'translation_name', $key);
		 } }
		else {
			$action_content_1[$k1] = $_SESSION['process']['action_content_1'][$k1];
		}
	break;
} } }

contact_user_mixed_combine($action_content_1, get_gp('action_user_id'), get_gp('action_contact_id'), $_SESSION['login']['login_user_id']);

# is friend? prepare 2013-04-20 vaskoiii
set_accept_friend_from_listing_and_container(
	$data['action']['result']['listing'][0],
	$action_content_1,
	$_SESSION['login']['login_user_id']
);

# TRANSLATION
# todo check if this is already handled by load_repsonse
foreach ($data['action']['response'] as $k1 => $v1)
foreach ($v1 as $k2 => $v2) {
	add_option($k2);
	add_translation('element', $k2);
	# hardcoded defaults
	if (!$_SESSION['process']['failure']) {
	if (!$x['load']['action']['id']) {
	switch($k2) {
		case 'parent_tag_path':
		case 'parent_tag_name':
			if (!$v2)
				$data['action']['response'][$k1][$k2] = '<|!|>';
		break;
		case 'dialect_name':
			 if (!$v2)
				$data['action']['response'][$k1][$k2] =  $_SESSION['dialect']['dialect_name'];
		break;
		case 'team_required_name':
			 if (!$v2)
				$data['action']['response'][$k1][$k2] =  '<|*|>';
		break;
		case 'location_name':
			if (!$v2)
				$data['action']['response'][$k1][$k2] = get_db_single_value('
					l.name 
				FROM 
					' . $config['mysql']['prefix'] . 'location l, 
					' . $config['mysql']['prefix'] . 'user u 
				WHERE 
					u.location_id = l.id AND 
					u.id = ' . (int)$_SESSION['login']['login_user_id']
			);
		break;
	} } }
}

$data['action']['response']['action_footer_1'] = get_action_footer_1($x['load']['action']['type'], $x['load']['action']['name']);
$data['action']['response']['action_footer_2'] = get_action_footer_2($x['load']['action']['type'], $x['load']['action']['name']);
