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

# Contents/Description: Placeholder for user_report
?> 

<div class="content_box ">
	<p> This page is a placeholder for generating a "call log" for the links clicked from Contacts/Metails </p>

	<h3><?= $data['user_report']['user_name']; ?></h3><?
	
	if ($data['user_report']['relative_score_amount']) { ?> 
		<p><?= tt('element', $data['user_report']['relative_grade_element_name']); ?></p>

		<p>	
			&sum;
			/ <?= (int)$data['user_report']['relative_score_amount']; ?> 
			= <?= to_html($data['user_report']['relative_grade_value_average']); ?> 
		</p><?
	} else { ?> 
		<p><?= tt('element', 'error_does_not_exist'); ?></p>

		<p>	
			&sum;
			/ <?= $data['user_report']['relative_score_amount']; ?> 
			= ?
		</p><?
	} ?> 
</div>

<div class="menu_1">
</div>

<div class="menu_2">
</div>

</div>
