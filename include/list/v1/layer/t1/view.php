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

# Issue: not sure if includes are wanted in this file
# Contents/Description: A bunch of selection actions that should probably be put in the database!!! ?> 

<div id="result"><!--result-->

<div id="view_title_box" class="title" style="display: <?= get_motion_style_display(); ?>;">
	<span class="go_back"><?= print_go_back('&lt;&lt;'); ?></span>
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
        <ul><?
                # Item | Message
                switch($x['load']['view']['type']) {
                        case 'contact': # needed because sometimes ONLY lock_user_id exists for contact_view
                        case 'user':
                                if (isset_gp('lock_user_id')) { ?> 
					<li>
						<a
							id="item_q_swap1"
							href="<?= ffm('page=&list_name=list&list_type=item&focus=action&expand%5B0%5D=', 0); ?>"
							onclick="javascript: if (document.getElementById('offer_q_box').style.display == 'block') more_toggle_swap('offer_q'); more_toggle_swap('item_q'); return false;"
						><?= tt('page', 'item_list'); ?></a>
						<a
							id="item_q_swap2"
							style="display: none; font-weight: bold;"
							href="<?= ffm('page=&list_name=list&list_type=item&focus=action&expand%5B0%5D=', 0); ?>"
						><?= tt('page', 'item_list'); ?></a>
						*
					</li>
					<li>
						<a
							id="offer_q_swap1"
							href="<?= ffm('page=&list_name=list&list_type=offer&focus=action&expand%5B0%5D=', 0); ?>"
							onclick="javascript: if (document.getElementById('item_q_box').style.display == 'block') more_toggle_swap('item_q'); more_toggle_swap('offer_q'); return false;"
						><?= tt('page', 'offer_list'); ?></a>
						<a
							id="offer_q_swap2"
							style="display: none; font-weight: bold;"
							href="<?= ffm('page=&list_name=list&list_type=offer&focus=action&expand%5B0%5D=', 0); ?>"
						><?= tt('page', 'offer_list'); ?></a>
						*
					</li><?



                                }
                        break;
                }
                # Sitemap
                switch($x['load']['view']['type']) {
                        case 'incident':
                        case 'meritopic':
                        break;
                        default: ?> 
                                <? /* <li><a href="<?= ffm('list_name=doc&list_type=sitemap&focus=&expand%5B0%5D=', 0); ?>"><?= tt('page', 'sitemap_doc'); ?></a>*</li> */ ?> 
                                <span class="spacer">&gt;&gt;</span> <a id="view_menu2_toggle" href="/sitemap_doc/" onclick="javascript: more_toggle('<?= to_html('view_menu2'); ?>'); return false;"/><?= tt('element', 'more'); ?></a>

<div id="view_menu2" style="margin-top: 10px; display: none">
<table>
<tr><td>
<dl>
	<dt><?= tt('page', 'new_area'); ?></dt>
	<dd>
		<span class="spacer"><?= $config['spacer']; ?></span><a href="<?= ffm('page=&list_name=report&list_type=top&focus=&expand%5B0%5D=', 0); ?>"><?= tt('page', 'top_report'); ?></a>*
		<span class="spacer"><?= $config['spacer']; ?></span><a href="<?= ffm('page=&list_name=report&list_type=new&focus=&expand%5B0%5D=', 0); ?>"><?= tt('page', 'new_report'); ?></a>*
		<span class="spacer"><?= $config['spacer']; ?></span><a href="<?= ffm('page=&list_name=report&list_type=search&focus=&expand%5B0%5D=', 0); ?>"><?= tt('page', 'search_report'); ?></a>*
	</dd><?
	foreach ($data['new_report']['page_id'] as $k1 => $v1) {
		switch ($v1['page_name']) {
			case 'ts_area':
			case 'new_area':
			break;
			default: ?> 
				<dt><?= tt('page', $v1['page_name']); ?></dt>
				<dd><?
			break;
		}
					if (!empty($v1['page_id'])) {
					foreach ($v1['page_id'] as $k2 => $v2) {
						$e1 = explode('_', $v2['page_name']); ?> 
						<nobr>
							<span class="spacer"><?= $config['spacer'] ?></span>
							<a href="<?= ffm('page=&list_name=' . $e1['1'] . '&list_type=' . $e1[0] . '&focus=&expand%5B0%5D=', 0); ?>"><?= tt('page', $v2['page_name']); ?></a>*
						</nobr><?
					} } ?> 
				</dd><?
	} ?> 
</dl>
</td></tr>
</table>
</div><?

                        break;
                } ?> 
        </ul>
        </div>
	<div class="menu_2">
	</div>
</div><?


// include('list/v1/layer/t1/quick.php');


?> 

</div><!--/result-->
