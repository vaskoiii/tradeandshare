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

# Content/Description: Display Action Content
# Known Issues: Kind Name Does NOT get remembered ?> 

<div class="title"><?
	$s1 = '';
	if ($x['load']['list']['name'] == 'report'
	 || $x['name'] == 'top_report') {
		$x['preload']['focus'] = 'action';
		if (get_gp('expand'))
			$x['preload']['expand'] = get_gp('expand');
		$s1 = get_translation('page', 'top_report');
	}
	else {
		if ($x['load']['action']['name'] == 'edit' && $x['load']['list']['type'] == $x['load']['action']['type'])
			$s1 .= get_translation('page', $x['load']['list']['type'] . '_list');
		else
			$s1 .= get_translation('page', $x['load']['action']['type'] . '_' . $x['load']['action']['name']);
	} ?> 
	<span class="go_back"><?= print_go_back('&lt;&lt;'); ?></span>
	<h2><?
		switch($_SESSION['process']['form_info']['load']) {
			case 'fast':
			case 'quick':
				print_ts_focus($s1, 'action');
			break;
			default:
				if (get_gp('focus'))
					print_ts_focus($s1, get_gp('focus'));
				else
					print_ts_focus($s1, 'action');
			break;
		} ?> 
	</h2>
	<p class="result_add"><?
	switch($x['load']['action']['name']) {
		case 'set':
		case 'recover':
		break;
		default:
		switch($x['load']['action']['type']) {
			case 'minder':
			case 'dialect':
			case 'invited':
			case 'login':
			case 'cycle':
			case 'transaction': # temporary until using actual transactions
			break;
			default:
			switch($x['name']) {
				case 'invite_edit':
				case 'profile_edit':
				break;
				case 'user_edit':
					# dont show edit button when registering
					if (!$_SESSION['login']['login_user_id'])
						break;
				# nobreak;
				default:
				if ($x['load']['action']['type']) {
					/*
					if ($x['load']['list']['name'] == 'report'
					 || $x['name'] == 'top_report') {
						if ($x['load']['action']['name'] == 'edit' && $x['load']['list']['type'] == $x['load']['action']['type'])
							echo tt('page', $x['load']['list']['type'] . '_list');
						else {
							# hack for top_report 2012-05-06 vaskoiii
							echo tt('page', $x['load']['action']['type'] . '_list');
						}
					}
					?><span id="action_box_toggle" style="display: none;"><?=
					# does this do anything 2014-01-04 vaskoiii
					$x['part'][1] == 'edit' 
						? tt('element', 'less')
						: tt('element', 'more');
					?></span></a><?= $x['load']['view']['name'] ? '*' : '';
					*/
					$b1 = 1;
					if (isset($_GET['expand']))
					if (is_array($_GET['expand']))
					foreach ($_GET['expand'] as $k1 => $v1)
					if ($v1 == 'action')
						$b1 = 2;

					# todo hide message associated with the form on hiding that box. same as fast and quick
					?> 
					<a id="action_el_swap1" style="display: <?= $b1 == 1 ? 'inline-block' : 'none'; ?>;" href="#" onclick="javascript: more_toggle_swap('action_el', 'action_el_swap2'); document.getElementById('list_title_box').style.display = 'block'; return false;"><?= tt('element', 'edit'); ?></a>
					<a id="action_el_swap2"  style="display: <?= $b1 == 2 ? 'inline-block' : 'none'; ?>;" href="#" onclick="javascript: more_toggle_swap('action_el', 'action_el_swap1'); document.getElementById('list_title_box').style.display = 'none'; return false;"><?= tt('element', 'list'); ?></a><?
				}
				break;
			}
			break;
		}
		break;
	}
	  ?> 
	</p>
</div><?

if ($x['preload']['focus'] == 'action')
	print_message_bar();

# action box start ?> 
<span id="action_el_box"  style="display: <?= get_action_style_display(); ?>;">
<div class="content"><?
# todo separate page for this?
$b1 = 2;
if ($x['page']['name'] == 'user_edit') {
	$b1 = 1;
}
if ($b1 == 1) { ?> 
	<form name="<?= $x['part'][1]; ?>_process" action="/index.php" method="POST">
	<input type="hidden" name ="x" value="<?= to_html($x['.']); ?>user_process/" /><?
}
else { ?> 
	<form name="<?= $x['part'][1]; ?>_process" action="/index.php" method="POST">
	<input type="hidden" name ="x" value="<?= to_html($x['.']); ?><?= $x['load']['action']['name']; ?>_process/" /><?
}
unset($b1);
?> 
<input type="hidden" name="q" value="<?= ff('', 1); ?>" />
<input type="hidden" name="load" value="action" />
<input type="hidden" name="type" value="<?= $x['load']['action']['type']; ?>" />
<input type="hidden" name="id" value="<?= (int)$x['load']['action']['id']; ?>" /><?

