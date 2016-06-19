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
<body<?= $b1 == 1 ? ' onLoad="document.getElementById(\'main_ts_focus\').focus();"' : ''; ?> onkeydown="return checkIt(event);"><?
include('list/v1/inline/t1/test.php'); # despite the name test.php launcher will fail if omitted 2014-09-01 vaskoiii ?> 
<div id="header">
<div style="background: url('/list/v1/theme/<?= to_html(preg_replace('/theme\_/', '', $_SESSION['theme']['theme_name'])); ?>/ts_icon.png') no-repeat -9999px -9999px;"></div>
<div class="title">
	<p id="topper"><?
		if (isset($_SESSION['login']['login_user_id'])) { ?> 
			<?= $_SESSION['login']['login_user_name'] . $config['spacer']; ?> 
			<a href="login_unset_process/<?= ff(''); ?>"><?= tt('element', 'unset_login'); ?></a>
			| <a href="config_report/<?= ff(''); ?>"><?= tt('page', 'config_report'); ?></a>
			| <a href="/manager/"><?= tt('page', 'manager'); ?></a>
			<span style="float: right;"><?= nod() . number_format((float)$_SESSION['login']['accounting_value_sum'], 2); ?></span><?
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
include('list/v1/inline/t1/header_helper.php');
include('list/v1/layer/t1/fast.php'); ?> 
<div class="title"><center><h2><a href="." id="main_ts_focus" onclick="return false;"><?= tt('page', 'main'); ?></a></h2></center></div>
<? print_message_bar(); ?><?
if ($_SESSION['login']['login_user_id']) { ?> 
	<div class="content">
		<div id="splash_search">
		<div class="splash_box">
			<div id="main_add">
				<a
					id="splash_search_focus"
					style="display: inline;"
					href="/item_edit/"
					onclick="simple_show_hide('splash_edit', 'splash_search'); return false;"
				><?
					?><img
						style="text-decoration: none; border: none;"
						alt="<?= tt('element', 'add_item'); ?>"
						src="/list/v1/theme/<?= str_replace('theme_', '', $_SESSION['theme']['theme_name']); ?>/plus_icon.jpg"
					/><br /><?= tt('element', 'add_item'); ?><?
				?></a>
			</div>
			<form id="main_search" name="ms" action="/index.php" method="POST">
				<input type="hidden" name="x" value="/item_list/search_process/" />
				<input type="hidden" name="q" value="<?= ff(); ?>" />
				<input type="hidden" name="load" value="list" />
				<input type="hidden" name="type" value="item" />
				<div class="main_keyword_box">
					<? print_keyword_box('asearch_box', 0, '', 1); ?> 
				</div>
				<div class="menu_1">
				</div>
			</form>
		</div>
		</div>
		<div id="splash_edit" style="display: none;">
			<div class="splash_box">
				<div id="main_add" style="">
					<a
						id="splash_edit_focus"
						style="display: inline;"
						href="/item_list/"
						onclick="simple_show_hide('splash_search', 'splash_edit'); return false;"
					><?
						?><img
							style="text-decoration: none; border: none;"
							alt="<?= tt('element', 'add_item'); ?>"
							src="/list/v1/theme/<?= str_replace('theme_', '', $_SESSION['theme']['theme_name']); ?>/ts_icon_256x256.png"
							width="128px"
							height="128px"
						/><br /><?= tt('element', 'find_item'); ?><?
					?></a>
				</div>
			</div>
			<div>
				<? # no need to return to the main page ?> 
				<form id="main_search" name="me" action="/index.php" method="POST">
					<input name="x" value="/item_edit/edit_process/" type="hidden" />
					<input name="q" value="<?= ff(); ?>" type="hidden" />
					<input name="load" value="action" type="hidden" />
					<input name="type" value="item" type="hidden" /><?
					$s1 = 'item';
					print_container($edit[$s1]['content_1'], $empty_listing, $key, $translation, 'main', $option);
					if(!empty($edit[$s1]['content_2'])) { ?> 
						<p class="more_solo">
							&gt;&gt; <a id="<?= to_html($s1); ?>_main_2_toggle" style="display: inline;" href="#" onclick="more_toggle('<?= to_html($s1); ?>_main_2'); return false;"><?= tt('element', 'more'); ?></a>
						</p><?
					} ?> 
					<div id="<?= to_html($s1);?>_main_2" style="margin-left: 20px; margin-bottom: 15px; display: none;"><?
						print_container($edit[$s1]['content_2'], $empty_listing, $key, $translation, 'main', $option); ?> 
					</div>
					<div class="menu_1">
						<ul>
							<input type="submit" value="<?= tt('element', 'add'); ?>" />
						</ul>
					</div>
					
				</form>
			</div>
		</div>
		<div class="splash_box">
			<div class="menu_2">
				<ul>
					<li><a href="top_report/<?= ff(get_lock_query()); ?>"><?= tt('page', 'top_report'); ?></a></li>
					<li><a href="new_report/<?= ff(get_lock_query()); ?>"><?= tt('page', 'new_report'); ?></a></li>
					<li><a href="search_report/<?= ff(get_lock_query()); ?>"><?= tt('page', 'search_report'); ?></a></li>
					<li><a href="member_report/<?= ff(get_lock_query()); ?>"><?= tt('page', 'member_report'); ?></a></li>
				</ul>
			</div>
		</div>
	</div><?
}
else { ?> 
	<div class="content">
		<div class="splash_box">
			<div id="main_intro">
				<center>
					<video width="224px" height="175px" controls />
						<source src="/list/v1/video/list_share.mp4" type="video/mp4" /><? # MP4 = H264 video / AAC audio ?> 
						<source src="/list/v1/video/list_share.ogg" type="video/ogg" /><? # Ogg = Theora video / Vorbis audio ?> 
						<source src="/list/v1/video/list_share.webm" type="video/webm" /><? # WebM = VP8 video / Vorbis audio ?> 
						Your browser does not support the video tag.
					</video> 
				</center>
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
	</div><?
}
