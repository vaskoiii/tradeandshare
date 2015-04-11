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

# Contents/Description: quickly navigate through most translated pages on TS.


$public_key = get_db_single_value('value from ' . $config['mysql']['prefix'] . 'pubkey where user_id = ' . (int)$_SESSION['login']['login_user_id'], false);

# todo these keys should not be public | use fread() instead
# todo not sure if this needs to run every time
# todo do not hardcode the document_root
if ($public_key) {
	# create qrcode in profile_edit
	# include('phpqrcode/1.1.4/qrlib.php');
	# QRcode::png('https://list.vaskos.com/host_portal/?public_key=' . to_url($public_key), '/www/site/list/public/phpqrcode/1.1.4/temp/' . $_SESSION['login']['login_user_name'] . '.png', 'L', 4, 2);
}
# setcookie used from javascript in the launcher this only sets the initial value
if(!isset($_COOKIE['launch'])) {
	$_COOKIE['launch'] =  array(
		'element_name' => 'main',
		'translation_name' => '!'
	);	
}

$s1 = str_replace('theme_', '', $_SESSION['theme']['launcher_theme_name']);
# added 2011-08-25
$data['theme']['color'] = $s1;
#$data['theme']['color'] = 'green'; # testing override 2012-03-18 vaskoiii

# extra translation  it is done again below.
add_translation('page', 'guest_portal');
add_translation('page', 'host_portal');
add_translation('page', 'main');
add_translation('page', 'login_set');
# elements for now
add_translation('element', 'page_parent');
add_translation('element', 'page_next');
add_translation('element', 'page_previous');
add_translation('element', 'page_last');
add_translation('element', 'page_first');
do_translation($key, $translation, $_SESSION['dialect']['dialect_id'], $_SESSION['login']['login_user_id']);

$data['lock_query'] = get_lock_query();

# reuse data from the ajax
include('list/v1/ajax/autopage_ajax.php');
# custom (back/next/previous/first/last)
#print_r($data);
$a1 = array(
	'page_parent',
	'page_next',
	'page_previous',
	'page_last',
	'page_first',
);
foreach ($a1 as $k1 => $v1) {
	$data['json'][] = array(
		'value' => $v1,
		'display' => tt('element', $v1),
	);
}

$pageJson = json_encode($data['json']);

# username overflows the launcher and there is no logic to search for user name as of 2013-10-23 vaskoiii
$_GET['all'] = 1;
$_GET['contact_only'] = 1;
if ($_SESSION['login']['login_user_id']) {
	$data['json'] = array(); # clear the previous page data
	include('list/v1/ajax/autocontact_autouser_ajax.php');
	# launcher fails with weird data.
	# todo fix people launcher from only working after page launcher is fired
	# todone use . as a wildcard in the launcher
	foreach ($data['json'] as $k1 => $v1) {
		/*
		//url encode makes things safe
		$data['json'][$k1]['value'] = urlencode(strip_tags($v1['value']));
		$data['json'][$k1]['display'] = urlencode(strip_tags($v1['display']));
		# it doesnt like the pipe |
		# becareful with characters js doesn't like
		 */
		// todo: make these characters more safe
		$data['json'][$k1]['value'] = (strip_tags($v1['value']));
		$data['json'][$k1]['display'] = (strip_tags($v1['display']));
		$a1 = array(
			'|', # |root|
			'\'' # john's
		);
		$a2 = array(
			'',
			''
		);
		$data['json'][$k1]['value'] = str_replace($a1, $a2, $data['json'][$k1]['value']);
		$data['json'][$k1]['display'] = str_replace($a1, $a2, $data['json'][$k1]['display']);
	}
	$peopleJson = json_encode($data['json']);
}
else
	$peopleJson = '';

# needed?
$request_uri =  $_SERVER['REQUEST_URI'];

# Easy customization of the empty pages (ordinarily style doesnt go in the engine pages)
# don't forget target="_top"
# whitespace in the fist column otherwise preg_replace() doesnt work right and the launcher wont load
$data['launch']['pager']['empty'] = <<<HTML
	<div>
		<a href="/"><img src="/list/v1/theme/{$data['theme']['color']}/ts_icon_256x256.png" /></a>
	</div>
HTML;
$data['launch']['pager']['empty'] = preg_replace('/\s\s+/', '', $data['launch']['pager']['empty']);

# people launcher
if ($_SESSION['login']['login_user_id'])
	$s1 = '/user_view/?lock_user_id=' . (int)$_SESSION['login']['login_user_id'];
else
	$s1 = '/login_set/';
# both
$s2 = '/list/v1/theme/select_none/ts_icon_256x256.png';
$data['launch']['peopler']['empty'] = <<<HTML
	<div>
		<a href="{$s1}"><img src="{$s2}" /></a>
	</div>
HTML;
$data['launch']['peopler']['empty'] = preg_replace('/\s\s+/', '', $data['launch']['peopler']['empty']);

# scan launcher
if ($public_key) {
$s1 = '/host_portal/?public_key=' . to_url($public_key);
$i1 = get_db_single_value('
		id
	from
		' . $config['mysql']['prefix'] . 'filer
	where
		user_id = ' . (int)$_SESSION['login']['login_user_id'] . '
', false);
$s2 = '/file/?id=' . (int)$i1; # png
$data['launch']['scanner']['empty'] = <<<HTML
	<div>
		<a href="{$s1}"><img src="{$s2}" /></a>
	</div>
HTML;
}
else {
$data['launch']['scanner']['empty'] = <<<HTML
	<div>
		<a href="/profile_edit/">Add Public Key</a>
	</div>
HTML;
}
$data['launch']['scanner']['empty'] = preg_replace('/\s\s+/', '', $data['launch']['scanner']['empty']);
