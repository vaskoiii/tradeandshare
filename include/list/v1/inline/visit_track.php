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

# Contents/Description: Update ts_login AND ts_visit - any page load is considered a login

if ($_SESSION['login']['login_user_id']) {
	// LOGIN (Any page accessed when you are logged in.)
	$sql = '
		DELETE FROM
			' . $config['mysql']['prefix'] . 'login
		WHERE
			user_id = ' . (int)$_SESSION['login']['login_user_id']
	;
	$result = mysql_query($sql) or die(mysql_error());

	$sql = '
		INSERT INTO
			' . $config['mysql']['prefix'] . 'login
		SET
			user_id = ' . (int)$_SESSION['login']['login_user_id'] . ',
			`when` = CURRENT_TIMESTAMP
	';
	$result = mysql_query($sql) or die(mysql_error());

	// VISIT
	if ($x['page']['monitor']) { # Remove for a login/visit to be considered for any page not just list pages marked in the database by [monitor]
		$sql = '
			DELETE FROM  
				' . $config['mysql']['prefix'] . 'visit
			WHERE 
				page_id = ' . (int)$x['page']['id'] . ' AND
				user_id = ' . (int)$_SESSION['login']['login_user_id'] 
		;
		$result = mysql_query($sql) or die(mysql_error());

		$sql = '
			INSERT INTO
				' . $config['mysql']['prefix'] . 'visit
			SET
				user_id = ' . (int)$_SESSION['login']['login_user_id'] . ',
				page_id = ' . (int)$x['page']['id'] . ',
				`when` = CURRENT_TIMESTAMP
		';
		$result = mysql_query($sql) or die(mysql_error());
	}
}
