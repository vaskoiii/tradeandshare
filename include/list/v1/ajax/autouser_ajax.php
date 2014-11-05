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

# Contents/Description: Search user_name and display user_name ONLY

# Known Issues: only used for user_name input field which is only displayed on the contact form
# may not be able to use this field elsewhere

# Allow: value = ''
$value = get_gp('value');
$all = get_boolean_gp('all'); # use to drop the limits ( Dont test against 0: true = 1 AND false = 2 )

# placeholder for when we want the result set to have differnt styles
# $style = get_gp('style');
# the launcher uses this page but we just use strip_tags()

# todo: shouldnt need an exception - potential desctruction if working with the original data array 2013-10-12 vaskoiii
if (!is_array($data))
	$data = array();


$data['not_user_id'] = array();

# Maybe we have to get an array of usernames that were already used (easiest)
# Get contacts with user names
# maybe can't use variables in the ajax
# if (get_gp('unused') == 1) {
	$sql = '
		SELECT
			lcu.user_id
		FROM
			' . $config['mysql']['prefix'] . 'contact c,
			' . $config['mysql']['prefix'] . 'link_contact_user lcu
		WHERE
			c.id = lcu.contact_id AND
			c.user_id = ' . (int)$_SESSION['login']['login_user_id'] . ' AND
			c.active = 1
	';
	# no limit because we have to get all of the values
	$result = mysql_query($sql) or die(mysql_error());
	while ($row = mysql_fetch_assoc($result)) {
		$data['not_user_id'][$row['user_id']] = $row['user_id'];
	}
# }

# second 5 are user matches (for contact/user pairs)
$sql = '
	SELECT
		u.name as user_name,
		u.id as user_id
	FROM
		ts_user u
	WHERE
		u.name LIKE ' . to_sql($value . '%') .
		(!empty($data['not_user_id']) ? ' AND u.id NOT IN ( ' . implode(', ', $data['not_user_id']) . ' )' : '') . '
';
if ($all != 1)
$sql .= '
	LIMIT
		10
';
$result = mysql_query($sql) or die(mysql_error());
while ($row = mysql_fetch_assoc($result)) {
	$data['autouser'][$row['user_id']] = array(
		'display' => '<span class="lock_user_name"><b>' . to_html(substr($row['user_name'], 0, strlen($value))) . '</b>' .  to_html(substr($row['user_name'], strlen($value), strlen($row['user_name']))) . '</span>',
		'value' =>  html_entity_decode($row['user_name'])
	);
	$data['user_id'][$row['user_id']] = $row['user_id'];
}
if (is_array($data['autouser'])) {
foreach ($data['autouser'] as $k1 => $v1) {
	$data['json'][] = $v1;
} }

# echo '<pre>'; print_r($data); echo '</pre>';
