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

# Contents/Description: Custom functions intended to be specific to this site. New functions that may be moved elsewhere later.

function print_go_back($name, $kind = null) {
	global $x;
	if ($_SESSION['login']['login_user_name']) {
		if ($_SESSION['feature']['feature_lock'] == 1 && $x['page']['name'] == 'lock_set') { ?> 
			<a href="<?= str_replace('_edit', '_list', $x['..']); ?><?= ffm(get_lock_query('preview%5B0%5D=&focus=&expand%5B0%5D='), -1); ?>"><?= 
				!empty($kind)
					? tt($kind, $name) 
					: $name
				;
			?></a><?
		} else { ?> 
			<a href="<?= str_replace('_edit', '_list', $x['..']); ?><?= ffm('preview%5B0%5D=&focus=&expand%5B0%5D=', -1); ?>"><?=
				!empty($kind)
					? tt($kind, $name) 
					: $name
				;
			?></a><?
		}
	} else { 
		if ($x['page']['name'] == 'login_set') {
			# if not logged in and you click back on login_set it will keep headering you back to login set! ?> 
			<a href="/<?= ff($q['active'][$x['level'] - 1], -1); ?>"><?= 
				!empty($kind)
					? tt($kind, $name) 
					: $name
				;
			?></a><?
		} else { ?> 
			<a href="<?= $x['..']; ?><?= ff($q['active'][$x['level'] - 1], -1); ?>"><?= 
				!empty($kind)
					? tt($kind, $name) 
					: $name
				;
			?></a><?
		} 
	}
}

# reverses the order of tt() parameters
# typed for documentation style pages (originally user_report)
function print_break_open($title_name, $title_kind = null) { ?> 
	<div class="title">
		<h2><?
			switch ($title_kind) {
				case null:
					# do not translate
					echo to_html($title_name);
				break;
				default:
					echo tt($title_kind, $title_name);
				break;
			} ?> 
		</h2>
	</div>
	<div class="content">
		<div class="content_box"><?
}
function print_break_close() { ?> 
		</div>
		<div class="menu_1"></div>
		<div class="menu_2"></div>
	</div><? 
}

function ts_die($s) {
	global $config;
	if ($config['debug'] == 1)
		die($s);
}

function encrypt_password($string) {
	return md5($string);
}

