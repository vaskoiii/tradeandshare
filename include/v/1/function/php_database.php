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

# Contents/Description: Retrieve parts that "may" be be better stored here than in a relational database.
# Ideally: Logic is minimal. (no [if] only [switch])
# Group By similar sections then sort alphabetically.
function get_load_same_edit($load) {
	switch($load) {
		case 'list':
			return 'action';
		case 'view':
			return 'motion';
	}
	return false;
}

function get_load_opposite_edit($load) {
	switch($load) {
		case 'view':
		case 'motion':
			return 'action';
		case 'list':
		case 'view':
			return 'motion';
	}
	return false;
}

function get_load_parent($load) {
	switch($load) {
		case 'view':
			return 'motion';
		case 'list':
			return 'action';
		break;
	}
	return false;
}

function get_x_load() {
	return array(
		'header' => array(
			'name' => '',
		),
		'motion' => array( # try to make parent and children editable from the same screen 2012-04-04 vaskoiii
			'name' => '',
			'type' => '',
			'id' => '',
		),
		'view' => array(
			'name' => '',
			'type' => '',
			'id' => '',
		),
		'action' => array(
			'name' => '',
			'type' => '',
			'id' => '',
		),
		'list' => array(
			'name' => '',
			'type' => '',
			'id' => '',
		),
		'footer' => array(
			'name' => '',
		),
	);
}

# beacause of the way tables are named in the database.
function get_combined_table_name($array) {
	sort($array);
	return implode('_', $array);
}

function get_table_name($type) {
	# We should have had this in the database a long time ago!
	switch($type) {
		case 'category':
		case 'contact':
		case 'feedback':
		case 'group':
		case 'incident':
		case 'metail':
		case 'note':
		case 'team':
		case 'meripost':
		case 'meritopic':
		case 'user':
		case 'minder':
			return $type;
		break;
		case 'groupmate':	return 'link_contact_group'; break; # previously link_contact_user 2012-04-19 vaskoiii
		case 'teammate':	return 'link_team_user'; break;
	}
	return false;
	//die('no such table for listing type: ' . $type);
}

// reciprocal of get_child?
function get_parent_listing_type($type) {
	switch($type) {
		case 'category':	return 'category'; break;
		case 'feedback':	return 'incident'; break;
		case 'groupmate':	return 'group'; break;
		case 'meripost':	return 'meritopic'; break;
		case 'metail':		return 'user'; break; // Super Important!
		case 'note':		return 'contact'; break; // Super Important!
		case 'teammate':	return 'team'; break;
		case 'user':		return 'location'; break;
	} 
	return false;
}

function get_child_listing_type($type) {
	switch($type) {
		case 'category':	return 'category'; break;
		case 'contact':		return 'note'; break; // Super Important!
		case 'group':		return 'groupmate'; break; # used to be contact 2012-04-19 vaskoiii
		case 'incident':	return 'feedback'; break;
		case 'location':	return 'user'; break; # testing this one out
		case 'meritopic':	return 'meripost'; break;
		case 'team':		return 'teammate'; break;
		case 'user':		return 'metail'; break; // Super Important!
	} 
	return false;
}

# Because you can't call an aliased table by it's original name
function get_main_table_alias($type) {
	$ta = 't1'; // ta = table alias
	switch ($type) {
		case 'contact':		$ta = 'c'; break;
		case 'group':		$ta = 'g'; break;	
		case 'groupmate':	$ta = 'lcg'; break;	
		case 'location':	$ta = 'lo'; break;
		case 'team':		$ta = 't'; break;
		case 'teammate':	$ta = 'ltu'; break;
		case 'user':		$ta = 'u'; break;
	}
	return $ta;
}

function get_secondary_table_alias($type) {
	# used in [new_report] and [top_report]
	$ta = 't2'; // t2 DNE but we could make it exist if it is convenient
	switch($type) {
		case 'teammate':	$ta = 't'; break; // team
	}
	return $ta;
}

/*
Potential Different Listing Behavior ON:
$type . '_view'
$type . '_list'
'feed_atom'
*/

# problem is that we need unique values... without them it is impossible to discern which generic values to keep and which to hide. ie)
# _
# _
# _
# but if we have unique value we can know which NOT to display
# user_name_spacer = _
# contact_name_spacer = _
# team_name_spacer = _
#
# in this way we have the best of both worlds... a specific value referring to a generic one...
#
# after getting all the fields we would need additional function for hiding stuff

function minimize_display_structure(& $array, $display) {

}


