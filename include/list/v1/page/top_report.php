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

# Contents/Description: Conglomerated feed of the most pertinent top most recently added listings. Currently: item, login, news
# Known Issues: results per page is variable because datetime may be the same for multiple entries which had a "selection action"
# todo put a limit on the variable max results per page (unless hacking the most results per page should be 19 because a "selection action" can only be done on 10 items)

# try to get the action box to work
if (get_gp('action_name'))
	$x['load']['action']['name'] = get_gp('action_name');
if (get_gp('action_type'))
	$x['load']['action']['type'] = get_gp('action_type');
if (get_gp('action_id'))
	$x['load']['action']['id'] = get_gp('action_id');
include('list/v1/layer/action.php');

if (empty($option['page_name']))
	$option['page_name'] = array();

if (!$_SESSION['login']['login_user_id'])
	die('not logged in...');

/*
todo: intense paging
Every section would have its own individual paging we would have to keep track of:
ie) next page would look like:
?item_offset=5&news_offset=12&login_offset=111
so results would display:
- 5 to 5+X for item
- 12 to 12+Y for news
- 111 to 111+Z for login
*/

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

add_translation('element', 'default');
add_translation('element', 'delete');
add_translation('element', 'edit');
add_translation('element', 'export');
add_translation('element', 'forget');
add_translation('element', 'import');
add_translation('element', 'item_uid');
add_translation('element', 'judge');
add_translation('element', 'known');
add_translation('element', 'less');
add_translation('element', 'login_uid');
add_translation('element', 'more');
add_translation('element', 'news_uid');
add_translation('element', 'page_name');
add_translation('element', 'remember');
add_translation('element', 'search_mixed');
add_translation('element', 'unset');
add_translation('element', 'view');
add_translation('kind', 'location');
add_translation('kind', 'tag');
add_translation('kind', 'team');
add_translation('page', 'item_list');
add_translation('page', 'lock_set');
add_translation('page', 'login_list');
add_translation('page', 'news_list');

$data['result']['result']['listing'] = array();

# doesnt account for entries with the same datetime
function top_reportage($type, $limit, $datetime_lower_limit = '', $datetime_upper_limit = '') {
	global $data;
	print_debug($type);
	search_lock($data['result'], $type, $_SESSION['login']['login_user_id']);
	listing_engine($data['result'], $type, $_SESSION['login']['login_user_id']);
	prepare_engine($data['result'], $type, $_SESSION['login']['login_user_id']);

	# Note: Facebook shows YOUR listings in the news.
	$table_alias = get_main_table_alias($type);

	# restrict by dates may have to be done in the engine 2012-03-28 vaskoiii
	$s2 = '.modified';
	if ($type == 'login')
		$s2 = '.`when`';
	$data['result']['search']['where'][] = $table_alias . $s2 . ' >= ' . to_sql($datetime_lower_limit);
	if ($datetime_upper_limit)
		$data['result']['search']['where'][] = $table_alias . $s2 . ' < ' . to_sql($datetime_upper_limit);

	if ($limit)
		$sql = get_engine_result_listing_sql($data['result'], (int)$limit);
	else
		$sql = get_engine_result_listing_sql($data['result']);

	$result = mysql_query($sql) or die(mysql_error());
	$i1 = 0;
	while ($row = mysql_fetch_assoc($result)) {
		++$i1;
		$data['result']['result']['listing_key_count']++;
		$listing_key = $type . '_' . $row[$type . '_id'];
		$data['result']['result']['listing'][$listing_key] = $row;
		$data['result']['result']['listing'][$listing_key]['list_type'] = $type;
		$data['result']['result']['listing_key'][$listing_key] = $row['modified']; # Use with arsort()
	}
	unset($data['result']['search']);

	# todo: populate $key 2012-02-08 vaskoiii
	listing_key_translation($key, $translation, $data['result'], $type, $_SESSION['login']['login_user_id']);

	if (!empty($data['result']['result']['listing'])) {
	foreach ($data['result']['result']['listing'] as $k1 => $v1) {
		# inject
		$x['load']['list']['name'] = 'list';
		$x['load']['list']['type'] = $type;
		key_list_listing(
			$v1,
			'list',
			$x,
			$key
		);
	} }

	return $i1;
}

# todo define this more special somewhere. 2012-03-28 vaskoiii
# todo ideally show everything except system listings.
# intentionally not sorted alphabetically !!! most common entries will be at the top (thus reducing overhead) 2012-04-22 vaskoiii
# comment out sections that you dont want to display in the conglomerated feed (todo ideally just |root| activity would be hidden and everything else would be shown)
$a1 = array(
	# DO NOT SORT!
	'offer' => '',
	'item' => '',
	'news' => '',
	'category' => '',
	'user' => '',
	'teammate' => '',
	'location' => '',
	'login' => '',
	'dialect' => '',
	'transfer' => '',
	'meripost' => '',
	'meritopic' => '',
	'invited' => '',
	'feed' => '',
	'metail' => '',
	'minder' => '',
#	'contact' => '',
#	'note' => '',
#	'team' => '',
#	'tag' => '',
#	'incident' => '',
#	'feedback' => '',
#	'translation' => '',
#	'jargon' => '',
	# todo let user choose which items they want to see ie) like a subscription
	# 'score' => '',
	# 'comment' => '',
	# 'report' => '',
);

