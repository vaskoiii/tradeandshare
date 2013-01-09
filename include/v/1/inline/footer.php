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

add_translation('page', 'sitemap_doc');
add_translation('page', 'contact_list');
add_translation('page', 'user_list');

do_option($option);
do_key($key, $translation, $_SESSION['dialect']['dialect_id'], $_SESSION['login']['login_user_id']);

do_translation($key, $translation, $_SESSION['dialect']['dialect_id'], $_SESSION['login']['login_user_id']); # must be after do key 2012-03-27 vaskoiii

sort_option(); # avoid left outer join 2012-02-23 vaskoiii