function minimize_listing_structure(& $array, $view_type, $list_type) {

	switch($view_type) {
		case 'contact':
		case 'user':
			switch($list_type) {
				case 'translation':
				case 'category':
				case 'dialect':
				case 'feed':
				case 'feedback':
				case 'group':
				case 'incident':
				case 'item':
				case 'jargon':
				case 'location':
				case 'minder':
				case 'news':
				case 'tag':
				case 'team':
				case 'meritopic':
				case 'meripost':
					unset($array['source']);
					unset($array['source_spacer']);
				break;
				case 'login':
				case 'teammate':
					unset($array['team_name_spacer']);
					unset($array['user_name']);
				break;
				case 'rating':
					# todo structure like offers
				break;
				case 'metail':
					unset($array['user_name']);
					unset($array['subject_endline']);
				break;
				case 'message':
				case 'invited':
				break;
				case 'user':
					# not really sure what to do with the user page either...
				case 'contact':
					# useful case only for adding contacts
					# todo helpful user message for this 2012-04-12 vaskoiii
				break;
				case 'note':
					unset($array['contact_name']);
					unset($array['subject_endline']);
				break;
			}
		break;
		case 'incident':
			switch($list_type) {
				case 'feedback':
					unset($array['source_spacer']);
					unset($array['incident_name']);
				break;
			}
		break;
		case 'team':
			switch($list_type) {
				case 'team':
					# just like the other cases with user/user and contact/contact 2012-04-12 vaskoiii
				break;
				case 'teammate':
					unset($array['team_name']);
					unset($array['team_name_spacer']);
					unset($array['source']);
					unset($array['source_spacer']);
					unset($array['direction_right_name']);
				break;
			}
		break;
		case 'group':
			switch($list_type) {
				case 'group':
					# never displays anything unless you are in your group 2012-04-12 vaskoiii
				break;
				case 'groupmate':
					unset($array['group_name']);
					unset($array['group_name_spacer']);
				break;
			}
		break;
		case 'location':
			switch($list_type) {
				case 'location':
					# another awesome case
				break;
				case 'user':
					unset($array['location_name']);
					unset($array['user_name_spacer']);
				break;
			}
		break;
		case 'meritopic':
			switch($list_type) {
				case 'meripost':
					unset($array['meritopic_name']);
					unset($array['source_spacer']);
				break;
			}
		break;
	}

}

function get_mask_author($type, $display) {
	# $display currently has 3 types: main, feed, email 2012-02-26 vaskoiii

	$array = array(
		'source' => 'user_name',
		'source_spacer' => '_'
	);
	switch($type) {
		# case 'feed': # uncomment if feeds are only seen by you 2012-04-04 vaskoiii
		case 'contact':
		case 'groupmate':
		case 'note':
			if ($display == 'feed')
				$array = array(
					'source' => 'my_user_name',
					'source_spacer' => '_',
				);
			else
				$array = array();
		break;
		case 'invited':
		case 'rating':
			$array = array(
				'source' => 'source_user_name'
			);
		break;
		case 'offer':
		case 'transfer': 
			if ($display == 'feed')
				$array = array(
					'source' => 'source_user_name',
					'source_spacer' => '_');
			else
				$array = array();
		break;
		case 'login':
		case 'user':
		case 'metail':
			if ($display == 'feed')
				$array = array(
					'source' => 'user_name',
					'source_spacer' => '_'); # Repeat info but makes the most sense. (Author of the user is that user) 2012-02-27 vaskoiii
			else
				$array = array();
		break;
		case 'teammate':
			$array = array(
				'source' => 'team_owner',
				'direction_right_name' => 'direction_right_name');
		break;
	}
	return $array;
}