foreach($a1 as $k1 => $v1) {
	add_translation('element', $k1 . '_uid');
}

if (get_gp('list_datetime_upper_limit'))
	$datetime_upper_limit = get_gp('list_datetime_upper_limit');
else
	$datetime_upper_limit = date('Y-m-d H:i:s', time());

if (get_gp('list_datetime_lower_limit'))
	$datetime_lower_limit = get_gp('datetime_lower_limit');
else
	$datetime_lower_limit = '2003-01-01 00:00:00'; # doesnt have to be exact

$data['result']['result']['listing_key'] = array();
$data['result']['result']['listing_key_count'] = 0;
$i2 = 10; # rerun datetime query for this
foreach ($a1 as $k1 => $v1) {
	add_translation('page', $k1 . '_list');
	if ($datetime_upper_limit)
		$a1[$k1] = top_reportage($k1, $i2, $datetime_lower_limit, $datetime_upper_limit);
	else
		$a1[$k1] = top_reportage($k1, $i2, $datetime_lower_limit);
	if (!empty($data['result']['result']['listing_key']))
		arsort($data['result']['result']['listing_key']);

	# free up some space
	$i3 = 1; # counter for the last spot!
	if (!empty($data['result']['result']['listing_key'])) {
	foreach ($data['result']['result']['listing_key'] as $k2 => $v2) {
		if ($i3 <= $i2) {
		if ($i3 == $i2 && $datetime_lower_limit < $v2) {
			$datetime_lower_limit = $v2;
			#die($datetime_lower_limit);
		} }
		else {
		if ($v2 < $datetime_lower_limit) { # unsetting causes ommissions
			unset($data['result']['result']['listing_key'][$k2]);
			unset($data['result']['result']['listing'][$k2]);
		} }
		$i3++;
	} }
}

# debug show how many entries were actually retrieved 2012-03-28 vaskoiii
# (before checking for dupicate datetimes)
if ($config['debug'] == 1) {
	echo '<hr>' . $data['result']['result']['listing_key_count'];
}

function datetime_reportage(& $data, $listing_key) {
	# get the the type and rerun the search to append neglected results
	$type = $data['result']['result']['listing'][$listing_key]['list_type'];
	$modified = $data['result']['result']['listing'][$listing_key]['modified'];

	search_lock($data['result'], $type, $_SESSION['login']['login_user_id']);
	listing_engine($data['result'], $type, $_SESSION['login']['login_user_id']);
	prepare_engine($data['result'], $type, $_SESSION['login']['login_user_id']);

	# Equal To (NOT range)
	$table_alias = get_main_table_alias($type);
	$s2 = '.modified';
	if ($type == 'login')
		$s2 = '.`when`';
	$data['result']['search']['where'][] = $table_alias . $s2 . ' = ' . to_sql($modified);

	$sql = get_engine_result_listing_sql($data['result']);

	$result = mysql_query($sql) or die(mysql_error());
	$counter = 0;
	while ($row = mysql_fetch_assoc($result)) {
		$listing_key = $type . '_' . $row[$type . '_id'];
		$data['result']['result']['listing'][$listing_key] = $row;
		$data['result']['result']['listing'][$listing_key]['list_type'] = $type;
		$data['result']['result']['listing_key'][$listing_key] = $row['modified']; # Use with arsort()
		$counter++;
	}
	unset($data['result']['search']);

	# todo: populate $key 2012-02-08 vaskoiii
	listing_key_translation($key, $translation, $data['result'], $type, $_SESSION['login']['login_user_id']);

	return $counter;
}

if ($config['debug'] == 1) {
	echo '<pre>'; print_r($a1); echo '</pre>';
}

# todo make this not repeate once per listing. only make it repeat once per page 2012-04-10 vaskoiii
$a2 = array();

foreach ($data['result']['result']['listing_key'] as $k1 => $v1) {
	if ($v1 == $datetime_lower_limit) {
		$e1 = explode('_', $k1);
		if ($a1[$e1[0]] >= $i2 && !in_array($e1[0], $a2)) { 
			$a2 += array($e1[0]);
			$a1[$e1[0]] = datetime_reportage($data, $k1);
		}
	}
	if (!empty($data['result']['result']['listing_key'])) {
		arsort($data['result']['result']['listing_key']);
	}
}
# todo keep like items together if made at same time doesnt work consistently 2012-04-10 vaskoiii
#krsort($data['result']['result']['listing_key']);
#arsort($data['result']['result']['listing_key']);

# debug show how many entries were actually retrieved 2012-03-28 vaskoiii
# (after checking for duplicate datetimes)
if ($config['debug'] == 1) {
	echo '<hr>' . $listing_key;
}
