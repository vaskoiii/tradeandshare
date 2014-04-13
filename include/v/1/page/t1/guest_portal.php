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


# todo move to when profile is updated
include('phpqrcode/qrlib.php');
QRcode::png('https://list.vaskos.com', '/www/site/list/public/phpqrcode/temp/' . $_SESSION['login']['login_user_name'] . '.png', 'L', 4, 2);
include('v/1/inline/t1/header_after.php'); ?> 

<div class="content_box" style="text-align: center;">
	<center>
	<div style="background: #fff; width: 300px; margin: 0px; padding: 0px; border: 1px dashed #000;"><?
		# todo use contact link instead if possible ?> 
		<a href="./user_view/<?= ff('list_name=list&list_type=item&lock_user_id=' . (int)$_SESSION['login']['login_user_id']); ?>" ><img src="/v/1/theme/select_none/ts_icon_256x256.png" style="width: 128px; height: 128px; margin: 10px 5px;" /></a>
		<a href="/host_portal/?public_key=<?= get_db_single_value('
					value
				from
					' . $config['mysql']['prefix'] . 'pubkey
				where
					user_id = ' . (int)$_SESSION['login']['login_user_id']
			, 0); ?>" ><img src="/phpqrcode/temp/list.png" style="width: 128px; height: 128px; margin: 10px 5px;" /></a>
	</div>
	</center>
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
	<center>
	<ul><?
		# <li><a href="javascript:window.print()">Print</a></li> ?> 
	</ul>
	</center> 
</div>
