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

# Contents/Description: Comglomerated feed also used on the landing page
# Known Issues: Lots of tricks to treat top_report like a multilist page. Still integrating.

# /user_view/?list=mixed
# /top_report/
include('v/1/layer/t1/action.php');

# uncomment out to use page warping instead of the standart edit action box 2012-05-06 vaskoiii
# must also remove the action.php includes ?> 

<style>
.top_odd {
	font-weight: bold;
	margin-bottom: -4px;
	margin-top: 15px;
	margin-left: -20px;
	}
.top_even {
	}
</style>

<div id="search">
	<div id="list_title_box" class="title" style="display: <?= get_action_style_display(); ?>;"><?
		$s1 = get_translation('page', 'top_report');
		if ($x['name'] != ''
		 && $x['name'] != 'main') { ?> 
			<h2><? print_ts_focus($s1, 'report'); ?></h2><?
		} else { ?> 
			<h2><?= to_html($s1); ?></h2><?
		} ?> 
		<p class="result_add">
		</p>
	</div><?
	if ($x['preload']['focus'] == 'list')
	print_message_bar(); ?> 
	<div class="content">

<div class="content_box">

<form name="form_search" action="/index.php" method="POST">
<input type="hidden" name="x" value="<?= to_html($x['.']); ?>search_process/" />
<input type="hidden" name="type" value="lock" /><?
print_keyword_box('search_report', 1); ?> 
</form>

<div style="clear: both;"></div><?
	$i1 = 0;
	if (!empty($data['result']['result']['listing_key']))
	foreach ($data['result']['result']['listing_key'] as $k1 => $v1) { 
		# if ($i1 < $stop_at_listing)
		$x['load']['list']['type'] = $data['result']['result']['listing'][$k1]['list_type']; ?> 
		<div style="margin-left: 20px; margin-right:20px">
			<p class="top_odd"><?= tt('page', $data['result']['result']['listing'][$k1]['list_type'] . '_list'); ?></p>
			<p class="top_even"><? echo print_listing_template(
				$data['result']['result']['listing'][$k1],
				$key,
				$translation,
				'list',
				'main',
				'all',
				$_SESSION['login']['login_user_id']
			); ?></p>
		</div><?
		$i1++;
	} 
	else
	echo tt('element', 'no_listings'); ?> 
</div>

<div class="menu_1">
</div>

<div class="menu_2">
<ul><? # hopefully the value of $datetime is still being held from the engine! if not put it in the $data array! 
	$s1 = '';
	if ($x['name'] == '' || $x['name'] == 'main')
		$s1 = 'top_report/'; ?> 
	<li><a href="./<?= $s1 . ffm('list_datetime_upper_limit=', 0); ?>">|&lt;&lt;</a></li><?
	if ($data['result']['result']['listing_key_count']) { ?> 
		<li><a href="./<?= $s1 . ffm(get_lock_query('list_name=report&list_type=top&list_datetime_upper_limit=' . to_url($datetime_lower_limit)), 0); ?>"><?# tt('page', 'top_report'); ?> &gt;&gt;</a></li><?
	} ?> 
</ul>
</div>
