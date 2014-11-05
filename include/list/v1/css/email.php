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

# Contents/Descriptoin: Setup Email Crazyness. Use a random theme

# NOTE: all spaces are eliminated in squirrelmail in the CSS such that the browser will not understand the style. 
# ie) BAD => margin: 1px 2px; => margin:1px2px;
# ie) GOOD => margin-top: 1px; margin-bottom: 1px; margin-left: 2px; margin-right: 2px; => margin-top:1px;margin-bottom:1px;margin-left:2px;margin-right:2px;

# theme_select_none = theme_id 11
#$data['css']['email']['theme_id'] = mt_rand(1,10);

/*
if ($data['css']['email']['theme_id']) {
	$data['css']['theme_name'] = get_db_single_value('
			t.name
		FROM
			' . $config['mysql']['prefix'] . 'theme t
		WHERE
			t.id = ' . (int)$data['css']['email']['theme_id']
	);
}
*/

# always empty?
if (empty($data['css']['email']))
	$data['css']['email'] = array();

# todo this should be in the $tsmail['data']['css'] array 2012-04-06 vaskoiii
$data['css']['email'] = array_merge($data['css']['email'], get_random_theme());
