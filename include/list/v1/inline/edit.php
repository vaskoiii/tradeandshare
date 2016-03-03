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



# lets make a new array to house the edit structure
# todo add to debug(); in footer.php
$edit = array();
# probably would be better at this point to just build out this entire array since it looks like it will now be used on every page.
# not sure about building a static data structure for the other box types 2014-02-22 vaskoiii
foreach($data['new_report'] as $k1 => $v1) {
foreach($v1 as $k2 => $v2) {
if (isset($v2['page_id'])) {
foreach($v2['page_id'] as $k3 => $v3) {
	$s1 = str_replace('_list', '', $v3['page_name']);
	$edit[$s1]['content_1'] = get_action_content_1($s1, 'edit');
	$edit[$s1]['content_2'] = get_action_content_2($s1, 'edit');
	if (empty($edit[$s1]['content_1']))
		unset($edit[$s1]['content_1']);
	if (empty($edit[$s1]['content_2']))
		unset($edit[$s1]['content_2']);
} } } }
foreach($edit as $k1 => $v1) 
	if (empty($v1))
		unset($edit[$k1]);
# echo '<pre>'; print_r($edit); echo '</pre>';
# since this edit array will be used on every page the stucture for edits can be grabbed from here generally.
# todo put in an engine file


# TRANSLATION
# todo check if this is already handled by load_repsonse
foreach ($edit as $k1 => $v1)
foreach ($v1 as $k2 => $v2)
foreach ($v2 as $k3 => $v3) {
	# needed here?
	add_option($k3);
	add_translation('element', $k3);
	# hardcoded defaults
	if (!$_SESSION['process']['failure']) {
	if (!$x['load']['fast']['id']) {
	switch($k3) {
		# may be useful if needed elsewhere
		# fgrep '|*|' . -R
		case 'status_name':
			if (!$v3)
				$edit[$k1][$k2][$k3] = 'status_neutral';
		break;
		case 'parent_tag_path':
		case 'parent_tag_name':
			if (!$v3)
				$edit[$k1][$k2][$k3] = '<|!|>';
		break;
		case 'dialect_name':
			 if (!$v3)
				$edit[$k1][$k2][$k3] =  $_SESSION['dialect']['dialect_name'];
		break;
		case 'team_required_name':
			 if (!$v3)
				$edit[$k1][$k2][$k3] =  '<|*|>';
		break;
		case 'location_name':
			if (!$v3)
				$edit[$k1][$k2][$k3] = get_db_single_value('
					l.name 
				FROM 
					' . $config['mysql']['prefix'] . 'location l, 
					' . $config['mysql']['prefix'] . 'user u 
				WHERE 
					u.location_id = l.id AND 
					u.id = ' . (int)$_SESSION['login']['login_user_id']
			);
		break;
	} } }
} 

