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

# Contents/Description: Helper heading for beginners ?>
<div class="content">
	<div>
		<center><p id="ts_helper_text">Stuff you Want or Want to Share</p></center>
	</div>
	<div class="menu_1">
		<? # todo make translatable ?>
		<center><ul>
			<li><a href="/item_list/" /><?= tt('page', 'item_list'); ?></a></li>
			<li><a href="/offer_list/" /><?= tt('page', 'offer_list'); ?></a></li>
			<li><a href="/sitemap_doc/" /><?= tt('page', 'sitemap_doc'); ?></a></li>
		</ul></center>
	</div>
	<div class="menu_2">
	</div>
</div><?

if ($x['name'] == '' || $x['name'] == 'main') { ?>
	<div class="title"><center><h2><?= tt('page', 'main'); ?></h2></center></div><?
}
