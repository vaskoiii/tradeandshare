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
		<center><p id="ts_helper_text">Stuff you Want or Want to Share</p></center>
	</div>
	<div class="menu_1">
		<center>
		<ul>
			<li><a href="/item_list/" /><?= tt('page', 'item_list'); ?></a></li>
			<li><a href="/offer_list/" /><?= tt('page', 'offer_list'); ?></a></li>
			<span style="text-align: left; display: inline-block; width: 90px;"><span class="spacer">&gt;&gt;</span> <a id="head_menu_toggle" href="/sitemap_doc/" onclick="javascript: more_toggle('<?= to_html('head_menu'); ?>'); return false;"/><?= tt('element', 'more'); ?></a></span>
		</ul>
		<div id="head_menu" style="display: none;">
		<div class="content_box" style="margin-top: -10px; display: inline-table; text-align: left;">
		<dl>
			<dt><?= tt('page', 'new_area'); ?></dt>
			<dd>
				<span class="spacer"><?= $config['spacer']; ?></span><a href="top_report/"><?= tt('page', 'top_report'); ?></a>
				<span class="spacer"><?= $config['spacer']; ?></span><a href="new_report/"><?= tt('page', 'new_report'); ?></a>
				<span class="spacer"><?= $config['spacer']; ?></span><a href="search_report/"><?= tt('page', 'search_report'); ?></a>
			</dd><?
			foreach ($data['new_report']['page_id'] as $k1 => $v1) {
				switch ($v1['page_name']) {
					case 'ts_area':
					case 'new_area':
					break;
					default:
				echo '<dt>'; echo tt('page', $v1['page_name']); echo '</dt><dd>';
					break;
				}
				if (!empty($v1['page_id']))
				foreach ($v1['page_id'] as $k2 => $v2) { ?> 
					<nobr><span class="spacer"><?= $config['spacer'] ?></span><a href="<?= to_html($v2['page_name']); ?>/"><?= tt('page', $v2['page_name']); ?></a></nobr><?
				}
				echo '</dd>';
			} ?>
		</dl>
		</div>
		</div>
		</center>
	</div>
	<div class="menu_2">
	</div>
</div><?

if ($x['name'] == '' || $x['name'] == 'main') { ?>
	<div class="title"><center><h2><?= tt('page', 'main'); ?></h2></center></div><?
}
