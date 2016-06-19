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
include('list/v1/inline/head.php');
include('list/v1/inline/header.php');

# the fast and quick box so it can get translations
include('list/v1/inline/edit.php');
include('list/v1/layer/fast.php');
include('list/v1/layer/quick.php');

include('list/v1/page/' . $x['page']['name'] . '.php');
include('list/v1/inline/footer.php');

include('list/v1/inline/t1/head.php');
include('list/v1/inline/t1/header.php');

include('list/v1/layer/t1/fast.php');
include('list/v1/layer/t1/quick.php');

include('list/v1/inline/t1/header_after.php');

include('list/v1/page/t1/' . $x['page']['name'] . '.php');
include('list/v1/inline/t1/footer.php');
