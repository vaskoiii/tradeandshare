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
# issue:
# todo:
#  allow submitting the qr_code url into the search box but strip off the first part
#  @^https://list.vaskos.com/host_portal/\?public_key=@i
#  will allow scanning the text directly and pressing submit
#  do qr codes allow just scanning of text easily like a barcode scanner?

# Todo: this should actually lookup the user by Public Key NOT lock_user_id in order to create a more portable identification system
# no way to enter a new public key from this page? depends on scanner

$s1 = get_gp('public_key');
#todo insert a newline after every 64 characters


$s2 = get_db_single_value('user_id from ' . $config['mysql']['prefix'] . 'pubkey where value = ' . to_sql($s1), false);

# todo if public key from qr code not found ?> 
	<div class="content_box">
		<div class="doc_box1" style="margin-top: 10px;">
			<form action="." method="get">
				Public Key: 
				<input id="focusOnThis" type="text" name="public_key" />
				<script>
					document.getElementById('focusOnThis').focus();
				</script>
				<input type="submit" value="submit" />
			</form>
		</div><?
if (!$s2) {
 		if (isset($_GET['public_key'])) { ?> 
			<div style="margin: 20px 0px 5px 0px;" class="doc_box1">
				<h3>Public Key Not Found</h3>
				<div style="margin-left: 5px; margin-top: -5px;">
					<p>This person has no accountability and no transparency</p>
					<p>This person is anonymous</p>
					<dl>
						<dt>Public Key</dt>
						<dd>
							<div class="uid"><pre><?= "\n" . $s1 . "\n"; ?></pre></div>
						</dd>
					</dl>
				</div>
			</div><?
		} ?> 
	</div>
	<div class="menu_1">
	</div>
	<div class="menu_2">
	</div>

	<?
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
		<div class="doc_box1">
			<h3 style="margin: 20px 0px 5px 0px;">Accountability</h3>
			<div style="margin-left: 15px;">
			<div>
				<img src="/list/v1/theme/select_none/ts_icon_256x256.png" style="width: 128px; height: 128px;" />
			</div>
			<p><?
				$s2 = $key['user_id']['result'][ $data['view']['result']['listing'][0]['user_id'] ]['user_name'];
				$s3 = $key['user_id']['result'][ $data['view']['result']['listing'][0]['user_id'] ]['contact_name'];

				if ($s3) { ?> 
					<span class="contact_name"><?= to_html($s3); ?></span><?
				}
				if ($s2) { ?> 
					<span class="user_name">(<?= to_html($s2); ?>)</span><?
				} ?> 
				<span class="spacer">&gt;&gt;</span>
				<a id="public_key_toggle" href="/" onclick="more_toggle('public_key'); return false;"/>More</a></span>
			</p>
			<div id="public_key" style="display: none; margin-left: 20px; padding-top: 10px;">
				<dl>
					<dt><span class="edit">SHA1</span>:</dt>
					<dd class="edit"><?= sha1($s2); ?></dd>
					<dt><span class="uid">Public Key</span>:</dt>
					<dd class="uid"><?= "\n" . $s1 . "\n"; ?></dd>
				</dl>
			</div>
			</div>
		</div>

		<div class="doc_box1">
			<h3 style="margin: 20px 0px -5px 0px;">Transparency</h3>
			<div style="margin-left: 15px;"><?
				if (!count($data['result']['result']['listing'])) { ?> 
					<p>This user has no ratings</p><?
				}

				foreach ($data['result']['result']['listing'] as $k1 => $v1) {
					# todo eliminate this
					if ($k1 < 3) { ?> 
						<p>
							<span class="user_name">(<?= to_html($v1['source_user_name']); ?>)</span>
							-
							<span class="grade_name"><?= to_html($translation['grade_name']['result'][ $v1['grade_name'] ]['translation_name']); ?></span>
							<br />
							<span><?= to_html($v1['rating_description']); ?></span>
						</p><?
					}

				}; ?> 
			</div>
		</div>
	</div><?
	# repeat from view.php =(
	# not sure if this page should be combined into a view page?
	?> 
        <div id="todohack" class="menu_1">
        <ul><?
                # Item | Message
		if (isset_gp('public_key')) { ?> 
			<li>
				<a
					id="item_q_swap1"
					class="todohack"
					href="<?= ffm('page=&list_name=list&list_type=item&focus=action&expand%5B0%5D=', 0); ?>"
					onclick="javascript: if (document.getElementById('offer_q_box').style.display == 'block') more_toggle_swap('offer_q'); more_toggle_swap('item_q'); return false;"
				><?= tt('page', 'item_list'); ?></a>
				<a
					id="item_q_swap2"
					class="todohack"
					style="display: none; font-weight: bold;"
					href="<?= ffm('page=&list_name=list&list_type=item&focus=action&expand%5B0%5D=', 0); ?>"
				><?= tt('page', 'item_list'); ?></a>
				*
			</li>
			<li>
				<a
					id="offer_q_swap1"
					class="todohack"
					href="<?= ffm('page=&list_name=list&list_type=offer&focus=action&expand%5B0%5D=', 0); ?>"
					onclick="javascript: if (document.getElementById('item_q_box').style.display == 'block') more_toggle_swap('item_q'); more_toggle_swap('offer_q'); return false;"
				><?= tt('page', 'offer_list'); ?></a>
				<a
					id="offer_q_swap2"
					class="todohack"
					style="display: none; font-weight: bold;"
					href="<?= ffm('page=&list_name=list&list_type=offer&focus=action&expand%5B0%5D=', 0); ?>"
				><?= tt('page', 'offer_list'); ?></a>
				*
			</li><?
		}
                # Sitemap
		$s1 = '';
		$s2_report = 'page=&list_name=report&focus=&expand%5B0%5D=&list_type=';
		$s2_list = 'page=&list_name=list&focus=&expand%5B0%5D=&list_type=';
                switch($x['load']['view']['type']) {
                        case 'incident':
                        case 'meritopic':
                        break;
                        default: ?> 
                                <span class="spacer">&gt;&gt;</span> <a id="view_menu2_toggle" class="todohack" href="/sitemap_doc/" onclick="javascript: more_toggle('<?= to_html('view_menu2'); ?>'); return false;"/><?= tt('element', 'more'); ?></a>

				<div id="view_menu2" style="margin-top: 10px; display: none">
				<table>
				<tr><td>
				<dl>
					<dt><?= tt('page', 'new_area'); ?></dt><?
					$a1 = array(
						'top',
						'new',
						'search',
					); ?> 
					<dd><?
						foreach ($a1 as $k1 => $v1) { ?> 
							<span class="spacer"><?= $config['spacer']; ?></span>
							<a class="todohack" href="<?= ffm('page=&list_name=report&focus=&expand%5B0%5D=&list_type=' . $v1, 0); ?>"><?= tt('page', $v1 . '_report'); ?></a>*<?
						} ?> 
					</dd><?
					foreach ($data['new_report']['page_id'] as $k1 => $v1) {
						switch ($v1['page_name']) {
							case 'ts_area':
							case 'new_area':
							break;
							default: ?> 
								<dt><?= tt('page', $v1['page_name']); ?></dt>
								<dd><?
									if (!empty($v1['page_id'])) {
									foreach ($v1['page_id'] as $k2 => $v2) {
										$e1 = explode('_', $v2['page_name']); ?> 
										<nobr>
											<span class="spacer"><?= $config['spacer'] ?></span>
											<a class="todohack" href="<?= ffm('page=&list_name=' . $e1['1'] . '&list_type=' . $e1[0] . '&focus=&expand%5B0%5D=', 0); ?>"><?= tt('page', $v2['page_name']); ?></a>*
										</nobr><?
									} } ?> 
								</dd><?
							break;
						}
					} ?> 
				</dl>
				</td></tr>
				</table>
				</div><?
                        break;
                } ?> 
        </ul>
        </div>

	<script>
		var a1 = document.getElementsByClassName('todohack');
		for (var i=0;i<a1.length;i++) {
			a1[i].onclick = '';
			a1[i].href = 'javascript: alert("todo: add functionality");';
			console.log(a1[i].onclick);
		}
	</script>
	<div class="menu_2">
	</div>
</div>

<div class="title">
	<h2>Classifier</h2>
</div>
<div class="content">
	
	<div class="content_box">
		<div class="doc_box1">
			<h3>Shared</h3>
			<ul>
			<?

			if (!empty($data['host_portal']['shared_team'])) {
			 foreach ($data['host_portal']['shared_team'] as $k1 => $v1) { ?>
				<li><?
					if ($data['host_portal']['my_team'][$k1]['owner_id'] == $_SESSION['login']['login_user_id']) { ?>
						<input type="checkbox" /><?
					}
					else
						echo '<input type="checkbox" disabled />'; ?> 
					<a href="./team_view/?lock_team_id=<?= (int)$k1; ?>"><span class="parent_tag_translation_name"><?= to_html($v1['team_name']); ?></span></a>
				</li><?
			} } 
			else { ?> 
				No teams are shared<?
			} ?>
			</ul>
		</div>
	</div>
	<div class="content_box">
		<div class="doc_box1">
			<h3>Unshared</h3>
			<form>
				<ul>
					<?
					foreach ($data['host_portal']['my_team'] as $k1 => $v1) {
						if (!$data['host_portal']['shared_team'][$k1]) { ?>
							<li><?
								if ($data['host_portal']['my_team'][$k1]['owner_id'] == $_SESSION['login']['login_user_id']) { ?>
									<input type="checkbox" /><?
								}
								else 
									echo '<input type="checkbox" disabled />'; ?> 
								<a href="./team_view/?lock_team_id=<?= (int)$k1; ?>"><span class="parent_tag_translation_name"><?= to_html($v1['team_name']); ?></span></a>
							</li><?
						} 
					}
					
					if (count($data['host_portal']['my_team']) == count($data['host_portal']['shared_team']))
						echo '<li>All teams are shared</li>'; ?> 
				</ul>
				<input style="margin-left: 10px; margin-top: 15px;" type="submit" value="submit" onclick="alert('TODO: add functionality.'); return false;" />
			</form>
		</div>
	</div>


<div class="menu_1">
</div>

<div class="menu_2">
</div>
</div>


<?
} ?> 

<?

# runs through the motions but doesn't pick up anything
/*
Array
(
    [source] => user_name
    [source_spacer] => _
    [0] => more_toggle
    [1] => modified
    [2] => _
    [3] => uid
    [edit] => edit
    [translate] => translate
    [delete] => delete
    [import] => import
    [export] => export
    [judge] => judge
)
*/

# todo figure out the right way to print this stuff
# for now just hack it so that we have a placeholder

/*
print_listing_template(
	$data['result']['result']['listing'][$k1],
	$key,
	$translation,
	'list',
	'main',
	'all',
	$_SESSION['login']['login_user_id']
	# , $style
); ?> 
</p><?
}
*/
