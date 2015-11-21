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

function debug_die($string) {
	global $config;
	if ($config['debug'] == 1)
		die((string)$string);
}

function get_x() {
	# use <form action="/index.php" method="POST"> for accessing POST variables
	# /index.php is the ONLY page that has access to POST variables and all pages including 404 are loaded through /index.php
	# Assume 1 ? is allowed in URL
	$x = explode('?', $_SERVER['REQUEST_URI']);

	# Force $_GET['var1'] and $_GET['var2'] when using <form action="index.php?var1=a&var2=b" method="POST">
	if (isset($x[1]))
		parse_str($x[1], $_GET);

	if (isset($_GET['x'])) {
		$x['array'] = explode('/', $_GET['x']);
	}
	elseif (isset($_POST['x'])) {
		$x['array'] = explode('/', $_POST['x']);
	}
	else {
		$x['array'] = explode('/', $x[0]);
	}
	return $x['array'];
}

function set_x($array) {
	global $x;
	$x['raw'] = $array;
	if (isset($x['raw'][0]))
		unset($x['raw'][0]);

	$x['level'] = count($x['raw']);

	# Force every page to end in / ( Will not affect files in ~/public/ )
	if (!empty($x['raw'][$x['level']])) {
		$s1 =  str_replace('/alias/', '/', $_SERVER['REDIRECT_URL']) . '/' . (
			$_SERVER['REDIRECT_QUERY_STRING'] 
				? '?' . $_SERVER['REDIRECT_QUERY_STRING'] 
				: 
			''
		); 
		header_debug($s1);
		header('location: ' . $s1);
		exit;
	}

	# Make [page] and [page/] load the same thing!
	if (!($x['level'] && !empty($x['raw'][$x['level']]) && isset($x['raw'][$x['level']]))) {
		unset($x['raw'][$x['level']]);
		$x['level']--;
	}

	$x['name'] = array_pop($x['raw']);

	# form processing vars
	$x['.'] = '/';
	$x['..'] = '/';
	if (!empty($x['raw']))
		$x['..'] .= implode('/', $x['raw']) . '/';
	unset($x['raw']);
	$x['.'] = $x['..'] . ($x['name'] ? $x['name'] . '/' : '');

	# deprecated: should use $x['load'] 2012-02-15 vaskoiii
	if ($x['name'])
		$x['part'] = explode('_', $x['name']);
}

# gp = get/post
function isset_gp($q) {
	if (isset($_GET[$q]))
		return true;
	else {
		if (isset($_POST[$q]))
			return true;
		else 
			return false;
	}
}

function get_gp($q) {
	if (isset($_GET[$q]))
		return $_GET[$q];
	else {
		if (isset($_POST[$q]))
			return $_POST[$q];
		else
			return false;
	}
}

function get_boolean_gp($q) {
	if (isset($_GET[$q]))
		return 1; // db true
	else {
		if (isset($_POST[$q]))
			return 1; // db true
		else
			return 2; // db false
	}
}

function to_url($vurl) {
	return urlencode(($vurl)); 
}

function from_url($f) {
	return urldecode($f);
}

function to_html($t) {
	return htmlentities($t, ENT_QUOTES, "UTF-8");
}

function from_html($f) {
	if ($f != NULL && $f != FALSE)
		return html_entity_decode($f, ENT_QUOTES, "UTF-8");
	else
		return $f;
}

function to_xml($t) {
	return '<![CDATA[' . $t . ']]>';
}

function from_xml($f) {
	return $f;
}

function to_sql($t) {
	# http://us2.php.net/mysql_real_escape_string
	# Example 3. A "Best Practice" query
	if (get_magic_quotes_gpc())
		$t = stripslashes($t);
	# todo dont add quotes here as it is less flexible 2012-06-05 vaskoiii
	$t = '"' . mysql_real_escape_string($t) . '"';
	return $t;
}

function from_sql($f) {
	return $f;
}

function get_db_enum($table_name, $field_name) {
	$sql = '
		SHOW
			COLUMNS
		FROM
			`' . $table_name . '`
		LIKE 
			"' . $field_name .'"
	';
	$result = mysql_query($sql); 
	$row = mysql_fetch_array($result); 
	$regex = "/'(.*?)'/";
	preg_match_all($regex , $row[1], $enum_array);
	$enum_fields = $enum_array[1];
	return $enum_fields;
}

function get_db_single_value($sql, $debug = false) {
	$sql = 'SELECT ' . $sql . ' LIMIT 1';
	if ($debug) 
		echo '<hr />' . $sql;
	$result = mysql_query($sql) or die(mysql_error());
	while ($row = mysql_fetch_row($result))
		return $row[0];
	return false;
}
