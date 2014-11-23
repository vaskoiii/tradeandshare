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

# description: custom code based on the timeline for modifying a channel
# issue:
# - really rough code!
# - have to deal with dynamically changing cycles
# - have to accomodate for automated daily script

# cycle length
# - min = 1 day?
# - max = 1 year? ( probably people wont use )
# - target = 30 days

# plan
# get the variable cycle data first
# future
# current
# previous
# then modifying renewals will be possible

# included from edit process
/*
$process = array(); # Send to process
$interpret = array(); # Interpreted from $process
$interpret['lookup'] = array();

$process['form_info'] = get_action_header_1();
foreach($process['form_info'] as $k1 => $v1)
	$process['form_info'][$k1] = get_gp($k1);

$process['action_content_1']  = get_action_content_1($process['form_info']['type'], 'edit');
$process['action_content_2']  = get_action_content_2($process['form_info']['type'], 'edit');

# shortcuts
$id = & $process['form_info']['id'];
$lookup = & $interpret['lookup'];
$action_content_1 = & $process['action_content_1'];
$action_content_2 = & $process['action_content_2'];
$prefix = & $config['mysql']['prefix'];
$type = & $process['form_info']['type'];
$message = & $interpret['message'];
$login_user_id = $_SESSION['login']['login_user_id'];
*/


# SET RESPONSE
if ($process)
foreach ($process as $k1 => $v1)
if ($v1)
foreach ($v1 as $k2 => $v2)
switch ($k2) {
	default:
		if (str_match('_description', $k2))
			$process[$k1][$k2] = trimmage(get_gp($k2));
		else
			$process[$k1][$k2] = get_gp($k2);
	break;
}

# lookup
process_field_missing('action_content_1');
process_data_translation('action_content_1');

# if does not exist do not allow adding for now
if (empty($lookup['channel_id'])) {
	$message = tt('element', 'does_not_exist') . ' - can not add new channels until logic is complete';
	# todo allow adding new channels
}

if (!$message) {
	# todo force a log when updating items
}

if (empty($message))
	$message = 'placeholder logic only - not allowed to modify channels until they can be setup in a log';
process_failure($message);
exit;
