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
	case 'scan':
		$process['action_content_1'] = array(
			'public_key' => get_gp('public_key'),
		);
	break;
	case 'go': # not actually a list type
		$process['action_content_1'] = array(
			'where' => get_gp('where'),
			'xx' => get_gp('xx'), # emphasize relation to $x
			'qq' => get_gp('qq'), # emphasize relation to $q
		);
	break;
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

# we already did this above
/*
if ($process['action_content_1']) {
foreach($process['action_content_1'] as $k1 => $v1) {
	$process['action_content_1'][$k1] = get_gp($k1);
} }
*/

switch($process['form_info']['type']) {
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
# process_does_not_exist('action_content_1');
# process_does_exist('action_content_1');

$location = false;
if (!$message) {
switch($process['form_info']['type']) {
	case 'go':
		switch ($action_content_1['where']) {
			case 'page_parent':
			break;
			case 'page_last':
				$message = tt('element', $action_content_1['where']) . ' : ' . tt('element', 'unimplemented');
				$s3 = str_replace('_edit', '_list', $action_content_1['xx']); # dont return to edit page
				$location = $s3 . $action_content_1['qq'];
			break;
			case 'page_next':
			case 'page_previous':
			case 'page_first':
			default:
				# back might always work but moving withing the pages may not ie) next,previous,first,last
				$a4 = explode('/', $action_content_1['xx']);
				foreach ($a4 as $k1 => $v1) {
					if (empty($v1))
						unset($a4[$k1]);
				}
				$s4 = $a4[count($a4)];
				$b4 = 2;
				if (str_match('_list', $s4))
					$b4 = 1;
				elseif (str_match('_edit', $s4))
					$b4 = 1;
				elseif (str_match('_view', $s4))
					$b4 = 1;
				if ($b4 == 2)
					$message = tt('element', $action_content_1['where']) . ' : ' . tt('element', 'error');

				$s3 = str_replace('_edit', '_list', $action_content_1['xx']); # dont return to edit page
				$location = $s3 . $action_content_1['qq'];
			break;
		}
	break;
	case 'page':
		process_field_missing('action_content_1');
	break;
	case 'contact':
	case 'people':
		process_field_missing('action_content_1');
		if (!$lookup['user_id']) {
		if (!$lookup['contact_id']) {
			$message = tt('element', 'contact_user_mixed') . ' : ' . tt('element', 'error_does_not_exist');
		} }
	break;
} }

process_failure($message, $location);

# success
$s1 = '';
switch($process['form_info']['type']) {
	case 'go':
		switch($action_content_1['where']) {
			case 'page_last':
				# difficult because we have to find the last page
			break;
			case 'page_parent':
				parse_str(ltrim($action_content_1['qq'], '?'), $a1);
				foreach ($a1 as $k1 => $v1) {
					$a3 = explode($config['mark'], $k1);
					if (count($a3) == 1)
						$a2[$interpret['xl']][$a3[0]] = $v1;
					elseif (count($a3) == 2)
						$a2[$a3[0]][$a3[1]] = $v1;
				}
				$i1 = count($a2);
				$s2 = '';
				if(!empty($a2))
				foreach ($a2 as $k1 => $v1) {
				if (!empty($k1)) {
					if ($k1 == $i1) {
						; # discard
					}
					elseif ($k1 == $i1 - 1) {
					foreach($v1 as $k2 => $v2) {
						$s2 .= '&' . $k2 . '=' . $v2; # active variables
					} }
					else {
					foreach ($v1 as $k2 => $v2) {
						$s2 .= '&' . $k1 . $config['mark'] . $k2 . '=' . $v2; # history
					} }
				} }
				$s2 = ltrim($s2, '&');
				$s3 = $action_content_1['xx'];
				$s3 = rtrim($s3, '/');
				$s3 = rtrim($s3, 'abcdefghijklmnopqrstuvwxyz_');
				if (!$s3)
					$s3 = '/';
				$s3 = str_replace('_edit', '_list', $s3); # dont return to edit mode
				$s1 = $s3 . ($s2 ? '?' : '') . $s2;
			break;
			case 'page_previous':
				parse_str(ltrim($action_content_1['qq'], '?'), $a1);
				foreach ($a1 as $k1 => $v1) {
				if ($k1 == 'page') {
				if ($v1 > 0) {
					$a1[$k1] = $v1 - 1;
				} } }
				$s3 = str_replace('_edit', '_list', $action_content_1['xx']); # dont return to edit mode
				$s1 = $s3 . (!empty($a1) ? '?' : '') . http_build_query($a1);
			break;
			case 'page_next':
				$b1 = 2;
				parse_str(ltrim($action_content_1['qq'], '?'), $a1);
				foreach ($a1 as $k1 => $v1) {
				if ($k1 == 'page') {
					$b1 = 1;
					if ($v1 > 0)
						$a1[$k1] = $v1 + 1;
				} }
				if ($b1 == 2)
					$a1['page'] = 2;
				$s3 = str_replace('_edit', '_list', $action_content_1['xx']); # dont return to edit mode
				$s1 = $s3 . (!empty($a1) ? '?' : '') . http_build_query($a1);
			break;
			case 'page_first':
				parse_str(ltrim($action_content_1['qq'], '?'), $a1);
				foreach ($a1 as $k1 => $v1) {
				if ($k1 == 'page') {
					unset($a1[$k1]);
				} }
				$s3 = str_replace('_edit', '_list', $action_content_1['xx']); # dont return to edit mode
				$s1 = $s3 . (!empty($a1) ? '?' : '') . http_build_query($a1);
			break;
		}
	break;
	case 'scan':
		# todo hash lookup based on the submitted public key
		# ie) ?sha1=pUbLiC_kEy
		$s1 = '/host_portal/?public_key=' . to_url($process['action_content_1']['public_key']);
	break;
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
