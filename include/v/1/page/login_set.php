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

# Contents/Description: Sets up the login page independent of the other pages
# Known Issue: Variables may be inconsitent with the rest of the site


# shortcut
$action_content_1 = & $data['action']['response']['action_content_1'];

$action_content_1 = get_action_content_1('login', 'set');

add_translation('page', 'login_recover');
foreach($action_content_1 as $k1 => $v1)
	add_translation('element', $k1);

# special exception where we have to get data from the SESSION instead of get or post tied into x.php
if (isset($_SESSION['process']['action_content_1']) && !isset($_SESSION['process']['success'])) {
	foreach($_SESSION['process']['action_content_1'] as $k1 => $v1) {
		$action_content_1[$k1] = $v1;
	}
}
