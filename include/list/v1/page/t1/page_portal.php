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

<div class="content_box">
	<form name="portal_process" action="/index.php" method="POST">
		<input type="hidden" name ="x" value="/page_portal/portal_process/" />
		<input type="hidden" name="q" value="<?= ff('', 1); ?>" />
		<input type="hidden" name="load" value="action" />
		<input type="hidden" name="type" value="page" />
		<div class="k"><span class="page_name"><?= tt('element', 'page_name'); ?></span>:</div>
		<div class="v">
			<input id="ts_focus" style="" autocomplete="off" class="page_name" name="page_name" value="" type="text">
			<input type="submit" value="!" />
		</div>
	</form>
	<div class="k">Hotkey:</div>
	<div class="v">Ctrl + Shift + Comma</div>

</div>

<div class="menu_1">
</div>

<div class="menu_2">
	<ul>
		<li><a href="/sitemap_doc/"><?= tt('page', 'sitemap_doc'); ?></a></li>
	</ul>
</div>
