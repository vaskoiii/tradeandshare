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

# Contents/Description: Depends!
# Notes: Separate this later into different dependencies depending on the template if we need to boost performance
# many functions can be loaded later through the corresponding "x file" ie) include/list/v1/x/layer.php
# for now it is easier to keep everything in one place

# CONFIG
require($x['site']['i'] . '/config/preset.php');

# FUNCTION
require($x['site']['i'] . '/function/main.php');
require($x['site']['i'] . '/function/circular_distance.php');
require($x['site']['i'] . '/function/q.php');
require($x['site']['i'] . '/function/key.php');
require($x['site']['i'] . '/function/option.php');
require($x['site']['i'] . '/function/php_database.php');
require($x['site']['i'] . '/function/arrangement.php');
require($x['site']['i'] . '/function/search.php');
require($x['site']['i'] . '/function/custom.php');
require($x['site']['i'] . '/function/link.php');
require($x['site']['i'] . '/function/engine.php');
require($x['site']['i'] . '/function/template.php');
require($x['site']['i'] . '/function/lock.php');
require($x['site']['i'] . '/function/css.php');
require($x['site']['i'] . '/function/file.php');
require($x['site']['i'] . '/function/member.php');
require($x['site']['i'] . '/function/payout.php');

# form processing
require($x['site']['i'] . '/function/process.php');
require($x['site']['i'] . '/function/email.php');

# DB CONNECT
require($x['site']['i'] . '/inline/mysql_connect.php');

# X
require($x['site']['i'] . '/config/x.php');
