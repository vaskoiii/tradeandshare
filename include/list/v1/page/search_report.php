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

# Contents/Description: Shows most relevant groupings of infomation

add_translation('element', 'more');
add_translation('element', 'less');
add_translation('element', 'everything');
add_translation('page', 'main');
add_translation('page', 'category_list');
add_translation('page', 'user_list');
add_translation('page', 'lock_set');
add_translation('element', 'search_mixed');

$data['search']['response']['search_miscellaneous'] = array( 'keyword' => get_gp('keyword'));
$data['search']['response']['search_content_1'] = get_search_content_1('item');
$data['search']['response']['search_content_2'] = get_search_content_2('item');
load_response('search_content_1', $data['search']['response']['search_content_1'], $_SESSION['login']['login_user_id']);
load_response('search_content_2', $data['search']['response']['search_content_2'], $_SESSION['login']['login_user_id']);
contact_user_mixed_combine($data['search']['response']['search_content_2'], get_gp('lock_user_id'), get_gp('lock_contact_id'), $_SESSION['login']['login_user_id'], 'lock_');
foreach ($data['search']['response'] as $k1 => $v1) {
if (!empty($v1)) {
foreach ($v1 as $k2 => $v2) {
	add_option($k2);
	add_translation('element', $k2);
} } }

$s1 = 'custom_key';
$s2 = 'custom_query';
$sql = '
	select
		tg.id as tag_id,
		tg.name as tag_name
	from
		' . $config['mysql']['prefix'] . 'tag tg,
		' . $config['mysql']['prefix'] . 'link_tag ltg
	where
		ltg.tag_id = tg.id
';
$result = mysql_query($sql) or ts_die(mysql_error());
while ($row = mysql_fetch_assoc($result)) {
	$data[$s1]['tag_id']['search'][$row['tag_id']] = $row['tag_id'];
	$data[$s1]['tag_id']['name'][$row['tag_id']] = $row['tag_name'];
	$data[$s1]['tag_id']['description'][$row['tag_id']] = '!'; # hardcode default
	$data[$s1]['tag_id']['count'][$row['tag_id']] = 0; # assume empty
}

# name/description
$sql = '
	select
		tln.kind_name_id as tag_id,
		tln.name as translation_name,
		tln.description as translation_description
	from
		' . $config['mysql']['prefix'] . 'translation tln
	where
		tln.kind_id = 11 and
		tln.kind_name_id in (' . implode(',', $data[$s1]['tag_id']['search']) . ') and
		tln.default = 1 and
		tln.dialect_id = ' . (int)$_SESSION['dialect']['dialect_id']
;
$result = mysql_query($sql) or ts_die(mysql_error());
while ($row = mysql_fetch_assoc($result)) {
	$data[$s1]['tag_id']['name'][$row['tag_id']] = $row['translation_name'];
	$data[$s1]['tag_id']['description'][$row['tag_id']] = $row['translation_description'];
}

# count
# todo: factor in vote/transfer count?
foreach($data[$s1]['tag_id']['search'] as $k1 => $v1) {
	$data[$s2] = array();
	$data[$s2]['search']['where_x'][] = 'a.parent_id = ' . (int)$k1;
	search_lock($data[$s2], 'item', $_SESSION['login']['login_user_id']);
	listing_engine($data[$s2], 'item', $_SESSION['login']['login_user_id']);
	get_engine_result_total($data[$s2]);
	$data[$s1]['tag_id']['count'][$k1] = $data[$s2]['result']['total'];
}

# sort
arsort($data[$s1]['tag_id']['count'], SORT_NUMERIC);
asort($data[$s1]['tag_id']['name']);
