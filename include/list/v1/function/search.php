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

# Contents/Description: print out the input form fields

# print_container(& $container, & $listing = null, & $key = null, & $translation = null, $load = '', & $option = null) {
function print_keyword_box($toggle = 'asearch_box', $lock_only = false, $load = '', $center = false) {
	# $load would always be list. This is the only place we use print_keyword box.
	$load = 'list'; # todo fix $load parameter is is useless...
	global $x;
	global $config;

	# todo fix too many globals!
	global $key;
	global $translation;
	global $option;
	global $data;

	if (isset($data['search']['response'])) { ?> 
	<div class="keyword_box" style="<?= $center ? 'text-align: center;' : ''; ?>" ><?
		switch($x['load'][$load]['type'] . '_' . $x['load'][$load]['name']) {
			case 'jargon_list': ?> 
				<input type="hidden" name="translation_kind_name" value="tag" /><? # hack because we need this to submit on search for jargon list!
			# nobreak;
			default: 
			if ($lock_only != 1) { ?> 
				<input class="keyword_part" type="text" id="keyword" name="keyword" value="<?= to_html($data['search']['response']['search_miscellaneous']['keyword']); ?>" maxlength="255" /><?
				$t1 = $x['part'][0];
				if ($x['load']['view']['name']) {
					$t1 = $x['load'][$load]['type'];
				}
				switch($t1) {
					case 'main':
					case '':
						$t1 = 'item';
					break;
				}
			}
			break;
		}
		if ($lock_only == 1) { ?> 
			<input type="submit" value="<?= tt('element', 'search_mixed'); ?>" /><?
		} else { ?> 
			<input type="submit" value="<?= tt('element', 'find_' . to_html($t1)); ?>" /><?
		} ?> 
		<nobr>
		<span class="keyword_part">
		<span class="spacer" style="margin: 0px;">&gt;&gt;</span>
		<a class="keyword_part"
			id="<?= to_html($toggle); ?>_toggle" 
			style="display: inline;" 
			href="./<? # if javascript is turned off
				if (get_gp('asearch_on') == 1) 
					echo ffm('asearch_on=', 0);
				else
					echo ffm('asearch_on=1', 0);
			?>"	
			onclick="javascript: more_toggle('<?= to_html($toggle); ?>'); return false;"
		><?= (!get_gp('asearch_on') && (!$_SESSION['process']['failure'])) ? tt('element', 'more') : tt('element', 'less'); ?></a><?
		echo ' (';
			$n1 = 0; # initialize search criteria count
			if ($data['search']['response']) {
			foreach($data['search']['response'] as $k1 => $v1) {
				if (!empty($v1)) {
				foreach($v1 as $k2 => $v2) {
				switch($k2) {
					case 'referrer':
					case 'child':
					case 'lock_contact_user_mixed':
					break;
					default:
						if ($v2 != '')
							$n1++;
					break;
				} } }
				if ($v1['lock_contact_name'])
				if ($v1['lock_user_name'])
					$n1--; # count this pair as a single
			} }
			switch($x['load'][$load]['type'] . '_' . $x['load'][$load]['name']) {
				case 'translation_list':
				case 'jargon_list':
					# $n1--; # count high by 1 for some reason 2012-04-18 vaskoiii
					# if ($data['search']['response']['search_content_1']['translation_kind_name'])
					# 	$n1--;
					# elseif ($data['search']['response']['search_content_1']['kind_name'])
					# 	$n1--;
				break;
				default:
				break;
			}
			if (isset_gp($t1 . '_uid'))
				$n1++; # count also uid (not yet present in a container)
		echo $n1 . ')'; ?> 
		</span>
		</nobr>
	</div>
	<br clear="all" /><?


# form processing will put a url variable for expand 2012-04-18 vaskoiii
$b1 = 2;
$a1 = get_gp('expand');
if (!is_array($a1))
	$a1 = array();
#if(in_array($load, $a1) && $_SESSION['interpret']['failure'])
if (in_array($load, $a1))
	$b1 = 1; ?> 
	<div id="<?= to_html($toggle); ?>" style="<?= ($b1 == 1) ? 'display: block;' : 'display: none;'; ?>">
		<? if ($lock_only != 1) { ?> 
		<div class="nonlock_box"><?
			print_container($data['search']['response']['search_content_1'], $listing, $key, $translation, $load, $option); ?> 
		</div><?
		} ?> 
		<div class="lock_box"><?
			print_container($data['search']['response']['search_content_2'], $listing, $key, $translation, $load, $option); ?> 
		</div><?

		$s1 = $x['load']['list']['type'];
		if ($x['name'] == '' || $x['name'] == 'main')
			$s1 = 'item';

		# Special Option Box [soption_box]
		if (!$lock_only == 1)
		if ($s1) { 
			if ($x['page']['name'] == '' || $x['page']['name'] == 'main')
			 	echo '<br clear="all" />'; ?> 
			<div class="soption_box"><? // TODO: We need to have this section get crap from the $session and stuff in the case of errors or other stuff....; ?> 
				<div class="k">
					<span class="uid"><?= tt('element', $s1 . '_uid'); ?></span>: 
				</div>
				<div class="v"><?
					$s2 = '';
					if (isset_gp($s1 . '_uid'))
						$s2 = get_gp($s1 . '_uid');
					elseif ($_SESSION['process']['search_miscellaneous'][$s1 . '_uid'])
						$s2 = $_SESSION['process']['search_miscellaneous'][$s1 . '_uid']; ?> 
					<input class="uid" type="text" name="<?= to_html($s1 . '_uid'); ?>" value="<?= to_html($s2); ?>" />
				</div><?
				# use if you dont want the add feed link to appear on the main page...
				$s1 = $x['load']['list']['type'];
				if (!$x['load']['list']['type'])
					$s1 = 'item'; ?> 
				<div class="k">
					<span class="extra"><?= tt('element', 'extra'); ?></span>:
				</div>
				<div class="v">
					<a href="feed_edit/<?= ff('feed_query=' . to_url(ltrim(ffm('page=', 0), '?')) . '&page_name=' . to_url($s1 . '_list') . '&feed_name=' . to_url(get_page_title())); ?>"><?= tt('element', 'add_feed'); ?></a>
				</div>
			</div><?
		} ?> 
	<br clear="all" />
	</div><?
	}
}

