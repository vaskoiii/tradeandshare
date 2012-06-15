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

# Contents/Description: Load main pages capable of dynamically displaying any combination of the following:
header('HTTP/1.0 200 Found');

# setting up display orientation 2012-04-05 vaskoiii
$x['preload'] = array(
	'focus' => 'action',
	'expand' => array(),
	'preview' => array(),
);
foreach($x['preload'] as $k1 => $v1) {
	if ($_SESSION['interpret']['preload'][$k1])
		$x['preload'][$k1] = $_SESSION['interpret']['preload'][$k1];
	elseif (isset_gp($k1)) # useful for crafting links
		$x['preload'][$k1] = get_gp($k1);
}

# easier way of doing this for now 2012-04-05 vaskoiii
# todo implement the above way 
if ($_SESSION['interpret']['failure'] == 1) {
if ($_SESSION['process']['form_info']['load']) {
	$x['preload'][] = $_SESSION['interpret']['preload'][$k1];
} }

$x['load'] = get_x_load();

# Page name loading instructions
switch ($x['part'][1]) {
	case 'edit':
		$x['load']['list']['name'] = 'list';
		$x['load']['list']['type'] = $x['part'][0];
	case 'set':
	case 'recover':
		$x['preload']['focus'] = 'action';
		$x['preload']['expand'][] = 'action';
		$x['load']['action']['name'] = $x['part'][1];
		$x['load']['action']['type'] = $x['part'][0];
	break;
	case 'view':
		$x['load']['motion']['name'] = 'edit';
		$x['load']['motion']['type'] = $x['part'][0];
		$x['load']['view']['name'] = 'main';
		$x['load']['view']['type'] = $x['part'][0];
	# nobreak;
	case 'list':
		$x['load']['list']['name'] = 'list';
		$x['load']['list']['type'] = $x['part'][0];
		if (!isset_gp('action_name')) {
			$x['load']['action']['name'] = 'edit';
			$x['load']['action']['type'] = $x['part'][0];
		}
	break;
}

# todo for added boxes make sure: ts.ts_page.login = 1
# beware of: login_set, login_recover, main, and any other page that doesn't already require a login that could use this logic to cheat and display data.

# Alternate URL var loading instructions
# todo eliminate the need for this switch! 2012-02-07
switch ($x['part'][1]) {
	case 'edit':
	case 'set':
	case 'recover':
		if (isset_gp('id'))
			$x['load']['action']['id'] = get_gp('id');
	break;
	case 'view':
		if (isset_gp('list_type')) {
			$x['load']['action']['name'] = 'edit';
			$x['load']['action']['type'] = get_gp('list_type');
			$x['load']['list']['name'] = 'list';
			$x['load']['list']['type'] = get_gp('list_type');
		}
		if (isset_gp($x['part'][0] . '_id'))
			$x['load']['view']['id'] = get_gp($x['part'][0] . '_id');
		if ($x['part'][0] == 'user')
		if (isset_gp('lock_user_id'))
			$x['load']['view']['id'] = get_gp('lock_user_id');
	# nobreak;
	case 'list':
		if (isset_gp($x['part'][0] . '_uid'))
			$x['load']['list']['id'] = get_gp($x['part'][0] . '_uid');
	break;
}

# Main URL Var loading instructions (Will override other set values)
foreach ($x['load'] as $k1 => $v1)
switch ($k1) {
	case 'header':
	case 'footer':
		# also intended to change the link format to link to target="blank" or perhaps to a custom target. 2012-02-08 vaskoiii
		if (get_gp('iframe') != 1) {
			$x['load']['header']['name'] = 'main';
			$x['load']['footer']['name'] = 'main';
		}
	break;
	case 'motion':
	case 'view':
	case 'action':
	case 'list':
		if (isset_gp($k1 . '_name'))
			$x['load'][$k1]['name'] = get_gp($k1 . '_name');
		if (isset_gp($k1 . '_type'))
			$x['load'][$k1]['type'] = get_gp($k1 . '_type');
		if (isset_gp($k1 . '_id'))
			$x['load'][$k1]['id'] = get_gp($k1 . '_id');
	break;
}



