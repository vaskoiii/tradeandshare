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

# Contents/Description: Display feed data - x.php should have already authenticated access
# Known Issues: Even though feed uses $_SESSION the session should not be started so values should NOT be persistent

$config['t1/']['result_amount_per_page'] = 20; # override exception on value defined in config!

$key = array();
$translation = array();
add_translation('element', 'add_' . str_replace('_list', '', $x['feed_atom']['page_name']));
start_engine($data['result'], $x['feed_atom']['part']['0'], $x['feed_atom']['user_id']);

if (!empty($data['result']['result']['listing'])) {
foreach($data['result']['result']['listing'] as $k1 => $v1) {
	key_list_listing(
		$v1,
		'list',
		$x,
		$key
	);
} }
listing_key_translation($key, $translation, $data['result'], $x['feed_atom']['part'][0], $_SESSION['login']['login_user_id']);
do_key($key, $translation, $_SESSION['dialect']['dialect_id'], $_SESSION['login']['login_user_id']);
do_translation($key, $translation, $_SESSION['dialect']['dialect_id'], $_SESSION['login']['login_user_id']);
