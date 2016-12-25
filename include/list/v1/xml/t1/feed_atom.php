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
# Enclose HTML Stuff inside: <![CDATA[]]>
# using an extra space at the line endings for better formatting when viewing source

echo '<?xml version="1.0" encoding="utf-8"?>' . PHP_EOL;
/*
# xml style possible?
echo '<?xml-stylesheet type="text/css" href="/text_style/" ?> ' . PHP_EOL;
*/

$listing = & $data['result']['result']['listing'];

# Mozilla Thunderbird :
# <author><name> => from.name
# <author><email> => from.email (faked)
# <link> => website
# <title> => subject
# <summary> => body
# to_xml() with thunderbird seems to want to enclose everything between < and > (it is omitted for now)
# https://bugzilla.mozilla.org/show_bug.cgi?id=679195

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
, 0); ?> 
<feed xmlns="http://www.w3.org/2005/Atom"> 
	<title type="html"><?= to_xml($x['feed_atom']['name']); ?></title><?
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
	<subtitle type="html"><?= to_xml(('Trade and Share - Want it? Get it! Have it? Share it! Don\'t want it? Trade it!')); ?></subtitle> 
	<author> 
		<name><?= to_xml(!empty($x['feed_atom']['contact_name']) 
			? $x['feed_atom']['contact_name'] 
			: $config['unabstracted_prefix'] . $x['feed_atom']['user_name']. $config['unabstracted_suffix']
		); ?></name> 
	</author><? # feed owner
	foreach ($listing as $k1 => $v1) { ?> 
		<entry> 
			<title type="html"><?
				$style = array(); # hack
				ob_start();
				print_listing_template($listing[$k1], $key, $translation, 'result', 'feed', 'title', $_SESSION['login']['login_user_id'], $style, $x['feed_atom']['part'][0] ); 
				$s1 = strip_tags(ob_get_clean());
				echo to_xml(
					# no HTML in RSS title (Similar to email subject)
					trim($s1)
				); 
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
				. 'lock_user_id=' . $s1
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
					# needs 2x trims for some reason
					$s1 = get_listing_template_output($a1, $listing[$k1], $key, $translation, 'list', 'feed', 'author', $_SESSION['login']['login_user_id'], $style, $x['feed_atom']['part'][0]);
					echo to_xml(html_entity_decode(trim(trim(strip_tags($s1)))));
				?></name><?
				# hack for thunderbird so to display the from name correctly
				# <email>@</email> ?> 
			</author> 
			<summary type="html"><?
				ob_start();
				print_listing_template($listing[$k1], $key, $translation, 'result', 'feed', 'summary', $_SESSION['login']['login_user_id'], $style, $x['feed_atom']['part'][0] );
				$s1 = ob_get_clean();
				$s1 = strip_tags($s1, '<a>');
				# hack
				$s1 = str_replace('style="color: ;" ', '', $s1);
				echo to_xml($s1); ?> 
			</summary> 
			<updated><?= to_xml(
				gmdate('c', strtotime($listing[$k1]['modified']))
			); ?></updated> 
		</entry><?
	} ?> 
</feed><?
if ($config['debug'] == 1)
	header_debug();
