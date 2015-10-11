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

# Contents/Description: Landing page engine

# be careful not to keep headering back to the same place here.
# REDIRECT_QUERY_STRING not supported in php 5.3.5?
if ($_SESSION['login']['login_user_id'] && !empty($_SESSION['lock']) && $_SESSION['feature']['feature_lock'] == 1 && empty($_SERVER['QUERY_STRING']) && empty($_SERVER['REDIRECT_QUERY_STRING'])) {
	header('location: ' . get_lock_query());
	exit;
}

add_translation('element', 'edit');
add_translation('element', 'add');
add_translation('boolean', 'false');
add_translation('boolean', 'true');
add_translation('element', 'add_feed');
add_translation('element', 'add_item');
add_translation('element', 'asearch_off');
add_translation('element', 'asearch_on');
add_translation('element', 'error');
add_translation('element', 'extra');
add_translation('element', 'find_item');
add_translation('element', 'item_uid');
add_translation('element', 'less');
add_translation('element', 'more');
add_translation('element', 'search');
add_translation('element', 'search_' . $x['part'][0]);
add_translation('element', 'set_again');
add_translation('element', 'set_login');
add_translation('element', 'translate');
add_translation('element', 'unset');
add_translation('element', 'unset_login');
add_translation('kind', 'tag');
add_translation('page', 'about_doc');
add_translation('page', 'config_report');
add_translation('page', 'disclaimer_doc');
add_translation('page', 'donate_doc');
add_translation('page', 'download_doc');
add_translation('page', 'faq_doc');
add_translation('page', 'item_list');
add_translation('page', 'vote_list');
add_translation('page', 'main');
add_translation('page', 'manager');
add_translation('page', 'new_report');
add_translation('page', 'offer_list');
add_translation('page', 'search_report');
add_translation('page', 'top_report');
add_translation('page', 'trailer_doc');
add_translation('page', 'transfer_list');
add_translation('page', 'cycle_report');

if ($_SESSION['login']['login_user_id']) {
	$data['search']['response']['search_miscellaneous'] = array(
		'keyword' => get_gp('keyword'),
	);

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
}
