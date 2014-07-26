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

# Contents/Description: Setup the main style of the site.

# KEY
# logo: TS logo
# bg0: main background
# bg1: main title background
# bg2: link background
# bg3: secondary title background
# bg4: content background color (currently only fade white is used though we could put another texture on top if we want)

// why don't we go by theme name? Id in this case is not so helpful.
if (get_gp('theme_id')) {
	$data['css']['theme_name'] = get_db_single_value('
			t.name
		FROM
			' . $config['mysql']['prefix'] . 'theme t
		WHERE
			t.id = ' . (int)get_gp('theme_id')
	);
}

// Same thing with this why don't we use the element name for this. It is more intuitive. and eliminates the extra SQL
if (get_gp('display_id') && empty($data['css']['display_name']))
	$data['css']['display_name'] = get_db_single_value('
		d.name
	FROM
		' . $config['mysql']['prefix'] . 'display d
	WHERE
		d.id = ' . (int)get_gp('display_id')
);

// DISPLAY
// Setting Display width is important so that the viewport can understard. If there was no viewport we would only have to use the display_width_1024_pixels
$data['css']['max_element_width'] = 'max-width: 235px;'; // not used for display_select_none
$data['css']['body_width_percentage'] = 'width: 97.5%;'; // not used for display_select_none

switch($data['css']['display_name']) {
	case 'display_select_none':
		// browser decide
		$data['css']['max_width'] = ''; // do not set!
		$data['css']['max_width_minus_2'] = ''; // do not set!
		$data['css']['description_width'] = 'width: 85%;';
	break;
	case 'display_width_320_pixels':
		$data['css']['max_width'] = 'max-width: 300px';
		$data['css']['max_width_minus_2'] = 'max-width: 298px;';
		$data['css']['description_width'] = 'width: 85%; max-width: 230px;';
	break;
	case 'display_width_480_pixels':
		$data['css']['max_width'] = 'max-width: 450px';
		$data['css']['max_width_minus_2'] = 'max-width: 448px;';
		$data['css']['description_width'] = 'width: 85%; max-width: 370px;';
	break;
	case 'display_width_1024_pixels':
		$data['css']['max_width'] = 'max-width: 950px';
		$data['css']['max_width_minus_2'] = 'max-width: 948px;';
		$data['css']['description_width'] = 'width: 85%; max-width: 816px;';
	break;
	case 'display_select_default':
		// most compatible
		$data['css']['max_width'] = 'max-width: 950px';
		$data['css']['max_width_minus_2'] = 'max-width: 948px';
		$data['css']['description_width'] = 'width: 85%; max-width: 816px;';
	break;

}
// There are currently 2 extra blank table rows inserted here to make this work. Current hack in function/search.php
//$data['css']['description_width'] .= ' resize: none; overflow: hidden; border: 2px inset #ffffff; height: 42px;';

