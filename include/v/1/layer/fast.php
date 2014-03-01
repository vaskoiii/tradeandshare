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

# Description: Make sure that fields get entered correctly if there was an error with processing [fast]

# hack to call forms with a nice name;
$empty_listing = array();

# todo set the correct fast_content
# none of the $data['fast'] data exists unless there was a form error
# only $edit exists
if (isset($_SESSION['interpret']['failure'])) {
if ($_SESSION['interpret']['failure'] == 1) {
if (isset($_SESSION['process']['form_info']['load'])) {
if ($_SESSION['process']['form_info']['load'] == 'fast') {

	# No need for the duplicate information of motion (superfluous previous data structure)
	# ie) $data['motion']['response']['motion_content_1']; # 2x motion keyword!
	$data['fast']['response']['form_info']['type'] = '';
	$data['fast']['response']['content_1'] = array();
	$data['fast']['response']['content_2'] = array();
	# will always be empty but used for consistency
	$data['fast']['result']['listing'][0] = array();
	# alias
	$fast_form_info = & $data['fast']['response']['form_info'];
	$fast_content_1 = & $data['fast']['response']['content_1'];
	$fast_content_2 = & $data['fast']['response']['content_2'];
	$fast_listing = & $data['fast']['result']['listing'][0];

	# todo combine use name if necessary
	# contact_user_mixed_combine($fast_content_1, get_gp('action_user_id'), get_gp('action_contact_id'), $_SESSION['login']['login_user_id']);

	# todo set default values in all content
	$s1 = $_SESSION['process']['form_info']['type'];
	$fast_form_info = $_SESSION['process']['form_info'];
	$fast_content_1 = $edit[$s1]['content_1'];
	$fast_content_2 = $edit[$s1]['content_2'];

	if (is_array($_SESSION['process']['action_content_1'])) {
	foreach ($_SESSION['process']['action_content_1'] as $k1 => $v1) {
		$fast_content_1[$k1] = $v1;
	} }
	if (is_array($_SESSION['process']['action_content_2'])) { 
	foreach ($_SESSION['process']['action_content_2'] as $k1 => $v1) {
		$fast_content_2[$k1] = $v1;
	} }

} } } }
