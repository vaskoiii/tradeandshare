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

# Contents/Description: Rebuild the magical team that has everyone in it.  This team is special for a few reasons:
# - main reason is that I can not figure out how to get rid of displaying private listings without having this team.
# - everyone is in it
# - you can not leave it
# - the only time this table is used is when a new user joins they are automatically put on this team.

echo $sql = '
	SELECT
		id as user_id
	FROM
		' . $config['mysql']['prefix'] . 'user'
;
$result = mysql_query($sql) or die(mysql_error());
while ($row = mysql_fetch_assoc($result))
	$data[] = $row;

echo '<hr />' . $sql = '
	DELETE FROM
		' . $config['mysql']['prefix'] . 'link_team_user
	WHERE
		team_id = ' . (int)$config['everyone_team_id']
;
$result = mysql_query($sql) or die(mysql_error());

foreach ($data as $k1 => $v1) {
	
	// We have to insert the other teams that these users MIGHT be on too before we can insert anything!
	echo '<hr />' . $sql = '
		INSERT INTO
			' . $config['mysql']['prefix'] . 'link_team_user
		SET
			team_id = ' . (int)$config['everyone_team_id'] . ',
			user_id = ' . (int)$v1['user_id'] . ',
			modified = CURRENT_TIMESTAMP,
			active = "1"
	';
	$result = mysql_query($sql) or die(mysql_error());
}
