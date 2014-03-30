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

$s1 = 'MIICgzCCAewCCQDxDqlEvwdIuzANBgkqhkiG9w0BAQUFADCBhTELMAkGA1UEBhMC
xVMxEzARBgNVBAgTCkNhbGlmb3JuaWExEjAQBgNVBAcTCVNhbiBEaWVnbzEPMA0G
A1UEChMGVmFza29zMRMwEQYDVQQDEwp2YXNrb3MuY29tMScwJQYJKoZIhvcNAQkB
FhhhZG1pbmlzdHJhdG9yQHZhc2tvcy5jb20wHhcNMDkwMTI5MTk1MzA2WhcNMTAw
MTI5MTk1MzA2WjCBhTELMAkGA1UEBhMCVVMxEzARBgNVBAgTCkNhbGlmb3JuaWEx
ejAQBgNVBAcTCVNhbiBEaWVnbzEPMA0GA1UEChMGVmFza29zMRMwEQYDVQQDEwp2
YXNrb3MuY29tMScwJQYJKoZIhvcNAQkBFhhhZG1pbmlzdHJhdG9yQHZhc2tvcy5j
b20wgZ8wDQYJKoZIhvcNAQEBBQADgY0AMiGJAoGBALf46G7mC0i8QrUN4vF6lwoS
DTil5ix5EvKgaYYffcKkDqL+Jq8K1/r8ClkSEXfDnkMi1z+y8aoAWdUmqfyI6+Lm
8CFYsse8weD6vofpswNffaIfs96OfI3mLr5Mvh+3aA/rdqW5IEgQCpnMpub/myIw
SQ71Jq94HswPUkUhzjBHAgMBAAEwDQYJKoZIhvcNAqEFBQADgYEAepuA2rG3L69/
W67BseGNghzr0x2QM5EylziliaYRPVBvuzSHutpDp+Kesndcvh82o4E9JPPPve1X
TrLW6z676ZtcYyT6tZyNt6ZVIay7EeBWqF6QNSmb970FuJmY8Jdwuq02Ix3eU4Hr
YP9YW/hvE4EJQWTst8AngnZ0PiIi8zc=';

include('v/1/inline/t1/header_after.php');


# todo if public key from qr code not found
if (get_gp('public_key') != 'TODO') { ?> 
	<div class="content_box">
		<div class="doc_box">
			<h3>TODO: Public Key Not Found</h3>
			<div class="uid"><pre>-----BEGIN CERTIFICATE-----<?= "\n" . $s1 . "\n"; ?>-----END CERTIFICATE-----</pre></div>
		</div>
		<div class="doc_box">
			<h3>TODO: Try Again</h3>
			<form>
				Public Key: 
				<input type="text" />
				<input type="submit" value="submit" />
			</form>
		</div>
	</div><?
}
else { ?> 
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
				<li><a href="./contact_view/?lock_user_id=<?= (int)$_SESSION['login']['login_user_id']; ?>&list_name=list&list_type=rating&direction_id=1"><span class="import">Ratings</span></a></li>
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
						name like ' . to_sql('<' . $_SESSION['login']['login_user_name'] . '>') . '
				'; ?> 
				<li><a href="./team_view/?lock_team_id=<?= (int)get_db_single_value($sql, 0); ?>"><span class="parent_tag_translation_name">&lt;<?= $_SESSION['login']['login_user_name']; ?>&gt;</span></a></li>
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