# needs to go before x starts getting set
# view page shared action/list var
if ($x['load']['view']['name']) {
switch($x['part'][0]) {
	case 'user':
	case 'contact':
		#action*
		#if (!get_gp('action_user_id'))
		#	$_GET['action_user_id'] = get_gp('user_id');
		if (!get_gp('action_user_id'))
			$_GET['action_user_id'] = get_gp('lock_user_id');
		if (!get_gp('action_user_id')) {
			#if (!get_gp('action_contact_id'))
			#	$_GET['action_contact_id'] = get_gp('contact_id');
			if (!get_gp('action_contact_id'))
				$_GET['action_contact_id'] = get_gp('lock_contact_id');
		}

		#view*
		if (!get_gp('view_id')) {
		switch($x['part'][0]) {
			case 'user':
				$x['load']['view']['id'] = $_GET['view_id'] = get_gp('lock_user_id');
			break;
			case 'contact':
				$x['load']['view']['id'] = $_GET['view_id'] = get_gp('lock_contact_id');
				# what about when contat is set as user_id?
				# todo make other parts of the site smart enough to utilize lock_user_id in place of lock_contact_id 2012-04-20 vaskoiii
				if (!$x['load']['view']['id'] && get_gp('lock_user_id')) {
					$i1 = get_db_single_value('
						contact_id from
							' . $config['mysql']['prefix'] . 'link_contact_user lcu,
							' . $config['mysql']['prefix'] . 'contact con
						where
							lcu.contact_id = con.id and
							lcu.user_id = ' . (int)get_gp('lock_user_id') . ' and
							con.user_id = ' . (int)$_SESSION['login']['login_user_id']
					, 0);
					if ($i1)
						$x['load']['view']['id'] = $_GET['view_id'] = $i1;
				}
			break;
		} }
	break;
	case 'team':
		#action*
		#if (!get_gp('action_team_id'))
		#	$_GET['action_team_id'] = get_gp('team_id');
		if (!get_gp('action_team_id'))
			$_GET['action_team_id'] = get_gp('lock_team_id');

		#view*
		if (!get_gp('view_id'))
			$x['load']['view']['id'] = $_GET['view_id'] = get_gp('lock_team_id');
	break;
	case 'group':
		#if (!get_gp('action_group_id'))
		#	$_GET['action_group_id'] = get_gp('group_id');
		if (!get_gp('action_group_id'))
			$_GET['action_group_id'] = get_gp('lock_group_id');

		#view*
		if (!get_gp('view_id'))
			$x['load']['view']['id'] = $_GET['view_id'] = get_gp('lock_group_id');
	break;
	case 'location':
		#action*
		if (!get_gp('action_location_id'))
			$_GET['action_location_id'] = get_gp('lock_location_id');
		if (!get_gp('action_range_id'))
			$_GET['action_range_id'] = get_gp('lock_action_range_id');

		#view*
		if (!get_gp('view_id'))
			$x['load']['view']['id'] = $_GET['view_id'] = get_gp('lock_location_id');

	break;
} }

/*
SELECT u.id as user_id, u.name as user_name, g.id AS group_id, g.name as group_name, g.description AS group_description, g.modified, g.active 
FROM 
	ts_group g, 
	ts_user u 
WHERE 
	g.user_id = u.id AND 
	g.user_id = 132 AND 
	g.id IN (14) 
GROUP BY g.id ORDER BY modified DESC LIMIT 0,10 

SELECT u.id as user_id, u.name as user_name, g.id AS group_id, g.name as group_name, g.description AS group_description, g.modified, g.active 
FROM 
	ts_link_contact_group lcg, 
	ts_group g, 
	ts_user u, 
	ts_contact c, 
	ts_link_contact_user lcu 
WHERE lcg.active = 1 AND g.active = 1 AND g.user_id = 132 AND u.id = g.user_id AND c.id = lcg.contact_id AND lcg.group_id = 14 AND u.active = 1 AND c.id = lcu.contact_id AND u.id = lcu.user_id AND g.id IN (14) GROUP BY g.id ORDER BY modified DESC LIMIT 0,10 
*/

