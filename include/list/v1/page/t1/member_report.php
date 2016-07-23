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

# description: most relevant memberships in decending order with the most people (similar to search_report)
?>

<div class="content_box">

<form name="form_search" action="/index.php" method="POST">
<input type="hidden" name="x" value="<?= to_html($x['.']); ?>search_process/" />
<input type="hidden" name="type" value="lock" /><?
print_keyword_box('member_report', 1); ?> 
</form>
<div class="doc_box">
	<h3>Channel</h3>
	<dl><?
		if (!empty($data['member_report']['order'])) {
		foreach($data['member_report']['order'] as $k1 => $v1) {
		if (is_numeric($k1)) {
			# present (not future_horizon) ?>
			<dt>
				<a href="./cycle_report/<?= ff('cycle_id=' . (int)$data['member_report']['channel'][$k1]['cycle_offset'][1]['id']); ?>"><?= to_html($data['member_report']['channel'][$k1]['info']['name']); ?></a>
				-
				<?= (int)$data['member_report']['order'][$k1]; ?>
			</dt>
			<dd><?= to_html($data['member_report']['channel'][$k1]['info']['description']); ?></dd><?
		} } }
		else { ?> 
			<dt>No channels with members were found</dt><?
		} ?> 
	</dl>
</div>
<? # print_r_debug($data['member_report']); ?> 

<? print_break_close(); ?> 
