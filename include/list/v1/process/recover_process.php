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

# Contents/Description: Email user with recover info
# Known Issues: Potentially you could spam a person if you know their user_name with login_recover

$process = array(); # Sent to process
$interpret = array(); # Interpreted from $process
$interpret['lookup'] = array();

$process['form_info'] = get_action_header_1();
foreach($process['form_info'] as $k1 => $v1)
	$process['form_info'][$k1] = get_gp($k1);

switch($process['form_info']['type']) {
	case 'login':
		$process['action_content_1']['login_user_name'] = get_gp('login_user_name');
	break;
	default:
	case 'feed':
		#process['id'] = get_gp('id'); // needed because the way process_failure() works
	break;
}

switch($process['form_info']['type']) {
	case 'login':
		process_data_translation('action_content_1');
		# sets $interpret['login_user_id']
		# its confusing because case 'feed_recover_process' gets the login_user_id elsewhere 

		process_field_missing('action_content_1');
		process_does_not_exist('action_content_1');
		process_does_exist('action_content_1');

		# todo: integrated with process_field_missing();
		if (!$interpret['message'])
		switch($process['form_info']['type']) {
			case 'login':
				if (!$interpret['lookup']['login_user_id']) {
					$interpret['message'] = tt('element', 'login_user_name') . $config['spacer'] . tt('element', 'error_field_missing');
				}
			break;
		}
		process_failure($interpret['message']);
	break;
	case 'feed':
		$interpret['lookup']['feed_id'] = get_db_single_value('
				id
			FROM
				' . $config['mysql']['prefix'] . 'feed f
			WHERE
				id = ' . (int)$process['form_info']['id'] . ' AND
				user_id = ' . (int)$_SESSION['login']['login_user_id'] . ' and
				`active` = 1
		');
		if (!$interpret['lookup']['feed_id'])
			$interpret['message'] = tt('element', 'error') . $config['spacer'] . tt('element', 'access_denied');

		# shouldnt happen wo hacking
		process_failure($interpret['message']);
	break;
}

switch($process['form_info']['type']) {
	case 'feed':
		$sql = 'SELECT
				f.id,
				f.`key`,
				f.user_id AS login_user_id,
				p.name as page_name
			FROM
				' . $config['mysql']['prefix'] . 'feed f,
				' . $config['mysql']['prefix'] . 'page p
			WHERE
				f.page_id = p.id AND
				f.id = ' . (int)$interpret['lookup']['feed_id'] . ' AND
				f.user_id = ' . (int)$_SESSION['login']['login_user_id'] . '
			LIMIT
				1
		';
		$result = mysql_query($sql) or die(mysql_error());
		while ($row = mysql_fetch_assoc($result)) {
			$interpret['lookup']['feed_id'] = $row['id'];
			$interpret['lookup']['feed_key'] = $row['key'];
			$interpret['lookup']['login_user_id'] = $row['login_user_id'];
			$interpret['lookup']['page_name'] = $row['page_name'];
		}
	break;
}

# Get EMAIL INFO from user_id
$result = array();
$sql = '
	SELECT
		id AS user_id,
		name AS user_name,
		password AS user_password,
		email AS user_email
	FROM
		' . $config['mysql']['prefix'] . 'user
	WHERE
		id = ' . (int)$interpret['lookup']['login_user_id'] . '
	LIMIT
		1
';
$result = mysql_query($sql) or die(mysql_error());
if (mysql_num_rows($result) > 0)
	$row = mysql_fetch_assoc($result);

# EMAIL (Do Minnotify Email Only)
# these are similar to the invite
# todo detect language (based on sender dialect) and send a pretty email accordingly.
$email_to = $row['user_email'];
$email_subject = $config['title_prefix'] . ucfirst($process['form_info']['type']) . ' Recover Link';
switch ($process['form_info']['type']) {
	case 'feed':
		$email_subject .= ': ' . ucfirst(str_replace('_list', '', $interpret['lookup']['page_name'])) . '';
	break;
	case 'login':
		$email_subject .= ': ' . $row['user_name'] . ''; 
	break;
}
$email_body = '';
switch($process['form_info']['type']) {
	case 'login':
		$email_body = 'https://' . $_SERVER['HTTP_HOST'] . '/login_set_process/?' 
			. 'login_user_name=' . to_url($row['user_name']) 
			. '&login_user_password=' . to_url($row['user_password']) 
			. '&login_request_uri=' . to_url('/profile_edit/')
		;
	break;
	case 'feed':
		$email_body = 'https://' . $_SERVER['HTTP_HOST'] . '/feed_atom/?'
			. 'set_feed_id=' . (int)$interpret['lookup']['feed_id']
			. '&set_feed_key=' . to_url($interpret['lookup']['feed_key'])
		;
	break;
}

$email_sent = mail(
	$email_to,
	$email_subject,
	$email_body,
	get_tsmail_header()
);

process_success(
	tt('element', 'transaction_complete') . (
	$email_sent
		? ' : ' . tt('element', 'email_sent')
		: ''
	) . (
	$auto_enabled == 1 
		?  ' : ' . tt('element', 'auto_enabled')
		: ''
	)
);
