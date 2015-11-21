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
# Note: motion is going to be the umbrella for:
# - set
# - recover
# - edit
# - select << obolete? 2012-02-06
# and any other "quick motion" box that displays focused data
# Warning: even though this file has the generic name of motion_content.php it is only so that we do not have to create several different files. However, use the naming conventions as if we were using several different files depending on what is being worked on! ie) edit_content.php, set_content.php, recover_content.php, etc...

add_translation('boolean', 'false');
add_translation('boolean', 'true');
add_translation('element', 'add');
add_translation('element', 'add_' . get_child_listing_type($x['load']['motion']['type']));
add_translation('element', 'add_feed');
add_translation('element', 'add_feedback');
add_translation('element', 'add_more');
add_translation('element', 'add_note');
add_translation('element', 'asearch_off');
add_translation('element', 'asearch_on');
add_translation('element', 'edit');
add_translation('element', 'error');
add_translation('element', 'extra');
add_translation('element', 'find_' . $x['load']['motion_type']);
add_translation('element', 'find_' . get_gp('child'));
add_translation('element', 'less');
add_translation('element', 'more');
add_translation('element', 'recover');
add_translation('element', 'reedit');
add_translation('element', 'search');
add_translation('element', 'search_' . $x['load']['motion']['type']);
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
if ($x['load']['motion']['type']) {
	add_translation('page', $x['load']['motion']['type'] . '_list');
	add_translation('page', $x['load']['motion']['type'] . '_edit');
}

$data['motion']['inquiry']['todo'] = 'inquiry = "in" & response = "out"';

$s1 = $x['part'][1];
$s2 = $x['part'][0];
if ($x['load']['motion']['type'])
	$s1 = $x['load']['motion']['type'];
if ($x['load']['motion']['name'])
	$s2 = $x['load']['motion']['name'];

# shortcut (be careful it is global)
$motion_content_1 = & $data['motion']['response']['motion_content_1'];
$motion_content_2 = & $data['motion']['response']['motion_content_2'];

$motion_content_1 = get_action_content_1($s1, $s2);
$motion_content_2 = get_action_content_2($s1, $s2);

# todo this shouldn't be retrieved 2x (also in view) 2012-04-04 vaskoiii
# will need to modify this hack to change how motion editing works
if (!$x['load']['motion']['id'])
	$x['load']['motion']['id'] = $x['load']['view']['id'];

# GET DATABASE VALUES
if (!$_SESSION['interpret']['failure']) {
if (!$_SESSION['process']['form_info']['load'] == 'motion') {
if ($x['load']['motion']['id']) {
	# super hack
	# problems because lock_group_id and group_id cause problems in the engine on the view page /group_view/?lock_group_id=100&group_id=100
	# the original version worked with: /group_view/?child=groupmate&group_id=14
	# but not with: /group_view/?child=groupmate&lock_group_id=14
	# and had the same behaviour with /group_view/?child=groupmate&group_id=14&lock_group_id=14
	# so this is probabably symptomatic of a problem with the engine.
	$b1 = 1;
	if ($x['name'] == 'group_view') {
	if (get_gp('group_id')) {
	if (get_gp('lock_group_id')) {
		$b1 = 2;
		$i1 = $_GET['lock_group_id'];
		unset($_GET['lock_group_id']);
		start_engine($data['motion'], $x['load']['motion']['type'], $_SESSION['login']['login_user_id'], array($x['load']['motion']['id']), 'view');
		$_GET['lock_group_id'] = $i1;
	} } }
	if ($b1 == 1)
		start_engine($data['motion'], $x['load']['motion']['type'], $_SESSION['login']['login_user_id'], array($x['load']['motion']['id']), 'view');
	listing_key_translation($key, $translation, $data['motion'], get_gp('edit_type'), $_SESSION['login']['login_user_id']);

	$motion_listing = & $data['motion']['result']['listing'][0];

	if (!empty($data['motion']['response']))
	foreach ($data['motion']['response'] as $k1 => $v1)
	foreach ($v1 as $k2 => $v2)
		$data['motion']['response'][$k1][$k2] = $motion_listing[$k2];

	# todo we have to figure out how to fix this guy
	# for now leave the the following intact so we don't have to change variable names too ridiculously ATM:
	# $data['motion']['search']
	# $data['motion']['result']

	if ($motion_listing['tag_id']) { 
		add_key('tag', $motion_listing['tag_id'], 'tag_name', $key);
		# may be better to just populate the fieled right away in which case we dont need the above line though it is much more wordy? 2012-03-27 vaskoiii
		$motion_content_1['tag_translation_name'] = get_immediate_tag_translation_name($motion_listing['tag_id'], $_SESSION['dialect']['dialect_id']);
	}

	# get the rest of contact_user_mixed
	$motion_content_1['user_name'] = $motion_listing['user_name'];
	if ($motion_content_1['user_name'] == '') {
		$motion_content_1['user_name'] = $motion_listing['destination_user_name'];
	}
	
	if ($motion_content_1['contact_name'])
		add_key('contact', $motion_listing['contact_id'], 'user_name', $key);
} } }
else {
	# process still gives us action 2012-04-05 vaskoiii
	load_response('action_content_1', $motion_content_1, $_SESSION['login']['login_user_id']);
	load_response('action_content_2', $motion_content_2, $_SESSION['login']['login_user_id']);
}

# INJECT AUTO VALUES (special invitation case)
if ($_SESSION['login']['login_user_name']) {
if ($x['load']['motion']['name'] == 'edit') {
if ($x['load']['motion']['type'] == 'user') {
	if (!$motion_content_1['invite_user_name'])
		$motion_content_1['invite_user_name'] = $_SESSION['login']['login_user_name'];
	if (!$motion_content_1['invite_user_name'])
		$motion_content_1['invite_password'] = 'peer_authenticated';
} } }

# is friend? prepare 2013-04-20 vaskoiii
set_accept_friend_from_listing_and_container(
	$data['motion']['result']['listing'][0],
	$motion_content_1,
	$_SESSION['login']['login_user_id']
);

# TRANSLATION
foreach ($data['motion']['response'] as $k1 => $v1) {
foreach ($v1 as $k2 => $v2) {
	add_option($k2);
	add_translation('element', $k2);
	# hardcoded defaults
	if (!$_SESSION['process']['failure']) {
	if (!$x['load']['motion']['id']) {
	switch($k2) {
		case 'parent_tag_path':
			if (!$v2)
				$data['motion']['response'][$k1][$k2] = '<|!|>';
		break;
		case 'dialect_name':
			 if (!$v2)
				$data['motion']['response'][$k1][$k2] =  $_SESSION['dialect']['dialect_name'];
		break;
		case 'team_required_name':
			 if (!$v2)
				$data['motion']['response'][$k1][$k2] =  '<|*|>';
		break;
		case 'location_name':
			if (!$v2)
				$data['motion']['response'][$k1][$k2] = get_db_single_value('
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
} }

$data['motion']['response']['motion_footer_1'] = get_action_footer_1($x['load']['motion']['type'], $x['load']['motion']['name']);
