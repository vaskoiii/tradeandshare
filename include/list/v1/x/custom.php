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

	# x/page.php may be a better choice for custom pages
	# instead of
	# x/custom.php
	# ie) for:
		# adder_report
		# config_report
		# contact_report
		# new_report
		# search_report
		# singularity_report
		# top_report
		# cycle_report

	case 'main':
	default: # in case the url is messed up it will still bring you home!
		# testing out more engagement for end users
		if (!empty($_SESSION['login']['login_user_id'])) {
			header('location: /top_report/');
			exit;
		}
		# how are fast and header included in main? should be uncommented here? 2014-09-01 vaskoiii
		include('list/v1/inline/head.php');
		include('list/v1/inline/edit.php');
		# include('list/v1/layer/fast.php');
		# include('list/v1/layer/quick.php');
		# include('list/v1/inline/header.php');
		include('list/v1/page/main.php');
		include('list/v1/inline/footer.php');

		include('list/v1/inline/t1/head.php');
		# include('list/v1/layer/t1/fast.php');
		# include('list/v1/layer/t1/quick.php');
		# include('list/v1/inline/t1/header.php');
		include('list/v1/page/t1/main.php');
		include('list/v1/inline/t1/footer.php');
	break;
}
