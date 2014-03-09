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

# Contents/Description: Test file for potential future integration - Current contents are a exploration to replace the iframe ?>

<script>
	function radical(tsType) {
		var lb = document.getElementById(tsType + '_box');
		// todo get this without the px part ie) lb.style.width
		lbW = '300';
		lbH = '200';
		console.log(lb.style);
		console.log(iii_clientWidth());
		var topPx = iii_scrollTop() + (iii_clientHeight() / 3) - (lbH / 2);
		if (topPx <= 0) {
			topPx = 0;
		}
		var leftPx = (iii_scrollLeft() + (iii_clientWidth() / 2)) - (lbW / 2);
		if (leftPx <= 0) {
			leftPx = 0;
		}
		lb.style.top = topPx + 'px';
		lb.style.left = leftPx + 'px';
		lb.style.display = 'block';
		// if (tsType == 'tslPeople')
		// 	setTimeout("gettsl_idocument('tslPeople').getElementById('tsl_input').focus()", 0);
		// else
		// 	setTimeout("gettsl_idocument('tsl').getElementById('tsl_input').focus()", 0);
	}
</script><?
# placeholder testing link for removing iframes 2014-03-08 vaskoiii ?> 
<a href="#" onclick="radical('peopler');">peopler</a>
<a href="#" onclick="radical('pager');">pager</a>
<div id="peopler_box">
	peopler
</div>
<?
// todo get dynamically from php colors
// todo have a link on the box by default for /peopler/
?> 
<style>
	#pager_box,
	#peopler_box {
		position: absolute;
		display: none;
		width: 300px;
		height: 200px;
		 background: green;
		// debug
		 border: 2px solid #fff;
		}
</style>
<style>
	#pager_meat_box { 
		height: 190px;
		}
	#pager_form {
		padding: 5px 20px 0px;
		background: none repeat scroll 0% 0% rgb(164, 164, 164);
		}
	#pager_x {
		margin-right: 10px;
		color: black;
		font-weight: bold;
		font-size: 1.5em;
		}
	#pager_suggest_one {
		color: black; font-weight: bold; font-size: 1.5em;
		}
	#pager_input {
		width: 200px; background: none repeat scroll 0% 0% rgb(254, 254, 254); color: rgb(1, 1, 1);
		}
	#pager_launch {
		margin: 5px; background: none repeat scroll 0% 0% rgb(254, 254, 254); color: rgb(1, 1, 1);
		}
	#pager_hr {
		background: none repeat scroll 0% 0% rgb(255, 255, 255); border: medium none; height: 2px; margin: 0px -20px;
		}
	#pager_suggest_more {
		margin: 5px 40px 0px; padding: 0px;
		}
	#pager_juice_box {
		height: 10px; border-top: 1px solid rgb(255, 255, 255); background: none repeat scroll 0% 0% rgb(100, 100, 100);
		}
</style>
<script>
	function simple_hide(s1) {
		document.getElementById(s1).style.display = 'none';
	}
</script>
<div id="pager_box" style="border: 1px solid #fff;">
	<div id="pager_meat_box">
		<div id="pager_main_box">
			<form onsubmit="window.parent.location = pager_suggest_one.href;" id="pager_form">
				<a href="javascript:simple_hide('pager_box')" id="pager_x">TS</a>
				<a href="javascript:top.remember('tsl', 'login_set', 'Enter');" target="_top" id="pager_suggest_one">Enter</a>
				<br />
				<input onkeyup="window.top.showHint('tsl', this.value);" autocomplete="off" id="pager_input" type="text">
				<input value="!" id="pager_launch" type="submit">
				<hr id="pager_hr">
			</form>
		</div>
		<div id="pager_alternative_box">
			<ul id="pager_suggest_more">
				<table><tbody><tr>
					<td>
						<a href="/" target="_top" style="margin-left: -20px;">
						<img src="/v/1/theme/black/ts_icon.png"></a>
					</td>
					<td style="padding-left: 5px;" valign="center">
						<a href="/" target="_top" style="color: #000;">Home</a>
						<br />
						<a href="/" target="_top" style="color: #000;">Peopler</a>
					</td>
				</tr></tbody></table>
			</ul>
		</div>
	</div>
	<div style="" id="pager_juice_box"></div>
</div><?
