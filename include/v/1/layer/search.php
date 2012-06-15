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

# Contents/Description: Sets up the form data && Decides what fields will be displayed

add_translation('boolean', 'false');
add_translation('boolean', 'true');
add_translation('element', $x['part'][0] . '_uid');
add_translation('element', 'add');
add_translation('element', 'add_' . get_child_listing_type($x['part'][0]));
add_translation('element', 'add_feed');
add_translation('element', 'add_feedback');
add_translation('element', 'add_more');
add_translation('element', 'add_note');
add_translation('element', 'asearch_off');
add_translation('element', 'asearch_on');
add_translation('element', 'edit');
add_translation('element', 'error');
add_translation('element', 'extra');
add_translation('element', 'find_' . $x['load']['list']['type']);
add_translation('element', 'find_' . $x['part'][0]);
add_translation('element', 'find_' . get_gp('child'));
add_translation('element', 'less');
add_translation('element', 'more');
add_translation('element', 'recover');
add_translation('element', 'reedit');
add_translation('element', 'search');
add_translation('element', 'search_' . $x['part'][0]);
add_translation('element', 'send');
add_translation('element', 'send_more');
add_translation('element', 'set');
add_translation('element', 'set_again');
add_translation('element', 'submit');
add_translation('element', 'translate');
add_translation('element', 'unset');
add_translation('kind', 'tag');

$data['search']['response']['search_miscellaneous'] = array( 'keyword' => get_gp('keyword'));
$data['search']['response']['search_content_1'] = get_search_content_1($x['load']['list']['type']);
$data['search']['response']['search_content_2'] = get_search_content_2($x['load']['list']['type']);

$search_content_1 = & $data['search']['response']['search_content_1'];
$search_content_2 = & $data['search']['response']['search_content_2'];
load_response('search_content_1', $search_content_1, $_SESSION['login']['login_user_id']);
load_response('search_content_2', $search_content_2, $_SESSION['login']['login_user_id']);

contact_user_mixed_combine($search_content_2, get_gp('lock_user_id'), get_gp('lock_contact_id'), $_SESSION['login']['login_user_id'], 'lock_');

# DOUBLE CHECK that [contact] is filled if [user] is used.
if (!empty($data['container']))
	foreach ($data['container'] as $k1 => $v1) {
		contact_filler($k1, 'lock_');
		contact_filler($k1);
	}

# hack 2012-04-22 vaskoiii
# only happens on translation and jargon
# overwrite default (to enable a language neutral search)
if (isset($search_content_1['dialect_name'])) {
if (!get_gp('dialect_id')) {
if (!$_SESSION['interpret']['failure'] == 1) {
	$search_content_1['dialect_name'] = '';
} } }

# translation
foreach ($data['search']['response'] as $k1 => $v1)
if (!empty($v1))
foreach ($v1 as $k2 => $v2) {
	add_option($k2);
	add_translation('element', $k2);
}

