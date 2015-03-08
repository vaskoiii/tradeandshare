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

?>

<div class="content_box"><?
if ($data['guest_portal']['user_id']['id']) {
# supposed to be printed out
# todo use contact link instead if possible ?> 
	<style>
		.content_box {
			text-align: center;
			}
		.tsid {
			background: #fff;
			border: 1px dashed #000;
			width: 306px;
			/* margin: 0px -18px; */
			text-align: left;
			display: inline-block;
			}
			.tsid_top {
				margin: 5px;
				}
				.tsid_icon {
				}
					.tsid_icon img {
						vertical-align: middle;
						width: 24px;
						height: 24px;
						margin-right: 5px;
					}
				.tsid_url {
					padding-bottom: 2px;
					text-align: left;
					}
			.tsid_body {
				margin: 5px;
				padding-bottom: 5px;
				width: 296px;
				}
				.tsid_face {
					text-align: center;
					width: 128px;
					height: 128px;
					}
					.tsid_face img {
						vertical-align: middle;
						max-width: 112px;
						max-height: 112px;
						}
				.tsid_question {
					width: 20px;
					text-align: center;
					font-size: 28px;
					line-height: 24px;
					font-weight: bold;
					}
				.tsid_qrcode {
					width: 128px;
					height: 128px;
					text-align: center;
					}
					.tsid_qrcode img {
						vertical-align: middle;
						max-width: 128px;
						max-height: 128px;
						}
		@media screen and (max-width: 480px) {
			.content_box {
				padding: 0px;
			}
		}
	</style>
<?
function guest_portal_print_id($minnify = 2) {
	global $data;
	global $config;
	ob_start(); ?> 
		<div class="tsid">
			<table class="tsid_top">
				<tr>
					<td class="tsid_icon">
						<img src="/list/v1/theme/select_none/ts_icon.png" />
					</td>
					<td class="tsid_url">
						<a href="/">https://<?= to_html($_SERVER['HTTP_HOST']); ?></a>
					</td>
				</tr>
			</table>
			<table class="tsid_body">
				<tr>
					<td class="tsid_face"><?
						$s1 = '/list/v1/theme/select_none/ts_icon_256x256.png';
						if (!empty($data['guest_portal']['face_filer_id']))
							$s1 = '/file/?id=' . (int)$data['guest_portal']['face_filer_id']; ?> 
						<a href="/"><img src="<?= $s1; ?>" /></a>
					</td>
					<td class="tsid_question">
						?
						<br />
						=
					</td>
					<td class="tsid_qrcode">
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
		</div><?
	$html = ob_get_contents();
	ob_clean();

	if ($minnify == 1) {
	    $search = array(
		'/\>[^\S ]+/s',  // strip whitespaces after tags, except space
		'/[^\S ]+\</s',  // strip whitespaces before tags, except space
		'/(\s)+/s'       // shorten multiple whitespace sequences
	    );
	    $replace = array(
		'>',
		'<',
		'\\1'
	    );
	    $html = preg_replace($search, $replace, $html);
	}
	return $html;
} ?>

<div id="guest_portal_id_container">
	<?= guest_portal_print_id(); ?> 
</div><?

# the following may be useful but may also be overly complicated
if (0) { ?> 
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
	</dl><? 
}
}
else { ?>
<center>
	<p>Please click "Edit" on <a href="/profile_edit/">your profile</a> to get your <?= tt('page', 'guest_portal'); ?></p>
</center><?
} ?> 
</div>

<script>
	function guest_portal_print_more() {
		o1 = document.createElement('span');
		o1.innerHTML = '<?= guest_portal_print_id(1); ?>';
		did('guest_portal_id_container').appendChild(o1);
	}
</script>

<div class="menu_1">
	<ul style="margin: 0px auto; text-align: center;">
		<li><a href="javascript: guest_portal_print_more();">Print More</a></li>
	</ul>
</div>

<div class="menu_2">
	<center>
	<ul><?
		# <li><a href="javascript:window.print()">Print</a></li> ?> 
	</ul>
	</center> 
</div>
