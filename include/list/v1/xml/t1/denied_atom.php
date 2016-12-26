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

# Contents/Description: ATOM 403
# todo: add translations (currently only in english)

echo '<?xml version="1.0" encoding="utf-8"?>' . "\n";
?><feed xmlns="http://www.w3.org/2005/Atom">
	<id>https://<?= $_SERVER['HTTP_HOST']; ?>/feed_list/</id>
	<link href="<?= 'https://' . $_SERVER['HTTP_HOST']; ?>/feed_list/" />
	<link href="https://<?= $_SERVER['HTTP_HOST']; ?>/feed_atom/?<?= to_html($_SERVER['REDIRECT_QUERY_STRING']); ?>" rel="self" /> 
	<updated><?= gmdate('c'); ?></updated>
	<title>Trade and Share</title>
	<subtitle>Want it? Get it! Have it? Share it! Don't want it? Trade it!</subtitle>
	<entry>
		<id><?= 'https://' . $_SERVER['HTTP_HOST']; ?>/feed_atom/?updated=<?= urlencode(gmdate('c')); ?></id>
		<updated><?= gmdate('c'); ?></updated>
		<title>Access Denied!</title>
		<link href="<?= 'https://' . $_SERVER['HTTP_HOST']; ?>/feed_list/" />
		<author>
			<name>TS</name>
		</author>
		<content>Error invalid feed.</content>
	</entry> 
</feed>
