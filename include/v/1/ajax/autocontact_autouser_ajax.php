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

# Allow: value = ''
$value = get_gp('value');
$all = get_boolean_gp('all'); # use to drop the limits ( Dont test against 0: true = 1 AND false = 2 )

# placeholder for when we want the result set to have differnt styles
# $style = get_gp('style');
# the launcher uses this page but we just use strip_tags()

# todo: shouldnt need an exception - potential desctruction if working with the original data array 2013-10-12 vaskoiii
if (!is_array($data))
	$data = array();

# first 5 are contact matches (for user/contact pairs)
$sql = '
	SELECT
		c.name as contact_name,
		c.id as contact_id
	FROM
		ts_contact c
	WHERE
		c.name LIKE ' . to_sql($value . '%') . ' AND
		c.user_id = ' . (int)$_SESSION['login']['login_user_id'] . ' AND
		c.active = 1
';
if ($all != 1)
$sql .= '
	LIMIT
		5
';
$result = mysql_query($sql) or die(mysql_error());
while ($row = mysql_fetch_assoc($result)) {
	$data['autocontact'][$row['contact_id']] = array(
		'value' => $row['contact_name'],
		'display' => '<span class="lock_contact_name">' . '<b>' . to_html(substr($row['contact_name'], 0, strlen($value))) . '</b>' .  to_html(substr($row['contact_name'], strlen($value), strlen($row['contact_name']))) . '</span>'
	);
	$data['contact_id'][$row['contact_id']] = $row['contact_id'];
}
if (!empty($data['contact_id'])) {
	$sql = '
		SELECT
			luc.contact_id,
			luc.user_id,
			u.name as user_name
		FROM
			ts_user u,
			ts_contact c,
			ts_link_contact_user luc
		WHERE
			luc.user_id = u.id AND
			luc.contact_id = c.id AND
			luc.contact_id IN ( ' . implode(', ', $data['contact_id']) . ' ) AND
			c.user_id = ' . (int)$_SESSION['login']['login_user_id'] . ' AND
			c.active = 1
	';
	$result = mysql_query($sql) or die(mysql_error());
	while ($row = mysql_fetch_assoc($result)) {
		$data['not_user_id'][] = $row['user_id'];
		$data['autocontact'][$row['contact_id']]['display'] .= ' <span class="lock_user_name">' . to_html($config['unabstracted_prefix'] . $row['user_name'] . $config['unabstracted_suffix'] )  . '</span>';
		// make sure output is htmlified!
		$data['autocontact'][$row['contact_id']]['value'] = html_entity_decode(strip_tags($data['autocontact'][$row['contact_id']]['display']));
	}
}

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
		5
';
$result = mysql_query($sql) or die(mysql_error());
while ($row = mysql_fetch_assoc($result)) {
	$data['autouser'][$row['user_id']] = array(
		'display' => '<span class="lock_user_name">' . to_html($config['unabstracted_prefix']) . '<b>' . to_html(substr($row['user_name'], 0, strlen($value))) . '</b>' .  to_html(substr($row['user_name'], strlen($value), strlen($row['user_name']))) . to_html($config['unabstracted_suffix']) . '</span>',
		'value' =>  html_entity_decode(to_html($config['unabstracted_prefix'] . $row['user_name'] . $config['unabstracted_suffix'] ) )
	);
	$data['user_id'][$row['user_id']] = $row['user_id'];
}
if (!empty($data['user_id'])) {
	$sql = '
		SELECT
			luc.user_id,
			c.name as contact_name
		FROM
			ts_contact c,
			ts_link_contact_user luc
		WHERE
			luc.contact_id = c.id AND
			luc.user_id IN ( ' . implode(', ', $data['user_id']) . ' ) AND
			c.user_id = ' . (int)$_SESSION['login']['login_user_id'] . ' AND
			c.active = 1
	';
	$result = mysql_query($sql) or die(mysql_error());
	while ($row = mysql_fetch_assoc($result)) {
		$data['autouser'][$row['user_id']]['display'] = '<span class="lock_contact_name">' . $row['contact_name'] . '</span> ' . $data['autouser'][$row['user_id']]['display'];
		$data['autouser'][$row['user_id']]['value'] = html_entity_decode(strip_tags($data['autouser'][$row['user_id']]['display']));
	}
}

# help issues with PHP json_encode() prior to 5.3.0 (and possibly after)
if (is_array($data['autocontact']) && is_array($data['autouser']))
	foreach (array_merge($data['autocontact'], $data['autouser']) as $k1 => $v1) {
		$data['json'][] = $v1;
	}
elseif (is_array($data['autocontact']))
	foreach ($data['autocontact'] as $k1 => $v1) {
		$data['json'][] = $v1;
	}
elseif (is_array($data['autouser']))
	foreach ($data['autouser'] as $k1 => $v1) {
		$data['json'][] = $v1;
	}
