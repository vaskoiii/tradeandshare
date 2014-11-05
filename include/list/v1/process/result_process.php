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

# Contents/Description: selection_action parsing/redirection page

$process = array(); # sent to process
$interpret = array(); # interpreted from $process

# preprocessing (only 1 should be set because you can only choose 1 submit) 2012-04-14 vaskoiii
# todo add to php database
$interpret['action_array'] = array(
	'memorize' => '1', # copy to contacts
	'merge' => '1', # tag combine
	'delete' => '1', # set inactive
	'export' => '1', # copy to transfer table
	'forget' => '1', # remove minder
	'import' => '1', # copy to item table
	'judge' => '1', # copy to vote table
	'remember' => '1', # add minder
);

foreach ($interpret['action_array'] as $k1 => $v1)
	if (isset_gp($k1))
		$process['action'] = $k1;
$process['list_name'] = get_gp('list_name');
$process['row'] = get_gp('row');  // we could remove duplicates here with array_unique() but we do it later...
$process['q'] = get_gp('q');

# temp disable for reconstruction of ts 2012-04-14 vaskoiii
switch ($process['action']) {
	case 'delete':
	case 'remember':
	case 'forget':
		# ok to continue
	break;
	default:
		die('result action : case not yet tested...');
	break;
}

# only thing that can really go wrong at this point is not selecting anything 2012-04-14 vaskoiii
if (empty($process['row']))
	$interpret['message'] = tt('element', 'error_field_missing');

# do it
# better to NOT use session
#$_SESSION['process'] = $process;
#$_SESSION['process']['message'] = $interpret['message'];

if ($interpret['message']) {
	#$s1 = $x['..'] . get_q_query($x['level']);
	$s1 = $x['..'] . ff('', -1);
	header_debug($s1);
	header('location: ' . $s1);
	exit;
}
else {
	# $s1 = $x['..'] . 'selection_action/' . get_q_query($x['level']);
	# $s1 = $x['..'] . 'selection_action/' . ff('', 0);
	$s1 = $x['..'] . 'selection_action/?' . http_build_query($process);
	header_debug($s1);
	header('location: ' . $s1);
	exit;
}
