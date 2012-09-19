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

# Contents/Description: Loads the appropriate pages and loads the 404 page if necessary.

# At this point we should know what version version we are dealing with
# See ($x['site']['i'] . 'inline/' . $x['site']['t'] . 'debug.php') for GLOBAL variables
# SCRIPTS can be run from after session_start (do a keyword search for this file you will see it):

if ($config['debug'] != 1)
	error_reporting(0); # Turn off all errors (2012-01-29 Untested)
else
	error_reporting (E_ALL ^ E_NOTICE); # turn off crazy amount of errors for now

# todo custom die() function is needed or a way to disable additional error output on the live system 2012-03-26 vaskoiii

# todo request is unused so dump it 2012-03-27 vaskoiii
unset($_REQUEST);

set_x(get_x());
set_q(get_q());

# PAGE
$sql = '
	SELECT
		p.name,
		p.login,
		f.name as file_name,
		p.id,
		p.parent_id,
		p.monitor
	FROM
		' . $config['mysql']['prefix'] . 'file f,
		' . $config['mysql']['prefix'] . 'page p
	WHERE
		f.id = p.file_id AND
		f.path = ' . to_sql($x['site']['i'] . 'x/') . ' AND
		p.name = ' . to_sql($x['name']) . '
	LIMIT
		1
';
$result = mysql_query($sql) or die(mysql_error());
if ($row = mysql_fetch_assoc($result))
	$x['page'] =  $row;



