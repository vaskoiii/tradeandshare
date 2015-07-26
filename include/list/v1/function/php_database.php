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
	default:
		return false;
} }

function get_load_opposite_edit($load) {
switch($load) {
	case 'view':
	case 'motion':
		return 'action';
	case 'list':
	case 'view':
		return 'motion';
	default:
		return false;
} }

function get_load_parent($load) {
switch($load) {
	case 'view':	return 'motion';
	case 'list':	return 'action';
	default:	return false;
} }

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

function get_base_type($type) {
switch($type) {
	case 'category':	return 'tag';
	case 'jargon':		return 'translation';
	default:		return $type;
} }

function get_table_name($type) {
switch($type) {
	case 'category':	return 'tag';
	case 'jargon':		return 'translation';
	case 'groupmate':	return 'link_contact_group';
	case 'teammate':	return 'link_team_user';
	default:		return $type;
} }

function get_parent_listing_type($type) {
switch($type) {
	case 'category':	return 'category';
	case 'feedback':	return 'incident';
	case 'groupmate':	return 'group';
	case 'meripost':	return 'meritopic';
	case 'metail':		return 'user'; # Super Important!
	case 'note':		return 'contact'; # Super Important!
	case 'teammate':	return 'team';
	case 'user':		return 'location';
	default:		return false;
} }

function get_child_listing_type($type) {
switch($type) {
	case 'category':	return 'category';
	case 'contact':		return 'note'; # Super Important!
	case 'group':		return 'groupmate';
	case 'incident':	return 'feedback';
	case 'location':	return 'user';
	case 'meritopic':	return 'meripost';
	case 'team':		return 'teammate';
	case 'user':		return 'metail'; # Super Important!
	default:		return false;
} }

function get_main_table_alias($type) {
switch ($type) {
	# case 'score': return 'se';
	# case 'comment': return 'cmt';
	# case 'carry': return 'cry';
	case 'contact':		return 'c';
	case 'group':		return 'g';
	case 'groupmate':	return 'lcg';
	case 'location':	return 'lo';
	case 'team':		return 't';
	case 'teammate':	return 'ltu';
	case 'user':		return 'u';
	default:		return 't1';
} }

function get_secondary_table_alias($type) {
switch($type) { # used in [new_report] and [top_report]
	case 'teammate':	return 't'; break; # team
	default:		return 't2'; # t2 DNE but we could make it exist if it is convenient
} }


function minimize_display_structure(& $array, $display) {
}

# eliminate redundant on view pages
function minimize_listing_structure(& $array, $view_type, $list_type) {
switch($view_type) {
	case 'contact':
	case 'user':
	switch($list_type) {
		case 'category':
		case 'dialect':
		case 'feed':
		case 'feedback':
		case 'group':
		case 'incident':
		case 'item':
		case 'jargon':
		case 'location':
		case 'meripost':
		case 'meritopic':
		case 'minder':
		case 'news':
		case 'tag':
		case 'team':
		case 'translation':
		case 'vote':
			unset($array['source']);
			unset($array['source_spacer']);
		break;
		case 'login':
		case 'teammate':
			unset($array['team_name_spacer']);
			unset($array['user_name']);
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
} }

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
		case 'carry':
		case 'score':
		case 'invited':
		case 'transfer': 
			$array = array(
				'source' => 'source_user_name',
			);
		break;
		case 'offer':
			if ($display == 'feed')
				$array = array(
					'source' => 'source_user_name',
					'source_spacer' => '_',
				);
			else
				$array = array();
		break;
		case 'login':
		case 'user':
		case 'metail':
			if ($display == 'feed')
				$array = array(
					'source' => 'user_name',
					'source_spacer' => '_',
				); # Repeat info but makes the most sense. (Author of the user is that user) 2012-02-27 vaskoiii
			else
				$array = array();
		break;
		case 'teammate':
			$array = array(
				'source' => 'team_owner',
				'direction_right_name' => 'direction_right_name',
			);
		break;
	}
	return $array;
}

