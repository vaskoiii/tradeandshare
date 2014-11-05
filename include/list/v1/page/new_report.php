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

# Contents/Description: Relevant recent activity stats for different list types throughout TS

include('list/v1/inline/site_map.php');

add_translation('element', 'ignore_process');
add_translation('element', 'expand_all');
add_translation('element', 'collapse_all');
add_translation('element', 'more');
add_translation('element', 'less');
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

function new_report_new_count($listing_type, $when) {
	// $table alias can be found from listing_engine() ... Maybe we should consolidate...
	global $data;
	global $config;
	if (!$when)
		return 0;
	switch($listing_type) {
		// _DOC
		case 'about_doc':
		case 'disclaimer_doc':
		case 'donate_doc':
		case 'download_doc':
		case 'faq_doc':
		case 'landingmap_doc':
		case 'merit_doc':
		case 'sitemap_doc':
		case 'tutorial_doc':
		case 'trailer_doc':
		case 'notation_doc':
			// we could probaly do these all at once with a single query since we are only checking whether the docs have been updated or not.  we don't need an actual count.
			// TODO make it save like 8 queries in the future...
			$data['result']['result']['total'] = get_db_single_value('
					1
				FROM
					' . $config['mysql']['prefix'] . 'doc d,
					' . $config['mysql']['prefix'] . 'visit v,
					' . $config['mysql']['prefix'] . 'page p
				WHERE
					d.page_id = p.id AND
					v.page_id = p.id AND
					p.name = ' . to_sql($listing_type) . ' AND
					v.user_id = ' . (int)$_SESSION['login']['login_user_id'] . ' AND
					d.modified > v.`when`
			');
			return $data['result']['result']['total'];
		break;
	}
	switch($listing_type) {
		// These links have no correllation with new activity though they are still monitored and possibly wanted for quick links.
		// _LIST
		case 'contact':
		case 'note':
		case 'group':
		case 'groupmate':
		case 'feed':
		// _RECOVER
		#case 'rss_recover':
		#case 'login_recover':
		// _SET
		#case 'login_set':
		#case 'lock':
		case 'theme':
		case 'display':
		case 'dialect':
			return 0;
		break;
	}
	// BUILD SQL
	unset($data['result']['search']);
	$data['result']['search']['where_x'] = array();
	search_lock($data['result'], $listing_type, $_SESSION['login']['login_user_id']);
	listing_engine($data['result'], $listing_type, $_SESSION['login']['login_user_id']);
	prepare_engine($data['result'], $listing_type, $_SESSION['login']['login_user_id']);
	$table_alias = get_main_table_alias($listing_type);
	switch($listing_type) {
		case 'login':
			$data['result']['search']['where'][] = $table_alias . '.`when` >= ' . to_sql($when);
		break;
		default:
			$data['result']['search']['where'][] = $table_alias . '.modified >= ' . to_sql($when);
		break;
	}
	$debug = false;
	if ($config['debug'] == 1) {
		$debug = true;
		echo '<hr />' . $listing_type; 
	}
	get_engine_result_total($data['result']);
	return $data['result']['result']['total'];
}

foreach($data['new_report']['page_id'] as $k1 => $v1) {
	if (!empty($v1['page_id'])) {
		add_translation('page', $v1['page_name']);
		foreach($v1['page_id'] as $k2 => $v2) {
			add_translation('page', $v2['page_name']);
			if ($v2['view_when'])
				$data['new_report']['page_id'][$k1]['page_id'][$k2]['new_amount'] = new_report_new_count(preg_replace('/\_list/', '', $v2['page_name']), $v2['view_when']);
			// Do NOT Care (Do NOT show new)
			// TODO: fix so we dont need to run the queries still.
			switch ($v2['page_name']) {
				case 'user_list':
				case 'tag_list':
					if (isset($data['new_report']['page_id'][$k1]['page_id'][$k2]['new_amount']))
						$data['new_report']['page_id'][$k1]['page_id'][$k2]['new_amount'] = 0;
				break;
			}
		}
	}
}
