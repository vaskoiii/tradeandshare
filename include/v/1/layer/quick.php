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

# Description: Make sure that fields get entered correctly if there was an error with processing [quick]






# todo dont repeat logic
# exact same file as fast.php but with fast and f replaced with quick and q





$empty_listing = array();

# todo set the correct quick_content
# none of the $data['quick'] data exists unless there was a form error
# only $edit exists
if (isset($_SESSION['interpret']['failure'])) {
if ($_SESSION['interpret']['failure'] == 1) {
if (isset($_SESSION['process']['form_info']['load'])) {
if ($_SESSION['process']['form_info']['load'] == 'quick') {

	# No need for the duplicate information of motion (superfluous previous data structure)
	# ie) $data['motion']['response']['motion_content_1']; # 2x motion keyword!
	$data['quick']['response']['form_info']['type'] = '';
	$data['quick']['response']['content_1'] = array();
	$data['quick']['response']['content_2'] = array();
	# will always be empty but used for consistency
	$data['quick']['result']['listing'][0] = array();
	# alias
	$quick_form_info = & $data['quick']['response']['form_info'];
	$quick_content_1 = & $data['quick']['response']['content_1'];
	$quick_content_2 = & $data['quick']['response']['content_2'];
	$quick_listing = & $data['quick']['result']['listing'][0];

	# todo combine use name if necessary
	# contact_user_mixed_combine($quick_content_1, get_gp('action_user_id'), get_gp('action_contact_id'), $_SESSION['login']['login_user_id']);

	# todo set default values in all content
	$s1 = $_SESSION['process']['form_info']['type'];
	$quick_form_info = $_SESSION['process']['form_info'];
	$quick_content_1 = $edit[$s1]['content_1'];
	$quick_content_2 = $edit[$s1]['content_2'];

	if (is_array($_SESSION['process']['action_content_1'])) {
	foreach ($_SESSION['process']['action_content_1'] as $k1 => $v1) {
		$quick_content_1[$k1] = $v1;
	} }
	if (is_array($_SESSION['process']['action_content_2'])) { 
	foreach ($_SESSION['process']['action_content_2'] as $k1 => $v1) {
		$quick_content_2[$k1] = $v1;
	} }

} } } }
