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

# Contents/Description: Print the keyword box. ?> 

<div id="search">
	<div id="list_title_box" class="title" style="display: <?= get_action_style_display(); ?>;">
		<h2><?= tt('page', $x['load']['list']['type'] . '_list'); ?></h2>
		<div class="result_add"></div>
	</div><?
	if ($x['preload']['focus'] == 'list')
		print_message_bar(); ?> 
	<div class="content">
	<form name="form_search" action="/index.php" method="POST">
		<input type="hidden" name="x" value="<?= to_html($x['.']); ?>search_process/" />
		<input type="hidden" name="q" value="<?= ff('', 1); ?>" />
		<input type="hidden" name="load" value="list" />
		<input type="hidden" name="type" value="<?= $x['load']['list']['type']; ?>" />
		<input type="hidden" name="id" value="<?= (int)$x['load']['list']['id']; ?>" /><?
		#todo searching with $_GET['list_name'] and $_GET['list_type'] destroys current page 2012-04-26 vaskoiii

	# do it
	print_keyword_box('asearch_box', 0, 'action'); ?> 

</form>
