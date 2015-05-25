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

# Contents/Description: display a count on new activity ?> 

<script>
	function more_on_all() {
		more_on('home_area_more');
		more_on('contact_area_more');
		more_on('doc_area_more');
		more_on('people_area_more');
		more_on('control_area_more');
		more_on('other_area_more');
		more_on('member_area_more');
		document.getElementById('all_on_area_more_toggle').innerHTML = '<?= tt('element', 'collapse_all'); ?>';
		document.getElementById('all_on_area_more_toggle').href = 'javascript: more_off_all();';
	}
	function more_off_all() {
		more_off('home_area_more');
		more_off('contact_area_more');
		more_off('doc_area_more');
		more_off('people_area_more');
		more_off('control_area_more');
		more_off('other_area_more');
		more_off('member_area_more');
		document.getElementById('all_on_area_more_toggle').innerHTML = '<?= tt('element', 'expand_all'); ?>';
		document.getElementById('all_on_area_more_toggle').href = 'javascript: more_on_all();';
	}
	function more_on(type_id_number) {
		document.getElementById(type_id_number).style.display='block';
		document.getElementById(type_id_number + '_toggle').innerHTML = '<?= tt('element', 'less'); ?>';
	}
	function more_off(type_id_number) {
		document.getElementById(type_id_number).style.display='none';
		document.getElementById(type_id_number + '_toggle').innerHTML = '<?= tt('element', 'more'); ?>';
	}
</script>

<div class="content_box">

<form name="form_search" action="/index.php" method="POST">
<input type="hidden" name="x" value="<?= to_html($x['.']); ?>search_process/" />
<input type="hidden" name="type" value="lock" />
<? print_keyword_box('search_report', 1); ?> 
</form> <? 

foreach ($data['new_report']['page_id'] as $k1 => $v1) {
	switch($v1['page_name']) {
		case 'people_area':
		case 'recover_area':
			echo '<br clear="all" />';
		break;
	}
	if (!empty($v1['page_id'])) { ?> 
	<div class="new_box"><?
		switch($v1['page_name']) {
			case 'new_area':
			case 'recover_area':
			break;
			default:
				echo '<br />';
			break;
		} ?> 
		<h4><?= tt('page', $v1['page_name']); ?></h4>
		<div><?
		$data['crappy_variable'] = false;
		foreach ($v1['page_id'] as $k2 => $v2) { 
		if ($v2['page_advanced'] == 2) {
			if ($v2['new_amount']) { ?> 
				<p class="list_new">
					<a href="<?= $v2['page_name']; ?>/<?= ff(get_lock_query()); ?>"><?= tt('page', $v2['page_name']); ?></a>:
					<?= $v2['new_amount']; ?> <?= tt('element', 'new'); ?> 
				</p><?
			} 
			elseif (!$v2['view_when']) { ?> 
				<p class="list_unseen">
					<a href="<?= $v2['page_name']; ?>/<?= ff(get_lock_query()); ?>"><?= tt('page', $v2['page_name']); ?></a>:
					<?= tt('element', 'unseen'); ?> 
				</p><?
			} else { ?> 
				<p class="list_seen">
					<a class="not_new" style="color: gray;" href="<?= $v2['page_name']; ?>/<?= ff(get_lock_query()); ?>"><?= tt('page', $v2['page_name']); ?></a>
				</p><?
			} 
		} } ?> 
		</div>
		<p><a style="margin-left: 0px;" id="<?= to_html($v1['page_name']); ?>_more_toggle" href="javascript: more_toggle('<?= to_html($v1['page_name']); ?>_more');"><?= tt('element', 'more'); ?></a></p>
		<div  id="<?= to_html($v1['page_name']); ?>_more" style="display: none; margin-left: 20px;"><?
		foreach ($v1['page_id'] as $k2 => $v2) { 
		if ($v2['page_advanced'] == 1) {
			if ($v2['new_amount']) { ?> 
				<p class="list_new">
					<a href="<?= $v2['page_name']; ?>/<?= ff(get_lock_query()); ?>"><?= tt('page', $v2['page_name']); ?></a>:
					<?= $v2['new_amount']; ?> <?= tt('element', 'new'); ?> 
				</p><?
			} 
			elseif (!$v2['view_when']) { ?> 
				<p class="list_unseen">
					<a href="<?= $v2['page_name']; ?>/<?= ff(get_lock_query()); ?>"><?= tt('page', $v2['page_name']); ?></a>:
					<?= tt('element', 'unseen'); ?> 
				</p><?
			} else { ?> 
				<p class="list_seen">
					<a class="not_new" style="color: gray;" href="<?= $v2['page_name']; ?>/<?= ff(get_lock_query()); ?>"><?= tt('page', $v2['page_name']); ?></a>
				</p><?
			} 
		} } ?> 
		</div>
	</div><?
	}
} ?> 
<div style="clear: both;"></div>
</div><? # end content_box ?> 

<div class="menu_1">
</div>

<div class="menu_2">
<ul>
	<li><a href="ignore_process/<?= ff(); ?>"><?= tt('element', 'ignore_process'); ?></a></li>
</ul>
</div><? # end content ?> 
