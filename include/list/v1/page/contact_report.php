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

# Contents/Description: Placeholder file. Not currently used on 2012-06-05 vaskoiii

/*
# RATING
$data['user_report']['user_name'] = get_db_single_value('
		name
	FROM
		' . $config['mysql']['prefix'] . 'user
	WHERE
		id = ' . (int)get_gp('user_id')
);

# Make sure at least one score exists!

# RELATIVE
$data['user_report']['relative_score_amount'] = get_db_single_value('
		count(*)
	FROM
		' . $config['mysql']['prefix'] . 'score r,
		' . $config['mysql']['prefix'] . 'link_team_user ltu
	WHERE
		r.team_id = ltu.team_id AND
		ltu.user_id = ' . (int)$_SESSION['login']['login_user_id'] . ' AND
		ltu.active = 1 AND
		destination_user_id = ' . (int)get_gp('user_id')
);

if ($data['user_report']['relative_score_amount']) {
	$data['user_report']['relative_grade_value_average'] = get_db_single_value('
			AVG(g.value)
		FROM
			' . $config['mysql']['prefix'] . 'score r,
			' . $config['mysql']['prefix'] . 'grade g,
			' . $config['mysql']['prefix'] . 'link_team_user ltu
		WHERE
			r.team_id = ltu.team_id AND
			ltu.user_id = ' . (int)$_SESSION['login']['login_user_id'] . ' AND
			ltu.active = 1 AND
			r.grade_id = g.id AND
			destination_user_id = ' . (int)get_gp('user_id')
	);
}

if ($data['user_report']['relative_score_amount']) {
	$data['user_report']['relative_grade_element_name'] = get_db_single_value('
			g.name
		FROM
			' . $config['mysql']['prefix'] . 'grade g
		WHERE
			g.value = ' . (int)$data['user_report']['relative_grade_value_average']
	);

}
 */

# FULL
/*
$data['user_report']['full_score_amount'] = get_db_single_value('
		count(*)
	FROM
		' . $config['mysql']['prefix'] . 'score
	WHERE
		destination_user_id = ' . (int)get_gp('user_id')
);

if ($data['user_report']['score_amount']) {
	$data['user_report']['full_grade_value_average'] = get_db_single_value('
			AVG(g.value)
		FROM
			' . $config['mysql']['prefix'] . 'score r,
			' . $config['mysql']['prefix'] . 'grade g
		WHERE
			r.grade_id = g.id AND
			destination_user_id = ' . (int)get_gp('user_id')
	);

	$data['user_report']['grade_element_name'] = get_db_single_value('
			e.name
		FROM
			' . $config['mysql']['prefix'] . 'grade g,
			' . $config['mysql']['prefix'] . 'element e
		WHERE
			e.id = g.element_id AND
			g.value = ' . (int)$data['user_report']['grade_value_average']
	);
}
*/
