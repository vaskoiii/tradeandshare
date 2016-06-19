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
require('list/v1/config/preset.php');

# FUNCTION
require('list/v1/function/main.php');
require('list/v1/function/circular_distance.php');
require('list/v1/function/q.php');
require('list/v1/function/key.php');
require('list/v1/function/option.php');
require('list/v1/function/php_database.php');
require('list/v1/function/arrangement.php');
require('list/v1/function/search.php');
require('list/v1/function/custom.php');
require('list/v1/function/link.php');
require('list/v1/function/engine.php');
require('list/v1/function/template.php');
require('list/v1/function/lock.php');
require('list/v1/function/css.php');
require('list/v1/function/file.php');
require('list/v1/function/member.php');
require('list/v1/function/payout.php');

# form processing
require('list/v1/function/process.php');
require('list/v1/function/email.php');

# DB CONNECT
require('list/v1/inline/mysql_connect.php');

# X
require('list/v1/config/x.php');
