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

# Contents/Description: A bunch of selection actions that should probably be put in the database!!! ?> 

<div id="result"><!--result-->

<div id="view_title_box" class="title" style="display: <?= get_motion_style_display(); ?>;">
	<h2><?
		switch ($x['load']['view']['type']) {
			/* uncomment if hybridizing user/contact pages
			case 'user':
			case 'contact': ?> 
				<? 'Person'; ?> 
			break;
			*/
			default: ?>
				<?= tt('page', $x['load']['view']['type'] . '_list'); ?><?
			break;
		} ?> 
	</h2>
	<div class="result_add">
	</div>
</div> <? 

if ($x['preload']['focus'] == 'view')
	print_message_bar(); ?> 

<div class="content">
	<div class="content_box"><?
	if ($data['view']['result']['listing'])
	foreach ($data['view']['result']['listing'] as $k1 => $v1) { ?>
	<div id="view_list"><?
		print_listing_template($data['view']['result']['listing'][0], $key, $translation, 'view', 'main', 'all', $_SESSION['login']['login_user_id']); ?> 
	</div><?
	} ?> 
	</div>

	<div class="menu_1">
	</div>

	<div class="menu_2">
	<ul><?
		# Item | Message
		switch($x['load']['view']['type']) {
			case 'contact': # needed because sometimes ONLY lock_user_id exists for contact_view
			case 'user': 
				if (isset_gp('lock_user_id')) { ?>
					<li><a href="<?= ffm('list_name=list&list_type=item&focus=action&expand%5B0%5D=', 0); ?>"><?= tt('page', 'item_list'); ?></a>*</li>
					<li><a href="<?= ffm('list_name=list&list_type=offer&focus=action&expand%5B0%5D=', 0); ?>"><?= tt('page', 'offer_list'); ?></a>*</li><?
				}
			break;
		}
		# Sitemap
		switch($x['load']['view']['type']) {
			case 'incident':
			case 'meritopic':
			break;
			default: ?> 
				<li><a href="<?= ffm('list_name=doc&list_type=sitemap&focus=&expand%5B0%5D=', 0); ?>"><?= tt('page', 'sitemap_doc'); ?></a>*</li><?
			break;
		} ?> 
	</ul>
	</div>

</div>

</div><!--/result-->
