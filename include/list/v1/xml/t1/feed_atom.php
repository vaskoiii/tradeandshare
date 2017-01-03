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

# Contents/Description: ATOM feed!

# Validate Changes at:
# http://validator.w3.org/feed/

# Notes:
# using an extra space at the php line endings for better formatting when viewing source
# type="xhtml" is not as widely supported as type="text" (validator gives a warning)
# but using type="xhtml" where possible as it has better view-source readability
# use htmlentities for atom feed type="text" tags:
# https://tools.ietf.org/html/rfc4287#page-8

# Mozilla Thunderbird:
# <author> => from
# <link> => website
# <title> => subject
# <content> => body

$listing = & $data['result']['result']['listing'];

$x['feed_atom']['contact_name'] = get_sender_contact_name($x['feed_atom']['user_id']);
$x['feed_atom']['contact_id'] = get_db_single_value('
		contact_id
	FROM
		' . $config['mysql']['prefix'] . 'link_contact_user lcu,
		' . $config['mysql']['prefix'] . 'contact c
		
	WHERE
		c.id = lcu.contact_id AND
		c.user_id = ' . (int)$x['feed_atom']['user_id'] . ' AND
		c.name = ' . to_sql($x['feed_atom']['contact_name'])
, 0);
echo '<?xml version="1.0" encoding="utf-8"?>'; ?> 
<feed xmlns="http://www.w3.org/2005/Atom"> 
	<title><?= to_html($x['feed_atom']['name']); ?></title><?
	# is either <link> needed here? 2016-12-24 vaskoiii ?> 
	<link href="<?= 
		to_html(
			'https://'
			. $_SERVER['HTTP_HOST'] 
			. '/' . $x['feed_atom']['page_name'] . '/' 
			. ($x['feed_atom']['query'] ? '?' . $x['feed_atom']['query'] : '') 
		); 
	?>" /> 
	<link href="https://<?= $_SERVER['HTTP_HOST']; ?>/feed_atom/?<?= to_html($_SERVER['REDIRECT_QUERY_STRING']); ?>" rel="self" /> 
	<id><?= to_html(
		'https://'
		. $_SERVER['HTTP_HOST']
		. '/' . $x['feed_atom']['page_name'] . '/?'
		. 'feed_uid=' . (int)$x['feed_atom']['id']
	); ?></id> 
	<updated><?= gmdate('c'); ?></updated> 
	<subtitle>Trade and Share - Want it? Get it! Have it? Share it! Don't want it? Trade it!</subtitle> 
	<author> 
		<name><?= to_html(!empty($x['feed_atom']['contact_name']) 
			? $x['feed_atom']['contact_name'] 
			: $config['unabstracted_prefix'] . $x['feed_atom']['user_name']. $config['unabstracted_suffix']
		); ?></name> 
	</author><? # feed owner
	foreach ($listing as $k1 => $v1) { ?> 
		<entry> 
			<title><?
				$style = array(); # hack
				ob_start();
				print_listing_template($listing[$k1], $key, $translation, 'result', 'feed', 'title', $_SESSION['login']['login_user_id'], $style, $x['feed_atom']['part'][0] ); 
				$s1 = strip_tags(ob_get_clean());
				echo trim($s1); 
			?></title><?
			# function call to get correct variable? 2013-03-22 vaskoiii
			$s1;
			switch($x['feed_atom']['page_name']) {
				case 'invited_list':
				case 'offer_list':
				case 'transfer_list':
					$s1 = (int)$listing[$k1]['source_user_id'];
				break;
				default:
					$s1 = (int)$listing[$k1]['user_id'];
				break;
			} ?> 
			<link href="<?= to_html(
				'https://'
				. $_SERVER['HTTP_HOST']
				. '/' . $x['feed_atom']['page_name'] . '/?'
				. 'lock_user_id=' . $s1 . '&dumb_'
				# hack for dumb feed readers that think <link> means <id>
				# ie) [sparse rss] on android
				. $x['feed_atom']['part'][0] . '_uid=' . (int)$listing[$k1][$x['feed_atom']['part'][0] . '_id']
			); ?>" /> 
			<id><?= to_html(
				'https://'
				. $_SERVER['HTTP_HOST']
				. '/' . $x['feed_atom']['page_name'] . '/?'
				. $x['feed_atom']['part'][0] . '_uid=' . (int)$listing[$k1][$x['feed_atom']['part'][0] . '_id']
			); ?></id> 
			<author> 
				<name><?
					$a1 = get_mask_author($x['feed_atom']['part'][0], 'feed');
					foreach($a1 as $k2 => $v2) {
						switch($v2) {
							case '_':
							case 'direction_right_name':
								unset($a1[$k2]);
							break;
						}
					}
					$s1 = get_listing_template_output($a1, $listing[$k1], $key, $translation, 'list', 'feed', 'author', $_SESSION['login']['login_user_id'], $style, $x['feed_atom']['part'][0]);
					echo strip_tags($s1);
				?></name>
			</author> 
			<content type="html"><?
				ob_start();
				print_listing_template($listing[$k1], $key, $translation, 'result', 'feed', 'summary', $_SESSION['login']['login_user_id'], $style, $x['feed_atom']['part'][0] );
				$s1 = ob_get_clean();
				$s1 = strip_tags($s1, '<a>');
				# hack
				$s1 = str_replace('style="color: ;" ', '', $s1);
				$s1 = trim($s1);
				echo PHP_EOL;
				# https://tools.ietf.org/html/rfc4287
				# (best option for rendering links?)
				# type="html" with CDATA
				echo to_xml($s1);
				# type="html" without CDATA and and double escaped & and <
				# echo to_xml_atom_html($s1);
				# type="xhtml" (preferential option if it had more client support)
				# echo to_xml_atom_xhtml($s1, 4); ?> 
			</content> 
			<updated><?= gmdate('c', strtotime($listing[$k1]['modified'])); ?></updated> 
		</entry><?
	} ?> 
</feed><?
if ($config['debug'] == 1)
	header_debug();
