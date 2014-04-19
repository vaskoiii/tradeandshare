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

# Contents/Description: Main page of the site should have the greatest importance! Also compensates for a page name of [] or [main] or other potential aliases!

header('HTTP/1.0 200 Found');

switch ($x['name']) {
	case 'file':
		filer_read((int)$_GET['id']);
		exit;
	break;
	case 'login_unset_process':
		header('location: /unset_process/?unset=login');
		exit;
	break;
	case 'manager':
		$s1 = '';
		if (get_lock_query())
			$s1 = '&' . get_lock_query();
		#header('location: /user_view/?list_name=report&list_type=top&lock_user_id=' . (int)$_SESSION['login']['login_user_id']);
		#header('location: /user_view/?list_name=list&list_type=item&lock_user_id=' . (int)$_SESSION['login']['login_user_id']);
		header('location: /user_view/?list_name=list&list_type=item&lock_user_id=' . (int)$_SESSION['login']['login_user_id']);
		exit;
	break;
	case 'main':
	default:
		# top report dirties the home page too much... and its not ideal for a phone screen.
		include($x['site']['i'] . '/inline/head.php');


		# the fast and quick box so it can get translations
		include($x['site']['i'] . 'inline/edit.php');
		include($x['site']['i'] . 'layer/fast.php');
		#include($x['site']['i'] . 'layer/quick.php');


		include($x['site']['i'] . '/page/main.php');
		#if ($_SESSION['login']['login_user_id'])
		#	include($x['site']['i'] . '/page/top_report.php');
		include($x['site']['i'] . '/inline/footer.php');

		include($x['site']['i'] . '/inline/' . $x['site']['t'] . '/head.php');

		# todo make it so not sqeezed in main.php
		# include($x['site']['i'] . '/layer/' . $x['site']['t'] . '/fast.php');
		# include($x['site']['i'] . '/layer/' . $x['site']['t'] . '/quick.php');


		include($x['site']['i'] . '/page/' . $x['site']['t'] . '/main.php');

		#if ($_SESSION['login']['login_user_id'])
		#	include($x['site']['i'] . '/page/' . $x['site']['t'] . '/top_report.php');
		include($x['site']['i'] . '/inline/' . $x['site']['t'] . '/footer.php');
	break;
}