switch ($data['css']['theme_name']) {
	# TODO: test theme_select_none... formatting is off...
	case 'theme_select_none': ?> 
		.table {
			margin-bottom: 10px;
		}
		.nonlock_box {
			margin-bottom: 20px;
		}
		.description_input {
			<?= $data['css']['description_width']; ?>
		}
		textarea {
			height: 55px;
		}
		.textarea {
			height: 40px;
		}
		body {
			background: #ffbfbf;
			<?= $data['css']['max_width']; ?>
		} <?
		exit;
	break;
	case 'theme_orange':
		$data['css']['logo'] = 'transparent url("/' . $x['site']['p'] . '/theme/orange/ts_icon.png") repeat';
		#$data['css']['bg0'] = 'transparent url("/' . $x['site']['p'] . '/theme/orange/background.jpg") repeat';
		$data['css']['bg1'] = '#ff955d url("/' . $x['site']['p'] . '/theme/orange/fade_up.png") repeat-x';
		$data['css']['bg2'] = '#ff8e57 url("/' . $x['site']['p'] . '/theme/orange/fade_up_1.png") repeat-x';
		$data['css']['bg3'] = '#fda33f url("/' . $x['site']['p'] . '/theme/orange/fade_down.png") repeat-x';
		$data['css']['bg4'] = '#fbb42b url("/' . $x['site']['p'] . '/theme/common/fade_white.png") repeat';
	break;
	case 'theme_yellow':
		$data['css']['logo'] = 'transparent url("/' . $x['site']['p'] . '/theme/yellow/ts_icon.png") repeat';
		#$data['css']['bg0'] = 'transparent url("/' . $x['site']['p'] . '/theme/yellow/background.jpg") repeat';
		$data['css']['bg1'] = '#ffd661 url("/' . $x['site']['p'] . '/theme/yellow/fade_up.png") repeat-x';
		$data['css']['bg2'] = '#ffd459 url("/' . $x['site']['p'] . '/theme/yellow/fade_up_1.png") repeat-x';
		$data['css']['bg3'] = '#fef31f url("/' . $x['site']['p'] . '/theme/yellow/fade_down.png") repeat-x';
		$data['css']['bg4'] = '#fbf42b url("/' . $x['site']['p'] . '/theme/common/fade_white.png") repeat';
	break;
	case 'theme_green':
		$data['css']['logo'] = 'transparent url("/' . $x['site']['p'] . '/theme/green/ts_icon.png") repeat';
		#$data['css']['bg0'] = 'transparent url("/' . $x['site']['p'] . '/theme/green/background.jpg") repeat';
		$data['css']['bg1'] = '#9ccd9c url("/' . $x['site']['p'] . '/theme/green/fade_up.png") repeat-x';
		$data['css']['bg2'] = '#9ccd9c url("/' . $x['site']['p'] . '/theme/green/fade_up_1.png") repeat-x';
		$data['css']['bg3'] = '#8bd08b url("/' . $x['site']['p'] . '/theme/green/fade_down.png") repeat-x';
		$data['css']['bg4'] = '#60c300 url("/' . $x['site']['p'] . '/theme/common/fade_white.png") repeat';
	break;
	case 'theme_blue':
		$data['css']['logo'] = 'transparent url("/' . $x['site']['p'] . '/theme/blue/ts_icon.png") repeat';
		#$data['css']['bg0'] = 'transparent url("/' . $x['site']['p'] . '/theme/blue/background.jpg") repeat';
		$data['css']['bg1'] = '#6cadf7 url("/' . $x['site']['p'] . '/theme/blue/fade_up.png") repeat-x';
		$data['css']['bg2'] = '#67aaf6 url("/' . $x['site']['p'] . '/theme/blue/fade_up_1.png") repeat-x';
		$data['css']['bg3'] = '#94b8fb url("/' . $x['site']['p'] . '/theme/blue/fade_down.png") repeat-x';
		$data['css']['bg4'] = '#2d5aec url("/' . $x['site']['p'] . '/theme/common/fade_white.png") repeat';
	break;
	case 'theme_purple':
		$data['css']['logo'] = 'transparent url("/' . $x['site']['p'] . '/theme/purple/ts_icon.png") repeat';
		#$data['css']['bg0'] = 'transparent url("/' . $x['site']['p'] . '/theme/purple/background.jpg") repeat';
		$data['css']['bg1'] = '#ce9ccd url("/' . $x['site']['p'] . '/theme/purple/fade_up.png") repeat-x';
		$data['css']['bg2'] = '#ce9cce url("/' . $x['site']['p'] . '/theme/purple/fade_up_1.png") repeat-x';
		$data['css']['bg3'] = '#d08ae8 url("/' . $x['site']['p'] . '/theme/purple/fade_down.png") repeat-x';
		$data['css']['bg4'] = '#752dec url("/' . $x['site']['p'] . '/theme/common/fade_white.png") repeat';
	break;
	case 'theme_pink';
		$data['css']['logo'] = 'transparent url("/' . $x['site']['p'] . '/theme/pink/ts_icon.png") repeat';
		#$data['css']['bg0'] = 'transparent url("/' . $x['site']['p'] . '/theme/pink/background.jpg") repeat';
		$data['css']['bg1'] = '#ffa0c1 url("/' . $x['site']['p'] . '/theme/pink/fade_up.png") repeat-x';
		$data['css']['bg2'] = '#ffa8d2 url("/' . $x['site']['p'] . '/theme/pink/fade_up_1.png") repeat-x';
		$data['css']['bg3'] = '#ff94d4 url("/' . $x['site']['p'] . '/theme/pink/fade_down.png") repeat-x';
		$data['css']['bg4'] = '#ec2dea url("/' . $x['site']['p'] . '/theme/common/fade_white.png") repeat';
	break;
	case 'theme_black':
		$data['css']['logo'] = 'transparent url("/' . $x['site']['p'] . '/theme/black/ts_icon.png") repeat';
		#$data['css']['bg0'] = 'transparent url("/' . $x['site']['p'] . '/theme/black/background.jpg") repeat';
		$data['css']['bg1'] = '#a4a4a4 url("/' . $x['site']['p'] . '/theme/black/fade_up.png") repeat-x';
		$data['css']['bg2'] = '#a5a5a5 url("/' . $x['site']['p'] . '/theme/black/fade_up_1.png") repeat-x';
		$data['css']['bg3'] = '#cacaca url("/' . $x['site']['p'] . '/theme/black/fade_down.png") repeat-x';
		$data['css']['bg4'] = '#000000 url("/' . $x['site']['p'] . '/theme/common/fade_white.png") repeat';
	break;
	case 'theme_brown':
		$data['css']['logo'] = 'transparent url("/' . $x['site']['p'] . '/theme/brown/ts_icon.png") repeat';
		#$data['css']['bg0'] = 'transparent url("/' . $x['site']['p'] . '/theme/brown/background.jpg") repeat';
		$data['css']['bg1'] = '#d7ae86 url("/' . $x['site']['p'] . '/theme/brown/fade_up.png") repeat-x';
		$data['css']['bg2'] = '#d8b28b url("/' . $x['site']['p'] . '/theme/brown/fade_up_1.png") repeat-x';
		$data['css']['bg3'] = '#d0b8a0 url("/' . $x['site']['p'] . '/theme/brown/fade_down.png") repeat-x';
		$data['css']['bg4'] = '#71653f url("/' . $x['site']['p'] . '/theme/common/fade_white.png") repeat';
	break;
	case 'theme_gray':
		$data['css']['logo'] = 'transparent url("/' . $x['site']['p'] . '/theme/gray/ts_icon.png") repeat';
		#$data['css']['bg0'] = 'transparent url("/' . $x['site']['p'] . '/theme/gray/background.jpg") repeat';
		$data['css']['bg1'] = '#d5d5d5 url("/' . $x['site']['p'] . '/theme/gray/fade_up.png") repeat-x';
		$data['css']['bg2'] = '#d4d4d4 url("/' . $x['site']['p'] . '/theme/gray/fade_up_1.png") repeat-x';
		$data['css']['bg3'] = '#f8f8f8 url("/' . $x['site']['p'] . '/theme/gray/fade_down.png") repeat-x';
		$data['css']['bg4'] = '#bbbbbb url("/' . $x['site']['p'] . '/theme/common/fade_white.png") repeat';
	break;
	default:
		$data['css']['logo'] = 'transparent url("/' . $x['site']['p'] . '/theme/red/ts_icon.png") repeat';
		#$data['css']['bg0'] = 'transparent url("/' . $x['site']['p'] . '/theme/red/background.jpg") repeat';
		$data['css']['bg1'] = '#ff9d9a url("/' . $x['site']['p'] . '/theme/red/fade_up.png") repeat-x';
		$data['css']['bg2'] = '#fb887b url("/' . $x['site']['p'] . '/theme/red/fade_up_1.png") repeat-x';
		$data['css']['bg3'] = '#fabcbc url("/' . $x['site']['p'] . '/theme/red/fade_down.png") repeat-x';
		$data['css']['bg4'] = '#ff0000 url("/' . $x['site']['p'] . '/theme/common/fade_white.png") repeat';
	break;
}