# function is the equivalent of combining all layers into 1 2012-03-15 vaskoiii
# we need to treat cetain inputs together as a set ie) contact_user_mixed ie) kind_name_name
function load_response($container_name, & $container_reference, $login_user_id) {

	global $x;
	global $config;
	global $_SESSION;

	$e1 = explode('_', $container_name);

	if (empty($container_reference))
		return;

	# id
	if (get_gp($e[0] . '_id')) {
		die('load_repsonse entering id case not yet examined');
		return; # case not yet examined
	}
	# form
	elseif (!empty($_SESSION['process'][$container_name])) {
	if ($_SESSION['interpret']['failure']) {
	foreach ($container_reference as $k1 => $v1) {
	if ($_SESSION['process'][$container_name][$k1]) {
		 $container_reference[$k1] = $_SESSION['process'][$container_name][$k1];
	} } } }

	# misc
	else {
		# get
		url_response($container_reference, $login_user_id, $e1[0]);

		# session
		foreach ($container_reference as $k1 => $v1) {
		if (!$v1) {
		switch ($k1) {
			case 'background_theme_name':
			case 'launcher_theme_name':
				if ($_SESSION['theme'][$k1])
					$container_reference[$k1] = $_SESSION['theme'][$k1];
			break;
			default:
				$e2 = explode('_', $k1);
				if ($e2[0] != 'login') { # don't use the already logged in name for stuff by default ie) user_edit 2012-04-10 vaskoiii
				if ($_SESSION[$e2[0]][$k1]) {
					$container_reference[$k1] = $_SESSION[$e2[0]][$k1];
				} }
			break;
		} } }

		# default
		foreach ($container_reference as $k1 => $v1) {
		if (!$v1) {
		# searching defaults should be blank. todo add param to get_default_value 2012-03-15 vaskoiii
		switch($e1[0]) {
			case 'search':
			break;
			default:
				$container_reference[$k1] = get_default_value($k1, $e1[0]);
			break;
		} } }
	}
}

