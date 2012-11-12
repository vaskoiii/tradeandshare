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

# Contents/Description: quickly navigate through most translated pages on TS.

# setcookie used from javascript in the launcher this only sets the initial value
if(!isset($_COOKIE['launch'])) {
	$_COOKIE['launch'] =  array(
		'element_name' => 'main',
		'translation_name' => '!'
	);	
}

$s1 = str_replace('theme_', '', $_SESSION['theme']['launcher_theme_name']);

# added 2011-08-25
$data['theme']['color'] = $s1;
#$data['theme']['color'] = 'green'; # testing override 2012-03-18 vaskoiii

# extra translation  it is done again below.
add_translation('page', 'main');
do_translation($key, $translation, $_SESSION['dialect']['dialect_id'], $_SESSION['login']['login_user_id']);

$data['css']['theme_name'] = 'theme_' . $data['theme']['color']; # like using a function paramerter for the include below
#include($x['site']['i'] . 'css/background_color.php');
$data['css'] = array_merge($data['css'], get_background($s1));

$data['lock_query'] = get_lock_query();

$sql = <<<SQL
		SELECT
			p.name AS page_name,
			tr.name AS translation_name
		FROM
			{$config['mysql']['prefix']}page p,
			{$config['mysql']['prefix']}translation tr,
			{$config['mysql']['prefix']}kind k
		WHERE
			k.id = tr.kind_id AND 
			k.name = "page" AND
			tr.`default` = 1 AND
			p.id = tr.kind_name_id AND
			
			p.launch = 1 AND
			tr.dialect_id = 
SQL;
$sql .= (int)$_SESSION['dialect']['dialect_id'] . "\n";
$sql .= <<<SQL
		ORDER BY 
			tr.name ASC
SQL;
$result = mysql_query($sql) or die(mysql_error());
while ($row = mysql_fetch_assoc($result))
	$data['launch'][] = $row;

$data['js_array_string'] = '';

foreach($data['launch'] as $k1 => $v1) {
	# Threatening characters are: & ' " and \ so these will be striped from the translation
	$data['js_array_string'] .= 'tsl["' . $v1['page_name'] . '"] = "' . preg_replace('/[\&\\\"\']/', '', $v1['translation_name']) . '";' ."\n";
}
