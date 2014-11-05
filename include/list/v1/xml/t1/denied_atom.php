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
# todo: make to_xml check for the pattern ]]> or whatever the terminating thing of cdata is.

echo '<?xml version="1.0" encoding="utf-8"?>' . "\n";
echo '<?xml-stylesheet type="text/css" href="/text_style/" ?>' . "\n";
?><feed xmlns="http://www.w3.org/2005/Atom">
	<title><?= to_xml(('Trade and Share')); ?></title>
	<link href="<?= 'https://' . $_SERVER['HTTP_HOST']; ?>" />
	<subtitle><?= to_xml(('Want it? Get it! Have it? Share it! Don\'t want it? Trade it!')); ?></subtitle>
	<entry>
		<title><?= to_xml(('Access Denied!')); ?></title>
		<link href="<?= 'https://' . $_SERVER['HTTP_HOST']; ?>" />
		<author><name>TS</name></author>
		<summary type="html"><?= to_xml(('Error invalid feed.')); ?></summary>
		<updated><?= to_xml((date('Y-m-d'))); ?></updated>
	</entry> 
</feed>
