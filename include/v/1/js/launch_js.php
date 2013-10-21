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
add_translation('page', 'main');
# elements for now
add_translation('element', 'page_parent');
add_translation('element', 'page_next');
add_translation('element', 'page_previous');
add_translation('element', 'page_last');
add_translation('element', 'page_first');
do_translation($key, $translation, $_SESSION['dialect']['dialect_id'], $_SESSION['login']['login_user_id']);

$data['css']['theme_name'] = 'theme_' . $data['theme']['color']; # like using a function paramerter for the include below
#include($x['site']['i'] . 'css/background_color.php');
$data['css'] = array_merge($data['css'], get_background($s1));

$data['lock_query'] = get_lock_query();

# reuse data from the ajax
include('v/1/ajax/autopage_ajax.php');
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

// slow?
$_GET['all'] = 1;
if ($_SESSION['login']['login_user_id']) {
	$data['json'] = array(); # clear the previous page data
	include('v/1/ajax/autocontact_autouser_ajax.php');
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
	//print_r($data['json']);

	$peopleJson = json_encode($data['json']);
}
else
	$peopleJson = '';

$request_uri=  $_SERVER['REQUEST_URI'];

# Easy customization of the empty pages (ordinarily style doesnt go in the engine pages)
# don't forget target="_top"
# whitespace in the fist column otherwise preg_replace() doesnt work right and the launcher wont load
$data['launch']['tsl']['empty'] = <<<HTML
 <table><tr>
	<td>
		<a href="/" target="_top" style="margin-left: -20px;"><img src="/v/1/theme/{$data['theme']['color']}/ts_icon.png" /></a>
	</td>
	<td valign="center" style="padding-left: 5px;">
	<a href="/" target="_top" style="color: #000;">{$translation['page_name']['result']['main']['translation_name']}</a></td>
 </tr></table>
HTML;
$data['launch']['tsl']['empty'] = preg_replace('/\s\s+/', '', $data['launch']['tsl']['empty']);

# people launcher
if ($_SESSION['login']['login_user_id']) {
$s1 = '/user_view/?lock_user_id=' . (int)$_SESSION['login']['login_user_id'];
$data['launch']['tsl']['empty'] = preg_replace('/\s\s+/', '', $data['launch']['tsl']['empty']);
$data['launch']['tslPeople']['empty'] = <<<HTML
 <table><tr>
	<td>
	<a href="{$s1}" target="_top" style="margin-left: -20px;"><img src="/v/1/theme/select_none/ts_icon.png" /></a>
	</td>
	<td valign="center" style="padding-left: 5px;">
	Me (<a href="{$s1}" target="_top" style="color: #000;">{$_SESSION['login']['login_user_name']}</a>)</td>
 </tr></table>
HTML;
}
else {
$s1 = '/login_set/';
$data['launch']['tslPeople']['empty'] = <<<HTML
 <table><tr>
	<td>
	<a href="{$s1}" target="_top" style="margin-left: -20px;"><img src="/v/1/theme/select_none/ts_icon.png" /></a>
	</td>
	<td valign="center" style="padding-left: 5px;">
	<a href="{$s1}" target="_top" style="color: #000;">{$translation['page_name']['result']['login_set']['translation_name']}</a>)</td>
 </tr></table>
HTML;
}
$data['launch']['tslPeople']['empty'] = preg_replace('/\s\s+/', '', $data['launch']['tslPeople']['empty']);