function get_user_id_from_contact_name($name, $login_user_id) {
	global $config;
	if (!$name)
		return;
	return get_db_single_value('
			lcu.user_id
		from
			' . $config['mysql']['prefix'] . 'link_contact_user lcu,
			' . $config['mysql']['prefix'] . 'contact con
		where
			lcu.contact_id = con.id and
			con.name = ' . to_sql($name) . ' and
			con.user_id = ' . (int)$login_user_id
	, 0);
}
function get_user_id_from_contact_id($id, $login_user_id) {
	global $config;
	if (!$id)
		return;
	if (!$login_user_id)
		return;
	$i1 = get_db_single_value('
			lcu.user_id
		from
			' . $config['mysql']['prefix'] . 'link_contact_user lcu,
			' . $config['mysql']['prefix'] . 'contact con
		where
			lcu.contact_id = con.id and
			con.id = ' . to_sql($id) . ' and
			con.user_id = ' . (int)$login_user_id
	, 0);
	return $i1;
}
function get_user_id_from_user_name($name) {
	global $config;
	if (!$name)
		return;
	$i1 = get_db_single_value('
			id
		from
			' . $config['mysql']['prefix'] . 'user
		where
			name = ' . to_sql($name)
	, 0);
	return $i1;
}
/*function get_friend_team_id_from_user_name($user_name) {
	if (!$user_name)
		return;
	$my_team_name = $config['reserved_prefix'] . '*' .  $_SESSION['login']['login_user_name'] . $config['reserved_suffix'];

	return get_db_single_value('
			id
		from
			' . $config['mysql']['prefix'] . 'team
		where
			name = ' . to_sql($my_team_name)
	, 0);
}*/
function get_friend_team_id_from_user_id($friend_user_id, $login_user_id) {
	if (!$friend_user_id)
		return;
	if (!$login_user_id)
		return;
	global $config;

	# todo we already have user_name in session so it shouldnt be neeeded to get it like this 2012-04-22 vaskoiii
	$login_user_name = get_db_single_value('
		name from
			' . $config['mysql']['prefix'] . 'user
		where
			id = ' . (int)$login_user_id
	, 0);

	$my_team_name = $config['reserved_prefix'] . '*' .  $login_user_name . $config['reserved_suffix'];

	return get_db_single_value('
			id
		from
			' . $config['mysql']['prefix'] . 'team
		where
			name = ' . to_sql($my_team_name)
	, 0);
}
function set_accept_friend_from_listing_and_container(& $listing, & $container, $login_user_id) {
	global $config;
	# warning this function will override whatever values you have!

	$b1 = 2;
	foreach ($container as $k1 => $v1) {
	if ($k1 == 'accept_friend') {
		$b1 = 1;
	} }

	if ($b1 == 1) {
	if (!$_SESSION['interpret']['failure'] == 1) {
		$friend_user_id = 0;
		if ($listing['contact_id']) { # cant use user_id here because that is your user id 2012-04-20 vaskoiii
			$friend_user_id = get_user_id_from_contact_id($listing['contact_id'], $login_user_id);
		}
		# 2 cases to make extra sure we get it
		if (!$friend_user_id)
			$friend_user_id = get_user_id_from_contact_name($container['contact_name'], $login_user_id);
		if (!$friend_user_id)
			$friend_user_id = get_user_id_from_user_name($container['user_name']);

		$my_team_id = get_friend_team_id_from_user_id($friend_user_id, $login_user_id);

		if ($friend_user_id) {
		if ($my_team_id) {
			$container['accept_friend'] = get_db_single_value('
					1
				from
					' . $config['mysql']['prefix'] . 'link_team_user
				where
					user_id = ' . (int)$friend_user_id . ' and
					team_id = ' . (int)$my_team_id . ' and
					active = 1
			', 0);
		} }
	} }
}


function is_homepage() {
	global $x;
	if ($x['name'] == '' || $x['name'] == 'main')
		return 1;
	else
		return 2;
}

function get_interpreted_variable($string) {
	switch($string) {
		case 'keyword':
			return $string;
		break;
		case 'tag_path':
		case 'parent_tag_path':
			if (str_match('_path', $string))
				return str_replace('_path', '_id', $string);
		break;
		default:
			if (str_match('_name', $string))
				return str_replace('_name', '_id', $string);
		
		break;
	}
	return $string;
}


function print_ts_focus($string, $load, & $x = null) {
	if (!$x)
		global $x;

	if ($x['preload']['focus'] == $load) { ?> 
		<a id="ts_focus" href="<?= str_replace('_edit', '_list', $x['.']) . ffm('id=&action_id=&action_name=&action_type=&preview%5B0%5D=&expand%5B0%5D=', 0); ?>"><?= to_html($string); ?></a><?
	}
	else
		echo to_html($string);
}

# potential functions to clean up view display 2012-04-12 vaskoiii
function get_mask_analogous($string) {
	switch($string) {
		case 'user_name':
		case 'source_user_name':
		case 'destination_user_name':
		case 'corresponding_user_name':
			return array(
				'user_name',
				'source_user_name',
				'destination_user_name',
				'corresponding_user_name',
			);
		break;
	}
	return array($string);
}

function get_list_minimal_structure($view_type, $list_type, $display, $part) {
	$minimal_structure = array();
	$view_structure = get_listing_template_structure($view_type, $display, $part);
	$listing_structure = get_listing_template_structure($list_type, $display, $part);
	$a1 = array(
		'.',
		'uid',
		'translate',
		'delete',
		'more_toggle',
	);
	# get_mask_analogous()
	$analogous = array();
	if (!empty($listing_structure))  {
	foreach ($listing_structure as $k1 => $v1) {
		if (in_array($v1, $a1)) {
			$minimal_structure[] = $v1;
		} else {
			$b1 = 2;
			$analogous = get_mask_analogous($v1);
			foreach ($analogous as $k2 => $v2) {
				if (in_array($v2, $view_structure)) {
					$b1 = 1;
				}
			}
			if ($b1 == 2) {
				$minimal_structure[] = $v1;
			}
		}
	} }
	return $minimal_structure;
}

# todo remove global $x;
function is_child_load($load, & $x = null) {
	if (!$x)
		global $x;
	if ($x['load'][$load]['name'] == 'list') {
	if ($x['load']['view']['type']) {
	if ($x['load']['view']['type'] == get_parent_listing_type($x['load']['list']['type'])) {
		return 1;
	} } }
	# may no longer be flexible enough as many child items arent necessarily children but still repeat information.
	return 0;
}

function get_motion_style_display() {
	global $x;
	if (is_array($x['preload']['expand']))
	if (in_array('motion', $x['preload']['expand']))
		return 'block';
	return 'none';
}

function get_action_style_display() {
	global $x;
	if (is_array($x['preload']['expand']))
	if (in_array('action', $x['preload']['expand']))
		return 'block';
	return 'none';
}


function get_immediate_tag_translation_name($tag_id, $dialect_id) {
	global $config;
	$name = get_db_single_value('
			name
		from
			' . $config['mysql']['prefix'] . 'translation
		where
			kind_id = 11 AND
			kind_name_id = ' . (int)$tag_id . ' AND
			dialect_id = ' . $dialect_id
	, 0);
	if (!$name) {
		$name = get_db_single_value('
				name
			from
				' . $config['mysql']['prefix'] . 'tag
			where
				id = ' . (int)$tag_id
		, 0);
		# probably better to leave off the unabstracted notation even if it isnt translated 2012-03-27 vaskoiii
	}
	return $name;
}

function ts_set_cookie($type, $name, $value, $time = false) {
	if (!$time)
		$time = time()+60*60*24*365; # time

	# intended http.conf configuration: 2012-05-22 vaskoiii
        # php_value session.cookie_secure 1
        # php_value session.cookie_httponly 1
	# this way even PHPSESSID is affected...
	# TS JS Launcher is not secure or httponly (which is ok)
	# check with:
	# $a1 = session_get_cookie_params(); echo '<pre>'; print_r($a1); echo '</pre>'; exit;

	if ($type) {
	if ($name) {
	if ($type) {
		setcookie(
			$type . '[' . $name . ']', 
			$value, 
			$time,
			'/'
		);
	} } }
}

function get_random_theme($prepend = '') {
	global $config;
	$sql = '
		select
			id,
			name
		from
			' . $config['mysql']['prefix'] . 'theme
		where
			`random` = 1
		order by
			rand()
		limit
			1
	';
	$result = mysql_query($sql) or die(mysql_error());
	while ($row = mysql_fetch_assoc($result)) {
		return array(
			$prepend . 'theme_id' => $row['id'],
			$prepend . 'theme_name' => $row['name'],
		);
	}
}

# now when printing the translations we have to explode the path again and print the translations ie) $e1[0] . '<>' . $e1[1] . '<>' . $e1[2];
function print_tag($string) {
	$a1 = array();
	$e1 = explode('<>', $string);
	$i1 = count($e1);
	# build differnt paths:
	# 1
	$i2 = 0;
	while ($i2 < $i1) {
		$a1[] =  $e1[$i2];
		$s1 = implode('<>', $a1);
		if ($translation['tag_name']['result'][$s1])
			echo tt('tag', $s1); # print level
		else
			echo to_html($e1[$i2]);
		$i2++;
		if ($i2 < $i1)
			echo to_html('<>');
	}
}

function get_page_title() {
	# a function because when the feed is displayed this gets computed again to create a link to add a feed such that the feed title will be the same as the previous title of the page.
	# todo store this as a variable somewhere so it only has to be computed 1 time. ie) $x['page']['title'] 2012-02-06 vaskoiii
	# todo this guy wont work anyway because it is based of the data container...
	global $config;
	global $data;
	global $x;

	$s1 = $config['title_prefix'];

	if ($x['name'] == '' || $x['name'] == 'main')
		$s1 .= get_translation('page', 'main');
	else
		$s1 .= get_translation('page', $x['name']);

	# todo if we want to display more information on the search we will have to rewrite the following
	/*
	if ($x['load']['view']['type'])
		$s1 .= ' | ' . get_translation('page', $x['load']['view']['type'] . '_view');
	if (get_gp('id'))
		$s1 .= ' | ' . (int)get_gp('id');


	if (!get_gp('id') && !empty($data['container']['search_content_box']['element'])) {
	foreach($data['container']['search_content_box']['element'] as $k1 => $v1) {
	if ($v1) {
		if (preg_match('/\_description/', $k1))
			;
		else switch($k1) {
			case 'user_name':
			case 'lock_user_name':
				// TODO: display corresponding contact_name in here such that it is consistent with the rest of the site.
				$s1 .= ' | <' . $v1 . '>';
			break;
			//case 'contact_name':
			//case 'lock_contact_name':
				// TODO: display only if corresponding username is not displayed
			//break;
			case 'contact_user_mixed':
			case 'lock_contact_user_mixed':
		
			case 'login_user_password':
			case 'login_user_password_unencrypted':
				// Don't show password in title;
			break;
			default:
				$s1 .= ' | ' . $v1; 
			break;
		}
	} } }
	*/
	return $s1;
}

# because php doesnt have it... =(
function str_match($pattern, $subject) {
	# we could get more fancy here but no need.
	return preg_match('/' . preg_quote($pattern, '/') . '/', $subject); 
}

# should be the line right above the header: location
# implemented in a function so that placement will be easier
function header_debug($location = false) {
	# globals ok here because this is debug only! 2012-02-28 vaskoiii
	global $config;
	if ($config['debug'] != 1)
		return;
	global $x;
	global $q;
	global $process;
	global $data;
	global $config;
	global $interpret;
	global $_SESSION;
	global $_COOKIE;
	global $translation;
	global $key;
	if ($location)
		echo '<a style="margin-bottom: -15px;" href="' . to_html($location) . '">' . to_html($location) . '</a>';
	else
		echo '<a style="margin-bottom: -15px;" href="javascript: back();">javascript: back();</a>';
	include('list/v1/inline/t1/debug.php');
	die('interruptig process intentionally for debug');
}

# default cookie time is 1 year. (31536000)
function refresh_cookie_array($cookie_array_name, $cookie_time = 31536000) {
	global $_COOKIE;
	if(isset($_COOKIE[$cookie_array_name])) {
	foreach($_COOKIE[$cookie_array_name] as $k1 => $v1) {
	if (isset($_COOKIE[$k1])) {
		setcookie(
			$k1, 
			$_COOKIE[$k1], 
			time()+$cookie_time, 
			'/'
		);
	} } }
}

function contact_user_mixed_combine(& $arrangement, $user_id, $contact_id = false, $login_user_id = false, $prepend = '') {
	global $config;
	if (!$login_user_id)
		$login_user_id = $_SESSION['login']['login_user_id'];
	if ($user_id) {
		$arrangement[$prepend . 'user_name'] = get_db_single_value('
				name
			from
				' . $config['mysql']['prefix'] . 'user
			where
				id = ' . (int)$user_id
		, 0);
		$arrangement[$prepend . 'contact_name'] = get_db_single_value('
				c.name
			from
				' . $config['mysql']['prefix'] . 'contact c,
				' . $config['mysql']['prefix'] . 'link_contact_user lcu
			where
				c.id = lcu.contact_id and
				lcu.user_id = ' . (int)$user_id . ' and
				c.user_id = ' . (int)$login_user_id
		, 0);
	}
	if (!$arrangement['contact_name'] && $contact_id) {
		#todo
	}
	if (!$arrangement['user_name'] && $contact_id) {
		#todo
	}
}

# parses contact_user_mixed and returns the 2 fields separated...
function contact_user_mixed_split($container, $lock = '', $keep_contact = false) {
	global $config;
	global $process;

	# get_gp() isset_gp()
	global $_SESSION;
	global $_GET;
	global $_POST;

	$base = & $process[$container];

	# Don't set stuff if it doesn't need!
	if (!isset($base[$lock . 'contact_user_mixed']))
		return;

	# Reset any bad data - todo we shouldn't receive these!
	$base[$lock . 'user_name'] = '';
	$base[$lock . 'contact_name'] = '';
	# Get the data we want
	$base[$lock . 'contact_user_mixed'] = get_gp($lock . 'contact_user_mixed');

	// Working:
	//$s = 'John Vasko <vask>';
	//$s = '<vask>';

	// Not Working:
	//$s = '<vask> John Vasko';

	// Needs Error Message
	// $s = 'Bad <vask> Input';
	// $s = '<>><><><<>>';
	// $s = '<ssssssss';
	// etc..
	
	if (isset_gp($lock . 'contact_user_mixed')) {
		$s = get_gp($lock . 'contact_user_mixed');
		preg_match('/\(.*\)/', $s, $su); # get value in unabstracted
		preg_match('/[^\(\)]*/', $s, $sc); # get value not in <>

		$base[$lock . 'user_name'] = preg_replace(array('/\(/', '/\)/'), array('', ''), $su[0]);
		$base[$lock . 'contact_name'] = rtrim($sc[0]);

		if ($base[$lock . 'contact_name'] && !$base[$lock . 'user_name']) {
			$base[$lock . 'user_name'] = get_db_single_value('
					u.name
				FROM
					' . $config['mysql']['prefix'] . 'user u,
					' . $config['mysql']['prefix'] . 'contact c,
					' . $config['mysql']['prefix'] . 'link_contact_user lcu
				WHERE
					u.id = lcu.user_id AND
					c.id = lcu.contact_id AND
					c.name = ' . to_sql($base[$lock . 'contact_name']) . ' AND
					c.user_id = ' . (int)$_SESSION['login']['login_user_id'] . ' AND
					c.active = 1
			');
		}
	}
	# don't let contact name be superflous in the URL
	# todo do this somewhere else. it will be confusing here.
	if (!$keep_contact)
	if ($base[$lock . 'user_name'])
		unset($base[$lock . 'contact_name']);
}

function up_date($date) {
	# only happens on documentation 2012-02-27 vaskoiii
	// TODO: can we get the modification $date from the file instead?
	// http://php.net/manual/en/function.filemtime.php
	// However: If you just make one spelling correction it will be like the document has been updated!

	// This function is mostly for programmer convenience when editing the [doc]s... all the updates will happen automatically.
	// Eventually the goal is to have documentation set up in a wiki...
	global $config;
	global $x;
	if (get_db_single_value('
			page_id
		FROM
			' . $config['mysql']['prefix'] . 'doc
		WHERE
			page_id = ' . (int)$x['page']['id'] . ' AND
			modified < ' . to_sql($date)
			, $debug = false)) {
		$sql = '
			UPDATE
				' . $config['mysql']['prefix'] . 'doc
			SET
				modified = ' . to_sql($date) . '
			WHERE
				page_id = ' . (int)$x['page']['id'] . '
			LIMIT
				1
		';
		$result = mysql_query($sql) or die(mysql_error());
	}
}

function get_random_string() {
	// parts are hardcoded here but this function is sooo simple... who cares...
	$chars = "abcdefghijklmnopqrstuvwxyz0123456789";
	srand((double)microtime()*1000000);
	$i = 0;
	$pass = '' ;
	while ($i <= 7) {
		$num = rand() % 35;
		$tmp = substr($chars, $num, 1);
		$pass = $pass . $tmp;
		$i++;
	}
	return $pass;
}

function print_message_bar() {
	global $config;
	global $x;
	global $_SESSION;

	# alias
	$interpret = & $_SESSION['interpret'];
	
	// We may want to default to hiding a success message. We can do that here later...
	if (isset($interpret['message'])) { ?> 
		<div class="message"><?
			# todo should be a switch ehhh. holding the form return
			if ($interpret['notice']) { ?> 
				<p class="notice"><?
			}
			elseif ($interpret['success']) { ?> 
				<p class="success"><?
			}
			else { ?> 
				<p class="failure"><? 
			} ?> 
				<?= to_html($interpret['message']); ?> 
			</p>
		</div><?
		unset($interpret['message']);
	}
}

function a_link_match($t) {
	$match_amount = 0;
	$t = str_replace("<", "&lt;", $t);
	$t = str_replace(">", "&gt;", $t);
	$t = ' ' . $t . ' ';
	$t = preg_replace('/ /', '  ', $t); # needed!
	$p1 = '([^ :]{1,})'; # works
	$p2 = '([^ ]{1,})'; # works
	$match_array1 = array();
	$match_amount = preg_match_all('/ ' . $p1 . '\:' . $p2 .' /', $t, $match_array1);
	return $match_amount;
}

function a_link_replace($t) {
	// All in one shot!
	//$t = preg_replace('/([a-z]+:[^:\s]+)(?!:)/i', '<a href="$1">$1</a>', $t);
	//return $t;
	$t = str_replace("<", "&lt;", $t);
	$t = str_replace(">", "&gt;", $t);
	$t = ' ' . $t . ' '; # needed!
	$t = preg_replace('/ /', '  ', $t); # needed!
	# Most generic check
	$p1 = '([^ :]{1,})';
	# If you would like to make the first character special:
	# $p1 = '([^ :0-9]{1,1}[^ :]{0,})';
	# If you would like to specify only allowed protocols:
	# $p1 = '(ftp|http|https|mailto|skype|)';
	$p2 = '([^ ]{1,})';
	# () field in parenthesis go to $1, $2, etc...
	# [] allowed characters
	# ^ negation
	# {min,max) character length requirement
	$t = preg_replace('/ ' . $p1 . '\:' . $p2 .' /', ' <a href="$1:$2" >$1:$2</a> ', $t);
	# Clean Up
	$t = preg_replace('/  /', ' ', $t);
	$t = ltrim($t, ' ');
	$t = rtrim($t, ' ');
	return $t;
}

# Consider http_build_query() instead 2012-02-07 vaskoiii
function get_lock_query($extra_query = '') {
	global $_SESSION;

	$lock_query = array();
	# add stuff to the array for merging
	if (!empty($extra_query))
		$lock_query[] = $extra_query;
	if (!empty($_SESSION['lock']) && $_SESSION['feature']['feature_lock'] == 1) {
	foreach($_SESSION['lock'] as $k1 => $v1) {
	switch($k1) {
		case 'lock_user_id':
		case 'lock_contact_id':
		case 'lock_group_id':
		case 'lock_team_id':
		case 'lock_location_id':
		case 'lock_range_id':
			$lock_query[] = $k1 . '=' . $v1;
		break;
	} } }
	if (count($lock_query))
		return('?' . implode('&', $lock_query));
	else
		return false;
}

function print_paging($current_page, $result_amount_total, $result_amount_per_page) {
	global $x;
	global $_SESSION;


	if (!$current_page)
		$current_page = 1; ?> 

	<ul><?
		$last_page = floor($result_amount_total / $result_amount_per_page) + 1;

		if ($result_amount_total % $result_amount_per_page == 0)
			$last_page--;

		if ($current_page && $current_page != 1) { ?> 
			<li><a href="<?= ffm('page=1&expand%5B0%5D=', 0); ?>">|&lt;&lt;</a></li>
			<li><a href="<?= ffm('page=' . (int)($current_page - 1) . '&expand%5B0%5D=', 0); ?>">&lt;&lt;</a></li><?
		}
		else { ?> 
			<li><span class="spacer">|&lt;&lt;</span></li> 
			<li><span class="spacer">&lt;&lt;</span></li> <?
		} ?> 

		<li><span id="current_page"><?= (int)($current_page); ?></span></li><?

		if ($current_page < $last_page && $last_page > 0) { ?> 
			<li><a href="<?= ffm('page=' . (int)($current_page + 1) . '&expand%5B0%5D=', 0); ?>">&gt;&gt;</a></li>
			<li><a href="<?= ffm('page=' . (int)$last_page . '&expand%5B0%5D=', 0); ?>">&gt;&gt;|</a></li><?
		} 
		else { ?> 
			<li><span class="spacer">&gt;&gt;</span></li>
			<li><span class="spacer">&gt;&gt;|</span></li><?
		} ?> 
	</ul><?
}
