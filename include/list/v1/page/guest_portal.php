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

# Description: Picture yourself in a line of people waiting to get scanned in at the counter

$sql = '
	select
		key
	from
		' . $config['mysql']['prefix'] . 'pubkey
	where
		user_id = ' . (int)$_SESSION['login']['login_user_id']
;

$sql = '
	select
		*
	from
		' . $config['mysql']['prefix'] . 'filer
	where
		user_id = ' . (int)$_SESSION['login']['login_user_id'] . '
	limit
		1
';
$result = mysql_query($sql) or die(mysql_error());
while ($row = mysql_fetch_assoc($result)) {

	$data['guest_portal']['user_id'] = $row;

}


