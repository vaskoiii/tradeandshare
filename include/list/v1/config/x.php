<?
# description: load the appropriate pages and loads the 404 page if necessary.

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
		f.path = ' . to_sql('list/v1/x/') . ' AND
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
			include('list/v1/x/' . $x['page']['file_name']);
			exit;
		}
		else {
			# todo support other languages in error message feed. 2012-06-10 vaskoiii
			$_SESSION['dialect']['dialect_id'] = '2'; # English
			header('HTTP/1.0 200 Found');
			header('Content-type: text/xml');
			header('Content-Type: application/xml; charset=ISO-8859-1'); 
			include('list/v1/xml/t1/denied_atom.php');
			exit;
		}
	break;

	# Normal $_SESSION
	default:
		session_start();
		# ONE TIME SCRIPTS
		if (0 && $_SESSION['login']['login_user_name'] == '|root|') {
			# require('list/v1/script/1time_do_it.php');
			exit;
		}
		# REUSABLE SCRIPTS
		if (0 && $_SESSION['login']['login_user_name'] == '|root|') {
			# include('list/v1/script/php_reusable/rebuild_index_tag.php');
			# include('list/v1/script/php_reusable/rebuild_team_everybody.php');
			# include('list/v1/script/php_reusable/rebuild_index_entry_lock.php');
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
			# uncomment for default themes
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
				include('list/v1/inline/head.php');
				include('list/v1/inline/header.php');
				include('list/v1/page/login_set.php'); # standalone
				include('list/v1/inline/footer.php');
				include('list/v1/inline/t1/head.php');
				include('list/v1/inline/t1/header.php');
				include('list/v1/page/t1/login_set.php'); # standalone
				include('list/v1/inline/t1/footer.php');
				exit;
			break;
			case 'login_set_process':
				header('HTTP/1.0 200 Found');
				include('list/v1/process/login_set_process.php');
				# login_set_process.php doesnt header anywhere so it is done below with process_failure/success()
				if ($interpret['message']) {
					foreach($process as $k1 => $v1)
						$_SESSION['process']['action_content_1'][$k1] = $v1;
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
			include('list/v1/process/login_set_process.php');
		}
		# HEADER IF AUTOLOGIN FAILS
		if (!$_SESSION['login']['login_user_id'] && $x['page']['login'] == 1) {

			$_SESSION['process']['action_content_1'] = get_action_content_1('login', 'set'); 
			foreach ($_SESSION['process']['action_content_1'] as $k1 => $v1) {
				$_SESSION['process']['action_content_1'][$k1] = get_gp($k1);
			}
			# todo build a redirect that will forward submitted data GET/POST to the correct place ie) http_build_query();
			if (str_match('index.php', $_SERVER['REQUEST_URI'])) # if a process page go the last known page
				$_SESSION['process']['action_content_1']['login_request_uri'] = $x['..'] . get_q_query($x['level']); 
			else
				$_SESSION['process']['action_content_1']['login_request_uri'] = $_SERVER['REQUEST_URI'];
			header_debug($x['.'] . 'login_set/');
			header('location: ' . $x['.'] . 'login_set/');
			exit;
		}
		elseif (isset($x['page']['file_name'])) {
			include('list/v1/x/' . $x['page']['file_name']);
			exit;
		}
		else {
			$x['page']['display_name'] = '404';
			$x['page']['id'] = '68';
			include('list/v1/x/404.php');
			exit;
		}
	break;
}
