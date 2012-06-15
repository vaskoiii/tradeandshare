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

# Contents/Description: Populate ts_index_tag by generating category paths

# todo we will probably need a function to automatically insert stuff when a new tag is made as well.

function ts_index_tag($placeholder) {
	global $config;

	$tag_id = array();
	$placeholder = get_db_single_value('
			id
		from
			' . $config['mysql']['prefix'] . 'tag
		where
			id >= ' . (int)$placeholder . '
		order by
			id ASC
	', 0);

	ts_recursive_tag($placeholder);

	return ($placeholder + 1);
}

# do it!
$placeholder = 1;
# while ($placeholder && $placeholder < 100) {
while ($placeholder) {
	$placeholder = ts_index_tag($placeholder);
}
