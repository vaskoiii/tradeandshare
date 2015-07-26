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

# Contents/Description: The template and more viewable code for the listings
# Warning: do NOT get too crazy with the abbreviations... use references instead if necessary.

# Function calling function (Rearranges Parameters)
#function print_listing_template(& $listing, & $key, & $translation, $load, $display, $part, $login_user_id, & $style = null) {
function print_listing_template(& $listing, & $key, & $translation, $load, $display, $part, $login_user_id, & $style = null, $type = '') {

	# todo fix this hack and the extra $type param
	global $x;
	if (!$type) {
		$type= $x['load'][$load]['type'];
	}
	$a1 = get_listing_template_structure($type, $display, $part);
	if ($load == 'list')
		minimize_listing_structure($a1, $x['load']['view']['type'], $x['load']['list']['type']);
	if (!$style)
		$style = array();
	echo get_listing_template_output($a1, $listing, $key, $translation, $load, $display, $part, $login_user_id, $style, $type);
}

# the old listing map became invalid because email, feed, list dont always use the same order
#function get_listing_template_structure(& $listing, & $key, & $translation, $load, $display, $part, $login_user_id) {
function get_listing_template_structure($type, $display, $part, $child = false) {

	switch($display) {
		case 'main':
		switch ($part) {
			case 'checkbox':
			case 'title':
			case 'summary':
			case 'detail':
			case 'action':
			case 'link':
				# placeholders
			break;
			case 'all':
				# should call itself for all of the above cases 2012-02-27 vaskoiii
				$a1 = array_merge(
					lt_checkbox($type),
					get_mask_author($type, 'main', $child),
					get_mask_subject($type, 'main', $child),
					lt_subextra($type),
					get_mask_endline($type, 'main', $child),
					lt_body($type),
					lt_more($type),
					lt_detail($type),
					lt_action($type)
					#lt_conglom($type) maybe it is better not to use weird symbols
				);
				# echo '<pre>'; print_r($a1); echo '</pre>'; exit;
				return $a1;
			break;
		}
		break;
		case 'email': # functions calls will have to be different here because of $style
		switch($part) {
			case 'from':
			case 'subject':
			case 'to':
				# placeholders
			break;
			case 'body':
				$ff_external = 1;
				return array_merge(
					get_mask_subject($type, 'main', $child),
					lt_subextra($type),
					get_mask_endline($type, 'main', $child),
					lt_body($type)
				);
			break;
		}
		break;
		case 'feed':
		switch($part) {
			#case 'author_name':
			#	get_mask_author($type, 'feed');
			#break;
			case 'author_name':
			case 'author_email':
			case 'id':
			case 'link':
			case 'updated':
				# standalone data only
			break;
			case 'title':
				return get_mask_subject($type, 'feed');
			break;
			case 'summary':
				$my_array = lt_body($type);
				if (empty($my_array)) {
					return array_merge(
						get_mask_author($type, 'feed'),
						lt_add_more($type)
					);
				} else {
					return array_merge(
						lt_body($type),
						array('_'),
						get_mask_author($type, 'feed'),
						lt_add_more($type)
					);
				}
			break;
		}
		break;
	}
}

