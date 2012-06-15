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

# unset the result pages if we have an error????
add_translation('page', $x['load']['view']['type'] . '_view');
add_translation('element', $x['load']['view']['type']);
# Author: John Vasko
# Contents/Description: List of NOT-SO-DYNAMIC view queries

# quick and dirty 2012-03-26 vaskoiii
$option['page_name'] = array();

switch($x['load']['view']['type']) {
	case 'category':
	case 'group':
	case 'incident':
	case 'location':
	case 'meritopic':
	case 'team':

	# super hack
	# problems because lock_group_id and group_id cause problems in the engine on the view page /group_view/?lock_group_id=100&group_id=100
	# the original version worked with: /group_view/?child=groupmate&group_id=14
	# but not with: /group_view/?child=groupmate&lock_group_id=14
	# and had the same behaviour with /group_view/?child=groupmate&group_id=14&lock_group_id=14
	# so this is probabably symptomatic of a problem with the engine.
	$b1 = 1;
	if ($x['name'] == 'group_view') {
	if (get_gp('group_id')) {
	if (get_gp('lock_group_id')) {
		$b1 = 2;
		$i1 = $_GET['lock_group_id'];
		unset($_GET['lock_group_id']);
		start_engine($data['view'], $x['load']['view']['type'], $_SESSION['login']['login_user_id'], array($x['load']['view']['id']), 'view');
		$_GET['lock_group_id'] = $i1;
	} } }
	if ($b1 == 1)
		start_engine($data['view'], $x['load']['view']['type'], $_SESSION['login']['login_user_id'], array($x['load']['view']['id']), 'view');
	break;
	case 'user':
		add_key($x['load']['view']['type'], 0, get_child_listing_type($x['load']['view']['type']) . '_count', $key);
		start_engine($data['view'], $x['load']['view']['type'], $_SESSION['login']['login_user_id'], array(get_gp('lock_user_id')), 'view');
	break;
	case 'contact':
		add_key($x['load']['view']['type'], 0, get_child_listing_type($x['load']['view']['type']) . '_count', $key);
		if (isset_gp('lock_user_id')) {
			
			# todo try and simplify with:
			# start_engine($data['view'], 'user', $_SESSION['login']['login_user_id'], array(get_gp('lock_user_id'))); # 2012-02-26 vaskoiii
			$sql = 'SELECT
					c.user_id,
					c.id as contact_id,
					c.name AS contact_name,
					c.modified
				FROM
					' . $config['mysql']['prefix'] . 'contact c,
					' . $config['mysql']['prefix'] . 'link_contact_user lcu
				WHERE
					c.id = lcu.contact_id AND
					c.active = 1 AND
					lcu.user_id = ' . (int)get_gp('lock_user_id') . ' AND
					c.user_id = ' . (int)$_SESSION['login']['login_user_id']
			;
			$sql .= ' LIMIT 1';
		
			if ($config['debug'] == 1)
				echo $sql;
			
			$data['view']['result']['listing'] = array();
			$result = mysql_query($sql) or die(mysql_error());
			while ($row = mysql_fetch_assoc($result))
				$data['view']['result']['listing'][] = $row;
		}
		# just in case something messed up and for some reason the contact_id is set instead of the user_id 2012-02-26 vaskoiii 
		elseif (isset_gp('lock_contact_id'))
			start_engine($data['view'], $x['load']['view']['type'], $_SESSION['login']['login_user_id'], array(get_gp('lock_contact_id')), 'view');
	break;
}

listing_key_translation($key, $translation, $data['view'], $x['load']['view']['type'], $_SESSION['login']['login_user_id']);

if (!empty($data['view']['result']['listing'])) {
foreach ($data['view']['result']['listing'] as $k1 => $v1) {
	key_list_listing(
		$v1,
		'view',
		$x,
		$key
	);
} }

add_translation('element', 'add_offer');
add_translation('element', 'add_contact');
