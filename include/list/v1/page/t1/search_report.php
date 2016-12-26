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

<div class="content_box">

	<form name="form_search" action="/index.php" method="POST">
	<input type="hidden" name="x" value="<?= to_html($x['.']); ?>search_process/" />
	<input type="hidden" name="type" value="lock" />
	<? print_keyword_box('search_report', 1); ?> 
	</form>

	<div class="doc_box">
		<h3><?= tt('page', 'main'); ?></h3>
		<p>
			<a href="item_list/<?= to_html(ff($q['raw'])); ?>"><?= tt('element', 'everything'); ?></a>
		</p>
	</div>

	<div class="doc_box">
		<h3><?= tt('page', 'category_list'); ?></h3>
		<p><?
		# shortcut
		$a1 = $data['custom_key']['tag_id'];
		$a2 = $data['custom_key']['tag_id']['count'];
		if (!empty($data['custom_key']['tag_id']['count'])) {
			echo '<dl>';
			foreach ($data['custom_key']['tag_id']['count'] as $k1 => $v1) {
			if ($v1 != 0) { ?> 
				<dt><?
					# todo fix so that the link maintains the locking and not all url vars 2012-06-16 vaskoiii ?> 
					<a href="item_list/<?= to_html(ff($q['raw'] . '&parent_tag_id=' . (int)$k1, $x['level'])); ?>"><?= to_html($data['custom_key']['tag_id']['name'][$k1]); ?></a>
					-
					<?= (int)$v1; ?> 
				</dt>
				<dd><?= to_html($data['custom_key']['tag_id']['description'][$k1]); ?></a></dd><?
			} }
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
	<li><a href="./category_list/<?= to_html(ff(get_lock_query())); ?>"><?= tt('page', 'category_list'); ?></a></li>
</ul>
</div>
