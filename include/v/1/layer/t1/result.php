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

# Contents/Description: Listings! ?> 

<form name="result_process" action="/index.php" method="POST">
<input type="hidden" name="x" value="<?= to_html($x['.']); ?>result_process/" />
<input type="hidden" name="q" value="<?= ff('', 1) ?>" />
<input type="hidden" name="load" value="list" />
<input type="hidden" name="type" value="<?= $x['load']['list']['type']; ?>" />
<input type="hidden" name="id" value="<?= (int)$x['load']['list']['id']; ?>" />

<div class="content_box"> <?
	if (!$_SESSION['process']['failure']) {
	if (!empty($data['result']['result']['listing'])) {
	foreach ($data['result']['result']['listing'] as $k1 => $v1) { ?> 
	<p><?
	print_listing_template(
		$data['result']['result']['listing'][$k1],
		$key,
		$translation,
		'list',
		'main',
		'all',
		$_SESSION['login']['login_user_id']
		# , $style
	); ?> 
	</p><?
	} } }

	if (!$data['result']['result']['total'] || $_SESSION['process']['failure']) { 
		if ($_SESSION['interpret']['failure']) { ?> 
			<p><?= tt('element', 'error'); ?></p><?
		}
		elseif ($_SERVER['REDIRECT_QUERY_STRING']) { ?> 
			<p><?= tt('element', 'no_result'); ?></p><?
			# todo message to retry with no search criteria 2012-05-01
			# todo alternate message for view pages (that have only the view id specified)
		} 
		else { ?> 
			<p><?= tt('element', 'no_result'); ?></p>
			<p><?= tt('element', 'is_empty'); ?></p><?
		}
	} ?> 
</div>

<div class="menu_1">
	<ul><?
		# todo listing_menu_1(get_gp('child')); 2012-02-16 vaskoiii ?> 
	</ul>
</div>

<div class="menu_2"><?
	if (!$_SESSION['process']['failure'] && $data['result']['result']['total'])
		print_paging(get_gp('page'), $data['result']['result']['total'], $config[$x['site']['t']]['result_amount_per_page']); ?> 
</div>

</form>
</div>
</div>
