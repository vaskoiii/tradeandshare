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

# Contents/Description: Redirect submitted results to the appropriate page
# Custom forwarding pages may account for locking and other factors | Not just a straigh search like in search_process.php

$process = array(); # Send to process
$interpret = array(); # Interpreted from $process
$interpret['lookup'] = array();
$interpret['message'] = array();

$process['form_info'] = get_action_header_1();

foreach($process['form_info'] as $k1 => $v1)
	$process['form_info'][$k1] = get_gp($k1);

# Guide for form processing see: arrangement.php
/*
[form_info] => Array (
    [x] => /item_list/search_process/
    [q] => 
    [load] => 
    [type] => item
    [id] => 
)
# custom variables not integrated into arrangement.php
[search_miscellaneous] => Array (
        [keyword] => 
        [asearch_on] => 
        [child] => 
        [referrer] => 
        [item_uid] => 
)
[action_content_1] => Array (
    [status_name] => 
    [parent_tag_path] => 
    [team_required_name] => 
)
*/

# todo use this instead of hardcoding in the switch below
# $process['action_content_1'] = get_action_content_1($process['form_info']['type']);
switch ($process['form_info']['type']) {
	case 'page':
		$process['action_content_1'] = array(
			'page_name' => get_gp('page_name'),
			# todo allow page translation as page_name is almost irrelevant in different form submissions
		);
	break;
	case 'contact':
	case 'people': # todo should be contact_portal not people_portal
		$process['action_content_1'] = array(
			'contact_user_mixed' => get_gp('contact_user_mixed'),
			'contact_name' => '',
			'user_name' => '',
		);
	break;
}

if ($process['action_content_1']) {
foreach($process['action_content_1'] as $k1 => $v1) {
	$process['action_content_1'][$k1] = get_gp($k1);
} }

switch($process['form_info']['type']) {
	case 'page':
	break;
	case 'contact':
	case 'people':
		contact_user_mixed_split('action_content_1', '', 1);
	break;
}

# shortcut
$lookup = & $interpret['lookup'];
$action_content_1 = & $process['action_content_1'];
$message = & $interpret['message'];

# translation
process_data_translation('action_content_1');

# error
process_field_missing('action_content_1');
# process_does_not_exist('action_content_1');
# process_does_exist('action_content_1');

if (!$message) {
switch($process['form_info']['type']) {
	case 'page':
	break;
	case 'contact':
	case 'people':
		if (!$lookup['user_id']) {
		if (!$lookup['contact_id']) {
			$message = tt('element', 'contact_user_mixed') . ' : ' . tt('element', 'error_does_not_exist');
		} }
	break;
} }

process_failure($message);

# success
$s1 = '';
switch($process['form_info']['type']) {
	case 'page':
		$s1 = '/' . $process['action_content_1']['page_name'] . '/';
	break;
	case 'contact':
	case 'people':
		# same priority as clicking on a contact (user)
		if ($lookup['contact_id'] && $lookup['user_id'])
			$s1 = '/contact_view/?lock_user_id=' . (int)$lookup['user_id'] . '&list_name=list&list_type=note';
		elseif ($lookup['contact_id'])
			$s1 = '/contact_view/?lock_contact_id=' . (int)$lookup['contact_id'] . '&list_name=list&list_type=note';
		elseif ($lookup['user_id'])
			$s1 = '/user_view/?lock_user_id=' . (int)$lookup['user_id'] . '&list_name=list&list_type=metail';
	break;
}

# forward to the correct page
process_success('', $s1);
exit;
