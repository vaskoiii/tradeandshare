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

# Contents/Description: Non-Javascript intermediate page before the corresponding action.
# Known Issues: Most data has to be submitted 2x ?> 

<p class="notice" style="margin: 0px 0px; margin-top: -10px;"><?= tt('page', get_gp('list_name') . '_list'); ?> : <?= tt('element', get_gp('action')); ?> : <?= count(get_gp('row')); ?></p>

<form name="f" action="/index.php" method="POST">
	<input type="hidden" name="x" value="<?= $x['..'] . 'selection_process/'; ?>" >
	<input type="hidden" name="q" value="<?= get_q_query($x['level']) ?>" />
	<input type="hidden" name="list_name" value="<?= get_gp('list_name'); ?>" />
	<input type="hidden" name="action" value="<?= get_gp('action'); ?>" /><?

	foreach (get_gp('row') as $k1 => $v1) { ?> 
		<input type="hidden" name="row[]" value="<?= $v1; ?>" /><? 
	} ?> 

	<div class="content_box"><?
	switch(get_gp('action')) {
		case 'merge': ?> 
		<div class="table">
		<tr>
			<td></td>
			<td>Merging tags is intended to be implemented in the future. This message is left as a reminder.</td>
		</tr>
		</div><?
		break;
		case 'export': ?> 
		<div class="table">
		<tr>
			<td><span class="contact_name"><?= tt('element', 'contact_name'); ?></span> <span class="user_name">(<?= tt('element', 'user_name'); ?>)</span>:</td>
			<td><input class="contact_user_mixed" type="text" name="contact_user_mixed" value="" /></td>
		</tr> 
		</div><?
		break;
		case 'import': ?> 
		<div class="table"><?
			$data['container']['search_content_box']['element']['team_required_name'] = '<|*|>'; 
			#include($x['site']['i'] . 'inline/option.php');  obsolete! 2012-02-06 vaskoiii
			# maybe instead use something like:
			/*
			foreach ($data['search']['response'] as $k1 => $v1)
			foreach ($v1 as $k2 => $v2) {
				add_option($k2);
				add_translation('element', $k2);
			}
			*/
			print_search_box('element'); ?> 
		</div><?
		break;
		case 'comment': ?> 
			<dl>
				<dt><span class="comment_description"><?= tt('element', 'comment_description'); ?></span></dt>
				<dd>
                                        <div class="textarea">
                                                <textarea onkeypress="if (event.which == 13) { event.preventDefault(); submit(); };" class="description_input" name="comment_description" maxlength="255" /></textarea>
                                        </div>
                                        &nbsp;
				</dd>
			</dl><?
		break;
	} ?> 
	</div>
	<div class="menu_1">
		<ul>
			<input type="submit" value="<?= tt('element', 'submit'); ?>" />
		</ul>
	</div>
	<div class="menu_2">
	</div>
</form>

</div>
