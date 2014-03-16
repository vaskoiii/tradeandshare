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
?>
<style>
	#peopler_box {
		border: 1px solid #fff; 
		position: absolute;
		width: 300px;
		height: 200px;
		background: red;
		}
	#pager_box {
		border: 1px solid #fff; 
		position: absolute;
		width: 300px;
		height: 200px;
		background: green;
		}
		#peopler_meat_box,
		#pager_meat_box { 
			height: 190px;
			}
		#peopler_form,
		#pager_form {
			padding: 5px 20px 0px;
			background: orange;
			}
		#peopler_x,
		#pager_x {
			margin-right: 10px;
			color: black;
			font-weight: bold;
			font-size: 1.5em;
			}
		#peopler_suggest_one,
		#pager_suggest_one {
			color: black;
			font-weight: bold;
			font-size: 1.5em;
			}
		#peopler_input,
		#pager_input {
			width: 200px;
			background: #ffe;
			color: #001;
			}
		#peopler_launch,
		#pager_launch {
			margin: 5px;
			background: #ffe,
			color: #001; 
			}
		#peopler_hr,
		#pager_hr {
			background: #ffe;
			border: medium none;
			height: 2px;
			margin: 0px -20px;
			}
		#peopler_suggest_more,
		#pager_suggest_more {
			margin: 5px 40px 0px;
			padding: 0px;
			}
		#peopler_juice_box,
		#pager_juice_box {
			height: 10px;
			border-top: 1px solid #ffe;
			background: blue;
			}
		#peopler_alternative_box .td2,
		#pager_alternative_box .td2{
			margin-left: 5px;
			padding-left: 5px;
			}
</style>
<script>
function tsSubmit(tsType) {
	switch(tsType) {
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
				<a href="javascript:simple_hide('pager_box')" id="pager_x">TS</a>
				<a href="javascript:remember('pager','<?= $_COOKIE['launch']['pager']['value']; ?>','<?= $_COOKIE["launch"]["pager"]["display"]; ?>');" id="pager_suggest_one"><?= $_COOKIE["launch"]["pager"]["display"]; ?></a>
				<br />
				<input onkeyup="showHint('pager', this.value);" autocomplete="off" id="pager_input" type="text">
				<input value="!" id="pager_launch" type="submit">
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
				<a href="javascript:simple_hide('peopler_box')" id="peopler_x">TS</a>
				<a href="javascript:top.remember('peopler','<?= $_COOKIE['launch']['peopler']['value']; ?>','<?= $_COOKIE["launch"]["peopler"]["display"]; ?>');" id="peopler_suggest_one"><?= $_COOKIE["launch"]["peopler"]["display"]; ?></a>
				<br />
				<input onkeyup="showHint('peopler', this.value);" autocomplete="off" id="peopler_input" type="text">
				<input value="!" id="peopler_launch" type="submit">
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

<script>
	document.getElementById('peopler_suggest_more').innerHTML = getSuggestMoreEmptyInput('peopler');
	document.getElementById('pager_suggest_more').innerHTML = getSuggestMoreEmptyInput('pager');
</script>