# values are overridden if they are redeclared
if (!empty($data['action']['response'])) {
foreach($data['action']['response'] as $k1 => $v1) {
if (!empty($v1)) {
foreach($v1 as $k2 => $v2) {
switch($k2) {
	case 'load_javascript':
	case 'enabled':
	case 'accept_friend':
	case 'accept_default':
	case 'accept_usage_policy':
	case 'remember_login':
	case 'notify_teammate_received':
	case 'notify_offer_received':
	case 'feature_lock':
	case 'feature_minnotify':
		# dont override checkboxes
	break;
	// Special variable ONLY used on invitation!
	case 'invite_user_name':
	case 'invite_password':
		if (isset_gp('edit_id') || $x['page']['name'] == 'invite_edit')  // password is autogenerated and uneditable 2008-12-04
			unset($data['action']['response']['action_content_1'][$k2]);
	break;
	case 'user_name':
	case 'lock_user_name':
	case 'contact_name':
	case 'lock_contact_name':
		// do nothing.
	break;
	default:
		//if (!empty($v2) { ?> 
			<input type="hidden" name="<?= to_html($k2); ?>" value="<?= to_html($v2); ?>" /><?
		//}
	break;
} } } } } ?> 

<div class="table">
	<div id="action_content_1"><?
		print_container($action_content_1, $action_listing, $key, $translation, 'action', $option); ?> 
	</div><?

	if(!empty($action_content_2)) { ?> 
	<p class="more_solo">
		&gt;&gt; <a id="action_content_2_toggle" style="display: inline;" href="#" onclick="more_toggle('action_content_2'); return false;"><?= tt('element', 'more'); ?></a>
	</p><?
	} 
?> 
	<div id="action_content_2" style="margin-left: 20px; margin-bottom: 15px; display: none;"><?
		print_container($action_content_2, $action_listing, $key, $translation, 'action', $option); ?> 
	</div>
</div>


<div class="menu_1">
<ul><?
if (!empty($data['action']['response']['action_footer_1']))
foreach($data['action']['response']['action_footer_1'] as $k1 => $v1) {
switch($k1) {
	case 'recover':
	case 'set':
	case 'submit': # used on confirmation pages 2012-02-10 vaskoiii
		if (!get_gp('preview') == 1) { ?> 
			<input type="submit" name="<?= $k1; ?>" value="<?= tt('element', $k1); ?>" /><?
		}
	break;
	case 'send': # use "send" context instead of "add"
	case 'add':
		switch($x['part'][0]) {
			case 'offer': ?> 
				<input type="submit" name="<?= $k1; ?>" value="<?= tt('element', 'send'); ?>" /><?
			break;
			default:
				if (!get_gp('id') && !get_gp('action_id') && $x['page']['name'] != 'profile_edit') { ?> 
					<input type="submit" name="<?= $k1; ?>" value="<?= tt('element', $k1); ?>" /><?
				}
			break;
		}
	break;
	case 'edit':
		if (!get_gp('preview') == 1) {
		switch($x['page']['name']) {
			case 'offer_edit':
			break;
			default:
				if (get_gp('id') || get_gp('action_id') || $x['page']['name'] == 'profile_edit') { ?> 
					<input type="submit" name="<?= (get_gp('preview') == 1 ? 're' : '') . $k1; ?>" value="<?= tt('element', (get_gp('preview') == 1 ? 're' : '') . $k1); ?>" /><?
				}
			break;
		}
		}
	break;
	default:
		echo '<tr><td colspan="2">Nothing Matched!!!' . $k1 . '</td></tr>';
	break;
} } ?> 
</ul>
</div>

<div class="menu_2">
<ul><?
	# todo make sure id is ONLY used for action if used at all 2012-05-02 vaskoiii
	# form clearing
	$b1 = 2;
	if (get_gp('action_tag_id'))
		$b1 = 1;
	elseif ($x['load']['action']['id'])
		$b1 = 1;
	if ($b1 == 1) { ?> 
		<li><a href="./<?= ffm('id=&action_id=&action_tag_id=&preview%5B0%5D=&expand%5B0%5D=action&focus=action', 0); ?>"><?= tt('element', 'new_form'); ?></a>*</li><?
	}

	if (!empty($data['action']['response']['action_footer_2'])) {
	foreach($data['action']['response']['action_footer_2'] as $k1 => $v1) {
	switch($k1) {
		case 'recover_feed':
			if (get_gp('action_id')) { ?> 
				<li><a href="feed_recover/<?= ff('id=' . (int)get_gp('action_id') ); ?>"><?= get_translation('element', 'recover'); ?></a></li><?
			}
		break;
		case 'recover_login':
			# has its own page
		break;
		default: ?> 
			<li><?= $k1; ?></li><?
		break;
		
	} } } ?> 
</ul>
</div>

</form>
</div>
</span><?
# action_box end ?> 

</div>
