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

# Contents/Description: generic process for all search submissions
# Known Issues: default case is relied on in switch statements for added flexibility

// TRANSLATIONS for messages
add_translation('element', 'transaction_complete');
add_translation('element', 'lock_contact_name');
add_translation('element', 'error_does_not_exist');

// DO Translation for message
do_translation($key, $translation, $_SESSION['dialect']['dialect_id'], $_SESSION['login']['login_user_id']);

$process = array(); // Sent to process
$interpret = array(); // Interpreted from $process

$process['form_info'] = get_search_header_1();
foreach($process['form_info'] as $k1 => $v1)
	$process['form_info'][$k1] = get_gp($k1);

$process['search_miscellaneous'] = array(
	'keyword' => '',
	'asearch_on' => '', # TODO allow asearch option!
	'child' => '', # deprecated
	'referrer' => '', # used anymore? 2012-04-19 vaskoiii
	$process['form_info']['type'] . '_uid' => '',
);

if (get_gp('keyword'))
	$interpret['lookup']['keyword'] = get_gp('keyword');

foreach($process['search_miscellaneous'] as $k1 => $v1)
	$process['search_miscellaneous'][$k1] = get_gp($k1);

$process['search_content_1'] = get_search_content_1($process['form_info']['type']);
if ($process['search_content_1'])
foreach($process['search_content_1'] as $k1 => $v1)
	$process['search_content_1'][$k1] = get_gp($k1);

$process['search_content_2'] = get_search_content_2($interpret['form_info']['type']);
if ($process['search_content_2'])
foreach($process['search_content_2'] as $k1 => $v1)
	$process['search_content_2'][$k1] = get_gp($k1);

contact_user_mixed_split('search_content_2', 'lock_');
contact_user_mixed_split('search_content_1', '');

if ($process) {
	foreach($process as $k1 => $v1) {
		process_data_translation($k1);
		process_does_not_exist($k1);
	}
	# process_does_exist($k1); # likely unnecessary for searching 2012-06-22 vaskoiii
}

# todo check for invalid fields
foreach($process as $k1 => $v1) {
if (!empty($v1)) {
foreach($v1 as $k2 => $v2) {
	$s1 = '';
	if ($v2)
	switch($k2) {
		case 'lock_contact_user_mixed':
			$s1 = 'lock_';
		case 'contact_user_mixed':
			if (!$interpret['lookup'][$s1 . 'user_id'] && !$interpret['lookup'][$s1 . 'contact_id'])
				$interpret['message'] = tt('element', $s1 . 'contact_user_mixed') . ' : ' . tt('element', 'error_does_not_exist');
		break;
	}
} } }

# MORE ERROR CHECKING

# TODO put this in the does not exist function!
foreach($process as $k1 => $v1)
if ($v1['kind_name_name'] && !$interpret['lookup']['kind_name_id'])
	$interpret['message'] = tt('element', 'kind_name') . ' + ' . tt('element', 'kind_name_name') . ' : ' . tt('element', 'error_does_not_exist');

process_failure($interpret['message']);

if ($process) {
foreach($process as $k1 => $v1) {
	if ($v1) {
	foreach($v1 as $k2 => $v2) {
	if ($v2 == '') {
		$s1 = get_interpreted_variable($k2);
		$interpret['lookup'][$s1] = '';
	} } }
} }

# initial page loading
$interpret['lookup']['page'] = '';
$interpret['lookup']['focus'] = '';
$interpret['lookup']['preview'] = array('');
$interpret['lookup']['expand'] = array('');

# todo integrate into process_data_translation()
# unsets the corresponding variables if they were set
if ($interpret['lookup']['user_id'] && !$interpret['lookup']['contact_id'])
	$interpret['lookup']['contact_id'] = '';
if (!$interpret['lookup']['user_id'] && $interpret['lookup']['contact_id'])
	$interpret['lookup']['user_id'] = '';
if ($interpret['lookup']['lock_user_id'] && !$interpret['lookup']['lock_contact_id'])
	$interpret['lookup']['lock_contact_id'] = '';
if (!$interpret['lookup']['lock_user_id'] && $interpret['lookup']['lock_contact_id'])
	$interpret['lookup']['lock_user_id'] = '';

if ($interpret['lookup'])
	$s1 = http_build_query($interpret['lookup']);
else
	$s1 = '';

$x['..'] = str_replace('_edit', '_list', $x['..']); # never return to edit page

# SUCCESS
process_success(tt('element', 'transaction_complete'), $x['..'] . ffm($s1, -1));
