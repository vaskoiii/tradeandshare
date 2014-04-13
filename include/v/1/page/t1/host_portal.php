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

# Description: Picture sitting at the counter scanning in guests

# Todo: this should actually lookup the user by Public Key NOT lock_user_id in order to create a more portable identification system
# no way to enter a new public key from this page? depends on scanner

$s1 = get_gp('public_key');
#todo insert a newline after every 64 characters



include('v/1/inline/t1/header_after.php');

$s2 = get_db_single_value('user_id from ' . $config['mysql']['prefix'] . 'pubkey where value = ' . to_sql($s1), false);

# todo if public key from qr code not found
if (!$s2) { ?> 
	<div class="content_box"><?
		if (isset_gp('public_key')) { ?> 
			<div class="doc_box">
				<h3>TODO: Public Key Not Found</h3>
				<div class="uid"><pre>-----BEGIN CERTIFICATE-----<?= "\n" . $s1 . "\n"; ?>-----END CERTIFICATE-----</pre></div>
			</div><?
		} ?> 
			<div class="doc_box">
				<form action="." method="get">
					Public Key: 
					<input type="text" name="public_key" />
					<input type="submit" value="submit" />
				</form>
			</div>
	</div><?
}
else {
	# todo we have to get everything for the username with the specified public key
	$a1 = array();
	$sql = '
		select
			name
		from
			' . $config['mysql']['prefix'] . 'user
		where
			id = ' . (int)$s2
	;
	$result = mysql_query($sql) or die(mysql_error());
	while ($row = mysql_fetch_assoc($result)) {
		$a1 = $row;
	} ?> 
	<div class="content_box">
		<div class="doc_box">
			<h3>Accountability</h3>
			<dl>
				<dt><span class="user_name">Face</span>:</dt>
				<dd><img src="/v/1/theme/select_none/ts_icon_256x256.png" style="width: 128px; height: 128px;" /></dd>
				<dt><span class="contact_name">SHA1</span>:</dt>
				<dd class="contact_name"><?= sha1($s1); ?></dd>
			</dl>
			<span class="spacer">&gt;&gt;</span>
			<a id="public_key_toggle" href="/" onclick="more_toggle('public_key'); return false;"/>More</a></span>
			<div id="public_key" style="display: none; margin-left: 20px; padding-top: 10px;">
				<dl>
					<dt><span class="uid">Public Key</span>:</dt>
					<dd class="uid"><pre>-----BEGIN CERTIFICATE-----<?= "\n" . $s1 . "\n"; ?>-----END CERTIFICATE-----</pre></dd>
				</dl>
			</div>
		</div>

		
		<div class="doc_box">
			<h3>Transparency</h3>
			<ul>
				<li><a href="./contact_view/?lock_user_id=<?= (int)$s2; ?>&list_name=list&list_type=rating&direction_id=1"><span class="import">Ratings</span></a></li>
			</ul>
			<ul><?
				# todo show teams that both you and the scaned user are on.
				# todo move logic to engine when complete
				# temp: show you and you team
				$sql = '
						id
					from
						' . $config['mysql']['prefix'] . 'team
					where
						name like ' . to_sql('<' . $a1['name'] . '>') . '
				'; ?> 
				<li><a href="./team_view/?lock_team_id=<?= (int)get_db_single_value($sql, 0); ?>"><span class="parent_tag_translation_name">&lt;<?= $a1['name']; ?>&gt;</span></a></li>
			</ul>
		</div>
		<div class="doc_box">
			<h3>TODO: Classify</h3>
			<form>
				<ul>
					<li><input type="checkbox" /><a href=""><span class="team_name">Team1</span></a></li>
					<li><input type="checkbox" /><a href=""><span class="team_name">Team2</span></a></li>
					<li><input type="checkbox" /><a href=""><span class="team_name">Team3</span></a></li>
				</ul>
				<input style="margin-left: 10px; margin-top: 15px;" type="submit" value="submit" onclick="alert('TODO: add functionality.'); return false;" />
			</form>
		</div>
	</div><?
} ?> 

<div class="menu_1">
</div>

<div class="menu_2">
</div>
