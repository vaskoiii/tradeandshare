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

# Contents/Description: Everything is stored to a variable because text style is used across EVERYTHING (email/feeds/html)

$data['css']['text_style'] = array();

if ($_SESSION['theme']['theme_name'] == 'theme_select_none') {
	$data['css']['text_style']['message_box'] .= '
		.failure	{ color: #cb0000; font-style: italic; } 
		.notice		{ color: #999221; font-style: italic; }
		.success	{ color: #186400; font-style: italic; }
	';
}

# broken up for email including: offer, transfer, teammate 2012-02-16 vaskoiii
# we can NOT use styles for LOTS of email clients because they only support the very first HTML specification.
# fields not listed below ie) description will be the default email client color.
$data['css']['text_style']['color']['link'] = '#000001'; # gmail ignores #000000
#$data['css']['text_style']['color']['direction_name'] = '#8ac202';
$data['css']['text_style']['color']['direction_name'] = '#0000cc';
$data['css']['text_style']['color']['user_name'] = '#cb0000';
$data['css']['text_style']['color']['contact_name'] = '#de7d00';
#$data['css']['text_style']['color']['spacer'] = '#333333';
$data['css']['text_style']['color']['spacer'] = '#8a8282';

$data['css']['text_style']['color']['offer_name'] = '#228866';
$data['css']['text_style']['color']['thing_name'] = '#186400';
$data['css']['text_style']['color']['status_name'] = '#0036ff';
$data['css']['text_style']['color']['team_name'] = '#39b628';

# only 1 style needed for all description types 2013-10-10 vaskoiii
$data['css']['text_style']['color']['description'] = '#000001';

