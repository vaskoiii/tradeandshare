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

# todo where is best to put this? 2012-01-13 vaskoiii
# also in head.php AND new_report.php
# include($x['site']['i'] . '/inline/site_map.php');

# Contents/Description: Helper heading for beginners ?>
<div class="content">
	<div>
		<center><p id="ts_helper_text">Stuff You Want or Want to Share</p></center>
	</div>



<!------------------------->
        <div class="menu_1_directory">
	<center>
		<ul><? # Item | Message
			# swap item?
			$b1 = 2;
			if (isset($_GET['focus']) && $_GET['focus'] == 'fast')
			if (
				isset($_SESSION['process']['form_info']) && 
				'item' == $_SESSION['process']['form_info']['type']
			)
				$b1 = 1;
			# swap message?
			$b2 = 2;
			if (isset($_GET['focus']) && $_GET['focus'] == 'fast')
			if (
				isset($_SESSION['process']['form_info']) && 
				'offer' == $_SESSION['process']['form_info']['type']
			)
				$b2 = 1;
			if ($_SESSION['login']['login_user_id']) { ?> 
				<li>
					<a
						id="item_f_swap1"
						style="display: <?= $b1 == 1 ? 'none' : 'inline'; ?>;" 
						href="/item_list/"
					><?= tt('page', 'item_list'); ?></a>
					<script>
						document.getElementById('item_f_swap1').onclick = function() {
							if (document.getElementById('offer_f_box').style.display == 'block')
								more_toggle_swap('offer_f');
							more_toggle_swap('item_f');
							return false;
						}
					</script>
					<a
						id="item_f_swap2"
						style="display: <?= $b1 == 1 ? 'inline' : 'none'; ?>; font-weight: bold;"
						href="/item_list/"
					><?= tt('page', 'item_list'); ?></a>
				</li>
				<li>
					<a
						id="offer_f_swap1"
						style="display: <?= $b2 == 1 ? 'none' : 'inline'; ?>;" 
						href="/offer_list/"
					><?= tt('page', 'offer_list'); ?></a>
					<script>
						document.getElementById('offer_f_swap1').onclick = function() {
							if (document.getElementById('item_f_box').style.display == 'block')
								more_toggle_swap('item_f');
							more_toggle_swap('offer_f');
							return false;
						}
					</script>
					<a
						id="offer_f_swap2"
						style="display: <?= $b2 == 1 ? 'inline' : 'none'; ?>; font-weight: bold;"
						href="/offer_list/"
					><?= tt('page', 'offer_list'); ?></a>
				</li><?
			}
			else { ?> 
				<li>
					<a href="/item_edit/"><?= tt('page', 'item_list'); ?></a>
				</li>
				<li>
					<a href="/offer_edit/"><?= tt('page', 'offer_list'); ?></a>
				</li><?
			}
					

			# Sitemap ?> 
		</ul>
		<span style="text-align: left; display: inline;">
			<span class="spacer">&gt;&gt;</span>
			<a id="head_menu_toggle" href="/sitemap_doc/" onclick="more_toggle('head_menu'); return false;"/><?= tt('element', 'more'); ?></a>
		</span>
	
		<div id="head_menu" style="display: none;">
			<dl>
			<dt><?= tt('page', 'new_area'); ?></dt>
			<dd>
				<span class="spacer"><?= $config['spacer']; ?></span><a href="/top_report/"><?= tt('page', 'top_report'); ?></a>
				<span class="spacer"><?= $config['spacer']; ?></span><a href="/new_report/"><?= tt('page', 'new_report'); ?></a>
				<span class="spacer"><?= $config['spacer']; ?></span><a href="/search_report/"><?= tt('page', 'search_report'); ?></a>
				<span class="spacer"><?= $config['spacer']; ?></span><a href="/score_report/"><?= tt('page', 'score_report'); ?></a>
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
				if (!empty($v1['page_id']))
				foreach ($v1['page_id'] as $k2 => $v2) {
					$e1 = explode('_', $v2['page_name']); ?> 
					<nobr>
						<span class="spacer"><?= $config['spacer'] ?></span>
						<a href="/<?= $e1['0']; ?>_<?= $e1[1]; ?>/"><?= tt('page', $v2['page_name']); ?></a>
					</nobr><?
				}
				switch ($v1['page_name']) {
					case 'ts_area':
					case 'new_area':
					break;
					default: ?> 
						</dd><?
					break;
				}
			} ?>
			</dl>
		</div>
	</center>
        </div>


	<div class="menu_2">
	</div>
</div><?

