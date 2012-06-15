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

# Contents/Description: populate the 4 helper tables: index_invited_user, index_offer_user, index_rating_user, index_transfer_user
# populate the helper table: index_tag

# $tag_id
function ts_recursive_tag($tag_id) {

	if (!$tag_id)
		die('tag_id was not supplied to ts_recursive_tag()');

	global $config;
	# recursion
	$continue = 1;
	# $r1 = recursive_tag_id
	#$r1 = $tag_id;
	$r1 = $tag_id;

	$tag_name_array = array();

	$i1=0;
	while ($continue == 1 && $i1 < $config['max_tag_depth']) {
		$continue = 2; # assume this will be the last execution of the loop
		$sql = '
			select
				parent_id,
				name
			from
				' . $config['mysql']['prefix'] . 'tag
			where
				id = ' . (int)$r1 . '
			limit
				1
		';
		$result = mysql_query($sql) or die(mysql_error());
		while ($row = mysql_fetch_assoc($result)) {
			# echo '<hr>' . $row['parent_id'] . ' ?= ' . $config['root_tag_id'];
			if ($config['root_tag_id'] != $row['parent_id'])
				$continue = 1; # not a parent of itself so keep going
			$tag_name_array[] = $row['name'];
			$r1 = $row['parent_id'];
			$i1++;
		}
	}

	$sql = '
		delete from
			' . $config['mysql']['prefix'] . 'index_tag
		where
			tag_id = ' . (int)$tag_id
	;
	$result = mysql_query($sql) or die(mysql_error());

	krsort($tag_name_array);
	$sql = '
		insert into
			' . $config['mysql']['prefix'] . 'index_tag
		set
			tag_id = ' . (int)$tag_id . ',
			tag_path = ' . to_sql( implode( $config['category_exploder'], $tag_name_array ))
	;
	$result = mysql_query($sql) or die(mysql_error());
}


/*
// NAMING CONVENTION
$entry_array = array(
	'invited',
	'offer',
	'rating',
	'transfer',
);
$lock_array = array(
	'user',
);
*/


function index_entry($entry, $entry_id, $source_user_id, $destination_user_id, $index_name = 'index', $debug = false) {
	global $config;
	$lock = 'user';

	$t0;
	if ($entry < $lock)
		$t0 = $entry . '_' . $lock;
	else
		$t0 = $lock . '_' . $entry;

	$lock_plus = array();

	switch($t0) {
		case 'invited_user':
		case 'offer_user':
		case 'rating_user':
		case 'transfer_user':
			$lock_plus[$source_user_id] = $source_user_id;
			$lock_plus[$destination_user_id] = $destination_user_id;
		break;
	}

	// DELETE
	$sql = '
		DELETE 
			t0
		FROM
			' . $config['mysql']['prefix'] . $index_name . '_' . $t0 . ' t0
		WHERE
			t0.' . $entry . '_id = ' . (int)$entry_id
	;
	if ($debug) {echo '<hr />' . $sql; }
	$result = mysql_query($sql) or die(mysql_error());

	// INSERT
	foreach($lock_plus as $k3 => $v3) {
		$sql = '
			INSERT INTO
				' . $config['mysql']['prefix'] . $index_name . '_' . $t0 . '
			SET
				' . $lock . '_id = ' . (int)$k3 . ',
				' . $entry . '_id = ' . (int)$entry_id
		;
		if ($debug) { echo '<hr />' . $sql; }
		$result = mysql_query($sql) or die(mysql_error());
	}
	if ($debug) { echo '<pre>'; print_r(array($source_user_id, $destination_user_id )); echo '</pre>'; }
}