# super simplify the URL vars (hopefully) though does cause some hidden $_GET vars! (maybe acceptable) 2012-10-12 vaskoiii
# again default page if not set
if ($x['load']['view']['type']) {
	# double vars
	switch($x['load']['view']['type']) {
		case 'team':
			if (!get_gp('team_id'))
				$_GET['team_id'] = $x['load']['view']['id'];
		break;
		case 'group':
			if (!get_gp('group_id'))
				$_GET['group_id'] = $x['load']['view']['id'];
		break;
		case 'location':
			if (!get_gp('location_id'))
				$_GET['location_id'] = $x['load']['view']['id'];
		break;
	}

	$s2 = get_child_listing_type($x['load']['view']['type']);
	if (!get_gp('list_name')) {
		$x['load']['list']['name'] = $_GET['list_name'] = 'list';
		$x['load']['action']['name'] = $_GET['action_name'] = 'edit';
	}
	if (!get_gp('list_type')) {
		$x['load']['list']['type'] = $_GET['list_type'] = $s2;
		$x['load']['action']['type'] = $_GET['action_type'] = $s2;}
	}
	# ehhh I dont want to have to specify the action too...

# set range default
if (!get_gp('lock_range_id')) {
if (get_gp('lock_location_id')) {
	$_GET['lock_range_id'] = 6;
} }

# on view pages only thing that must absolutely be set is then the corresponding lock

# atypical pages 2012-04-10 vaskoiii
switch ($x['name']) {
	case 'profile_edit':
	case 'invite_edit':
		unset($x['load']['list']);
	break;
}

# hack for test:
if ($x['load']['list']['name'] == 'report') {
	unset($x['load']['action']);
}
if ($x['load']['list']['name'] == 'doc') {
	unset($x['load']['action']);
}

# header to _list if _view is missing it's info
# todo make the search generate an error? 2012-04-22 vaskoiii
# but maybe not (functionality allows the user to escape the view)
if ($x['part'][1] == 'view') {
if (!$x['load']['view']['id']) {
	# overwrite previous error message?
	#$_SESSION['interpret']['message'] = tt('element', 'error');
	$s1 = $x['..'] . $x['part'][0] . '_list/' .  ($_SERVER['REDIRECT_QUERY_STRING'] ? '?' . $_SERVER['REDIRECT_QUERY_STRING'] : '');
	header('location: ' . $s1);
	exit;
} }

# ENGINE
if ($x['load']['header']['name']) {
	include($x['site']['i'] . '/inline/head.php');
	include($x['site']['i'] . 'inline/header.php');
}
if ($x['load']['motion']['name'])
	include($x['site']['i'] . 'layer/motion.php');
if ($x['load']['view']['name'])
	include($x['site']['i'] . 'layer/view.php');
if ($x['load']['action']['name'])
	include($x['site']['i'] . 'layer/action.php');
switch($x['load']['list']['name']) {
	case 'list':
		include($x['site']['i'] . 'layer/search.php');
		include($x['site']['i'] . 'layer/result.php');
	break;
	case 'doc':
		# not yet...
	break;
	case 'report':
		include($x['site']['i'] . '/page/' . $x['load']['list']['type'] . '_' . $x['load']['list']['name'] . '.php');
	break;
}

if ($x['load']['footer']['name'])
	include($x['site']['i'] . '/inline/footer.php');

# TEMPLATE
if ($x['load']['header']['name']) {
	include($x['site']['i'] . '/inline/' . $x['site']['t'] . 'head.php');
	include($x['site']['i'] . 'inline/' . $x['site']['t'] . 'header.php');
}
if ($x['load']['motion']['name'])
	include($x['site']['i'] . 'layer/' . $x['site']['t'] . 'motion.php');
if ($x['load']['view']['name'])
	include($x['site']['i'] . 'layer/' . $x['site']['t'] . 'view.php');
if ($x['load']['action']['name'])
	include($x['site']['i'] . 'layer/' . $x['site']['t'] . 'action.php');
switch($x['load']['list']['name']) {
	case 'list':
		include($x['site']['i'] . 'layer/' . $x['site']['t'] . 'search.php');
		include($x['site']['i'] . 'layer/' . $x['site']['t'] . 'result.php');
	break;
	case 'doc':
		include($x['site']['i'] . '/inline/' . $x['site']['t'] . '/header_after.php'); ?> 
		<div class="content"><div class="content_box"><?
			include($x['site']['i'] . '/html/' . $x['load']['list']['type'] . '_' . $x['load']['list']['name'] . '.php'); ?> 
		</div></div>
		<div class="menu_1"></div>
		<div class="menu_2"></div><?
	break;
	case 'report':
		include($x['site']['i'] . '/page/' . $x['site']['t'] . $x['load']['list']['type'] . '_' . $x['load']['list']['name'] . '.php');
	break;
}
if ($x['load']['footer']['name'])
	include($x['site']['i'] . '/inline/' . $x['site']['t'] . 'footer.php');
