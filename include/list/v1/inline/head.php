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

add_translation('page', $x['name']);
add_translation('page', 'guest_portal');

# TODO where is best to put this? 2012-01-13 vaskoiii
include($x['site']['i'] . '/inline/site_map.php');
foreach($data['new_report']['page_id'] as $k1 => $v1) {
	if (!empty($v1['page_id'])) {
		add_translation('page', $v1['page_name']);
		foreach($v1['page_id'] as $k2 => $v2) {
			add_translation('page', $v2['page_name']);
			// Do NOT Care (Do NOT show new)
			// TODO: fix so we dont need to run the queries still.
			switch ($v2['page_name']) {
				case 'user_list':
				case 'tag_list':
					if (isset($data['new_report']['page_id'][$k1]['page_id'][$k2]['new_amount']))
						$data['new_report']['page_id'][$k1]['page_id'][$k2]['new_amount'] = 0;
				break;
			}
		}
	}
}
