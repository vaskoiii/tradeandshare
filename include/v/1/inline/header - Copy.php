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

# Contents/Description: header page for every displayed page except for /main/

add_translation('element', 'list');
add_translation('element', 'go_back');
add_translation('element', 'more');
add_translation('element', 'less');
add_translation('page', 'offer_list');
add_translation('page', 'transfer_list');
add_translation('page', 'news_list');
add_translation('page', 'item_list');
add_translation('page', 'vote_list');


include($x['site']['i'] . '/inline/visit_track.php');

# Easy customization of the empty pages (ordinarily style doesnt go in the engine pages)
# don't forget target="_top"
# whitespace in the fist column otherwise preg_replace() doesnt work right and the launcher wont load
$data['launch']['pager']['empty'] = <<<HTML
 <table><tr>
	<td>
		<a href="/" target="_top" style="margin-left: -20px;"><img src="/v/1/theme/{$data['theme']['color']}/ts_icon.png" /></a>
	</td>
	<td valign="center" style="padding-left: 5px;">
	<a href="/" target="_top" style="color: #000;">{$translation['page_name']['result']['main']['translation_name']}</a></td>
 </tr></table>
HTML;
$data['launch']['pager']['empty'] = $data['launch']['pager']['empty'] = preg_replace('/\s\s+/', '', $data['launch']['pager']['empty']);

# people launcher
if ($_SESSION['login']['login_user_id']) {
$s1 = '/user_view/?lock_user_id=' . (int)$_SESSION['login']['login_user_id'];
$data['launch']['pager']['empty'] = preg_replace('/\s\s+/', '', $data['launch']['pager']['empty']);
$data['launch']['peopler']['empty'] = <<<HTML
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
$data['launch']['peopler']['empty'] = <<<HTML
 <table><tr>
	<td>
	<a href="{$s1}" target="_top" style="margin-left: -20px;"><img src="/v/1/theme/select_none/ts_icon.png" /></a>
	</td>
	<td valign="center" style="padding-left: 5px;">
	<a href="{$s1}" target="_top" style="color: #000;">{$translation['page_name']['result']['login_set']['translation_name']}</a></td>
 </tr></table>
HTML;
}
$data['launch']['peopler']['empty'] = preg_replace('/\s\s+/', '', $data['launch']['peopler']['empty']);