# maybe doesnt need $dialect_id because these are not translations they are just human readable data 2012-03-15 vaskoiii
function url_response(& $container, $login_user_id, $load) {

	global $x;
	global $config;
	global $_GET;

	# shortcut
	$prefix = $config['mysql']['prefix'];
	$prepend = '';
	switch ($load) {
		case '':
		case 'search':
		case 'result':
		case 'list':
		break;
		default:
			$prepend = $load . '_';
		break;
	}

	if (!empty($container)) {
	foreach ($container as $k1 => $v1) {
	switch ($k1) {
		case 'channel_parent_name':
			$container[$k1] = get_db_single_value('
					name
				FROM
					' . $prefix . 'channel
				WHERE
					id = ' . (int)get_gp('channel_parent_id')
			, 0);
		break;
		break;
		case 'keyword':
		case 'invite_password':
		case 'feed_query':
		case 'page_name':
		case 'feed_name':
			if (isset_gp($prepend . $k1))
				$container[$k1] = get_gp($prepend . $k1);
		break;
		case 'meritopic_id':
		case 'incident_id':
		case 'feedback_id':
		if ($k1) 
			$container[$k1] = get_db_single_value('
					id
				FROM
					' . $prefix . str_replace('_id', '', $k1) . '
				WHERE
					id = ' . (int)get_gp($prepend . $k1)
			, 0);
		break;
		case 'dialect_name':
		case 'direction_name':
		case 'element_name':
		case 'grade_name':
		case 'group_name':
		case 'invite_user_name':
		case 'location_name':
		case 'lock_group_name':
		case 'lock_location_name':
		case 'lock_range_name':
		case 'lock_team_name':

		case 'parent_tag_path': # special
		case 'parent_tag_name':

		case 'decision_name':
		case 'phase_name':
		case 'status_name':
		case 'tag_name':
		case 'team_name':
		case 'team_required_name':
			$a1 = array(
				'_path',
				'_name',
			);
			$s1 = str_replace($a1, array('',''), $k1);
			$a2 = array(
				'_name',
				'_path',
				'_required',
				'invite_',
				'lock_',
				'parent_',
			);
			$s2 = str_replace($a2, array('','','','','','',''), $k1);
			if (isset_gp($prepend . $s1 . '_id'))
				$container[$k1] = get_db_single_value('
						name
					FROM
						' . $prefix . $s2 . '
					WHERE
						id = ' . (int)get_gp($prepend . $s1 . '_id')
				, 0);
		break;
		case 'contact_name':
		case 'user_name':
		case 'lock_contact_name':
		case 'lock_user_name':
			# do together as mixed
		break;
		case 'contact_user_mixed':
		case 'lock_contact_user_mixed':
			# handled after the foreach
			# todo should happen in this function 2012-03-11 vaskoiii
			$s1 = '';
			if (str_match('lock_', $k1))
				$s1 = 'lock_';
			if (isset_gp($prepend . $s1 . 'user_id')) {
				$container[$s1 . 'user_name'] = get_db_single_value('
						name
					FROM
						' . $prefix . 'user
					WHERE
						id = ' . (int)get_gp($prepend . $s1 . 'user_id')
				, 0);
				# Try to obtain contact_name from user_name 2012-02-15 vaskoiii
				# $_GET['contact_id'] is intentionally NOT set if $_GET['user_id'] is set.
				$container[$s1 . 'contact_name'] = get_db_single_value('
						c.`name`
					FROM
						' . $prefix . 'user u,
						' . $prefix . 'contact c,
						' . $prefix . 'link_contact_user lcu
					WHERE
						u.id = lcu.user_id AND
						c.id = lcu.contact_id AND
						u.name = ' . to_sql($container[$s1 . 'user_id']) . ' AND
						c.user_id = ' . (int)$login_user_id . ' AND
						c.active = 1
				', 0);
			}
			if (!$container[$s1 . 'contact_name'])
			if (isset_gp($prepend . $s1 . 'contact_id'))
				$container[$s1 . 'contact_name'] = get_db_single_value('
						name
					FROM
						' . $prefix . 'contact
					WHERE
						id = ' . (int)get_gp($prepend . $s1 . 'contact_id') . ' AND
						user_id = ' . (int)$login_user_id
				, 0);
			$container[$s1 . 'contact_user_mixed'] = $container[$s1 . 'user_name'];
			if ($container[$s1 . 'user_id'])
			if ($container[$s1 . 'contact_id'])
				$container[$s1 . 'contact_user_mixed'] .= ' ';
			if ($container[$s1 . 'contact_id'])
				$container[$s1 . 'contact_user_mixed'] .= $container[$s1 . 'contact_name'];
		break;
		case 'minder_kind_name':
		case 'translation_kind_name':
			# done in kind_name_name 2012-03-11 vaskoiii
		break;
		case 'kind_name_name':
			$t1 = 'kind';
			if (get_gp($prepend . 'translation_kind_id'))
				$t1 = 'translation_kind';
			elseif (get_gp($prepend . 'minder_kind_id'))
				$t1 = 'minder_kind';
			if (!$container[$t1 . '_name']) {
			if (isset_gp($prepend . $t1 . '_id')) {
				$container[$t1 . '_name'] = get_db_single_value('
						name
					FROM
						' . $prefix . 'kind
					WHERE
						id = ' . (int)get_gp($prepend . $t1 . '_id')
				, 0);
			} }
			# no sql injection 2012-03-11 vaskoiii
			if (get_gp($prepend . 'kind_name_id')) {
			if (get_gp($prepend . $t1 . '_id')) {
				$container[$k1] = get_db_single_value('
						name
					FROM
						' . mysql_real_escape_string($prefix  . $container[$t1 . '_name']) . '
					WHERE
						id = ' . (int)get_gp($prepend . 'kind_name_id') . '
				', 0);
			} }
		break;
	} } }
}

# todo 2012-03-11 vaskoiii
# get_machine_value($input, $value, $array = array()) return array('id' => 'value');
# get_human_value($input, $value, $array = array()) return array('name' => 'value');

function get_default_value($input, $load, & $x = null) {
	# hardcodes! but maybe ok 2012-03-15 vaskoiii
	# note: most default values are blank!
	global $_SESSION;
	if (empty($x))
		global $x;

	switch($input) {
		case 'status_name':
		switch($load) {
			case 'search':
			break;
			default:
				return 'status_neutral';
			break;
		}
		break;
		case 'page_name':
		switch($load) {
			case 'search':
			break;
			default:
				return 'item_list';
			break;
		}
		break;
		case 'parent_tag_path':
		case 'parent_tag_name':
		switch($load) {
			case 'search':
			break;
			default:
				return '<|!|>';
			break;
		}
		break;
		case 'dialect_name': # session is always set to this never gets to here unless you are not logged in 2012-04-19 vaskoiii
		switch($load) {
			case 'search':
			break;
			default:
				# dialect name should not be user modifyable
				return 'English';
			break;
		}
		break;
		break;
		case 'team_required_name':
		switch($load) {
			case 'search':
			break;
			default:
				return '<|*|>';
			break;
		}
		break;
		case 'location_name':
		case 'lock_location_name':
		switch($load) {
			case 'search':
			break;
			default:
				return '<|?|>';
			break;
		}
		break;
	}
	return '';
}

# maybe load should be the first parameter 2012-03-15 vaskoiii
function print_container(& $container, & $listing = null, & $key = null, & $translation = null, $load = '', & $option = null) {


	# todo: these globals should not be used! 2012-03-07 vaskoiii
	if (!$listing) {
		global $data;
		$listing = & $data['action']['result']['listing'][0];
	}
	if (!$key)
		global $key;
	if (!$translation);
		global $translation;
	if (!$option)
		global $option;

	# 2012-12-03 vaskoiii
	# TODO: use the following variables to prepoulate necessary (but not necessarily all) information for importing:
	# import_item_id
	# import_transfer_id
	# TODO: account for:
	# item >> item
	# item >> transfer
	# transfer >> transfer
	# transfer >> item
	# TODO: use URL values of [id] not [name] and not [description] ie) action_status_id=2 NOT action_status_name=Neutral
	$load_ = $load . '_'; # prefix used to autopopulate values from URL - [search] is default so no prefix used on URL values
	switch($load) {
		case '':
		case 'search':
		case 'result':
		case 'list': # alias for [search] and [result] as a combined entity
			$load_ = '';
		break;
	}

	# ok globals
	global $x;
	global $config;

	if (!empty($container)) {
	foreach($container as $k1 => $v1) {

	$s3 = ''; # reset each run;

	if ($k1 == 'background_theme_name')
		;#die('v1=' . $v1);

	if (preg_match('/^todo/', $k1)) {
		echo '<div class="k">' . to_html($k1) . '</div>';
	}
	else switch($k1) {
		case 'pubkey_value': ?>
			<span valign="top">
				<div class="k"><span class="user_name"><?= tt('element', $k1); ?></span>:</div>
				<div class="v">
					<div class="textarea"><?
						# 4096 = 2^12 = DB Supported/Intended Max Length
						# QR Code Scanning is best with less characters ( Version 4 = Target = Up to 50 char! )
						# QR Code Alphanumeric Max: 4,296 characters
						# (0–9, A–Z [upper-case only], space, $, %, *, +, -, ., /, :) ?> 
						<textarea style="background: #ddd; color: #000;" onkeypress="if (event.which == 13) { event.preventDefault(); submit(); };" class="description_input" name="" disabled /><?= $v1 ? to_html($v1) : to_html(get_gp($load_ . $k1)); ?> (Corresponds with your QR Code)</textarea>
						<input type="hidden" name="<?= $k1; ?>" value="<?= (int)$_SESSION['login']['login_user_id']; ?>" />
					</div><?
					# todo input to change ?> 
					<!-- 
					<br />
					<div>
						<?= $v1 ? to_html($v1) : to_html(get_gp($load_ . $k1)); ?>
					</div>
					-->
				</div>
			<span>
			<br /><?
		break;
		case 'face_file':
			# combined into the case below
		break;
		case 'face_filer_id': ?> 
			<span valign="top">
				<div class="k"><span class="user_name"><?= tt('element', 'face_file'); ?></span>:</div>
				<div class="v">
					<input type="file" name="face_file" style="margin-top: 5px; border: 2px inset #eee;" />
					<br />
					<table style="width: 128px; height: 128px; text-align: center; margin-bottom: 10px; margin-top: 5px;" cellpadding="0" cellspacing="0"></tr><td><?
						if (!empty($v1)) { ?> 
							<img src="/file/?id=<?= (int)$v1; ?>" /><?
						}
						else {
							# ts_icon ?> 
							<img src="/list/v1/theme/select_none/ts_icon_256x256.png" style="width: 128px; height: 128px;" /><?
						} ?> 
					</td></tr></table>
				</div>
			<span><?
		case 'face_md5':
		case 'face_extension':
			# using yet?
		break;
		# case 'kind_name_translation_name':
		# todo create less ambiguity by creating a separate case
		# break;
		case 'invite_id': ?> 
			<input type="hidden" name="invite_id" value="<?= get_gp('action_invite_id'); ?>" /><?
		break;
		case 'kind_name_name': # tricky because engine uses generic values instead of the more specific translation_kind_name_name 2012-03-10 vaskoiii ?> 
			<span valign="top">
				<div class="k"><span class="<?= $k1; ?>"><?= tt('element', $k1); ?></span>:</div>
				<div class="v"><?
				if ($_SESSION['interpret']['failure'] == 1) { ?> 
					<input type="text" class="<?= $k1; ?>" name="<?= $k1; ?>" value="<?= $container[$k1]; ?>" maxlength="255" /><?
				} else { 
					if (!empty($listing)) { ?> 
						<input type="text" class="<?= $k1; ?>" name="<?= $k1; ?>" value="<?= kk($listing['kind_name'], $listing['kind_name_id'], $listing['kind_name'] . '_name', '' /*$listing['kind_name'] . '_id: ' . $listing['kind_name_id']*/, $key); ?>" maxlength="255" /><?
					} else {  ?> 
						<input type="text" class="<?= $k1; ?>" name="<?= $k1; ?>" value="<?= # get_gp($load_ . 'kind_name_id') ??? 2012-12-03
							kk($container['translation_kind_name'], get_gp('kind_name_id'), $container['translation_kind_name'] . '_name', '', $key); ?>" maxlength="255" /><?
						# todo tricky! 2012-05-01 vaskoiii
					}
				} ?> 
				</div>
			</span><?
		break;
		case 'tag_translation_name': ?> 
			<span valign="top">
				<div class="k"><span class="<?= $k1; ?>"><?= tt('element', $k1); ?></span>:</div>
				<div class="v">
					<input type="text" class="<?= $k1; ?>" name="<?= $k1; ?>" value="<?= $v1 ? to_html($v1) : get_gp($load_ . $k1); ?>" maxlength="255" />
				</div>
			</span><?
		break;
		# back to 1st switch functions 2012-02-10 vaskoiii
		case 'enabled':
			unset($container['enabled']);
		break;
		// REDIRECT!
		case 'login_request_uri':
		case 'redirect': ?> 
			<div class="k"><?= tt('element', $k1); ?>:</div>
			<div class="v"><?= to_html($v1); ?></div>
			<input type="hidden" name="<?= $k1; ?>" value="<?= to_html($v1); ?>" /><?
		break;
		case 'keyword':
		case 'asearch_on':
		case 'referrer':
		case 'child':
			// special case
		break;
		case 'user_password_unencrypted':
		case 'user_password_unencrypted_again': ?> 
			<div class="k"><span class="<?= $k1; ?>"><?= tt('element', $k1); ?></span>:</div>
			<div class="v"><input type="password" name="<?= $k1; ?>" value="<?= to_html($v1); ?>" /></div><?
		break;
		// Checkbox
		// might be confusing because checkboxes don't get submitted but if we read the values from the database they are either 1 or 2 so they kind of have different logic for display and entry.
		case 'enabled':
		case 'remember_login':
		case 'accept_friend':
		case 'accept_usage_policy': ?> 
			<div class="k"><span class="<?= $k1; ?>"><?= tt('element', $k1); ?></span>:</div>
			<div class="v"><input type="checkbox" name="<?= $k1; ?>" <?= ($v1 != 1) ? '' : 'checked="checked"'; ?> /></div><?
		break;
		// case to not appear straight away - feed_edit only fields
		/*
		case 'page_name':
		case 'feed_query': ?> 
			<input type="hidden" name="<?= $k1; ?>" value="<?= to_html($v1); ?>" /><?
		break;	
		*/
		case 'kind_name':
		case 'minder_kind_name':
		case 'translation_kind_name':
			# Warning action_minder_kind_id can be set in the url but listing info can be empty
			if (!$v1) { # probably helps same check as below
			if ($listing['kind_name']) { # make sure set so not seting to empty!
				# tricky because engine gets kind_id but display is uses translation_kind_name/minder_kind_name 2012-03-10 vaskoiii
				$v1 = $listing['kind_name'];
			} }
		#nobreak;
		case 'background_theme_name':
		case 'launcher_theme_name':
		if (str_match('_theme_name', $k1)) #todo this is a crappy check also theme_name will have had to have gone 1st to work right 2012-04-05 vaskoiii
			$option[$k1] = $option['theme_name'];
		#nobreak
		case 'channel_parent_name':
		case 'channel_name': # errr
		case 'decision_name':
		case 'default_boolean_name':
		case 'dialect_name':
		case 'direction_name':
		case 'display_name':
		case 'feed_query':
		case 'grade_name':
		case 'group_name':
		case 'location_name':
		case 'lock_group_name':
		case 'lock_location_name':
		case 'lock_range_name':
		case 'lock_team_name':
		case 'meritype_name':
		case 'page_name':
		case 'parent_tag_name':
		case 'parent_tag_path':
		case 'phase_name':
		case 'point_name':
		case 'range_name':
		case 'status_name':
		case 'team_name':
		case 'team_required_name':
		case 'theme_name': ?> 
			<div class="k"><span class="<?= $k1; ?>"><?= tt('element', $k1); ?></span>:</div>
			<div class="v"><?

			$i1 = 1; # input type="text"
			if (!empty($option[$k1]))
				$i1 = 2; # select
		#echo '<pre>'; print_r($option['status_name']); echo '</pre>';
			if ($load == 'action')
			if ($x['load']['action']['name'] != 'set')
			if ($x['load']['action']['type'] == str_replace('_name', '', $k1))
				$i1 = 1; # alternatively we could define another input type for use in this case 2012-03-08 vaskoiii

			if ($i1 == 2) { ?> 
			<select onkeypress="if (event.which == 13) { event.preventDefault(); submit(); };" class="<?= $k1; ?>" name="<?= $k1; ?>">
				<option></option><?
				$b1 = 2; # reset master select

				if ($x['load']['action']['name'] == 'set')
				if ($x['load']['action']['type'] == 'theme')
					$b1 = 1;
				# don't have an option for certain pages: ????
				foreach ($option[$k1] as $k2 => $v2) {
					# echo "\n k1=".$k1 . ' k2='.$k2 . ' v1='.$v1 . ' v2='.$v2;
					# echo "\n" . $k1 . ' | ' ; echo $v1 . ' ?= ' . $k2;
					if ($v1 == $k2) {
						$b1 = 1; # get master select
					}
				}
				foreach ($option[$k1] as $k2 => $v2) {
				# do not allow mixing listing types in feeds 2012-06-05 vaskoiii ?> 
				<option <?
					// use master select
					if ($b1 == 1 && $v1 == $k2) { ?> 
						 selected="selected" <?
					} 
					elseif (get_gp($load_ . $k1) == $v2) { # Needed if value is set in the URL ?> 
						 selected="selected" <?
					}
					elseif ($b1 != 1) {
					switch ($k1) {
						case 'default_boolean_name':
						if (!isset_gp('id') && $k2 == '1')
							; // echo ' selected="selected" ';
						break;
					} } ?> 
					value="<?= $k2; ?>"><?
					#echo 'b1=' . $b1 . '&v1=' . $v1 . '&k2=' . $k2;
					switch($k1) {
						# NOT translated
						case 'parent_tag_path':
						case 'parent_tag_name':
							echo to_html($v2);
						break;
						# NOT translated
						case 'channel_parent_name':
						case 'channel_name': # err
						case 'location_name':
						case 'lock_location_name':
						case 'team_name':
						case 'lock_team_name':
						case 'team_required_name':
						case 'dialect_name':
						case 'lock_group_name':
						case 'group_name': ?> 
							<?= to_html($k2); ?><?
						break;
						case 'default_boolean_name': ?> 
							<?=  tt('boolean', $k2); ?><?
						break;
						case 'translation_kind_name':
						case 'minder_kind_name': ?> 
							<?= tt('kind', $k2); ?><?
						break;
						case 'background_theme_name':
						case 'launcher_theme_name':
							echo tt('theme', $k2);
						break;
						default:?> 
							<?= tt(str_replace(array('lock_', '_name'), array('', ''), $k1), $k2); ?><?
						break;
					} ?> 
				</option><?
				} ?> 
			</select><?
			}
			else { ?> 
				<input class="<?= $k1; ?>" type="text" name="<?= $k1; ?>" value="<?= to_html($v1); ?>" maxlength="255" /><?
			} ?> 
			</div><?
		break;
		case 'order_name':
			# Ignore
		break;
		# BOOLEAN implementation
		# recommend: 1 = true and 2 = false to avoid using a 0 in the data type
		case 'load_javascript': ?> 
			<div class="k"><span class="<?= $k1; ?>"><?= tt('element', $k1); ?></span>:</div>
			<div class="v"><input type="checkbox" name="<?= $k1; ?>" <?= ($_SESSION['load']['load_javascript'] == 1) ? 'checked="checked"' : ''; ?> /></div><?
		break;

		case 'feature_lock':
		case 'feature_minnotify':
		case 'notify_offer_received':
		case 'notify_teammate_received': ?> 
				<div class="k"><span class="<?= $k1; ?>"><?= tt('element', $k1); ?></span>:</div>
				<div class="v"><input type="checkbox" name="<?= $k1; ?>" <?= ($v1 == 'true' || $v1 == 1) ? 'checked="checked"' : ''; ?> /></div><?
		break;
		case 'contact_user_mixed':
			$b2 = 2;
			switch($load) {
				case 'motion':
				case 'action':
					if ($x['load'][$load]['type'] == 'contact') {
					if ($x['load'][$load]['name'] == 'edit') {
						$b2 = 1;
					} }
				break;
			}
			if ($b2 == 2) { ?> 
			<div class="k"><span class="contact_name"><?= tt('element', 'contact_name'); ?></span> <span class="user_name"><?= to_html($config['unabstracted_prefix']) . tt('element', 'user_name') . to_html($config['unabstracted_suffix']); ?></span>:</div>
			<div class="v"><?
				$s1 = ''; ?> 
				<input class="<?= $k1; ?>" type="text" name="<?= $k1; ?>" value="<?
				if ($_SESSION['interpret']['failure'] == 1) {
					echo $container['contact_user_mixed'];
				}
				else if ($load == 'fast') {
					; # print nothing in the box;
				}
				else if ($load == 'quick') {

					# todo this is extra work and could be implemented without the overhead
					$i1 = (int)$_GET['contact_id'];
					if (!$i1)
						$i1 = (int)$_GET['lock_contact_id'];
					$i2 = (int)$_GET['user_id'];
					if (!$i2)
						$i2 = (int)$_GET['lock_user_id'];

					$a1 = array();
					contact_user_mixed_combine($a1, $i2, $i1, $_SESSION['login']['login_user_id']);

					# echo '<pre>'; print_r($empty_listing); echo '</pre>'; exit;

					echo $a1['contact_name'];
					if (
						!empty($a1['user_name']) &&
						!empty($a1['user_name'])
					)
						echo ' ';
					if (!empty($a1['user_name']))
						echo '(' . $a1['user_name'] . ')';
				}
				else {
					# dont forget to account for the possible destination_user_id 2012-03-27 vaskoiii
					if ($container['contact_name'])
						echo $container['contact_name']; 
					elseif ($key['user_id']['result'][ $listing['user_id'] ]['contact_name'])
						echo $key['user_id']['result'][ $listing['user_id'] ]['contact_name'];
					elseif ($key['user_id']['result'][ $listing['destination_user_id'] ]['contact_name'])
						echo $key['user_id']['result'][ $listing['destination_user_id'] ]['contact_name'];

					if ($container['user_name']) 
						$s1 .= $container['user_name'];
					elseif ($key['contact_id']['result'][ $listing['contact_id'] ]['user_name'])
						$s1 .= $key['contact_id']['result'][ $listing['contact_id'] ]['user_name'];
					if ($s1)
						echo ' ' . to_html($config['unabstracted_prefix'] . $s1 . $config['unabstracted_suffix']);
				}
				?>" />
			</div><?
			}
		break;
		case 'lock_contact_user_mixed':
			$b2 = 2;
			switch($load) {
				case 'motion':
				case 'action':
					if ($x['load'][$load]['type'] == 'contact') {
					if ($x['load'][$load]['name'] == 'edit') {
						$b2 = 1;
					} }
				break;
			}
			if ($b2 == 2) { ?> 
			<div class="k"><span class="lock_contact_name"><?= tt('element', 'lock_contact_name'); ?></span> <span class="lock_user_name"><?= to_html($config['unabstracted_prefix']); ?><?= tt('element', 'lock_user_name'); ?><?= to_html($config['unabstracted_suffix']); ?></span>:</div>
			<div class="v"><input type="text" class="<?= $k1; ?>" name="<?= $k1; ?>" value="<?

			if ($_SESSION['process']['failure'])
				echo $_SESSION['process']['search_content_2']['lock_contact_user_mixed'];
			else {
				echo $container['lock_contact_name'];
				if ( $container['lock_contact_name'] &&  $container['lock_user_name']) 
					echo ' ';
				echo ($container['lock_user_name']
					? to_html($config['unabstracted_prefix']) . $container['lock_user_name'] . to_html($config['unabstracted_suffix'])
					: ''
				);
			} ?>" /></div><?
			}
		break;
		case 'lock_user_name':
		case 'lock_contact_name':
			# might need to be shown on contact_edit only
			# $s3 = ''; # at begining of foreach()
			$s3 = 'lock_';
		# nobreak;
		case 'user_name':
		case 'contact_name': 
			$b2 = 2;
			switch($load) {
				case 'motion':
				case 'action':
					if ($x['load'][$load]['type'] == 'contact') {
					if ($x['load'][$load]['name'] == 'edit') {
						$b2 = 1;
					} }
				break;
			}
			if ($b2 == 1) {
				# todo rename this to unused_user_name/unused_lock_user_name if we need to differentiate
				# we already do login_user_name to differentiate
				# need a special classname for the ajax ?> 
				<div class="k"><span class="<?= $s3.$k1; ?>"><?= tt('element', $s3.$k1); ?></span></div>
				<div class="v"><input type="text" class="<?= $s3.$k1; ?>" name="<?= $s3.$k1; ?>" value="<?
					if ($_SESSION['process']['failure'])
						echo $_SESSION['process']['search_content_2'][$s3.$k1];
					else {
						if ($k1 == 'user_name') {
							echo $key['contact_id']['result'][$listing['contact_id']]['user_name'];
						}
						echo $container[$s3.$k1];
					}
				?>" /></div><?
			}
		break;
		default: ?> 
		<span valign="top">
			<div class="k"><span class="<?= $k1; ?>"><?= tt('element', $k1); ?></span>:</div>
			<div class="v"><?
				if ( !str_match('_description', $k1) ) { ?> 
					<input type="text" class="<?= $k1; ?>" name="<?= $k1; ?>" value="<?= ( $v1 !== 0 || !empty($v1)) ? to_html($v1) : to_html(get_gp($load_ . $k1)); ?>" maxlength="255" /><?
				}
				else { ?> 
					<? # textarea display hacks 2012-02-26 vaskoiii ?> 
					<div class="textarea">
						<textarea style="" onkeypress="if (event.which == 13) { event.preventDefault(); submit(); };" class="description_input" name="<?= $k1; ?>" maxlength="255" /><?= $v1 ? to_html($v1) : to_html(get_gp($load_ . $k1)); ?></textarea>
					</div>
					&nbsp;<?
				} ?> 
			</div>
		</span><?
		break;
	} } }
}
