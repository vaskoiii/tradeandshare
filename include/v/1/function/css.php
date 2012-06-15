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

# Contents/Description: css configuration

function get_background($theme) {
	switch ($theme) {
		case 'orange':
		return array(
			'c0' => '#fdecc9',
			'c1' => '#ff955d',
			'c2' => '#fa7915',
		);
		break;
		case 'yellow':
		return array(
			'c0' => '#fdfcc9',
			'c1' => '#ffd661',
			'c2' => '#fcd602',
		);
		break;
		case 'green':
		return array(
			'c0' => '#d7efbf',
			'c1' => '#9ccd9c',
			'c2' => '#34ae35',
		);
		break;
		case 'blue':
		return array(
			'c0' => '#cad5fa',
			'c1' => '#6cadf7',
			'c2' => '#206bf6',
		);
		break;
		case 'purple':
		return array(
			'c0' => '#dccafa',
			'c1' => '#ce9ccd',
			'c2' => '#a826d4',
		);
		break;
		case 'pink';
		return array(
			'c0' => '#facaf9',
			'c1' => '#ffa0c1',
			'c2' => '#ff37af',
		);
		break;
		case 'black':
		return array(
			'c0' => '#bfbfbf',
			'c1' => '#a4a4a4',
			'c2' => '#646464',
		);
		break;
		case 'brown':
		return array(
			'c0' => '#dbd8ce',
			'c1' => '#d7ae86',
			'c2' => '#a77b4f',
		);
		break;
		case 'gray':
		return array(
			'c0' => '#ededed',
			'c1' => '#d5d5d5',
			'c2' => '#c1c1c1',
		);
		break;
	}
	return array(
		'c0' => '#ffbfbf',
		'c1' => '#ff9d9a',
		'c2' => '#ee292a',
	);
}
