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

# Contents/Description: Setup versioning info. Declare $x. Get dependancies.

# todo force a single $_SERVER['HTTP_HOST'] and $_SERVER['SERVER_PROTOCOL']

$x = array();

# todo make multiple versions and then make a way to be able to select which one you want to load:
# ie) USING $_SESSION data loaded by order of: ( $_GET $_POST $_SESSION $_COOKIE <Default!> )

# Next 3 lines = default:
$x['site']['i'] = 'v/1/'; # INCLUDE VERSION - v is default application dir. 1 is the version number.
$x['site']['p'] = 'v/1/'; # PUBLIC VERSION  - Separated out so we can possibly share public media between versions instead of duplicating the whole directory.
$x['site']['t'] = 't1/'; # TEMPLATE - Extra dir for templating on the same version

require($x['site']['i'] . 'config/dependancy.php');
