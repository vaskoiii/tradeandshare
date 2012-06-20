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

# Contents/Description: Print out the top categories! ?> 

<div id="result">
	<div class="title">
		<h2><? $x['preload']['focus'] = 'report'; print_ts_focus(get_translation('page', $x['page']['name']), 'report'); ?></h2>
		<div class="result_add">
		</div>
	</div><?
	print_message_bar();
	?> 
	<div class="content">

<div class="content_box">


<form name="form_search" action="/index.php" method="POST">
<input type="hidden" name="x" value="<?= to_html($x['.']); ?>search_process/" />
<input type="hidden" name="type" value="lock" />
<? print_keyword_box('search_report', 1); ?>
</form>

	<div class="doc_box">
		<h3><?= tt('page', 'main'); ?></h3>
		<p>
			<a href="item_list/<?= ff(get_lock_query()); ?>"><?= tt('element', 'everything'); ?></a>
		</p>
	</div>

	<div class="doc_box">
		<h3><?= tt('page', 'category_list'); ?></h3>
		<p><?
		
		if (!empty($data['search_report']['result']['listing'])) {
			echo '<dl>';
			foreach ($data['search_report']['result']['listing'] as $k1 => $v1) { ?>
				<dt>
					<a href="item_list/<?= ff(get_lock_query('parent_tag_id=' . (int)$v1['tag_id'])); ?>"><?= kk('tag', $v1['tag_id'], 'translation_name', $v1['tag_name']); ?></a>
					-
					<?= (int)$v1['tag_count']; ?>
				</dt>
				<dd><?= kk('tag', $v1['tag_id'], 'translation_description', '!'); ?></a></dd><?
			}
			echo '</dl>';
		}
		else
			echo tt('element', 'no_results'); ?>
		</p>
	</div>

	<div style="clear: both;"></div>
</div>

<div class="menu_1">
</div>

<div class="menu_2">
<ul>
	<li><a href="./category_list/<?= ff(get_lock_query()); ?>"><?= tt('page', 'category_list'); ?></a></li>
</ul>
</div>
