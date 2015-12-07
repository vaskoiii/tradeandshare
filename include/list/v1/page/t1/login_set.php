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
?> 
<div id="action">
	<div class="title">
		<h2><? $x['preload']['focus'] = 'action'; print_ts_focus(get_translation('page', $x['page']['name']), 'action'); ?></h2>
		<div class="result_add">
		</div>
	</div><?
	print_message_bar(); ?> 
	<div class="content">
	<div id="action_content_1">
<? ################### ?> 


<form name="fs" action="/index.php" method="POST"> 
<input type="hidden" name ="x" value="<?= to_html($x['.']); ?>login_set_process/" />
<input type="hidden" name="q" value="<?= get_q_query($x['level']) ?>" /><?
	if (!empty($_SESSION['process'])) {
	foreach($_SESSION['process'] as $k1 => $v1) { 
	switch($k1) {
		case 'remember_login':
		case 'container':
			// Do NOT set [container] as a string when it should be an array!
		break;
		default: ?> 
			<input type="hidden" name="<?= to_html($k1); ?>" value="<?= to_html($v1); ?>" /><?
		break;
	} } }
	foreach($_POST as $k1 => $v1) { ?> 
		<input type="hidden" name="<?= to_html($k1); ?>" value="<?= to_html($v1); ?>" /><?
	}
	foreach($_GET as $k1 => $v1) { ?> 
		<input type="hidden" name="<?= to_html($k1); ?>" value="<?= to_html($v1); ?>" /><?
	} ?> 
	<div class="table">
		<div class="k"><span class="login_user_name"><?= tt('element', 'login_user_name'); ?></span>:</div>
		<div class="v"><input class="login_user_name" type="input" name="login_user_name" value="<?= to_html($action_content_1['login_user_name']); ?>" /></div>
		
		<span valign="top">
			<div class="k"><span class="login_user_password_unencrypted"><?= tt('element', 'login_user_password_unencrypted'); ?></span>:</div>
			<div class="v"><input class="login_user_password_unencrypted" type="password" name="login_user_password_unencrypted" value="<?= to_html($action_content_1['login_user_password_unencrypted']); ?>" maxlength="255" /></div>
		</span>
		<span valign="top">
			<div class="k"><span class="remember_login"><?= tt('element', 'remember_login'); ?></span></div>
			<div class="v"><input class="remember_login" type="checkbox" name="remember_login" <?= ($action_content_1['remember_login'] == 1) ? 'checked="checked"' : ''; ?> /></div>
		</span>
			<? # careful with redirect ?> 
			<input type="hidden" name="login_request_uri" value="<?= to_html($action_content_1['login_request_uri']); ?>" />
			<div class="k"><span class="redirect"><?= tt('element', 'redirect'); ?></span></div>
			<div class="v"><span class="redirect"><?= to_html($action_content_1['login_request_uri']); ?></span></div>
	</div>
	<div class="menu_1">
		<ul>
			<input type="submit" name="login_set" value="<?= tt('page', 'login_set'); ?>" />
		</ul>
	</div>
	<div class="menu_2">
		<ul>
			<li><a href="login_recover/<?= ff(); ?>"><?= tt('page', 'login_recover'); ?></a></li>
		</ul>
	</div>
</form>
</div>
