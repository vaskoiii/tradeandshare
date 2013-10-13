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

# currently only 1 warp but may need to add more in the futre.

$process = array(); # Send to process
$interpret = array(); # Interpreted from $process
$interpret['lookup'] = array();
$interpret['message'] = array();

$process['form_info'] = get_action_header_1();

foreach($process['form_info'] as $k1 => $v1)
	$process['form_info'][$k1] = get_gp($k1);

$process['action_content_1'] = array(
	'contact_user_mixed' => '',
);
contact_user_mixed_split('action_content_1', '', 1);

# shortcut
$lookup = & $interpret['lookup'];
$action_content_1 = & $process['action_content_1'];
$message = & $interpret['message'];

# translation
process_data_translation('action_content_1');

# error
# process_field_missing('action_content_1');
# process_does_not_exist('action_content_1');
# process_does_exist('action_content_1');
if (!$lookup['user_id']) {
if (!$lookup['contact_id']) {
	$message = tt('element', 'contact_user_mixed') . ' : ' . tt('element', 'error_does_not_exist');
} }

process_failure($message);

# success
# same priority as clicking on a contact (user)
$s1 = '';
if ($lookup['contact_id'] && $lookup['user_id'])
	$s1 = '/contact_view/?lock_user_id=' . (int)$lookup['user_id'] . '&list_name=list&list_type=note';
elseif ($lookup['contact_id'])
	$s1 = '/contact_view/?lock_contact_id=' . (int)$lookup['contact_id'] . '&list_name=list&list_type=note';
elseif ($lookup['user_id'])
	$s1 = '/user_view/?lock_user_id=' . (int)$lookup['user_id'] . '&list_name=list&list_type=metail';
# forward to the correct page
process_success('', $s1);
exit;
