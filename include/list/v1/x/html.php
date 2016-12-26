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

# Contents/Description: Should be the ONLY exception in X where we allow HTML code 
# Notes: X is intended for just including files

# todo these are mainly used for documentation pages which are not translated. Perhaps there is a way to have these pages translatable. Perhaps a wiki would be a better solution.

header('HTTP/1.0 200 Found');

include('list/v1/inline/head.php');
include('list/v1/inline/header.php');

include('list/v1/inline/edit.php');
include('list/v1/layer/fast.php');
include('list/v1/layer/quick.php');

include('list/v1/inline/footer.php');


include('list/v1/inline/t1/head.php');
include('list/v1/inline/t1/header.php');

include('list/v1/layer/t1/fast.php');
include('list/v1/layer/t1/quick.php');

include('list/v1/inline/t1/header_after.php');

# done just to help with readability (othewise we would just use ints) 2012-06-09 vaskoiii
$s1 = get_db_single_value('
		`code`
	from
		' . $config['mysql']['prefix'] . 'dialect
	where
		id = ' . to_sql($_SESSION['dialect']['dialect_id'])
, 0); ?> 

<div class="content_box"><?
switch ($s1) {
	case 'en':
		include('list/v1/html/' . $s1 . '/' . $x['page']['name'] . '.php');
	break;
	case 'jp':
	case 'de':
	default:
		# todo helpful message if page is not yet translated. 2012-06-09 vaskoiii
		include('list/v1/html/en/' . $x['page']['name'] . '.php');
	break;
} ?> 
</div>
<div class="menu_1"></div><?
switch($x['page']['name']) {
	case 'trailer_doc': ?> 
		<div class="menu_2" style="text-align: center;">
			<ul>
				<li><a href="download_doc/<?= to_html(ff()); ?>"><?= tt('page', 'download_doc'); ?></a></li>
				<li><a href="about_doc/<?= to_html(ff()); ?>"><?= tt('page', 'about_doc'); ?></a></li>
				<li><a href="faq_doc/<?= to_html(ff()); ?>"><?= tt('page', 'faq_doc'); ?></a></li>
				<li><a href="sitemap_doc/<?= to_html(ff()); ?>"><?= tt('page', 'sitemap_doc'); ?></a></li>
				<li><a href="disclaimer_doc/<?= to_html(ff()); ?>"><?= tt('page', 'disclaimer_doc'); ?></a></li>
				<li><a href="donate_doc/<?= to_html(ff()); ?>"><?= tt('page', 'donate_doc'); ?></a></li>
			</ul>
		</div><?
	break;
	default: ?> 
		<div class="menu_2"></div><?
	break;
}
include('list/v1/inline/t1/footer.php');
