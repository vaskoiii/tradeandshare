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

# Contents/Description: Render engine and template files in the [page] directory

header('HTTP/1.0 200 Found');
include($x['site']['i'] . '/inline/head.php');
include($x['site']['i'] . 'inline/header.php');

# the fast and quick box so it can get translations
include($x['site']['i'] . 'inline/edit.php');
include($x['site']['i'] . 'layer/fast.php');
include($x['site']['i'] . 'layer/quick.php');

include($x['site']['i'] . '/page/' . $x['page']['name'] . '.php');
include($x['site']['i'] . '/inline/footer.php');

include($x['site']['i'] . '/inline/' . $x['site']['t'] . '/head.php');
include($x['site']['i'] . 'inline/' . $x['site']['t'] . '/header.php');

include($x['site']['i'] . '/layer/' . $x['site']['t'] . '/fast.php');
include($x['site']['i'] . '/layer/' . $x['site']['t'] . '/quick.php');

include($x['site']['i'] . '/page/' . $x['site']['t'] . '/' . $x['page']['name'] . '.php');
include($x['site']['i'] . '/inline/' . $x['site']['t'] . '/footer.php');