function get_listing_template_output($structure, & $listing, & $key, & $translation, $load, $display, $part, $login_user_id, & $style = null, $type = '') {
	global $config;
	global $x; # ok as a global but we can not base things off of $x['page']['name'] 2012-03-03 vaskoiii
	global $_SERVER;

	# alias
	if (!$type)
		$type = $x['load'][$load]['type'];
	# $style is the previous $data['css'] array;
	if ($style) {
		$color = & $style['text_style']['color'];
	}
	else {
		$color = ''; # prevent adding extra crap to the global array if not needed 2012-02-29 vaskoiii
	}

	# Cant always use the '_' case because sometimes the spacer is conditional
	$spacer = '<span class="spacer">' .  $config['spacer'] . '</span>';
	if ($display == 'email') {
	if ($color['spacer']) {
		# email requires special treatment to ensure the correct colors are used
		# Needed on Thunderbird with white text on black font 2013-10-10 vaskoiii	
		$spacer = '<span style="color: ' . $color['description'] . ';">' .  $config['spacer'] . '</span>';
	} }
	# Description Attribute
	$dattrib = '';
	if ($display == 'email') {
	if ($color['description']) {
		$dattrib = 'style="color:' . $color['description'] . ';"';
	} }


	$key_user = & $key['user_id']['result'];
	$key_contact = & $key['contact_id']['result'];

	$grab = '';

	$ff_level = 1;
	$glue = '';
	switch ($display) {
		case 'main':
			# we don't need loooooooong links
		break;
		case 'feed':
		case 'email':
			# set up {go_back} link
			# todo unsure if this will still work 2012-02-06 vaskoiii
			$ff_level = - $x['level']; // should bring ff() to level 0 where is won't add any additional URL vars.
			$glue = '&';
			$href_append = '1' . $config['mark'] . 'lock_user_id=' . (int)$login_user_id;
			$href_prepend = 'https://' . $_SERVER['HTTP_HOST'] . '/' . $type . '_list/'; // can't start with / for emails...
		break;
	}

	# used for relative links - works if / is the working directory 2012-02-16 vaskoiii
	# todo use $href_prepend and $href_append as they are more accurate!
	$href_rss_start = 'href="';

	# Do it!
	foreach ($structure as $k1 => $v1) {
	# echo $v1 . '<br />';
	if (preg_match('/^todo/', $v1)) {
		if (!preg_match('/_\*$/', $v1))
			$grab .= ' ' . $v1 . ' ';
	}
	else switch($v1) {
		case 'carry_value':
		case 'score_value': # may need to format to 2 decimal places
		case 'id':
		case 'mark_id':
		case 'kind_name_id':
			# used for testing
			$grab .= $listing[$v1];
		break;
		case 'cycle_id':
			$grab .= tt('element', 'cycle_uid') . ': ' . (int)$listing[$v1];
		break;
		case 'transaction_value':
		case 'cost_value':
		case 'channel_value':
		// case 'cycle_value':
		case 'renewal_value':
			$grab .= '$' . $listing[$k1]; # $ can eventually be changed to a symbol for TS Credit
		break;

		# todo print human readable data for:
		case 'class_id':
		case 'class_name_id':
			$grab .= to_html($k1) . ': ' . $listing[$k1]; 
		break;
		case 'channel_percent':
			$grab .= (int)$listing[$k1] . '%';
		break;
		case 'channel_offset':
		// case 'cycle_start':
			$grab .= $listing[$k1] . ' day';
		break;
		case 'remaining_time':
			/*
			$s1 = strtotime($listing['membership_start']) + 30*24*3600;
			$s2 = strtotime(date('Y-m-d'));
			$s3 = $s1 - $s2;
			if ($s3 < 0)
				$s3 = 0;
			$s3 /= 86400;
			$grab .= $s3;
			*/
			$grab .= 'forever';
			
		break;
		case 'checkbox':
			# temporary disabled fields while we restore functionality to TS 2012-02-16 vaskoiii
		break;
		case 'translate':
			if (0) # disable until usable 2012-04-21 vaskoiii
			if ($load == 'list' && $type == 'tag') {
				# todo fix hardcode for kind id 2012-03-02 vaskoiii
				$grab .= $spacer . ' <a href="jargon_edit/?parent_tag_id=' . (int)$listing['parent_tag_id'] . '&amp;kind_id=11&amp;kind_name_id=' . (int)$listing['tag_id'] . '"><span class="translate">' . tt('element', 'translate') . '</span></a> ';
				$grab .= ' (<a href="jargon_list/?parent_tag_id=' . (int)$listing['parent_tag_id'] . '&amp;kind_id=11&amp;kind_name_id=' . (int)$listing['tag_id'] . '"><span class="translate">#</span></a>) ';
			}
		break;
		case 'judge':
			if ($type == 'transfer' || $type == 'item' || $type == 'vote')
				$grab .= $spacer . '<a href="vote_list/' . ff('action_tag_id=' . $listing['tag_id'] . '&expand[0]=action&focus=action') . '"><span class="judge">' . tt('element', $k1) . '</span></a>';
		break;
		case 'import':
			if ($type == 'transfer' || $type == 'item' || $type == 'vote')
				$grab .= $spacer . '<a href="item_list/' . ff('action_tag_id=' . $listing['tag_id'] . '&expand[0]=action&focus=action') . '"><span class="import">' . tt('element', $k1) . '</span></a>';
		break;
		case 'digest':
			switch($type) {
				case 'cycle':
					$grab .= $spacer . '<a href="score_report/' . ff('channel_parent_id=' . (int)$listing['channel_parent_id'] . '&cycle_id=' . $listing['cycle_id']) . '"><span class="digest">' . tt('element', $k1) . '</span></a>';
				break;
			}
		break;
		case 'export':
			if ($type == 'transfer' || $type == 'item' || $type == 'vote')
				$grab .= $spacer . '<a href="transfer_list/' . ff('action_tag_id=' . $listing['tag_id'] . '&expand[0]=action&focus=action') . '"><span class="export">' . tt('element', $k1) . '</span></a>';
		break;
		case 'like':
		case 'dislike':
			# confirm
			# $grab .= ' - <a href="./result_process/?' . to_html($k1) . '=1&list_name=' . to_html($type) . '&q=' . to_url(ltrim(ff('', 1), '?')) . '&' . ('row[]=' . (int)$listing[$type . '_id'] ) . '"><span class="' . to_html($k1) . '">' . tt('element', $k1) . '</span></a> ';
			# todo alternative really needed? ( seems to do the same as result_process )
			# $grab .= ' - <a href="./selection_action/?action=' . to_html($k1) . '&list_name=' . to_html($type) . '&' . ('row[]=' . (int)$listing[$type . '_id'] ). '&q=' . to_url(ltrim(ff('', 1), '?'))  . '"><span class="' . to_html($k1) . '">' . tt('element', $k1) . '</span></a> ';
			# no confirm
			switch ($type) {
				case 'cycle':
					# no user input
				break;
				default:
					$grab .= ' - <a href="./selection_process/?action=' . to_html($k1) . '&list_name=' . to_html($type) . '&' . ('row[]=' . (int)$listing[$type . '_id'] ). '&q=' . to_url(ltrim(ff('', 1), '?'))  . '"><span class="' . to_html($k1) . '">' . tt('element', $k1) . '</span></a> ';
				break;
			}
		break;
		case 'comment':
			switch ($type) {
				case 'cycle':
					# no user input
				break;
				default:
					$grab .= ' - <a href="./result_process/?' . to_html($k1) . '=1&list_name=' . to_html($type) . '&q=' . to_url(ltrim(ff('', 1), '?')) . '&' . ('row[]=' . (int)$listing[$type . '_id'] ) . '"><span class="' . to_html($k1) . '">' . tt('element', $k1) . '</span></a> ';
				break;
			}
		break;
		case 'report':
			# todo allow reporting similar to comment
		break;
		case 'delete':
		if ($load == 'list') {
		switch ($type) {
			# todo make deletable: 2012-04-19 vaskoiii
			case 'jargon':
			case 'translation':
			break;
			# not deletable:
			case 'channel':
			case 'cycle':
			case 'renewal':
			case 'incident':
			case 'meritopic':
			case 'tag':
			case 'location':
			case 'login':
			case 'dialect':
			case 'minder': # forgettable is the terminology instead of delete 2012-04-21 vaskoiii
				# no delete
			break;
			case 'category':
				# anyone can delete/add a category - It is a superficial link table
				$grab .= ' - <a href="./result_process/?delete=1&list_name=' . to_html($type) . '&q=' . to_url(ltrim(ff('', 1), '?')) . '&' . ('row[]=' . (int)$listing['tag_id'] ) . '"><span class="delete">' . tt('element', 'delete') . '</span></a> ';
			break;
			case 'score':
			# case 'carry': # compute automatically
				if ($listing['source_user_id'] == $login_user_id)
					$grab .= ' - <a href="./result_process/?delete=1&list_name=' . to_html($type) . '&q=' . to_url(ltrim(ff('', 1), '?')) . '&' . ('row[]=' . (int)$listing[$type . '_id'] ) . '"><span class="delete">' . tt('element', 'delete') . '</span></a> ';
			break;
			case 'offer':
			case 'transfer':
				# can you delete offers that arent yours? 2013-10-11 vaskoiii
				if ($listing['source_user_id'] == $login_user_id || $listing['destination_user_id'])
					$grab .= ' - <a href="./result_process/?delete=1&list_name=' . to_html($type) . '&q=' . to_url(ltrim(ff('', 1), '?')) . '&' . ('row[]=' . (int)$listing[$type . '_id'] ) . '"><span class="delete">' . tt('element', 'delete') . '</span></a> ';
			break;
			case 'teammate':
				if ($key['team_id']['result'][ $listing['team_id'] ]['team_owner_user_id'] == $login_user_id)
					$grab .= ' - <a href="./result_process/?delete=1&list_name=' . to_html($type) . '&q=' . to_url(ltrim(ff('', 1))) . '&' . ('row[]=' . (int)$listing[$type . '_id'] ) . '"><span class="delete">' . tt('element', 'delete') . '</span></a> ';
			break;
			case 'news':
			case 'metail':
			default:
				if ($listing['user_id'] == $login_user_id)
					$grab .= ' - <a href="./result_process/?delete=1&list_name=' . to_html($type) . '&q=' . to_url(ltrim(ff('', 1))) . '&' . ('row[]=' . (int)$listing[$type . '_id'] ) . '"><span class="delete">' . tt('element', 'delete') . '</span></a> ';
			break;
		} }
		break;
		case 'more_toggle':
			$s1 = $type;
			switch ($type) {
				case 'category':
				case 'tag':
					$s1 = 'tag';
				break;
				case 'jargon':
				case 'translation':
					$s1 = 'translation';
				break;
			}
			$s2 = '';
			 if ($load == 'view')
				$s2 = 'view_';
			$grab .= '<span class="spacer"> &gt;&gt; </span><a id="' . $s2 . to_html($s1) . '_id_' . (int)$listing[$s1 . '_id'] . '_toggle" href="javascript: more_toggle(\'' . $s2 . to_html($s1) . '_id_' . (int)$listing[$s1 . '_id'] . '\');">' 
					. tt('element', 'more', 'translation_name', $translation) 
				. '</a>'
				. '</p><p class="more" style="border: none; display: none; padding: 0px; margin: 0px 20px 10px 20px;" id="' . $s2 . to_html($s1) . '_id_' . (int)$listing[$s1 . '_id'] . '">';
		break;
		case 'private':
			$grab .= tt('element', 'private', 'translation_name', $translation);
		break;
		case '!':
		case '+':
		case '/':
		case '=':
			$grab .= ' ' . $v1 . ' ';
		break;
		case '_':
			$grab .= $spacer;
		break;
		case '.':
			$grab .= '<br />';
		break;
		case 'uid':
			$s1 = $type;
			if ($listing['list_type']) # for top_report.php 2012-02-29 vaskoiii
				$s1 = $listing['list_type'];
			ob_start(); ?> 
			<span class="uid"><?= tt('element', $s1 . '_uid', 'translation_name', $translation); ?>: <?= (int)$listing[ get_base_type($s1) . '_id']; ?></span><?
			$grab .= ob_get_clean();
		break;
		case 'ts_link':
			$grab .= a_link_replace(
				'https://' . $_SERVER['HTTP_HOST'] . '/'
				. $listing['page_name'] . '/'
				. ($listing['query'] ? '?' . $listing['query'] : '')
			);
		break;
		case 'way_name':
			$s1 = 'style="color: ' . (isset($color['direction_name']) ? $color['direction_name'] : '') . ';"';
			if ($listing['source_user_id'] == $login_user_id)
				$grab .= '<span ' . $s1 . ' class="direction_name"> &gt;&gt; </span>';
			else
				$grab .= '<span ' . $s1 . ' class="direction_name"> &lt;&lt; </span>';
		break;
		case 'direction_right_name':
			$grab .= '<span class="direction_name"> &gt;&gt; </span>';
		break;
		case 'meritopic_children':
		case 'group_children':
		case 'location_children':
		case 'team_children':
		case 'user_children':
		case 'contact_children':
		case 'incident_children':
			$e1 = explode('_', $v1);
			$grab .= '<span class="' . $v1  . '">' . ($key[$e1[0] . '_id']['result'][ $listing[$e1[0] . '_id'] ][get_child_listing_type($e1[0]) . '_count']
				? $key[$e1[0] . '_id']['result'][ $listing[$e1[0] . '_id'] ][get_child_listing_type($e1[0]) . '_count']
				: '0'
			) . '</span>';
		break;
		case 'my_user_name': # feed only
			if (!empty($x['feed_atom'])) {
				$grab .= ($x['feed_atom']['contact_name'] 
					? '<a href="' . $href_prepend . 'contact_view/' . ffm('list_name=&list_type=&lock_contact_id=' . (int)$x['feed_atom']['contact_id'] . $glue . $href_append, $ff_level) . '">' . $x['feed_atom']['contact_name'] . '</a>' 
					: '<a href="' . $href_prepend . 'user_view/' . ffm('list_name=&list_type=&lock_user_id=' . (int)$x['feed_atom']['user_id'] . $glue . $href_append, $ff_level) . '">' . to_html($config['unabstracted_prefix'] . $x['feed_atom']['user_name'] . $config['unabstracted_suffix']) . '</a>'
				);
			}
		break;
		case 'add_more': # feed only
			# doesnt make sense for:
			# contact_list - possible repeat {add_contact} - {add_contact}
			# feed_list - cant add feeds like this
			$grab .=  '<a href="' . $href_prepend . $type . '_edit/' . ff($href_append, $ff_level) . '">' . tt('element','add_' . $type, 'translation_name', $translation ) . '</a>';
		break;
		case 'contact_name':
			$i1 = 2; # print contact (do href mod)
			$i3 = 2; # print user (do href mod)

		       if ($load == 'view' && $type == 'contact') {
				$i3 = 1;
			}
			elseif ($load == 'list' && $type == 'contact') {
				$i1 = 1;
				$i3 = 1;
			}
			else
				$i1 = 1;
			if ($load == 'view' && ($type == 'user' || $type == 'contact')) {
				$i1 = 1;
				$i3 = 1;
			}

			if ($i1 == 1) {
				$grab .= '<a href="' . $href_prepend . 'contact_view/' . ffm('list_name=&list_type='
					.
					((int)$key_contact[  $listing['contact_id']  ]['user_id']
						?  '&lock_user_id=' . (int)$key_contact[  $listing['contact_id']  ]['user_id']
						: '&lock_contact_id=' .  $listing['contact_id'] 
					)
				. $glue . $href_append, $ff_level)
				.  '"><span class="contact_name">'. to_html($listing['contact_name']) . '</span></a>';
			}
			if ($i3 == 1) {
			if ($key_contact[ $listing['contact_id'] ]['user_id']) {
			if ($key_contact[ $listing['contact_id'] ]['user_name']) {
				$grab .= ' <a href="' . $href_prepend . 'user_view/' 
					. ffm('list_name=&list_type=&lock_user_id=' . $key_contact[ $listing['contact_id'] ]['user_id']  . $glue . $href_append, $ff_level) 
				. '"><span class="user_name">'
					. to_html($config['unabstracted_prefix'] . $key_contact[ $listing['contact_id'] ]['user_name'] . $config['unabstracted_suffix']) 
				. '</span></a>';
			} } }
		break;
		case 'team_owner':
			$i1 = 2; # print contact (href mod)
			$i2 = 2; # print user (href mod)

			$s1 = 'style="color: ' . (isset($color['link']) ? $color['link'] : '') .';"';
			$s2 = 'style="color: ' . (isset($color['contact_name']) ? $color['contact_name'] : '') . ';"'; 
			$s3 = 'style="color: ' . (isset($color['user_name']) ? $color['user_name'] : '') . ';"'; 
			if ( $key_user[ $listing['team_owner_user_id'] ]['contact_name'] )
				$i1 = 1;
			else
				$i2 = 1;
			if ($i1 == 1) {
				$grab .= '<a ' . $s1  . ' href="' . $href_prepend . 'contact_view/'
					. ffm('list_name=&list_type=&lock_user_id=' . (int)$listing['team_owner_user_id'] . $glue . $href_append, $ff_level)
					. '"><span ' . $s2 . ' class="contact_name">'
						. $key_user[ $listing['team_owner_user_id'] ]['contact_name']
					. '</span></a>';
			}
			if ($i2 == 1) {
				$grab .= ' <a ' . $s1 .  ' href="' . $href_prepend . 'user_view/'
					. ff('list_name=&list_type=&lock_user_id=' . (int)$listing['team_owner_user_id'] . $glue . $href_append, $ff_level)
					. '"><span ' . $s3 . ' class="user_name">'
						. to_html($config['unabstracted_prefix'] . $key_user[ $listing['team_owner_user_id'] ]['user_name'] . $config['unabstracted_suffix'])
					. '</span></a>';
			}
		break;
		case 'corresponding_user_name': # only call with source and destination user names.
			if ($listing['source_user_id'] != $login_user_id)
				$v1 = 'source_user_name';
			else
				$v1 = 'destination_user_name';
		# nobreak;
		case 'source_user_name':
		case 'destination_user_name':
		case 'user_name': 
			$i1 = 2; # print contact name (href mod)
			$i3 = 2; # print user name (href mod)
			$s1 = str_replace('_name', '_id', $v1);

			if ($load == 'view' && $type == 'user')
				$i1 = 1;
			elseif ($load == 'list' && $type == 'user') {
					$i1 = 1;
					$i3 = 1;
			}
			else {
				if ($key_user[ $listing[$s1] ]['contact_id'] && $key_user[ $listing[$s1] ]['contact_name'])
					$i1 = 1;
				else
					$i3 = 1;
			}
			if ($load == 'view' && ($type == 'user' || $type == 'contact')) {
				$i1 = 1;
				$i3 = 1;
			}

			if ($i1 == 1) {
			if ($key_user[ $listing[str_replace('_name', '_id', $v1)] ]['contact_name']) {
				$s1 = str_replace('_name', '_id', $v1);
				$grab .= '<a style="color: ' . (isset($color['link']) ? $color['link'] : '') .';" href="' . $href_prepend . 'contact_view/' 
					. ffm('list_name=&list_type=' .
						((int)$listing[$s1]
							?  '&lock_user_id=' . (int)$listing[$s1]
							: '&lock_contact_id=' . (int)$key_user[ $listing[$s1] ]['contact_id']
					)  . $glue . $href_append, $ff_level)
					. '"><span style="color: ' . (isset($color['contact_name']) ? $color['contact_name'] : '') . ';" class="contact_name">'
						. $key_user[ $listing[$s1] ]['contact_name'] 
					. '</span></a>';
			} }
			if ($i3 == 1) {
				$grab .= ' <a style="color: ' . (isset($color['link']) ? $color['link'] : '') .';" href="' . $href_prepend . 'user_view/' 
					. ffm('list_name=&list_type=&lock_user_id=' . to_url($listing[str_replace('_name', '_id', $v1)]) . $glue . $href_append, $ff_level) 
					. '"><span style="color: ' . (isset($color['user_name']) ? $color['user_name'] : '') . ';" class="' . $v1 . '">' 
						. to_html($config['unabstracted_prefix'] . $listing[$v1] . $config['unabstracted_suffix']) 
					. '</span></a>';
			}
		break;
		case 'contact_name_reply':
			if ($key_contact[ $listing['contact_id'] ]['user_id'] &&
				$key_contact[ $listing['contact_id'] ]['user_name']
			)
				$grab .= '<a href="' . $href_prepend . 'offer_edit/' . ff('user_id=' . (int)$key_contact[ $listing['contact_id'] ]['user_id'] . $glue . $href_append, $ff_level) . '"><span class="user_name">' . tt('element', 'reply', 'translation_name', $translation) . '</span></a>';
		break;
		case 'parent_tag_name':
		case 'phase_name':
		case 'element_name':
			$s1 = str_replace('_name', '', $v1);
			if ($s1 == 'parent_tag')
				$s1 = 'tag';
			$grab .= '<span class="' . $v1 . '">' . tt($s1, $listing[$v1], 'translation_name', $translation) . '</span>';
		break;
		case 'mark_name':
		case 'point_name':
		case 'timeframe_name':
		case 'decision_name':
		case 'grade_name':
		case 'meritype_name':
		case 'page_name':
		case 'status_name':
			$grab .= '<span style="color: ' . (isset($color['status_name']) ? $color['status_name'] : '') . ';" class="' . $v1 . '">' .  tt(str_replace('_name', '', $v1), $listing[$v1]) . '</span>';
		break;
		case 'description':
			$grab .= '<span ' . $dattrib . ' class="' . $v1 . '">'
				. ($listing[$type . '_' . $v1] 
					? a_link_replace($listing[$type . '_' . $v1]) 
					: tt('element', 'unset_result_element_name', 'translation_name', $translation)
				)
			. '</span>';
		break;
		case 'comment_description':
		case 'channel_description':
		case 'group_description':
		case 'translation_description':
		case 'team_description':
		case 'transfer_description':
		case 'metail_description':
		case 'note_description':
		case 'offer_description':
		case 'meripost_description':
		case 'meritopic_description':
		case 'incident_description':
		case 'feedback_description':
		case 'dialect_description':
		case 'item_description':
		case 'news_description':
		case 'tag_description':
		case 'vote_description':
			# newline on next line is used for email
			$grab .= " \n" . '<span ' . $dattrib . ' class="' . $v1 . '">'
				. ($listing[$v1] 
					? a_link_replace($listing[$v1]) 
					: tt('element', 'unset')
				)
			. '</span>';
		break;
		# translated channels will likely cause divergence - not what we want 2014-11-11 vaskoiii
		# case 'channel_translation_description':
		# 	$grab .= " \n" . '<span ' . $dattrib . ' class="' . $v1 . '">'
		# 		# . '!'
		# 		. (
		# 			(
		# 				$listing['channel_id'] && 
		# 				$key['channel_id']['result'][ $listing['channel_id'] ]['translation_description']
		# 			) 
		# 				? a_link_replace($key['channel_id']['result'][ $listing['channel_id'] ]['translation_description']) 
		# 				: tt('element', 'unset')
		# 		)
		# 	. '</span>';
		# break;
		case 'tag_translation_description':
			$grab .= " \n" . '<span ' . $dattrib . ' class="' . $v1 . '">'
				# . '!'
				. ($listing['tag_id']
					? a_link_replace($key['tag_id']['result'][ $listing['tag_id'] ]['translation_description']) 
					: tt('element', 'unset')
				)
			. '</span>';
		break;
		case 'offer_name':
			$grab .= '<span  style="color: ' . (isset($color['offer_name']) ? $color['offer_name'] : '') . ';" class="' . $v1 . '">'
				. to_html($listing[$v1])
			. '</span>';
		break;
		case 'kind_name':
			$grab .= '<span class="' . $v1 . '">'
				. tt(preg_replace('/\_name/', '', $v1), $listing[$v1])
			. '</span>';
		break;
		case 'tag_name':
			$grab .= '<span style="color: ' . (isset($color['thing_name']) ? $color['thing_name'] : '') . ';" class="' . $v1 . '">'
				#. tt('tag', $listing[$v1])
				. kk('tag', $listing['tag_id'], 'translation_name', $listing[$v1])
			. '</span>';
		break;
		case 'parent_tag_path':
		case 'parent_tag_translation_path':
		case 'tag_path':
		case 'tag_translation_path':
			# todo make tag_translation_path actually translated 2012-03-06 vaskoiii
			# todo make a mixed translated/untranslated path: Animal<>(mammal)<>Rhino NOT: (Animal<>mammal<>Rhino) 2012-03-06 vaskoiii
			if ($listing[$v1] == '<|!|>') # todo make a regular expression to match any < or > not in <> 2012-03-06 vaskoiii
				$grab .= '<span class="' . $v1 . '">' . $listing[$v1] . '</span>';
			else
				$grab .= '<span class="' . $v1 . '">(' . $listing[$v1] . ')</span>';
		break;
		case 'location_name':
			ob_start(); ?> 
			<span class="<?= $v1; ?>"><?= to_html($listing[$v1]); ?></span><?
			$grab .= ob_get_clean();
			/* <a href="./location_view/<?= ffm('list_name=&list_type=&lock_location_id=' . (int)$listing['location_id'], 1); ?>"><span class="<?= $v1; ?>"><?= to_html($listing[$v1]); ?></a></span><?  */
		break;
		case 'cycle_start':
		case 'renewal_start':
		case 'channel_name':
		case 'dialect_name':
		case 'meritopic_name':
		case 'incident_name':
		case 'feed_name':
		case 'news_name':
		case 'invite_password':
		case 'modified':
		case 'translation_name':
			$grab .= '<span class="' . $v1 . '">'
				. to_html($listing[$v1])
			. '</span>';
		break;
		# case 'modified_yyyy_mm_dd':
		# 	$grab .= '<span class="modified">'
		# 		. to_html(date('Y-m-d', strtotime($listing['modified'])))
		# 	. '</span>';
		# break;
		case 'parent_tag_translation_name':
			$grab .= '<span class="parent_tag_translation_name">'
				. kk('tag', $listing['parent_tag_id'], 'translation_name', $listing['parent_tag_path'], $key)
			. '</span>';
		break;
		case 'kind_name_translation_path':
			# todo
		break;
		case 'kind_name_translation_name':
			$s1 = $listing['kind_name'];
			switch($s1) {
				case 'team':
				case 'location':
					# translated form is the untranslated form ie) minder_list 2012-04-24 vaskoiii
					$grab .= '<span class="kind_name_name">'
						. kk($s1, $listing['kind_name_id'], $s1 . '_name') 
					. '</span>';
				break;
				default:
					# todo translation_name ? needs to be added when cycling through the listing info
					$grab .= '<span class="kind_name_name">'
						. kk($s1, $listing['kind_name_id'], 'translation_name') # s1_name
					. '</span>';
				break;
			}
		break;
		case 'kind_name_path':
			switch($listing['kind_name']) {
				case 'tag':
					# todo - s1_path
					# needs to be added when cycling through the listing info
					$grab .= '<span class="kind_name_name">'
						. '(' .  kk($listing['kind_name'], $listing['kind_name_id'], 'tag_path') . ')'
					. '</span>' .
					$spacer;
				break;
			}
		break;
		case 'kind_name_name':
			$s1 = $listing['kind_name'];
			if ($s1 == 'tag') {
				# todo make display correctly 2012-03-06 vaskoiii
				$grab .= '<span class="kind_name_name">('
					. to_html($listing['tag_path'])
				. ')</span>';
			}
			else {
				$grab .= '<span class="kind_name_name">('
					. to_html($key[ $s1 . '_id' ]['result'][  $listing['kind_name_id']  ][ $s1 . '_name' ])
				. ')</span>';
			}
		break;
		case 'checkbox': # checkboxes are intended to be reimplemented and toggled on by clicking [Select] right next to [Edit] in the title bar 2012-05-06 vaskoiii
			if ($load == 'atom')
				# top_report: allow selection but add more error checking ie) all same type? 2012-03-02 vaskoiii
				break;
			elseif ($load == 'view') // goes along with view_menu_1.php and view_menu_2.php
				break;
			switch($type) {
				case 'item':
				case 'vote':
					$grab .= '<input type="checkbox" name="row[]" value="' . $listing[ $type . '_id'] . '" ' 
						. ($_SESSION['process']['selection'][$listing[$type . '_id']] ? 'checked="checked"' : '') 
					. ' />';
				break;
				case 'meritopic':
				case 'meripost':
				case 'incident':
				case 'feedback':
					if ($listing['user_id'] == $login_user_id)
						$grab .= '<input type="checkbox" name="row[]" value="' . $listing[$type . '_id'] . '" ' 
							. ($_SESSION['process']['selection'][$listing[$type . '_id']] ? 'checked="checked"' : '') . ' />';
				break;
				default: 
					$grab .= '<input type="checkbox" name="row[]" value="' . $listing[$type . '_id'] . '" ' 
						. ($_SESSION['process']['selection'][$listing[$type . '_id']] ? 'checked="checked"' : '') 
					. ' />';
				break;
			}
			$grab .= ' ';
		break;
		case 'edit':
		if ($load != 'view') {
		switch($type) { 
			case 'offer':
			case 'user': # (editable from profile but not list)
				# not editable
			break;
			default:
				$b1 = 2;
				$s1 = $type;
				# tag/category: not editable (only mergable or addable) 2012-06-04 vaskoiii
				# todo make mergable
				switch($s1) {
					case 'translation':
					case 'location':
					if (substr($listing[$s1 . '_name'], 0, 1) != '<')
						$b1 = 1;
					break;
					case 'jargon':
					if (substr($listing['translation_name'], 0, 1) != '<')
						$b1 = 1;
					break;
					case 'teammate':
					if ($key['team_id']['result'][ $listing['team_id'] ]['team_owner_user_id'] == $login_user_id && 
						$listing['team_id'] != $config['everyone_team_id']
					)
						$b1 = 1;
					break;
					case 'transfer':
					if ($listing['source_user_id'] == $login_user_id)
						$b1 = 1;
					break;
					case 'channel':
					case 'note':
					case 'group':
					case 'team':
					case 'groupmate':
					case 'feed':
					case 'meritopic':
					case 'meripost':
					case 'news':
					case 'metail':
					case 'item':
					case 'feedback':
					case 'incident':
					case 'contact':
					case 'vote':
					if ($listing['user_id'] == $login_user_id)
						$b1 = 1;
					break;
				}
				if ($b1 == 1) {
					$a1 = array(
						get_load_parent($load) . '_id' => (int)$listing[$s1 . '_id'],
						get_load_parent($load) . '_type' => $s1,
						get_load_parent($load) . '_name' => 'edit',
						get_load_opposite_edit($load) . '_id' => '',
						'focus' => get_load_same_edit($load),
						'preview' => array(),
						'expand' => array(get_load_same_edit($load)),
					);
					# inline style needed for html mail 2012-05-06 vaskoiii
					$grab .= $spacer . '<a ' . $href_rss_start  . ffm(http_build_query($a1), 0) . '"><span class="' . $v1 . '">' . tt('element', 'edit', 'translation_name', $translation) . '</span></a>';
				}
			break;
		} }
		break;
		case 'translation_default_boolean_name':
			if ($listing['default_boolean_id'] == 1)
				$grab .= $spacer . '<span class="' . to_html($v1) . '">' . tt('element', 'default') . '</span>';
		break;
		case 'known': 
			switch($type) {
				case 'minder':
					$s1 = $listing['kind_name'];
					if ($key[$s1 . '_id']['result'][ $listing['kind_name_id'] ]['link'] == 'yes' )
						$grab .= '<span class="spacer">' . $spacer . '</span>'  . '<span class="' . $v1 . '">' . tt('element', 'known', 'translation_name', $translation) . '</span>';
				break;
				case 'category':
				case 'tag':
				case 'location':
				case 'team':
					$s1 = $type;
					if ($type == 'category')
						$s1 = 'tag';
					if ($key[$s1 . '_id']['result'][ $listing[$s1 . '_id'] ]['link'] == 'yes' )
						$grab .= '<span class="spacer">' . $spacer . '</span>'  . '<span class="' . $v1 . '">' . tt('element', 'known', 'translation_name', $translation) . '</span>';
				break;
			}
		break;
		case 'children':
		case 'parent':
		break;
		case 'location_coordinate':
			$grab .= '(<span class="location_latitude">' . to_html($listing['location_latitude']) . '</span>, 
			<span class="location_longitude">' . to_html($listing['location_longitude']) . '</span>)';
		break;
		case 'meritopic_id':
		 	if (!$x['load']['list']['type'])
				$grab .= '<span class="' . $v1 . '">' .  tt('element', $v1, 'translation_name', $translation) . '</span>: <span class="' . $v1 . '">' . (int)$listing['meritopic_id'] . '</span>';
			else
				$grab .= '<span class="' . $v1 . '">' .  tt('element', $v1, 'translation_name', $translation) . '</span>: <span class="' . $v1 . '">' . (int)$listing['meritopic_id'] . '</span>';
		break;
		case 'feed_link':
			if ($listing['user_id'] == $login_user_id)
				$grab .= $spacer . '<a href="' . $href_prepend . 'feed_recover/' . ff('id=' . (int)$listing['feed_id'] . $glue . $href_append, $ff_level) . '"><span class="feed_recover">' . tt('element', 'recover') . '</span></a>';
		break;
		# remember/forget
		case 'minder_minder':
			$s1 = $listing['kind_name'];
			$s2 = 'kind_name_id';
			if ($key[$s1 . '_id']['result'][ $listing[$s2] ]['link'] != 'yes')
				$grab .= ' - <a href="./result_process/?remember=1&list_name=' . to_html($s1) . '&q=' . to_url(ltrim(ff('', 1), '?')) . '&' . ('row[]=' . (int)$listing[$s2] ) . '"><span class="remember">' . tt('element', 'remember') . '</span></a> ';
			else
				$grab .= ' - <a href="./result_process/?forget=1&list_name=' . to_html($s1) . '&q=' . to_url(ltrim(ff('', 1), '?')) . '&' . ('row[]=' . (int)$listing[$s2] ) . '"><span class="forget">' . tt('element', 'forget') . '</span></a> ';
		break;
		case 'category_minder':
		case 'tag_minder': # not used but here to emphasize that a category is a tag
			$s1 = 'tag';
		case 'team_minder':
		case 'location_minder':
			if ($k1 != 'category_minder')
				$s1 = str_replace('_minder', '', $v1);
			if ($key[$s1 . '_id']['result'][ $listing[$s1 . '_id'] ]['link'] != 'yes')
				$grab .= ' - <a href="./result_process/?remember=1&list_name=' . to_html($s1) . '&q=' . to_url(ltrim(ff('', 1), '?')) . '&' . ('row[]=' . (int)$listing[$s1 . '_id'] ) . '"><span class="remember">' . tt('element', 'remember') . '</span></a> ';
			else
				$grab .= ' - <a href="./result_process/?forget=1&list_name=' . to_html($s1) . '&q=' . to_url(ltrim(ff('', 1), '?')) . '&' . ('row[]=' . (int)$listing[$s1 . '_id'] ) . '"><span class="forget">' . tt('element', 'forget') . '</span></a> ';
		break;
		case 'incident_view':
		case 'meritopic_view':
			$s1 = str_replace('_view', '', $v1);
				$grab .= $spacer . '<a ' . $href_rss_start . $s1 . '_view/' . ffm('list_name=&list_type=&' . $s1 . '_id=' . (int)$listing[$s1 . '_id'] . '&action_' . $s1 . '_id=' . (int)$listing[$s1 . '_id']) . '"><span class="' . $s1 . '_name">' . tt('element', 'view') . '</span></a>';
		break;
		case 'user_view':
		case 'team_view':
		case 'group_view':
		case 'location_view':
			$s1 = str_replace('_view', '', $v1);
			$grab .= $spacer . '<a ' . $href_rss_start . $s1 . '_view/' . ffm('list_name=&list_type=&lock_' . $s1 . '_id=' . (int)$listing[$s1 . '_id']) . '"><span class="' . $s1 . '_name">' . tt('element', 'view') . '</span></a>';
		break;
		case 'incident_id':
			$grab .= '<span class="' . $v1 . '">' .  tt('element', $v1, 'translation_name', $translation) . '</span>: <span class="' . $v1 . '">' . (int)$listing['incident_id'] . '</span>';
		break;
		case 'incident_id':
		 	if (!$x['load']['list']['type'])
				$grab .= '<span class="' . $v1 . '">' .  tt('element', $v1, 'translation_name', $translation) . '</span>: <span class="' . $v1 . '">' . (int)$listing['incident_id'] . '</span>';
			else
				$grab .= '<span class="' . $v1 . '">' .  tt('element', $v1, 'translation_name', $translation) . '</span>: <span class="' . $v1 . '">' . (int)$listing['incident_id'] . '</span>';
		break;
		case 'team_required_name':
		case 'team_name': 
			$grab .= '<span style="color: ' . (isset($color['team_name']) ? $color['team_name'] : '') . '" class="' . $v1 . '">' . to_html($listing[$v1]) . '</span>';
		break;
		case 'group_name': 
			$grab .= '<span style="color: ' . (isset($color['group_name']) ? $color['group_name'] : '') . '" class="' . $v1 . '">' . to_html($listing[$v1]) . '</span>';
		break;
	}
	}
	return $grab;
}

