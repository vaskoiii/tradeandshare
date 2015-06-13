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

# Description: Picture sitting at the counter scanning in guests
# issue:
#  injecting into official arrays (this page only!)

# login/contact name
# can cheat now because public_key = user_id
# this page is similar to the view page but totally custom potentially integrate into the menu later
# gets too much information all we really needed was the user name
start_engine($data['view'], 'user', $_SESSION['login']['login_user_id'], array((int)get_gp('public_key')), 'view');

# gets contact id
listing_key_translation($key, $translation, $data['view'], 'user', $_SESSION['login']['login_user_id']);
# Still have to get the contact name

# need to set the search criteria
# can we do like:
# $data['search']['response']['search_content_1']['direction_name'] = '';
# $data['search']['response']['search_content_1']['lock_user_name'] = get_gp('public_key');
#can we do like:
# todo gets top 10 but we only need top 3
$a1 = $_GET;
unset($_GET);
$_GET['direction_id'] = 1;
$_GET['lock_user_id'] = (int)$a1['public_key']; # cheating again by using publick key as the user id
# start_engine($data['result'], 'score', $_SESSION['login']['login_user_id'], array());
unset($_GET);
$_GET = $a1;


#					(int)get_gp('public_key') . '
# my team
$sql = '
	select
		tm.id team_id,
		tm.name team_name,
		tm.user_id as owner_id
	from
		' . $config['mysql']['prefix'] . 'team tm,
		' . $config['mysql']['prefix'] . 'link_team_user tme
	where
		tm.id = tme.team_id and
		tm.user_id in (' .  (int)$_SESSION['login']['login_user_id'] . ')
';
$result = mysql_query($sql) or die(mysql_error());
while ($row = mysql_fetch_assoc($result)) {
	$data['host_portal']['my_team'][$row['team_id']] = $row;
}		
# my teammate
$sql = '
	select
		tm.id team_id,
		tm.name team_name,
		tm.user_id as owner_id
	from
		' . $config['mysql']['prefix'] . 'team tm,
		' . $config['mysql']['prefix'] . 'link_team_user tme
	where
		tm.id = tme.team_id and
		tme.user_id in (' .  (int)$_SESSION['login']['login_user_id'] . ')
';
$result = mysql_query($sql) or die(mysql_error());
while ($row = mysql_fetch_assoc($result)) {
	$data['host_portal']['my_team'][$row['team_id']] = $row;
}		

# build implodes
foreach ($data['host_portal']['my_team'] as $k1 => $v1) {
	$data['host_portal']['my_team_implode'][$k1] = $v1['team_id'];
}

# todo distinguish teams that both members are not on from where both members ie) owner but not on the team (should be disallowed)
# todo don't allow users to not be on teams that they own
# shared team
$sql = '
	select
		tm.id team_id,
		tm.name team_name
	from
		' . $config['mysql']['prefix'] . 'team tm,
		' . $config['mysql']['prefix'] . 'link_team_user tme
	where
		tm.id = tme.team_id and
		(
			tm.user_id in (' .  (int)get_gp('public_key') . ') or
			tme.user_id in (' .  (int)get_gp('public_key') . ')
		) and
		tm.id in (' . implode(',', $data['host_portal']['my_team_implode']) . ')
';
$result = mysql_query($sql) or die(mysql_error());
while ($row = mysql_fetch_assoc($result)) {
	$data['host_portal']['shared_team'][$row['team_id']] = $row;
}		
