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
# unless we implement with the md5 file system will not be scalable

# easiest for testing here.
# todo Move to process for profile_edit when complete

# todo if new pubkey

include('list/v1/inline/t1/header_after.php');

?>

<div class="content_box"><?
if ($data['guest_portal']['user_id']['id']) {
# supposed to be printed out
# todo use contact link instead if possible ?> 
	<style>
		#tsid {
			background: #fff;
			border: 1px dashed #000;
			width: 308px;
			margin: 0px -18px;
			text-align: left;
			
			}
			#tsid_top {
				margin: 5px;
				}
				#tsid_icon {
					}
					#tsid_icon img {
						vertical-align: middle;
						width: 24px;
						height: 24px;
					}
				#tsid_url {
					padding-bottom: 2px;
					text-align: left;
					}
			#tsid_body {
				margin: 5px;
				padding-bottom: 5px;
				}
				#tsid_face {
					width: 128px;
					height: 128px;
					text-align: center;
					}
					#tsid_face img {
						vertical-align: middle;
						max-width: 128px;
						max-height: 128px;
						}
				#tsid_question {
					width: 20px;
					text-align: center;
					font-size: 28px;
					line-height: 28px;
					font-weight: bold;
					}
				#tsid_qrcode {
					width: 128px;
					height: 128px;
					text-align: center;
					}
					#tsid_qrcode img {
						vertical-align: middle;
						max-width: 128px;
						max-height: 128px;
						}
	</style>
	<center>
	<div id="tsid">
		<table id="tsid_top">
			<tr>
				<td id="tsid_icon">
					<img src="/list/v1/theme/select_none/ts_icon.png" />
				</td>
				<td id="tsid_url">
					<a href="/">https://<?= to_html($_SERVER['HTTP_HOST']); ?></a>
				</td>
			</tr>
		</table>
		<table id="tsid_body">
			<tr>
				<td id="tsid_face"><?
					$s1 = '/list/v1/theme/select_none/ts_icon_256x256.png';
					if (!empty($data['guest_portal']['face_filer_id']))
						$s1 = '/file/?id=' . (int)$data['guest_portal']['face_filer_id']; ?> 
					<a href="/"><img src="<?= $s1; ?>" /></a>
				</td>
				<td id="tsid_question">
					?
					<br />
					=
				</td>
				<td id="tsid_qrcode">
					<a href="/host_portal/?public_key=<?= get_db_single_value('
								value
							from
								' . $config['mysql']['prefix'] . 'pubkey
							where
								user_id = ' . (int)$_SESSION['login']['login_user_id']
						, 0); ?>"
					><img src="/file/?id=<?= (int)$data['guest_portal']['user_id']['id']; ?>" /></a>
				</td>
			</tr>
		</table>
	</div>
	</center>
	
	<? if (0) { ?>
	<center>
	<div style="background: #fff; width: 286px; margin: 0px -16px; padding: 0px; border: 1px dashed #000;">
		<table style="margin: 8px 0px; padding: 0px;">
			<tr>
				<td>
					<img src="/list/v1/theme/select_none/ts_icon.png" style="width: 24px; height: 24px;" />
				</td>
				<td style="vertical-align: top; padding-top: 2px;">
					<a href="/">https://<?= to_html($_SERVER['HTTP_HOST']); ?></a>
				</td>
			</tr>
		</table>
		<div>
			<a href="/"><img src="/list/v1/theme/select_none/ts_icon_256x256.png" style="width: 256px; height: 256px; border: 1px solid black;" /></a>
		</div>
		<div style="font-size: 48px; font-weight: bold; margin-bottom: 5px;">
			? =
		</div>
		<div style="margin-bottom: 10px;">
			<img src="/phpqrcode/1.1.4/temp/list.png" style="width: 256px; height: 256px; border: 1px solid black;" /></a>
		</div>
	</div>
	</center>
	<? } ?> 
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
<?
}
else { ?>
<center>
	<p>Please click "Edit" on <a href="/profile_edit/">your profile</a> to get your <?= tt('page', 'guest_portal'); ?></p>
</center><?
} ?> 
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
