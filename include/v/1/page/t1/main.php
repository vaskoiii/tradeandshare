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

$b1 = 1;
if ($_SESSION['login']['login_user_id']) {
	if ($_SESSION['interpret']['failure'] == 1)
		$b1 = 2;
}
else
	$b1 = 2;
?> 
<body<?= $b1 == 1 ? ' onLoad="javascript: document.getElementById(\'keyword\').focus();"' : ''; ?> onkeydown="checkIt(event, navigator.appName);">
<div id="header"><? // preload div must be coordinated with the launcher and also duplicated for the main page ?> 

<div style="background: url('/v/1/theme/<?= to_html(preg_replace('/theme\_/', '', $_SESSION['theme']['theme_name'])); ?>/ts_icon.png') no-repeat -9999px -9999px;"></div>
<div class="title">
	<p id="topper"><?
		if (isset($_SESSION['login']['login_user_id'])) { ?>
			<?= $_SESSION['login']['login_user_name'] . $config['spacer']; ?>
			<a href="login_unset_process/<?= ff(''); ?>"><?= tt('element', 'unset_login'); ?></a>
			| <a href="config_report/<?= ff(''); ?>"><?= tt('page', 'config_report'); ?></a>
			| <a href="/manager/"><?= tt('page', 'manager'); ?></a><?
		} else { ?>
			<a href="login_set/<?= ff(); ?>"><?= tt('element', 'set_login'); ?></a>
			| <a href="config_report/<?= ff(''); ?>"><?= tt('page', 'config_report'); ?></a><?
		} ?> 
	</p><?
	if ($_SESSION['feature']['feature_lock'] == 1) { ?> 
		<h1 id="website_name"><a href="/<?= get_lock_query(); ?>"><nobr><?= $config['website_name']; ?></nobr></a></h1><?
	} else { ?> 
		<h1 id="website_name"><a href="/"><nobr><?= $config['website_name']; ?></nobr></a></h1><?
	} ?> 
</div><?
include('v/1/inline/t1/header_helper.php'); ?>

<?

		include($x['site']['i'] . '/layer/' . $x['site']['t'] . '/fast.php');
		# todo needed?
		include($x['site']['i'] . '/layer/' . $x['site']['t'] . '/quick.php');


?>
	<div class="title"><center><h2><?= tt('page', 'main'); ?></h2></center></div>



<? print_message_bar(); ?><?

if ($_SESSION['login']['login_user_id']) { ?>
<div class="content">
<div class="splash_box">
	<div id="main_add" style=""><?
		if ($_SESSION['theme']['theme_name'] != 'theme_select_none') {
			?><a id="item_f_swap1" style="display: inline;" href="/item_list/" onclick="javascript:  if (document.getElementById('offer_f_box').style.display == 'block') more_toggle_swap('offer_f'); more_toggle_swap('item_f'); return false;"><img style="text-decoration: none;" alt="<?= tt('add_item'); ?>" src="/<?= $x['site']['p']; ?>theme/<?= str_replace('theme_', '', $_SESSION['theme']['theme_name']); ?>/plus_icon.jpg" style="border: none;" /><?
		} else { ?> 
			<a id="item_f_swap1" style="display: inline;" href="/item_list/" onclick="javascript:  if (document.getElementById('offer_f_box').style.display == 'block') more_toggle_swap('offer_f'); more_toggle_swap('item_f'); return false;"><img style="text-decoration: none;" alt="<?= tt('add_item'); ?>" src="/<?= $x['site']['p']; ?>theme/<?= str_replace('theme_', '', $_SESSION['theme']['theme_name']); ?>/plus_icon.png" style="border: none;" /><?
		} ?><br /><?= tt('element', 'add_item'); ?></a>
	</div>
	<form id="main_search" name="ms" action="/index.php" method="POST">
		<input type="hidden" name="x" value="/item_list/search_process/" />
		<input type="hidden" name="q" value="<?= ff(); ?>" />
		<? # obsolete? 2012-02-01 ?> 
		<input type="hidden" name="list_name" value="item_entry" />
		<input type="hidden" name="type" value="item" />
		<div class="main_keyword_box">
			<? print_keyword_box('asearch_box', 0, '', 1); ?> 
		</div>
	</form>
	<div class="menu_1">
	</div>
	<div class="menu_2">
		<ul>
			<li><a href="top_report/<?= ff(get_lock_query()); ?>"><?= tt('page', 'top_report'); ?></a></li>
			<li><a href="new_report/<?= ff(get_lock_query()); ?>"><?= tt('page', 'new_report'); ?></a></li>
			<li><a href="search_report/<?= ff(get_lock_query()); ?>"><?= tt('page', 'search_report'); ?></a></li>
		</ul>
	</div>
</div>
</div><?
}
else { ?>
<div class="content">
<div class="splash_box">
	<div id="main_intro">
		<a href="./trailer_doc/"><img src="/v/1/theme/<?= str_replace('theme_', '',  $_SESSION['theme']['theme_name']); ?>/ts_icon_256x256.png" width="128px" height="128px" /><br /><?= tt('page', 'trailer_doc'); ?></a>
	</div>
<div class="menu_1">
	<ul></ul>
</div>
<div class="menu_2">
	<center>
	<ul>
		<li><a href="download_doc/<?= ff(); ?>"><?= tt('page', 'download_doc'); ?></a></li>
		<li><a href="about_doc/<?= ff(); ?>"><?= tt('page', 'about_doc'); ?></a></li>
		<li><a href="faq_doc/<?= ff(); ?>"><?= tt('page', 'faq_doc'); ?></a></li>
		<li><a href="disclaimer_doc/<?= ff(); ?>"><?= tt('page', 'disclaimer_doc'); ?></a></li>
		<li><a href="donate_doc/<?= ff(); ?>"><?= tt('page', 'donate_doc'); ?></a></li>
		
	</ul>
	</center>
</div>
</div>
<? }