switch ($x['page']['name']) {
	# Dangerous if session_start() ever runs for these pages.
	# http://us3.php.net/manual/en/function.session-id.php
	case 'feed_atom':
		if (session_id())
			die('$_SESSION can not be set here!');
		# Alternative Authentication w/o session
		$x['feed_atom'] = array();
		$sql = '
			SELECT
				p.name as page_name,
				f.id,
				f.user_id,
				u.name as user_name,
				f.dialect_id,
				f.page_id,
				f.`key`,
				f.name,
				f.query,
				f.modified,
				f.active
			FROM
				' . $config['mysql']['prefix'] . 'feed f,
				' . $config['mysql']['prefix'] . 'page p,
				' . $config['mysql']['prefix'] . 'user u 
			WHERE
				f.user_id = u.id AND
				f.page_id = p.id AND
				f.id = ' . (int)get_gp('set_feed_id') . ' AND
				f.`key` =  ' . to_sql(get_gp('set_feed_key')) . ' AND
				f.active = 1
			LIMIT
				1
		';
		$result = mysql_query($sql) or die(mysql_error());
		while($row = mysql_fetch_assoc($result))
			$x['feed_atom'] = $row;
		if (!empty($x['feed_atom'])) {
			$x['feed_atom']['part'] = explode('_', $x['feed_atom']['page_name']);
			# todo alternative trick: use $sudo 2012-02-15 vaskoiii
			# trick here is that $_SESSION is just a regular variable because we never did session_start() so it is cleared after the page is loaded
			$_SESSION['login']['login_user_id'] = $x['feed_atom']['user_id'];
			$_SESSION['login']['login_user_name'] = $x['feed_atom']['user_name'];
			$_SESSION['dialect']['dialect_id'] = $x['feed_atom']['dialect_id'];
			parse_str($_SERVER['REDIRECT_QUERY_STRING'] . ($x['feed_atom']['query'] ? '&' . $x['feed_atom']['query'] : ''), $_GET);
			include($x['site']['i'] . '/x/' . $x['page']['file_name']);
			exit;
		}
		else {
			# todo support other languages in error message feed. 2012-06-10 vaskoiii
			$_SESSION['dialect']['dialect_id'] = '2'; # English
			header('HTTP/1.0 200 Found');
			header('Content-type: text/xml');
			header('Content-Type: application/xml; charset=ISO-8859-1'); 
			include($x['site']['i'] . '/xml/' . $x['site']['t'] . '/denied_atom.php');
			exit;
		}
	break;

	# Normal $_SESSION
	default:
		session_start();
		# ONE TIME SCRIPTS
		if (0 && $_SESSION['login']['login_user_name'] == '|root|') {
			# require('v/1/script/1time_do_it.php');
			exit;
		}
		# REUSABLE SCRIPTS
		if (0 && $_SESSION['login']['login_user_name'] == '|root|') {
			# include('v/1/script/php_reusable/rebuild_index_tag.php');
			# include('v/1/script/php_reusable/rebuild_team_everybody.php');
			# include('v/1/script/php_reusable/rebuild_index_entry_lock.php');
			exit;
		}
		# Set on login
		if (!isset($_SESSION['lock']))
			$_SESSION['lock'] = array(
			);
		if (!isset($_SESSION['feature']))
			$_SESSION['feature'] =  array(
				'feature_lock' => '2'
				# 'feature_minnotify' not needed in $_SESSION
			);
		if (isset($_SESSION['load'])) { ; }
		elseif(isset($_COOKIE['load'])) {
			$_SESSION['load'] = $_COOKIE['load']; 
			refresh_cookie_array('load');
		}
		else
			$_SESSION['load'] =  array(
				'load_javascript' => '1',
			);

		# unset($_SESSION['theme']); unset($_COOKIE['theme']);
		# todo function to get individual variables instead of testing a single variable as the whole 2012-04-26 vaskoiii
		# goes for: theme, dialect, display,
		# can we use load_set()?
		if (isset($_SESSION['theme']['background_theme_id'])) { ; }
		elseif(isset($_COOKIE['theme']['background_theme_id'])) {
			$_SESSION['theme'] = $_COOKIE['theme']; 
			refresh_cookie_array('theme');
		}
		else {
			$_SESSION['theme'] = array_merge(
				get_random_theme(),
				get_random_theme('background_'),
				get_random_theme('launcher_')
			);
			# simplifyable 2012-03-24 vaskoiii
			ts_set_cookie('theme', 'theme_name', $_SESSION['theme']['theme_name']);
			ts_set_cookie('theme', 'theme_id', $_SESSION['theme']['theme_id']);
			ts_set_cookie('theme', 'background_theme_name', $_SESSION['theme']['background_theme_name']);
			ts_set_cookie('theme', 'background_theme_id', $_SESSION['theme']['background_theme_id']);
			ts_set_cookie('theme', 'launcher_theme_name', $_SESSION['theme']['launcher_theme_name']);
			ts_set_cookie('theme', 'launcher_theme_id', $_SESSION['theme']['launcher_theme_id']);
			# uncomment for default themes:w
			#$_SESSION['theme'] =  array(
			#	'theme_name' => 'theme_purple',
			#	'theme_id' => '6',
			#);
		}
		if (isset($_SESSION['display']['display_id'])) { ; } // (ideally auto-detect!)
		elseif(isset($_COOKIE['display']['display_id'])) { 
			$_SESSION['display'] = $_COOKIE['display']; 
			refresh_cookie_array('display');
		}
		else
			$_SESSION['display'] =  array(
				'display_name' => 'display_select_default',
				'display_id' => '7',
			);
		if (isset($_SESSION['dialect']['dialect_id'])) { ; }
		elseif(isset($_COOKIE['dialect']['dialect_id'])) { 
			$_SESSION['dialect'] = $_COOKIE['dialect']; 
			refresh_cookie_array('dialect');
		}
		else
			$_SESSION['dialect'] = array(
				'dialect_name' => 'English',
				'dialect_code' => 'en', # as specified in DB
				'dialect_id' => '2'
			);
		# LOGIN 
		switch ($x['page']['name']) {
			case 'login_set':
				header('HTTP/1.0 200 Found');
				include($x['site']['i'] . '/inline/head.php');
				include($x['site']['i'] . '/inline/header.php');
				include($x['site']['i'] . '/page/login_set.php'); # standalone
				include($x['site']['i'] . '/inline/footer.php');
				include($x['site']['i'] . '/inline/' . $x['site']['t'] . '/head.php');
				include($x['site']['i'] . '/inline/' . $x['site']['t'] . '/header.php');
				include($x['site']['i'] . '/page/' . $x['site']['t'] . '/login_set.php'); # standalone
				include($x['site']['i'] . '/inline/' . $x['site']['t'] . '/footer.php');
				exit;
			break;
			case 'login_set_process':
				header('HTTP/1.0 200 Found');
				include('v/1/process/login_set_process.php');
				# login_process doesn't header anywhere...
				if ($interpret['message']) {
					# todo we really need to get the data structure for action_content_1
					foreach($process as $k1 => $v1) {
						$_SESSION['process'][$k1] = $v1;
					}
					process_failure($interpret['message']);
				}
				elseif (get_gp('login_request_uri'))
					process_success(tt('element', 'transaction_complete'), get_gp('login_request_uri'));
				else
					process_success(tt('element', 'transaction_complete'), $config['start_page'] . get_lock_query());
				exit;
			break;
		}

		# TRY TO AUTOLOGIN
		if (!$_SESSION['login']['login_user_id']) {
			include('v/1/process/login_set_process.php');
		}
		# HEADER IF AUTOLOGIN FAILS
		if (!$_SESSION['login']['login_user_id'] && $x['page']['login'] == 1) {
			# not sure if the get and post are forwarded correctly acter partitioning out the content_1 and content_2 etc... 2012-03-19 vaskoiii
			foreach($_POST as $k1 => $v1)
				$_SESSION['process'][$k1] = $v1;
			foreach($_GET as $k1 => $v1)
				$_SESSION['process'][$k1] = $v1;
			# _process PAGE REQUESTED ( Doesnt happen normally but untested sending a query sting to a process file)
			if (str_match('index.php', $_SERVER['REQUEST_URI']))
				$_SESSION['process']['login_request_uri'] = $x['.'] . get_q_query($x['level']); 
			# NON_process PAGE REQUESTED
			else
				$_SESSION['process']['login_request_uri'] = $_SERVER['REQUEST_URI'];
			header_debug($x['.'] . 'login_set/');
			header('location: ' . $x['.'] . 'login_set/');
			exit;
		}
		elseif (isset($x['page']['file_name'])) {
			include('v/1/x/' . $x['page']['file_name']);
			exit;
		}
		else {
			$x['page']['display_name'] = '404';
			$x['page']['id'] = '68';
			include($x['site']['i'] . '/x/404.php');
			exit;
		}
	break;
}