function get_mask_subject($type, $display) {
	# $display is used but barely. It is still good to have because it allows more flexibility and doesnt hurt 2012-02-26 vaskoiii
	$array = array();
	switch($type) {
		# todo no need for this page
		case 'membership':
			$array = array(
				'channel_name' => 'channel_name',
				'channel_spacer' => '_',
				'remaining_time' => 'remaining_time', # actually min time/max time (calculated value)
			);
		break;
		case 'transaction':
			$array = array(
				'transaction_value' => 'transaction_value',
			);
		break;
		case 'renewal':
			$array = array(
				'channel_name' => 'channel_name',
				'channel_spacer' => '_',
				'cycle_id' => 'cycle_id',
				'cycle_id_spacer' => '_',
				'point_name' => 'point_name',
				'point_name_spacer' => '_',
				# 'renewal_value' => 'renewal_value',
				# 'renewal_value_spacer' => '_',
				'renewal_start' => 'renewal_start',
				'renewal_start_spacer' => '_',
				'timeframe_name' => 'timeframe_name',
			);
		break;
		case 'cycle':
			$array = array(
				'channel_name' => 'channel_name',
				'channel_name_spacer' => '_',
				'cycle_start' => 'cycle_start',
				'cycle_start_spacer' => '_',
				'point_name' => 'point_name',
				'point_name_spacer' => '_',
				'timeframe_name' => 'timeframe_name',
			);
		break;
		case 'cost':
			$array = array(
				'channel_name' => 'channel_name',
				'channel_spacer' => '_',
				'cost_value' => 'cost_value',
			);
		break;
		case 'category':
		case 'tag':
			$array = array(
				'tag_name' => 'tag_name',
			);
		break;
		case 'channel':
			$array = array(
				$type . '_name' => $type . '_name',
				'channel_name_spacer' => '_',
				'channel_offset' => 'channel_offset',
				'channel_offset_spacer' => '_',
				'channel_value' => 'channel_value',
				'channel_value_spacer' => '_',
				'channel_percent' => 'channel_percent',
				'channel_percent_spacer' => '_',
				'timeframe_name' => 'timeframe_name',
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
		case 'comment':
			$array = array(
				'kind_name' => 'kind_name',
				'kind_name_spacer' => '_',
				'kind_name_id' => 'kind_name_id',
			);
		break;
		case 'carry':
			$array = array(
				'direction_right_name' => 'direction_right_name',
				'destination_user_name' => 'destination_user_name',
				'destination_user_name_spacer' => '_',
				'score_value' => 'score_value',
				'score_value_divide' => '/',
				'carry_value' => 'carry_value',
				# not useful for payout computation:
				# 'carry_value_equal' => '=',
				# 'carry_value_result' => 'carry_value_result',
			);
		break;
		case 'score': 
			$array = array(
				'direction_right_name' => 'direction_right_name',
				'destination_user_name' => 'destination_user_name',
				'destination_user_name_spacer' => '_',
				'mark_id' => 'mark_id',
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
		case 'vote':
			$array = array(
				'tag_name' => 'tag_name',
				'tag_name_spacer' => '_',
				'decision_name' => 'decision_name',
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
		case 'transfer': 
		switch($display) {
			default;
				$array = array(
					'direction_right_name' => 'direction_right_name',
					'destination_user_name' => 'destination_user_name',
					'destination_user_name_spacer' => '_',
					'tag_name' => 'tag_name',
				);
			break; 
		}
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
		case 'comment':
		case 'carry':
		case 'score': 
			$array = array(
				# todo
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
		# todo less wordy probably to show which guys dont have the endline
		case 'comment':
		case 'carry':
		case 'score':

		case 'category':
		case 'channel':
		case 'dialect':
		case 'feedback':
		case 'group':
		case 'incident':
		case 'item':
		case 'jargon':
		case 'meripost':
		case 'meritopic':
		case 'metail':
		case 'news':
		case 'note':
		case 'offer':
		case 'tag':
		case 'team':
		case 'transfer': 
		case 'translation':
		case 'vote':
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
		case 'vote':
			$array = array('checkbox');
		break;	
	}
	return $array;
}

# feed specific function repeats a little of the work done in the layer headers. 2012-02-27 vaskoiii
function lt_add_more($type) {
	switch($type) {
		case 'feed':
			$array = array('!');
		break;
		case 'invited':
		case 'transfer':
			$array = array(
				'_',
				'add_more'
			);
		break;
		default:
			$array = array('add_more');
		break;
	}
	return $array;
}

function lt_body($type) {
	$array = array();
	switch($type) {
		case 'comment':
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
		case 'team':
		case 'transfer': 
		case 'vote':
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
		case 'channel':
			# having a channel work across multiple languages may cause divergence
			# ie. 2 different languages can view the same channel with a different purpose
			# $array = array('channel_translation_description');
			$array = array('channel_description');
		break;
		case 'category':
		case 'tag':
			$array = array('tag_path', '=', 'tag_translation_description');
		break;
		# todo update integrate 2012-02-27 vaskoiii
		case 'carry':
			# todo add channel_name
			$array = array('cycle_id',);
		break;
		case 'score':
			$array = array('kind_name', '_', 'kind_name_id');
		break;
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
		case 'transaction':
			$array = array(
				'class_id' =>'class_id',
				'class_id_spacer' => '_',
				'class_name_id' => 'class_name_id',
				'class_name_spacer' => '_',
			);
		break;
		case 'feed':
			$array = array('dialect_name', '_');
		break;
		case 'feedback':
			$array = array('incident_id', '_');
		break;
		case 'item':
		case 'transfer':
		case 'vote':
			# ideally translate each level in the tag path. For now just the parent is translated 2012-04-24 vaskoiii
			# choose from parent_tag_path, parent_tag_translation_name
			$array = array('parent_tag_translation_name', '_', 'team_required_name', '_', 'tag_translation_description', '.');
			# todo: index_parent_tag
			# - tag_id
			# - parent_tag_id
			# - level?
			# (will allow non recursive lookups of parent tag paths) to translate each level in the category path
		break;
		case 'location':
			 $array = array('location_coordinate', '_');
		break;
		case 'meripost':
			$array = array('meritopic_id', '_');
		break;
		# case 'score': placeholder
		case 'news':
		case 'metail':
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
		'like' => 'like',
		'dislike' => 'dislike',
		'comment' => 'comment',
		'edit' => 'edit',
		'translate' => 'translate',
		'delete' => 'delete',
		'import' => 'import',
		'export' => 'export',
		'digest' => 'digest',
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
		case 'feed':
			$array[$type . '_link'] = $type . '_link';
		break;
	}
	return  $array;
}

# todo fix this 2012-02-28 vaskoiii
function lt_conglom($type) {
	# actually this is a conglomeration of the other functions to add the appropriate links at the end.
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
