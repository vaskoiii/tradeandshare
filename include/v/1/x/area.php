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

# Contents/Description: Placeholder if we want to have splash pages to certain sections. Not being used 2012-02-02 vaskoiii

header('HTTP/1.0 200 Found');

include($x['site']['i'] . '/inline/head.php');
include($x['site']['i'] . '/inline/header.php');
include($x['site']['i'] . '/page/area.php');
include($x['site']['i'] . '/inline/footer.php');

include($x['site']['i'] . '/inline/' . $x['site']['t'] . '/head.php');
include($x['site']['i'] . '/inline/' . $x['site']['t'] . '/header.php');
include($x['site']['i'] . '/page/' . $x['site']['t'] . '/area.php');
include($x['site']['i'] . '/inline/' . $x['site']['t'] . '/footer.php');
