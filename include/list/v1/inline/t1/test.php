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

# todo where is best to put this? 2012-01-13 vaskoiii
# also in head.php AND new_report.php
# include($x['site']['i'] . '/inline/site_map.php');

# Description: Test file for potential future integration
# Current contents are a exploration to replace the iframe
# Issue: colors are not yet dynamic
# 	Not integrated in to a non test file even though this file is critical to TS!

# Modified to support 128px by 128px images 2014-03-29 vaskoiii

# launcher theme
$s1 = str_replace('theme_', '', $_SESSION['theme']['launcher_theme_name']);
# added 2011-08-25
$data['theme']['color'] = $s1;
#$data['theme']['color'] = 'green'; # testing override 2012-03-18 vaskoiii
$data['css']['theme_name'] = 'theme_' . $data['theme']['color']; # like using a function paramerter for the include below
#include($x['site']['i'] . 'css/background_color.php');
$data['css'] = array_merge($data['css'], get_background($s1));

?>
<style>
	#peopler_box,
	#scanner_box,
	#pager_box {
		border: 1px solid #fff; 
		position: absolute;
		width: 298px;
		min-height: 221px;
		background: <?= $data['css']['c0']; ?>; 
		}
		#scanner_box img,
		#peopler_box img,
		#pager_box img{ 
			margin: 0px auto;
			padding: 0px;
			max-width: 128px;
			max-height: 128px;
			}
		#scanner_meat_box,
		#peopler_meat_box,
		#pager_meat_box { 
			min-height: 210px;
			}
		#scanner_form,
		#peopler_form,
		#pager_form {
			padding: 5px 10px 0px;
			background: <?= $data['css']['c1']; ?>;
			}
		#scanner_x,
		#peopler_x,
		#pager_x {
			position: absolute;
			width: 25px;
			height: 30px;
			right: 5px;
			top: 5px;
			color: #333;
			font-size: 1.5em;
			text-align: center;
			}
		#scanner_suggest_one,
		#peopler_suggest_one,
		#pager_suggest_one {
			color: black;
			font-weight: bold;
			font-size: 1.5em;
			display: inline-block;
			max-width: 255px;
			}
		#scanner_input,
		#peopler_input,
		#pager_input {
			width: 200px;
			background: #ffe;
			color: #001;
			}
		#scanner_launch,
		#peopler_launch,
		#pager_launch {
			margin: 5px;
			background: #ffe,
			color: #001; 
			}
		#scanner_hr,
		#peopler_hr,
		#pager_hr {
			background: #ffe;
			border: medium none;
			height: 2px;
			margin: 0px -10px;
			}
		#scanner_suggest_more,
		#peopler_suggest_more,
		#pager_suggest_more {
			margin: 5px 15px 0px;
			padding: 0px;
			}
		#scanner_juice_box,
		#peopler_juice_box,
		#pager_juice_box {
			height: 10px;
			border-top: 1px solid #ffe;
			background: <?= $data['css']['c2']; ?>;
			}
		#scanner_alternative_box .td2,
		#peopler_alternative_box .td2,
		#pager_alternative_box .td2{
			margin-left: 5px;
			padding-left: 5px;
			}
		#scanner_box li,
		#peopler_box li,
		#pager_box li{ 
			margin: 0px 0px 0px 20px;
			}
</style>
<script>
function tsSubmit(tsType) {
	switch(tsType) {
		case 'scanner': <?
			# pushing users to use a public key ?> 
			// user id
			var s2 = '<?= get_db_single_value('value from ' . $config['mysql']['prefix'] . 'pubkey where user_id = ' . to_sql($_SESSION['login']['login_user_id']), false); ?>';
			var o1 = document.getElementById('scanner_input');
			if (o1.value)
				window.parent.location =  '/host_portal/?public_key=' + encodeURIComponent(o1.value); 
			else if (s2)
				window.parent.location =  '/host_portal/?public_key=' + encodeURIComponent(s2); 
			else {
				alert('Please enter a public key');
				window.parent.location =  '/profile_edit/';
			}
		break;
		case 'peopler':
			window.parent.location = document.getElementById('peopler_suggest_one').href;
		break;
		case 'pager':
			window.parent.location = document.getElementById('pager_suggest_one').href;
		break;
	}
}
</script>
<div id="pager_box" style="display: none;">
	<div id="pager_meat_box">
		<div id="pager_main_box">
			<form name="peopler_form" onsubmit="tsSubmit('pager'); return false;" id="pager_form">
				<div>
					<a href="javascript:remember('pager','<?= $_COOKIE['launch']['pager']['value']; ?>','<?= $_COOKIE["launch"]["pager"]["display"]; ?>');" id="pager_suggest_one"><?= $_COOKIE["launch"]["pager"]["display"]; ?></a>
					<a href="javascript:simple_hide('pager_box')" id="pager_x">X</a>
				</div>
				<div>
					<input onkeyup="showHint('pager', this.value);" autocomplete="off" id="pager_input" type="text">
					<input value="!" id="pager_launch" type="submit">
				</div>
				<hr id="pager_hr">
			</form>
		</div>
		<div id="pager_alternative_box">
			<ul id="pager_suggest_more">
				<!-- populated with js -->
			</ul>
		</div>
	</div>
	<div style="" id="pager_juice_box"></div>
</div>
<div id="peopler_box" style="display: none;">
	<div id="peopler_meat_box">
		<div id="peopler_main_box">
			<form name="peopler_form" onsubmit="tsSubmit('peopler'); return false;" id="peopler_form">
				<div>
					<a href="javascript:top.remember('peopler','<?= $_COOKIE['launch']['peopler']['value']; ?>','<?= $_COOKIE["launch"]["peopler"]["display"]; ?>');" id="peopler_suggest_one"><?= $_COOKIE["launch"]["peopler"]["display"]; ?></a>
					<a href="javascript:simple_hide('peopler_box')" id="peopler_x">X</a>
				</div>
				<div>
					<input onkeyup="showHint('peopler', this.value);" autocomplete="off" id="peopler_input" type="text">
					<input value="!" id="peopler_launch" type="submit">
				</div>
				<hr id="peopler_hr">
			</form>
		</div>
		<div id="peopler_alternative_box">
			<ul id="peopler_suggest_more">
				<!-- populated with js -->
			</ul>
		</div>
	</div>
	<div id="peopler_juice_box"></div>
</div>
<div id="scanner_box" style="display: none;">
	<div id="scanner_meat_box">
		<div id="scanner_main_box">
			<form name="scanner_form" onsubmit="tsSubmit('scanner'); return false;" id="scanner_form">
				<div>
					<span style="font-weight: bold; font-size: 1.5em;">
						<a href="javascript: tsSubmit('scanner');"><?= tt('page', 'host_portal'); ?> </a>
					</span>
					<a href="javascript:simple_hide('scanner_box')" id="scanner_x">X</a>
				</div>
				<div>
					<input id="scanner_input" type="text">
					<input value="!" id="scanner_launch" type="submit">
				</div>
				<hr id="scanner_hr">
			</form>
		</div>
		<div id="scanner_alternative_box">
			<ul id="scanner_suggest_more">
				<!-- populated with js -->
			</ul>
		</div>
	</div>
	<div id="scanner_juice_box"></div>
</div>

<script>
	document.getElementById('scanner_suggest_more').innerHTML = getSuggestMoreEmptyInput('scanner');
	document.getElementById('peopler_suggest_more').innerHTML = getSuggestMoreEmptyInput('peopler');
	document.getElementById('pager_suggest_more').innerHTML = getSuggestMoreEmptyInput('pager');
</script>
