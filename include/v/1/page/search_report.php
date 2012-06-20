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
$my_list_name = 'category';

$data[$my_array_name]['search']['select'][] = 'count(t1.id) as tag_count';
$data[$my_array_name]['search']['select'][] = 't1.name as tag_name';
$data[$my_array_name]['search']['select'][] = 't1.id as tag_id';

$data[$my_array_name]['search']['from'][] = 'ts_tag t2';
$data[$my_array_name]['search']['from'][] = 'ts_item i';

$data[$my_array_name]['search']['where'][] = 'i.active = 1';
$data[$my_array_name]['search']['where'][] = 'i.tag_id = t2.id';
$data[$my_array_name]['search']['where'][] = 't2.parent_id = t1.id';

search_lock($data[$my_array_name], $my_list_name, $_SESSION['login']['login_user_name']);
listing_engine($data[$my_array_name], $my_list_name, $_SESSION['login']['login_user_name']);

# todo fix the need for this override in the engine. vaskoiii 2012-06-16
$limit = get_db_single_value('
		count(tag_id)
	from
		' . $config['mysql']['prefix'] . 'link_tag
');
$data[$my_array_name]['search']['order_by'] = array('tag_count desc'); # todo fix the necessity for this override in the engine. vaskoiii 2012-06-16
# order by is not needed it happens later.

$sql = get_engine_result_listing_sql($data[$my_array_name], $limit);

# todo add another case for listings (with this one exception) so this hack is unneded. 2012-06-16 vaskoiii
$sql = str_replace('t1.user_id = u.id', 'i.user_id = u.id', $sql);

$data[$my_array_name]['result']['listing'] = array();
$result = mysql_query($sql) or die(mysql_error());
while ($row = mysql_fetch_assoc($result))
	$data[$my_array_name]['result']['listing'][] = $row;

# translate
$key['tag_id']['select']['translation_description'] = 'translation_description';
foreach($data[$my_array_name]['result']['listing'] as $k1 => $v1) {
	add_key('tag', $v1['tag_id'], 'tag_name', $key);
}
# todo sort categories alphabetical without left outer join. vaskoiii 2012-06-16
# todo add some base categories to the initial install.
