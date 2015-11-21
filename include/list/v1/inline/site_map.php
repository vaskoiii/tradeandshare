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

# Contents/Description: Sets up a data structure for page ordering... 
# Hardcoding:
# 321 = Parent of itself (Root)
# 68 = Parent of _area pages

# file also used in new_report.php
add_translation('page', 'member_report');
add_translation('page', 'member_report', 'translation_description');
add_translation('page', 'cycle_report');
add_translation('page', 'cycle_report', 'translation_description');
add_translation('page', 'new_area');
add_translation('page', 'new_area', 'translation_description');
add_translation('page', 'new_report');
add_translation('page', 'new_report', 'translation_description');
add_translation('page', 'search_report');
add_translation('page', 'search_report', 'translation_description');
add_translation('page', 'top_report');
add_translation('page', 'top_report', 'translation_description');
add_translation('page', 'ts_area');
add_translation('page', 'ts_area', 'translation_description');

# get base area order
$sql = '
	SELECT
		p.id as page_id,
		p.name as page_name,
		p.`order` as page_order
	FROM
		' . $config['mysql']['prefix'] . 'page p
	WHERE
		p.parent_id = 68
	ORDER BY
		p.`order`
';
$result = mysql_query($sql) or die(mysql_error());
while ($row = mysql_fetch_assoc($result))
	$data['new_report']['page_id'][$row['page_id']] = array(
		'page_name' => $row['page_name'], 
		'page_order' => $row['page_order']
	);

# get monitored pages
$sql = '
	SELECT
		v.when as view_when,
		p.name as page_name,
		p.id AS page_id,
		p.parent_id as parent_page_id,
		p.advanced as page_advanced,
		p.order AS page_order
	FROM
		' . $config['mysql']['prefix'] . 'page p
	LEFT OUTER JOIN
		' . $config['mysql']['prefix'] . 'visit v
	ON
		(
			p.id = v.page_id AND
		 	v.user_id = ' . (int)$_SESSION['login']['login_user_id'] . '
		)
	WHERE
		p.parent_id != 68 AND
		p.monitor = "1"
	ORDER BY
		p.`order`
';
$result = mysql_query($sql) or die(mysql_error());
while ($row = mysql_fetch_assoc($result))
	$data['new_report']['page_id'][$row['parent_page_id']]['page_id'][$row['page_id']] = array(
		'page_name' => $row['page_name'],
		'view_when' => $row['view_when'],
		'page_advanced' => $row['page_advanced'],
		'page_order' => $row['page_order']
	);
