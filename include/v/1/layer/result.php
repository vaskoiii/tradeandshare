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

add_translation('element', 'delete');
add_translation('element', 'export');
add_translation('element', 'import');
add_translation('element', 'judge');
add_translation('element', 'remember');
add_translation('element', 'forget');
add_translation('element', 'memorize');
add_translation('element', 'tag_uid');
add_translation('element', 'is_empty');
add_translation('element', 'no_result');
add_translation('element', 'browse_all');
add_translation('element', 'error');
add_translation('element', 'edit');
add_translation('element', 'view');
add_translation('element', 'unset');
add_translation('element', 'remember');
add_translation('element', 'default');
add_translation('element', 'forget');
add_translation('element', 'more');
add_translation('element', 'less');
add_translation('element', 'is_empty');
add_translation('element', 'known');
add_translation('kind', 'category');
add_translation('kind', 'location');
add_translation('kind', 'team');
add_translation('element', $x['load']['list']['type'] . '_uid');

start_engine($data['result'], $x['load']['list']['type'], $_SESSION['login']['login_user_id']);
listing_key_translation($key, $translation, $data['result'], $x['load']['list']['type'], $_SESSION['login']['login_user_id']);

switch($x['load']['list']['type']) {
	case 'user':
	case 'contact':
	case 'incident':
	case 'meritopic':
	case 'location':
	case 'group':
	case 'team':
		# todo get 2x child counts for user and contact ie) contact (user) - X (X) 2012-04-18 vaskoiii
		add_key($x['load']['list']['type'], 0, get_child_listing_type($x['load']['list']['type']) . '_count', $key);
	break;
}

if (!empty($data['result']['result']['listing'])) {
foreach ($data['result']['result']['listing'] as $k1 => $v1) {
	switch($x['load']['list']['type']) {
		case 'channel':
			# todo fix hardcode!
			add_key($x['load']['list']['type'], $v1['channel_id'], 'translation_name');
			# alternatively could select translation_description
		break;
	}
	key_list_listing(
		$v1,
		'list',
		$x,
		$key
	);
} }