function get_mask_subject($type, $display) {
	# $display is used but barely. It is still good to have because it allows more flexibility and doesnt hurt 2012-02-26 vaskoiii
	$array = array();
	switch($type) {
		case 'category':
		case 'tag':
			$array = array(
				'tag_name' => 'tag_name'
			);
		break;
		case 'contact':
		case 'dialect':
		case 'feed':
		case 'news':
			$array = array(
				$type . '_name' => $type . '_name',
			);
		break;
		case 'location':
		case 'group':
			$array = array(
				$type . '_name' => $type . '_name',
				$type . '_name_spacer' => '_',
				$type . '_children' => $type . '_children',
			);
		break;
		case 'jargon':
		case 'translation': 
			$array = array(
				'translation_name' => 'translation_name',
			);
		break;
		case 'feedback':
			$array = array(
				'incident_name' => 'incident_name',
			);
		break;
		case 'groupmate':
			$array = array(
				'group_name' => 'group_name',
				'group_name_spacer' => '_',
				'contact_name' => 'contact_name',
			);
		break;
		case 'incident':
			$array = array(
				'incident_name' => 'incident_name',
				'incident_name_spacer' => '_',
				'phase_name' => 'phase_name',
			);
		break;
		case 'invited':
			$array = array(
				'direction_right_name' => 'direction_right_name',
				'destination_user_name' => 'destination_user_name',
			);
		break;
		case 'item':
			$array = array(
				'tag_name' => 'tag_name',
				'tag_name_spacer' => '_',
				'status_name' => 'status_name',
			); 
		break;
		case 'login':
		case 'metail':
			$array = array(
				'user_name' => 'user_name',
			);
		break;
		case 'meripost':
			$array = array(
				'meritopic_name' => 'meritopic_name',
				'meritopic_spacer' => '_',
				'meritype_name' => 'meritype_name',
			);
		break;
		case 'meritopic':
			$array = array(
				'meritopic_name' => 'meritopic_name',
			);
		break;
		case 'note':
			$array = array(
				'contact_name' => 'contact_name',
			);
		break;
		case 'rating':
			$array = array(
				'direction_right_name' => 'direction_right_name',
				'destination_user_name' => 'destination_user_name',
				'destination_user_name_spacer' => '_',
				'grade_name' => 'grade_name',
			);
		break;
		case 'team':
			$array = array(
				'team_name' => 'team_name',
			); 
		break;
		case 'teammate':
			$array = array(
				'team_name' => 'team_name',
				'team_name_spacer' => '_',
				'user_name' => 'user_name',
			);
		break;
		case 'minder':
			$array = array(
				'kind_name' => 'kind_name',
				'kind_name_spacer' => '_',
				'kind_name_translation_name' => 'kind_name_translation_name',
			);
		break;
		case 'offer':
		switch ($display) {
			case 'feed':
				$array = array(
					'direction_right_name' => 'direction_right_name',
					'destination_user_name' => 'destination_user_name',
					'destination_user_name_spacer' => '_',
					'offer_name' => 'offer_name',
				);
			break;
			default:
				$array = array(
					'way_name' => 'way_name',
					'corresponding_user_name' => 'corresponding_user_name',
					'corresponding_user_name_spacer' => '_',
					'offer_name' => 'offer_name',
				);
			break;
		}
		break;
		case 'transfer': 
		switch($display) {
			case 'feed':
				$array = array(
					'direction_right_name' => 'direction_right_name',
					'destination_user_name' => 'destination_user_name',
					'destination_user_name_spacer' => '_',
					'tag_name' => 'tag_name',
				);
			break;
			default:
				$array = array(
					'way_name' => 'way_name',
					'corresponding_user_name' => 'corresponding_user_name',
					'corresponding_user_name_spacer' => '_',
					'tag_name' => 'tag_name',
					'tag_name_spacer' => '_',
					'status_name' => 'status_name',
				);
			break; 
		}
		break;
		case 'user':
			$array = array(
				'user_name' => 'user_name',
				'user_name_spacer' => '_',
				'location_name' => 'location_name'
			);
		break;
	}
	return $array;
}

function lt_subextra($type) {
	$array = array();
	switch($type) {
		case 'category':
		case 'location':
		case 'tag':
		case 'minder':
			$array = array(
				$type . '_known' => 'known',
			);
		break;
		case 'jargon':
		case 'translation':
			$array = array(
				'translation_default_boolean_name' => 'translation_default_boolean_name',
			);
		break;
		case 'contact':
		case 'incident':
		case 'meritopic':
		case 'user':
			$array = array(
				$type . '_children_spacer' => '_',
				$type . '_children' => $type . '_children',
			);
		break;
		case 'team':
			$array = array(
				$type . '_childen_spacer' => '_',
				$type . '_children' => $type . '_children',
				$type . '_known' => 'known',
			); 
		break;
	}
	return $array;
}

function get_mask_endline($type, $display, $child) {
	$array = array();
	switch($type) {
		# todo les wordy probably to show which guys dont have the endline
		case 'category':
		case 'dialect':
		case 'feedback':
		case 'group':
		case 'incident':
		case 'item':
		case 'jargon':
		case 'meripost':
		case 'meritopic':
		case 'news':
		case 'offer':
		case 'rating':
		case 'tag':
		case 'team':
		case 'transfer': 
		case 'translation':
		case 'metail':
		case 'note':
			$array = array(
				'subject_endline' => '.'
			);
		break;
	}
	return $array;
}

