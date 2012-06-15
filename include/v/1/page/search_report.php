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

$my_array_name = 'search_report';
$my_list_name = 'minder';

$data[$my_array_name]['search']['select'][] = 'COUNT(t1.kind_name_id)';
$data[$my_array_name]['search']['select'][] = 'tr.name AS translation_name';
$data[$my_array_name]['search']['select'][] = 'tr.description AS translation_description';
$data[$my_array_name]['search']['select'][] = 'tr.kind_name_id';

$data[$my_array_name]['search']['from'][] = $config['mysql']['prefix'] . 'translation tr';
//$data[$my_array_name]['search']['from'][] = $config['mysql']['prefix'] . 'kind ki';

//$data[$my_array_name]['search']['where'][] = 'mi.name = ki.name'; // join on name because id's are different
$data[$my_array_name]['search']['where'][] = 'ki.id = tr.kind_id';
$data[$my_array_name]['search']['where'][] = 't1.kind_name_id = tr.kind_name_id';

//$data[$my_array_name]['search']['where_x'][] = 'mi.name = "category"';
$data[$my_array_name]['search']['where_x'][] = 'ki.name = "tag"';
$data[$my_array_name]['search']['where_x'][] = 'tr.dialect_id = ' . (int)$_SESSION['dialect']['dialect_id'];

$data[$my_array_name]['search']['group_by'][] = 't1.kind_name_id';

$data[$my_array_name]['search']['order_by'][] = 'COUNT(t1.kind_name_id) DESC';

search_lock($data[$my_array_name], $my_list_name, $_SESSION['login']['login_user_name']);
listing_engine($data[$my_array_name], $my_list_name, $_SESSION['login']['login_user_name']);

$sql = get_engine_result_listing_sql($data[$my_array_name]);

$data[$my_array_name]['result']['listing'] = array();
$result = mysql_query($sql) or die(mysql_error());
while ($row = mysql_fetch_assoc($result))
	$data[$my_array_name]['result']['listing'][] = $row;
