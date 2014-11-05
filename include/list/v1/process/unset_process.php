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

# Contents/Description: unset certain fields

add_translation('element', 'transaction_complete');
do_translation($key, $translation, $_SESSION['dialect']['dialect_id'], $_SESSION['login']['login_user_id']);

$process = array(); # Sent to process
$interpret = array(); # Interpreted from $process (unused for unset)

# Done this way for the ts_launcher. launcher only uses page name!
switch($x['name']) {
	case 'login_unset_process':
	case 'lock_unset_process':
	case 'dialect_unset_process':
	case 'theme_unset_process':
		$process['unset'] = str_replace('_unset_process/', '', $x['name']);
	break;
	# But we can still use a variable if we want!
	default: 
		$process['unset'] = get_gp('unset');
	break;
}

# DO IT!!!
unset($_SESSION[$process['unset']]);

# Firefox 3.5.9 does NOT show cookies with an empty string or expired cookies
if (isset($_COOKIE[$process['unset']]) && is_array($_COOKIE[$process['unset']])) {
	foreach($_COOKIE[$process['unset']] as $k1 => $v1) {
		ts_set_cookie($process['unset'], $k1, '', time() - 3600); # unset
	}
}

# EXTRA
switch($process['unset']) {
	case 'login':
		unset($_SESSION['lock']);
		unset($_SESSION['feature']);
		unset($_SESSION['level']);
	break;
	case 'lock':
		$sql = '
			DELETE FROM
				' . $config['mysql']['prefix'] . 'lock
			WHERE
				user_id = ' . (int)$_SESSION['login']['login_user_id']
		;
		$result = mysql_query($sql) or die(mysql_error());
	break;
}

# RETURN
switch($process['unset']) {
	case 'login':
		process_success(tt('element', 'transaction_complete'), '/'); # Return to Homepage!
	break;
	default:
		process_success(tt('element', 'transaction_complete'), '/' . $process['unset'] . '_set/?preview=1');
	break;
}