function lt_checkbox($type) {
	$array = array();
	switch($type) {
		case 'category':
		case 'contact':
		case 'feed':
		case 'group':
		case 'groupmate':
		case 'invite':
		case 'item':
		case 'location':
		case 'note':
		case 'metail':
		case 'offer':
		case 'tag':
		case 'team':
		case 'teammate':
		case 'minder':
		case 'transfer': 
			$array = array('checkbox');
		break;	
	}
	return $array;
}

# feed specific function repeats a little of the work done in the layer headers. 2012-02-27 vaskoiii
function lt_add_more($type) {
	$array = array('add_more');
	switch($type) {
		case 'feed':
			$array = array('!');
		break;
	}
	return $array;
}

function lt_body($type) {
	$array = array();
	switch($type) {
		case 'dialect':
		case 'feedback':
		case 'group':
		case 'incident':
		case 'item':
		case 'meripost':
		case 'meritopic':
		case 'metail':
		case 'news':
		case 'note': 
		case 'offer':
		case 'rating':
		case 'team':
		case 'transfer': 
			$array = array($type . '_description');
		break;
		# case 'feed':
		# 	$array = array('ts_link'); # show link to the "list" page (not the "feed") - useful as a bookmark - 2012-02-27 vaskoiii
		# break;
		# todo update integrate 2012-02-27 vaskoiii
		
		#case 'tag':
			# $array = array('parent_tag_name', '+', 'tag_name', '=', 'tag_translation_description');
		#break;
		# todo update integrate 2012-02-27 vaskoiii
		case 'category':
		case 'tag':
			$array = array('tag_path', '=', 'tag_translation_description');
		break;
		# todo update integrate 2012-02-27 vaskoiii
		case 'jargon':
			$array = array('kind_name', '+', 'tag_path', '+', 'dialect_name', '=', 'translation_description');
		break;
		case 'translation': 
			// switching r2 and r1 in this case a bit may make more sense.
			$array = array('kind_name', '+', 'kind_name_name', '+', 'dialect_name', '=', 'translation_description');
		break;
	}
	return $array;
}

function lt_more($type) {
	return array('more_toggle');
}

function lt_detail($type) {
	$array = array();
	switch($type) {
		case 'feed':
			$array = array('dialect_name', '_');
		break;
		case 'feedback':
			$array = array('incident_id', '_');
		break;
		case 'item':
			# ideally translate each level in the tag path. For now just the parent is translated 2012-04-24 vaskoiii
			# choose from parent_tag_path, parent_tag_translation_name
			$array = array('parent_tag_translation_name', '_', 'team_required_name', '_', 'tag_translation_description', '.');
			# todo: index_parent_tag
			# - tag_id
			# - parent_tag_id
			# - level?
			# (will allow non recursive lookups of parent tag paths) to translate each level in the category path
		break;
		case 'transfer': 
				$array = array('parent_tag_path', '_', 'tag_translation_description', '.');
		break;
		case 'location':
			 $array = array('location_coordinate', '_');
		break;
		case 'meripost':
			$array = array('meritopic_id', '_');
		break;
		case 'news':
		case 'metail':
		case 'rating':
			$array = array('team_required_name', '_');
		break;
		case 'category':
		case 'tag':
			# todo if we want to show the untranslated category path
			# $array = array('tag_path', '_');
		break;
		case 'minder':
			$array = array('kind_name_path');
		break;
	}
	$array = array_merge($array, array('modified', '_', 'uid'));
	return $array;
}

function lt_action($type) {
	$array = array(
		'edit' => 'edit',
		'translate' => 'translate',
		'delete' => 'delete',
	);
	if (get_child_listing_type($type))
		$array[$type . '_view'] = $type . '_view';
	# uncomment if you want to have children linking to the parent 2012-04-19 vaskoiii
	$s1 = get_parent_listing_type($type);
	if ($s1)
		$array[$s1 . '_view'] = $s1 . '_view';
	switch($type) {
		# remember/forget
		case 'team':
		case 'location':
		case 'category':
		case 'tag':
		case 'minder':
			$array[$type . '_minder'] = $type . '_minder';
		break;
	}

	return  $array;
}

# todo fix this 2012-02-28 vaskoiii
function lt_conglom($type) {
	// actually this is a conglomeration of the other functions to add the appropriate links at the end.
	$array = array_merge(
		lt_checkbox($type),
		#lt_author($type),
		#lt_subject($type),
		lt_subextra($type),
		#lt_endline($type),
		lt_body($type),
		lt_detail($type)
	);
	foreach ($array as $k1 => $v1) {
		$array[$k1] = $v1 . '_*';
		# todo unset superfluous data 2012-02-27 vaskoiii
		# use $data['result']['display'] if necessary.
	}
	return $array;
}