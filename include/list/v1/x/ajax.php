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

# Contents/Description: JS calls these files to retrieve data Asyncronously (NOT 'Content-Type: text/javascript:') - though these might be the same.

header('HTTP/1.0 200 Found');
header('Content-type: text/plain');

include($x['site']['i'] . '/ajax/' . $x['page']['name'] . '.php');
# template file (included below) will output JSON data ONLY
# needed because engine files can not (should not) ouput any data ie) the above include
include($x['site']['i'] . '/ajax/' . $x['site']['t'] . $x['page']['name'] . '.php');
