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
# when using include notation make sure the vim gf command remains useful
# if need to have different version or template specific renderings just create an entirely router/loader file
require('list/v1/config/dependancy.php');
