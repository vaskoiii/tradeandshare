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

# Contents/Description: Repopulates our helper index table (index_entry_user)
# This guy won't actually have been tested since reworking the tables in the database... 2009-10-08
# require($x['site']['i'] . 'function/link.php');

$entry_array = array(
	'offer',
	'rating',
	'transfer',
	'invited',
);

foreach ($entry_array as $k1 => $v1) {
	$data = array();
	$sql = '
		SELECT
			id,
			source_user_id,
			destination_user_id
		FROM
			' . $config['mysql']['prefix'] . $v1
	;
	$result = mysql_query($sql) or die(mysql_error());
	while ($row = mysql_fetch_assoc($result))
		$data[] = $row;
	foreach($data as $k2 => $v2) {
		index_entry(
			$v1,
			$v2['id'],
			$v2['source_user_id'],
			$v2['destination_user_id'],
			true
		);
	}
}
