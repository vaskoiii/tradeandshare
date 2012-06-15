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

# Contents/Description: Mark/ignore visited pages that are currently being shown.

$process = array(); # todo $process['search_miscellaneous'] could be useful 2012-04-10
$interpret = array(); # interpreted from data sent here

# VISITED
$sql = <<<SQL
	SELECT
		v.page_id AS page_id, 
		v.id AS visit_id
	FROM 
		{$config['mysql']['prefix']}visit v,
		{$config['mysql']['prefix']}page p
	WHERE
		p.id = v.page_id AND
		user_id = 
SQL;
$sql .= (int)$_SESSION['login']['login_user_id'];


$result = mysql_query($sql) or die(mysql_error());
while ($row = mysql_fetch_assoc($result)) 
	$interpret['key']['page_id']['result'][$row['page_id']]['visit_id'] = $row['visit_id'];

# Mark only Visited
foreach($interpret['key']['page_id']['result'] as $k1 => $v1) {

	unset($interpret['visit_track']['where']);
	$interpret['visit_track']['where'][] = 'id = ' . (int)$interpret['key']['page_id']['result'][$k1]['visit_id'];
	$interpret['visit_track']['where'][] = 'user_id = ' . (int)$_SESSION['login']['login_user_id'];
	$sql = '
		' . ($interpret['key']['page_id']['result'][$k1] ? 'UPDATE' : 'INSERT INTO') . '
			' . $config['mysql']['prefix'] . 'visit
		SET
			user_id = ' . (int)$_SESSION['login']['login_user_id'] . ',
			page_id = ' . (int)$k1 . ',
			`when` = CURRENT_TIMESTAMP
		' . ($interpret['key']['page_id']['result'][$k1]['visit_id'] ? 'WHERE ' . implode(' AND ', $interpret['visit_track']['where']) : '') . '
	';
	$result = mysql_query($sql) or die(mysql_error());
}

process_success(get_translation('transaction_complete'), $x['..'] . get_q_query($x['level'] - 1, get_q_query_modified($x['level'] - 1, $q['parsed'][$x['level'] -1 ] )));
