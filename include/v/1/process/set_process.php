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

# Contents/Description: process a submitted SESSION
# NOTE: we do nothing with [login_set] or [login_set_process] because they are already integrated into EVERY PAGE!

# Translation
add_translation('element', 'transaction_complete');
do_translation($key, $translation, $_SESSION['dialect']['dialect_id'], $_SESSION['login']['login_user_id']);

# There can only be 1 form submitted here at a time
$process = array(); # Sent to process
$interpret = array(); # Intrepreted from $process

$process['form_info'] = get_action_header_1();
foreach($process['form_info'] as $k1 => $v1)
	$process['form_info'][$k1] = get_gp($k1);

$process['action_content_1'] = get_action_content_1($process['form_info']['type'], 'set');
foreach($process['action_content_1'] as $k1 => $v1)
	$process['action_content_1'][$k1] = get_gp($k1);


foreach($process['action_content_1'] as $k1 => $v1)
switch($k1) {
	case 'load_javascript':
		$process['action_content_1'][$k1] = get_boolean_gp($k1);
	break;
	case 'lock_contact_user_mixed':
		contact_user_mixed_split('action_content_1', 'lock_');
		# process_data_translation() works on the already split fields not when they are combined! 2012-02-15 vaskoiii
	break;
	case 'theme_name':
	case 'background_theme_name':
	case 'launcher_theme_name':


	case 'dialect_name':
	case 'display_name':

	case 'lock_group_name':
	case 'lock_team_name':
	case 'lock_location_name':
	case 'lock_range_name':
		$process['action_content_1'][$k1] = get_gp($k1);
	break;
}

if ($process)
foreach($process as $k1 => $v1) {
	process_data_translation($k1);
	# not much error checking needed? 2012-06-05 vaskoiii
	# process_check_existence();
	# process_field_missing($k1);
	# process_does_not_exist($k1);
	# process_does_exist($k1);
}

# Critical part if we want the user to stay logged in.
process_failure($interpret['message']);

# DO IT
unset($_SESSION[$process['form_info']['type']]);

# SET DB and MISC
switch(get_gp('type')) {
case 'lock':
	$sql = '
		DELETE FROM
			' . $config['mysql']['prefix'] . 'lock
		WHERE
			user_id = ' . (int)$_SESSION['login']['login_user_id'] . '
	';
	$result = mysql_query($sql) or die(mysql_error());

	foreach($process['action_content_1'] as $k1 => $v1) {
	switch($k1) {
	case 'lock_user_name':
	case 'lock_contact_name':
	case 'lock_group_name':
	case 'lock_team_name':
	case 'lock_location_name':
	case 'lock_range_name':
		$s1 = str_replace('_name', '_id', $k1);
		if ($interpret[$s1]) {
			$sql = '
				INSERT INTO
					' . $config['mysql']['prefix'] . 'lock
				SET
					user_id = ' . (int)$_SESSION['login']['login_user_id'] . ',
					name = ' . to_sql($s1) . ',
					value = ' . (int)$interpret['lookup'][$s1]
			;
			$result = mysql_query($sql) or die(mysql_error());
		}
	break;
	} }
break;
}

# SET SESSION
foreach($process['action_content_1'] as $k1 => $v1) {
	switch($k1) {
		case 'load_javascript':
			$_SESSION['load'][$k1] = get_boolean_gp($k1);
		break;
		case 'dialect_name':
			$s1 = str_replace('_name', '', $k1);
			if ($interpret['lookup'][$s1 . '_id']) {
				$_SESSION[$s1][$s1 . '_id'] = $interpret['lookup'][$s1 . '_id'];
				$_SESSION[$s1][$s1 .'_code'] = $interpret['lookup'][$s1 . '_code'];
				$_SESSION[$s1][$s1 .'_name'] = $process['action_content_1'][$s1 . '_name'];
			}
		break;
		case 'background_theme_name':
		case 'display_name':
		case 'launcher_theme_name':
		case 'lock_contact_name':
		case 'lock_group_name':
		case 'lock_location_name':
		case 'lock_range_name':
		case 'lock_team_name':
		case 'lock_user_name':
		case 'theme_name':
			$s1 = str_replace('_name', '_id', $k1);
			if ($interpret['lookup'][$s1]) {
				$_SESSION[$process['form_info']['type']][$s1] = $interpret['lookup'][$s1];
				$_SESSION[$process['form_info']['type']][$k1] = $process['action_content_1'][$k1];
			}
		break;
	}
}

# SET COOKIE
foreach($process['action_content_1'] as $k1 => $v1) {
	switch($k1) {
		case 'load_javascript':
			setcookie(
				$process['form_info']['type'] . '[' . $k1 . ']', 
				$v1, 
				time()+60*60*24*365, # 1 year
				'/'
			);
		break;
		case 'theme_name':
			ts_set_cookie('theme', 'theme_name', $_SESSION['theme']['theme_name']);
			ts_set_cookie('theme', 'theme_id', $_SESSION['theme']['theme_id']);
		break;
		case 'background_theme_name':
			ts_set_cookie('theme', 'background_theme_name', $_SESSION['theme']['background_theme_name']);
			ts_set_cookie('theme', 'background_theme_id', $_SESSION['theme']['background_theme_id']);
		break;
		case 'launcher_theme_name':
			ts_set_cookie('theme', 'launcher_theme_name', $_SESSION['theme']['launcher_theme_name']);
			ts_set_cookie('theme', 'launcher_theme_id', $_SESSION['theme']['launcher_theme_id']);
		break;
		case 'dialect_name':
			$s1 = str_replace('_name', '', $k1);
			ts_set_cookie($s1, $s1 . '_name', $_SESSION[$s1][$s1 . '_name']);
			ts_set_cookie($s1, $s1 . '_code', $_SESSION[$s1][$s1 . '_code']);
			ts_set_cookie($s1, $s1 . '_id', $_SESSION[$s1][$s1 . '_id']);
		break;
		case 'display_name':
			$s1 = str_replace('_name', '', $k1);
			ts_set_cookie($s1, $s1 . '_name', $_SESSION[$s1][$s1 . '_name']);
			ts_set_cookie($s1, $s1 . '_id', $_SESSION[$s1][$s1 . '_id']);
		break;
	}
}

# SUCCESS
switch($process['form_info']['type']) {
	case 'dialect':
		// Display resulting message in the correct dialect!
		$interpret['new_dialect_message'] = get_db_single_value('
				t.name
			FROM
				' . $config['mysql']['prefix'] . 'translation t,
				' . $config['mysql']['prefix'] . 'element e,
				' . $config['mysql']['prefix'] . 'kind k
				
			WHERE
				k.id = t.kind_id AND
				k.name = "element" AND
				t.kind_name_id = e.id AND
				t.dialect_id = ' . (int)$interpret['dialect_id'] . ' AND
				e.name = ' . to_sql('transaction_complete')
		);
		if (!$interpret['new_dialect_message'])
			$interpret['new_dialect_message'] = tt('element', 'transaction_complete');
		process_success($interpret['new_dialect_message']);
	break;
	case 'lock':
		# process_success(tt('element', 'transaction_complete'), $x['..'] . get_lock_query()); // useful if you want to copy the url to your friend.
	# nobreak;
	default:
		process_success(tt('element', 'transaction_complete'));
	break;
}
