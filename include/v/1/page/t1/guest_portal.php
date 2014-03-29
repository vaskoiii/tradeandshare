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

# Description: Picture yourself in a line of people waiting to get scanned in at the counter


# Dependancy:
# http://phpqrcode.sourceforge.net/examples/index.php

include('phpqrcode/qrlib.php');
QRcode::png('https://list.vaskos.com', '/www/site/list/public/phpqrcode/temp/list.png', 'L', 4, 2);

include('v/1/inline/t1/header_after.php'); ?> 
<div class="content_box" style="text-align: center;">
	<p><a href="/host_portal/?public_key=TODO"><img src="/phpqrcode/temp/list.png" /></a></p>
	<p>TODO: Scan QR code for identification</p>
	<a href="/host_portal/?public_key=TODO">Found</a>
	:
	<a href="/host_portal/">NOT Found</a>

	<? # the foollowing may be useful but may also be overly complicated ?>
	<!--
	<h3>Enter</h3>
	<dl>
		<dt>Lock</dt>
			<dd><a href="">todo link to lock process for last team joined</a></dd>
			<dd>Could also just be a single person ie) &lt;vask&gt;</dd>
		<dt>View</dt>
			<dd><a href="">todo link to the teammates page</a></dd>
	</dl>
	<h3>Exit</h3>
	<dl>
		<dt>Unlock</dt>
			<dd>Unset your locks</dd>
		<dt>View</dt>
			<dd>See your teams</dd>
	</dl>
	-->
</div>

<div class="menu_1">
</div>

<div class="menu_2">
</div>
