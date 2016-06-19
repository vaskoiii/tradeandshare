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

# Contents/Description: Overall report of the configuration you are using to view the site in one place

add_translation('display', $_SESSION['display']['display_name']);
add_translation('element', 'edit');
add_translation('element', 'load_javascript');
add_translation('load', $_SESSION['load']['load_name']);
add_translation('page', 'item_list');
add_translation('page', 'dialect_set');
add_translation('page', 'display_set');
add_translation('page', 'load_set');
add_translation('page', 'lock_set');
add_translation('page', 'profile_edit');
add_translation('page', 'theme_set');
add_translation('element', 'team_name');
add_translation('theme', $_SESSION['theme']['theme_name']);
add_translation('theme', $_SESSION['theme']['background_theme_name']);
add_translation('theme', $_SESSION['theme']['launcher_theme_name']);
add_translation('boolean', 'true');
add_translation('boolean', 'false');
add_translation('element', 'view');

$my_team_name = $config['reserved_prefix'] . '*' .  $_SESSION['login']['login_user_name'] . $config['reserved_suffix'];
$my_team_id = get_db_single_value('
		id
	from
		' . $config['mysql']['prefix'] . 'team
	where
		name = ' . to_sql($my_team_name)
, 0);