# todo add to php_database.php
function listing_menu_1($type) { ?> 
	<input type="hidden" name="list_name" value="<?= $type; ?>" /><?
	switch($type) {
		case 'team':
		case 'location':
		case 'tag': ?> 
			<input type="submit" name="remember" value="<?= get_translation('remember'); ?>" />
			<input type="submit" name="forget" value="<?= get_translation('forget'); ?>" /><?
		break;
	}
	switch($type) {
		case 'tag':
		case 'category': ?> 
			<input type="submit" name="merge" value="<?= get_translation('merge'); ?>" /><?
		break;
	}
	switch($type) {
		case 'feed':
		case 'teammate':
		case 'invite':
		case 'transfer':
		case 'contact':
		case 'note':
		case 'group':
		case 'groupmate':
		case 'item':
		case 'vote':
		case 'metail':
		case 'offer': ?> 
			<input type="submit" name="delete" value="<?= get_translation('delete'); ?>" /><?
		break;
	}
	/*switch($type) {
		case 'feed': ?> 
			<input type="submit" name="disable" value="<?= get_translation('disable'); ?>" /><?
		break;
	}*/
	switch($type) {
		case 'vote':
		case 'item': 
		case 'transfer': ?> 
			<input type="submit" name="export" value="<?= get_translation('export'); ?>" /><?
		break;
	}
	switch($type) {
		case 'vote':
		case 'item':
		case 'transfer': ?> 
			<input type="submit" name="import" value="<?= get_translation('import'); ?>" /><?
		break;
	}
	switch($type) {
		case 'vote':
		case 'item': 
		case 'transfer': ?> 
			<input type="submit" name="judge" value="<?= get_translation('judge'); ?>" /><?
		break;
	}
	switch($type) {
		case 'metail': ?> 
			<input type="submit" name="memorize" value="<?= get_translation('memorize'); ?>" /><?
		break;
	}
}
