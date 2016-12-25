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

# Contents/Description: process specific function (form input submission functions/handling)

function get_q() {
	$q;
	if (isset($_GET['q']))
		$q = $_GET['q'];
	elseif (isset($_POST['q']))
		$q = $_POST['q'];
	else
		$q = $_SERVER['REDIRECT_QUERY_STRING'];

	return ltrim($q, '?');
}

# x must be set first!
#todo q doesnt handle arrays? 2012-04-05 vaskoiii
function set_q($q_string) {
	global $q;
	global $x;
	global $config;
	global $data;
	$level = (int)$x['level'];
	
	$v = array();
	$v1 = array();
	$q = array();
	$q['raw'] = $q_string;

	if (!empty($q_string)) {
		# note: parse_string() converts the . separator to an underscore  2012-04-05 vaskoiii
		parse_str($q_string, $q['preparsed']);
		foreach ($q['preparsed'] as $k1 => $v1) {
			$a1 = explode($config['mark'], $k1);
			if (count($a1) == 1)
				$q['parsed'][$x['level']][$a1[0]] = $v1;
			elseif (count($a1) == 2)
				$q['parsed'][$a1[0]][$a1[1]] = $v1;
		}

		krsort($q['parsed']);
		foreach($q['parsed'] as $k1 => $v1) {
			$q['active'][$k1] =  http_build_query($v1);
			$q['inactive'][$k1] =  $k1 . $config['mark'] . http_build_query($v1, '', '&' . $k1 . $config['mark']);
		}
	}
}

# Brains of ff()
function get_q_query($destination_level, $string = '') {
	global $q;

	if ($string)
		$q_query = trim($string, '?&');

	if (!empty($q['inactive'])) {
		foreach($q['inactive'] as $k1 => $v1) {
			if ($k1 < $destination_level && $q['inactive'][$k1]) {
				if ($q['inactive'][$k1])
					$q_query .= '&';
				$q_query .= $q['inactive'][$k1];
			}
		}
	}
	$q_query = trim($q_query, '?&');

	if ($q_query)
		return '?' . $q_query;
	else
		return $q_query;
}

# Remove/Replace Variables
# ie)
#   Level 2 String:		  2.a=aaa&2.b=bbb
# + PHP Modification:		+ get_q_query_modified(2, array('a' => '', 'c' => 'ccc'))
# = Modified String:		= b=bbb&c=ccc
# Note: even though not specified we still have: b=bbb
function get_q_query_modified($destination_level, $variables) {
	# $destination_level = integer destination_level of q with variables to modify.
	# $variables = array of values of variables set at $destination_level to overwrite
	global $q;
	$query = array();
	$page_query = array();

	if (empty($q['parsed'][$destination_level])) {
		$query = $variables;
	}
	else {
		# leftmost parameters get replaced by rightmost parameters with duplicates 2012-04-05 vaskoiii
		$query = array_merge($q['parsed'][$destination_level], $variables);
	}

	if (!empty($query)) {
	foreach ($query as $k1 => $v1) {
		if (is_array($v1)) {
		foreach($v1 as $k2 => $v2) {
		if (empty($v2)) {
			unset($query[$k1][$k2]);
		} } }
		else {
		if (empty($v1)) {
			unset($query[$k1]);
		} }
	} }

	if (!empty($query)) {
		return (count($query) ? '?' : '') . http_build_query($query);
	}
	else
		return false;
}

# ALL TS LINKS
# ff() | ffm()

# ie)
#   HTML Page URL 		  /new_report/item_list/?keyword=fun&1.lock_user_id=132
# + PHP HREF			+ <a href="./new_report/item_list/contact_view/<!= ff('keyword=yay&lock_user_id=132'); !>">
# = DESTINATION Page URL	= /new_report/item_list/contact_view/?keyword=yay&lock_user_id=132&2.keyword=fun&1.lock_user_id=132

# Fast Forward
# $string becomes active query string on $destination_level
# For external links make $destination_level 0 or 1. ie) ATOM?
function ff($string = '', $ff = 1) {
	global $x;
	$destination_level = $ff + $x['level'];
	$string = trim($string, '?&');
	return htmlentities(get_q_query($destination_level, $string));
}

# Fast Forward Modification
# $string Modifies $q variables on $destination_level if they exist
function ffm($string, $ff = 1) {
	global $x;
	$destination_level = $ff + $x['level'];
	$string = trim($string, '?&');
	$array = array();
	parse_str($string, $array);
	return htmlentities(get_q_query($destination_level, get_q_query_modified($destination_level, $array)));
}
