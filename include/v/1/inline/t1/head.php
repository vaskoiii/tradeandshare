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

# Contents/Description: Main head for html files

?><!DOCTYPE html>
<html>
	<head>
		<title><?= to_html(get_page_title()); ?></title>
		<link rel="image_src" href="/v/1/theme/select_none/ts_icon_256x256.png" /><? # facebook autofinds this picture? ?> 
		<meta name="description" content="Trade and Share. Stuff you want or want to share. Want it? Get it! Have it? Share it! Don't want it? Trade it! An Identity-Based Merit System." />
		<meta name="keywords" content="Trade and Share, Identity-Based Merit System, Alternate Economy" />
		<meta name="author" content="John Vasko III" />
		<meta charset="utf-8" /><?
		# for smartphone
		switch($_SESSION['display']['display_name']) {
			case 'display_select_default':
				# desktop browsers will likely ignore viewport and then settings will be the same as [display_width_1024_pixels]
				# try and get the most compact view and the most versatile view simultaneously ?> 
				<meta name="viewport" content="width=320" /><?
			break;
			case 'display_select_none':
				# browser decides viewport
			break;
			case 'display_width_320_pixels': ?> 
				<meta name="viewport" content="width=320" /><?
			break;
			case 'display_width_480_pixels': ?> 
				<meta name="viewport" content="width=480" /><?
			break;
			case 'display_width_1024_pixels': ?> 
				<meta name="viewport" content="width=1024" /><?
			break;
		} 

		# COMPATIBILITY MODE
		# todo make liST not dependent on JS 2014-03-09 vaskoiii
		# ideally js can still be used for showing and hiding but otherwise no!
		if ($_SESSION['load']['load_javascript'] != 2) { ?> 
			<script src="/more_js/"></script>
			<script src="/launch_js/?theme_id=<?= $_SESSION['theme']['theme_id']; ?>"></script>
			<script src="/v/1/autocomplete/jquery-1.4.2.min.js"></script>
			<script src="/v/1/autocomplete/jquery.metadata.js"></script>
			<script src="/v/1/autocomplete/jquery.auto-complete.iii.js"></script>
			<script src="/v/1/autocomplete/jquery.ts.js"></script>
			<link rel="stylesheet" type="text/css" href="/v/1/autocomplete/jquery.auto-complete.css" /><?
		} ?> 

		<link rel="shortcut icon" href="/favicon.ico" type="image/vnd.microsoft.icon" />
		<link rel="stylesheet" type="text/css" media="all" href="/color_theme/?theme_id=<?= $_SESSION['theme']['theme_id']; ?>&amp;display_id=<?= (int)$_SESSION['display']['display_id']; 
?>" />
		<link rel="stylesheet" type="text/css" media="all" href="/text_style/" />
		<?
		# todo placeholder for removing iframes
		# only issue is that we may lose the separate color choice for the launcher
		$hh_b1 = 2; // dont use this variable anymore after implementation 2014-03-08 vaskoiii
		if ($hh_b1 == 1) { ?> 
			<style>
				#pager_box,
				#peopler_box {
					position: absolute;
					display: none;
					width: 300px;
					height: 200px;
					// debug
					border: 20px solid red;
					background: green;
					
					}
			</style>
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
		} ?> 
	</head>

