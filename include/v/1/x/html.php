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

# Contents/Description: Should be the ONLY exception in X where we allow HTML code 
# Notes: X is intended for just including files

# todo these are mainly used for documentation pages which are not translated. Perhaps there is a way to have these pages translatable. Perhaps a wiki would be a better solution.

header('HTTP/1.0 200 Found');

include($x['site']['i'] . '/inline/head.php');
include($x['site']['i'] . 'inline/header.php');
include($x['site']['i'] . 'inline/footer.php');

include($x['site']['i'] . '/inline/' . $x['site']['t'] . '/head.php');
include($x['site']['i'] . 'inline/' . $x['site']['t'] . 'header.php');
include($x['site']['i'] . '/inline/' . $x['site']['t'] . '/header_after.php');

# done just to help with readability (othewise we would just use ints) 2012-06-09 vaskoiii
$s1 = get_db_single_value('
		`code`
	from
		' . $config['mysql']['prefix'] . 'dialect
	where
		id = ' . to_sql($_SESSION['dialect']['dialect_id'])
, 0); ?> 

<div class="content_box"><?
switch ($s1) {
	case 'en':
		include($x['site']['i'] . 'html/' . $s1 . '/' . $x['page']['name'] . '.php');
	break;
	case 'jp':
	case 'de':
	default:
		# todo helpful message if page is not yet translated. 2012-06-09 vaskoiii
		include($x['site']['i'] . 'html/en/' . $x['page']['name'] . '.php');
	break;
} ?> 
</div>
<div class="menu_1"></div>
<div class="menu_2"></div><?

include($x['site']['i'] . 'inline/' . $x['site']['t'] . 'footer.php');
