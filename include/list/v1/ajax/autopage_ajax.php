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

# Contents/Description: Search contact_name then user_name display contact_name (user_name)

# Possible Issues: Might be faster to NOT use AJAX every time but rather to generate the complete result set and just filter the results thereafter. ie) Call 1 time and filter the results from there
# See: 
# http://www.codenothing.com/demos/2009/auto-complete/demo/
# >> [Supply a data set for autoComplete to use.]

# allows: value = ''
$value = get_gp('value'); # LIKE '{$value}%'

# placeholder variable for when we want ajax to start using limits
# $all = get_boolean_gp('all'); # use to drop the limits ( Dont test against 0: true = 1 AND false = 2 )

# placeholder for when we want the result set to have differnt styles
# $style = get_gp('style');

# todo: shouldnt need an exception - potential desctruction if working with the original data array 2013-10-12 vaskoiii
if (!is_array($data))
	$data = array();

$sql = '
	SELECT
		p.id AS page_id,
		p.name AS page_name,
		tr.name AS translation_name
	FROM
		' . $config['mysql']['prefix'] . 'page p,
		' . $config['mysql']['prefix'] . 'translation tr,
		' . $config['mysql']['prefix'] . 'kind k
	WHERE
		k.id = tr.kind_id AND 
		k.name = "page" AND
		tr.`default` = 1 AND
		p.id = tr.kind_name_id AND
		p.launch = 1 AND
		tr.name LIKE ' . to_sql($value . '%') . ' AND
		tr.dialect_id = ' . (int)$_SESSION['dialect']['dialect_id'] . '
	ORDER BY 
		tr.name ASC
';

$result = mysql_query($sql) or die(mysql_error());
while ($row = mysql_fetch_assoc($result)) {
	#$data['launch'][] = $row;
	$data['autopage'][$row['page_id']] = array(
		'value' => $row['page_name'],
		'display' => $row['translation_name'],
	);
	$data['page_id'][$row['page_id']] = $row['page_id'];
}

# help issues with PHP json_encode() prior to 5.3.0 (and possibly after)
if (is_array($data['autopage'])) {
foreach ($data['autopage'] as $k1 => $v1) {
	$data['json'][] = $v1;
} }
