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

# Contents/Description: process a submitted login separate from set_process.php because of the importance of logging in. 
# Known Issues: The login is technically a part of every page.

add_translation('element', 'transaction_complete');
add_translation('element', 'field_missing');
add_translation('element', 'login_user_name');
add_translation('element', 'login_user_password');
add_translation('element', 'does_not_exist');
add_translation('element', 'error');
add_translation('element', 'login_set');
do_translation($key, $translation, $_SESSION['dialect']['dialect_id'], $_SESSION['login']['login_user_id']);

$process = array(); # Sent to process
$process['action_content_1'] = array();
$interpret = array(); # Intrepreted from $process
$interpret['lookup'] = array();

# shortcut
$action_content_1 = & $process['action_content_1'];

$action_content_1 = get_action_content_1('login', 'set');

foreach($action_content_1 as $k1 => $v1)
	$action_content_1[$k1] = get_gp($k1);

# custom variable settting
$action_content_1['login_user_password'] = get_gp('login_user_password'); # not login_user_password_unencrypted
if (!$action_content_1['login_user_password'])
	$action_content_1['login_user_password_unencrypted'] = get_gp('login_user_password_unencrypted'); # Not stored in DB
$action_content_1['remember_login'] = get_boolean_gp('remember_login');

# cookie data
if (!$action_content_1['login_user_name'] && !$action_content_1['login_user_password']) {
	$action_content_1['login_user_name'] = $_COOKIE['login']['login_user_name'];
	$action_content_1['login_user_password'] = $_COOKIE['login']['login_user_password'];
}

# TRANSLATIONS
$interpret['remember_login'] = 0;
if ($_COOKIE['login']['login_user_name'] && $_COOKIE['login']['login_user_password'])
	$interpret['remember_login'] = 1;
elseif (get_gp('remember_login'))
	$interpret['remember_login'] = 1;
if ($action_content_1['login_user_name'])
	$interpret['login_user_id'] = get_db_single_value('
			id
		FROM
			' . $config['mysql']['prefix'] . 'user
		WHERE
			name = ' . to_sql($action_content_1['login_user_name']) . ' AND
			active = 1
	');
if ($action_content_1['login_user_password_unencrypted'])
	$action_content_1['login_user_password'] = md5($action_content_1['login_user_password_unencrypted']);

# ERROR CHECKING
if (!$action_content_1['login_user_name'])
	$interpret['message'] = tt('element', 'field_missing') . ' : ' . tt('element', 'login_user_name');
elseif (!$action_content_1['login_user_password'])
	$interpret['message'] = tt('element', 'field_missing') . ' : ' . tt('element', 'login_user_password');
elseif (!$interpret['login_user_id'])
	$interpret['message'] = tt('element', 'does_not_exist') . ' : ' . tt('element', 'login_user_name');
elseif (!get_db_single_value('
		id 
	FROM 
		' . $config['mysql']['prefix'] . 'user 
	WHERE 
		name = ' . to_sql($action_content_1['login_user_name']) . ' AND 
		password = ' . to_sql($action_content_1['login_user_password'])  . ' AND
		active = 1'
))
	$interpret['message'] = tt('element', 'error') . ' : ' . tt('element', 'login_set');

# FAILURE
if ($x['page']['login'] == '2') // autologin doesn't need a message override for not logged in pages
	; // keep in mind we still have the ERROR CHECKING going on above because we need it... It's just that a user that isn't logged in won't notice...
elseif ($interpret['message']) {
	$_SESSION['process']['message'] = $interpret['message'];
}

# do it
if (!$interpret['message']) {
	# Typical scenerio here with variables is to:
	# 1. Unset/Set as Default
	# 2. Reset/Set as User Defined

	# SET FEATURES
	$_SESSION['feature'] = array(
		'feature_lock' => '2',
		'feature_minnotify' => '2',
	);
	$sql = '
		SELECT
			feature_lock,
			feature_minnotify
		FROM
			' . $config['mysql']['prefix'] . 'user_more
		WHERE
			id = ' . (int)$interpret['login_user_id'] . '
		LIMIT
			1
	';
	$result = mysql_query($sql) or die(mysql_error());
	while ($row = mysql_fetch_assoc($result)) {
		$_SESSION['feature']['feature_lock'] = $row['feature_lock'];
		$_SESSION['feature']['feature_minnotify'] = $row['feature_minnotify'];
	}

	# SET FEATURE LOCK
	if ($_SESSION['feature']['feature_lock']) {
		$_SESSION['lock'] = array();
		$sql = '
			SELECT
				name,
				value
			FROM
				' . $config['mysql']['prefix'] . 'lock l
			WHERE
				user_id = ' . (int)$interpret['login_user_id']
		;
		$result = mysql_query($sql) or die(mysql_error());
		while ($row = mysql_fetch_assoc($result))
			$_SESSION['lock'][$row['name']] = $row['value'];
		foreach($_SESSION['lock'] as $k1 => $v1) {
			switch($k1) {
				case 'lock_range_id':
					// Element Name (Not translated name)
					$s1 = str_replace('_id', '_name', $k1);
					$_SESSION['lock'][$s1] = (get_db_single_value('
							el.`name`
						FROM
							' . $config['mysql']['prefix'] . 'element el,
							' . $config['mysql']['prefix'] . str_replace(array('lock_', '_id'), array('',''), $k1) . ' gt
						WHERE
							el.id = gt.element_id AND
							gt.id = ' . (int)$v1
					, 0));
				break;
				default:
					$_SESSION['lock'][str_replace('_id', '_name', $k1)] = get_db_single_value('
							gt.name
						FROM
							' . $config['mysql']['prefix'] . str_replace(array('lock_', '_id'), array('',''), $k1) . ' gt
						WHERE
							gt.id = ' . (int)$v1
					, 0);
				break;
			}
			if (!$_SESSION['lock'][str_replace('_id', '_name', $k1)])
				unset($_SESSION['lock'][$k1]);
		}
	}

	unset($_SESSION['login']);
	$_SESSION['login']['login_user_id'] = $interpret['login_user_id'];
	$_SESSION['login']['login_user_name'] = $action_content_1['login_user_name'];
	$_SESSION['login']['login_user_password'] = $action_content_1['login_user_password'];

	if (isset($_COOKIE['login']) && is_array($_COOKIE['login'])) {
		foreach($_COOKIE['login'] as $k1 => $v1) {
			ts_set_cookie('login', $k1, '', time() - 3600); # unset
		}
	}

	# With each login you have [60*60*24*30 = 30 days in seconds] more for your cookies to be active.
	if ($interpret['remember_login']) {
		ts_set_cookie('login', 'login_user_id', $interpret['login_user_id']);
		ts_set_cookie('login', 'login_user_name', $action_content_1['login_user_name']);
		ts_set_cookie('login', 'login_user_password', $action_content_1['login_user_password']);
	}

	# login/visit tracking are mixed and handled elsewhere

	# SUCCESS
	# just continue in:
	# v/1/config/x.php
}
