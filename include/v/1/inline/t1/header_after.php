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

# Contents/Description: Sometimes needed chunk of data

# necessary? 2012-02-16 vaskoiii
# custom pages might be better w/o this include 
?>

<div id="action">
	<div class="title">
		<h2><? print_ts_focus(get_translation('page', $x['page']['name']), 'action'); ?></h2>
		<div class="result_add">
		</div>
	</div><?
	print_message_bar(); ?> 
	<div class="content">
