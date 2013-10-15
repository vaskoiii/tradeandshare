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

# Contents/Description: Fastest way to get to the contact (user) center
# This page can be customize since the launcher is differnt from the rest of the site
?> 

<div id="result">
	<div class="title">
		<h2><?= get_translation('page', $x['page']['name']); ?></h2>
		<div class="result_add">
		</div>
	</div><?
	print_message_bar(); ?> 

<div class="content">
<div class="content_box">
	<form name="portal_process" action="/index.php" method="POST">
		<input type="hidden" name ="x" value="/people_portal/portal_process/" />
		<input type="hidden" name="q" value="<?= ff('', 1); ?>" />
		<input type="hidden" name="load" value="action" />
		<input type="hidden" name="type" value="contact" />
		<div class="k"><span class="contact_name">Contact</span> <span class="user_name">(User)</span>:</div>
		<div class="v">
			<input id="ts_focus" style="" autocomplete="off" class="contact_user_mixed" name="contact_user_mixed" value="" type="text">
			<input type="submit" value="!" />
		</div>
	</form>
	<div class="k">Hotkey:</div>
	<div class="v">Ctrl + Shift + Period</div>
	<? # todo: implement hotkey ie) Ctrl + Shift + . ?>
</div>

<div class="menu_1">
</div>

<div class="menu_2">
	<ul>
		<li><a href="/contact_list/"><?= tt('page', 'contact_list'); ?></a>/<a href="/note_list/"><?= tt('page', 'note_list'); ?></a></li>
		<li><a href="/user_list/"><?= tt('page', 'user_list'); ?></a>/<a href="/metail_list/"><?= tt('page', 'metail_list'); ?></a></li>
	</ul>
</div>

</div>
