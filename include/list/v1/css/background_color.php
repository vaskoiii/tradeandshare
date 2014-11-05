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

# Contents/Description: 3 main colors for the content boxes colors correspond to: header & body & footer

# todo functions are needed for css because it is integrated in differnent kinds of displays 2012-04-06 vaskoiii
$data['css'] = array_merge(
		get_background(str_replace('theme_', '', $_SESSION['theme']['theme_name'])),
		$data['css']
);
