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

# description: custom code based on the timeline for renewing a membership
# issue:
# - really rough code!
# - have to deal with dynamically changing cycles
# - have to accomodate for automated daily script
# - todo allow up to seconds in the cycles/renewals

# variable
$data['renewal_process']['cycle'] = array();
$data['renewal_process']['renewal'] = array();

# alias
$renewal = & $data['renewal_process']['renewal'];
$cycle = & $data['renewal_process']['cycle'];

/*
db table map
todo: explain value relations

channel
-------
id
parent_id
user_id
name
description
value
offset
modified
active

cycle
-----
id
channel_id
modified
active

renewal
-------
id
point_id
user_id
cycle_id
rating_value
value
modified
active
*/

# cycle length
# - min = 1 day?
# - max = 1 year? ( probably people wont use )
# - estimated norm = 30 days

# included from edit process
/*
$process = array(); # Send to process
$interpret = array(); # Interpreted from $process
$interpret['lookup'] = array();

$process['form_info'] = get_action_header_1();
foreach($process['form_info'] as $k1 => $v1)
	$process['form_info'][$k1] = get_gp($k1);

$process['action_content_1']  = get_action_content_1($process['form_info']['type'], 'edit');
$process['action_content_2']  = get_action_content_2($process['form_info']['type'], 'edit');

# shortcuts
$id = & $process['form_info']['id'];
$lookup = & $interpret['lookup'];
$action_content_1 = & $process['action_content_1'];
$action_content_2 = & $process['action_content_2'];
$prefix = & $config['mysql']['prefix'];
$type = & $process['form_info']['type'];
$message = & $interpret['message'];
$login_user_id = $_SESSION['login']['login_user_id'];
*/

# manual renewal chart
/*
S = Start
C = Continue
E = End
MR = Renewal (from form submit)
AR = Auto-Renewal (from daily script)

Manual Renew (from first start cycle)
S------E
or
S------C
S--MR--C------E (no autorenew chosen on MR)
S--MR--C------C (autorenew chosen on MR)

Auto Renew (from first start cycle)
S------E (dies here unless extended manually on before expiration)
or
S------C
S--AR--C------C (extended automatically pending sufficient funds on previous day)
*/

# only 2 fields received
# todo probably should be channel parent name
$process['action_content_1']['channel_name'] = get_gp('channel_name');
$process['action_content_1']['autorenew'] = get_gp('autorenew');

# only 1 field to translate
$lookup['channel_parent_id'] = get_db_single_value('
		parent_id
	from
		' . $prefix . 'channel cnl
	where
		name = ' . to_sql($process['action_content_1']['channel_name']) . ' and
		active = 1
');

process_field_missing('action_content_1');

# todo merge into:
# process_does_not_exist('action_content_1');
if (!$lookup['channel_parent_id']) {
	$message = tt('element', 'channel_name') . ' : ' . tt('element', 'error_does_not_exist');
}

if (empty($message))
	$message = 'forced fail: too abstract for the generic edit process - most checks are going to be custom';
process_failure($message);

# do it!
# prerequire only and at least 1 possible future cycle
# get 3 most recent non-future cycles
# min cycles that can exist at this point?
$sql = '
	select
		cce.id as cycle_id,
		cce.modified as cycle_modified,
		cnl.id as channel_id,
		cnl.parent_id as channel_parent_id,
		cnl.modified as channel_modified
	from
		' . $prefix . 'cycle cce,
		' . $prefix . 'channel cnl
	where
		cnl.id = cce.channel_id and
		cnl.id = ' . (int)$lookup['channel_parent_id'] . ' and
		cce.modified < now() and
		cce.active = 1 and
		cnl.active = 1
	order by
		cnl.modified desc
	limit
		3
';
# isn't it possible to have more than 1 future cycle?
## at present no need however funds can be amassed in the transaction table and autorenew can be set
# channel changes accounted for?

# easier to start
# already a member
$i1 = 1;
$result = mysql_query($sql) or die(mysql_error());
while ($row = mysql_fetch_assoc($result)) {
	# only works if all cycles are guaranteed to exist
	switch ($i1) {
		case '1':
			# if brand new team does this contain the channel data for inserting a renewal
			$cycle['current'] = $row;
		break;
		case '2':
			# key for current channel data when inseting renewal
			$cycle['previous'] = $row;
		break;
		case '3':
			$cycle['prepre'] = $row;
		break;
	}
	$i1++;
}

# prerequire
## cycle when channel is created
## all previous cycles

# future cycle
$sql = '
	select
		cce.id as cycle_id,
		cce.modified as cycle_modified,
		cnl.id as channel_id,
		cnl.parent_id as channel_parent_id,
		cnl.modified as channel_modified
	from
		' . $prefix . 'cycle cce,
		' . $prefix . 'channel cnl
	where
		cnl.id = cce.channel_id and
		cnl.id = ' . (int)$lookup['channel_parent_id'] . ' and
		cce.modified > now() and
		cce.active = 1 and
		cnl.active = 1
	order by
		cnl.modified asc
	limit
		2
';
$result = mysql_query($sql) or die(mysql_error());
$i1 = 1;
while ($row = mysql_fetch_assoc($result)) {
	# only care about the most immediat future cycle
	$cycle['future'] = $row;
	# only works if all cycles are guaranteed to exist
	switch ($i1) {
		case '1':
			# if brand new team does this contain the channel data for inserting a renewal
			$cycle['future'] = $row;
		break;
		case '2':
			# key for current channel data when inseting renewal
			$cycle['horizon'] = $row;
		break;
	}
	$i1++;
}

# get valid future channel data
if (!empty($cycle['current']['cycle_modified'])) {
	$sql = '
		select
			id as channel_id,
			parent_id as channel_parent_id,
			value as channel_value,
			offset as channel_offset
		from
			' . $prefix . 'channel
		where
			modified < ' . to_sql($cycle['current']['cycle_modified']) . '
		order by
			modified desc
		limit
			1
	';
	$result = mysql_query($sql) or die(mysql_error());
	while ($row = mysql_fetch_assoc($result)) {
		# overwriting should overwrite with same values if they exist
		$cycle['future']['channel_id'] = $row['channel_id'];
		$cycle['future']['channel_parent_id'] = $row['channel_parent_id'];
		$cycle['future']['channel_value'] = $row['channel_value'];
		$cycle['future']['channel_offset'] = $row['channel_offset'];

		$cycle['future']['cycle_modified'] = date('Y-m-d', strtotime($cycle['current']['cycle_modified']) + $row['channel_offset']*86400);
	}
}

# ensure future cycle exists in db
if (empty($cycle['future']['cycle_id'])) {
	$sql = '
		insert into
			' . $prefix . 'cycle
		set
			modified = ' . to_sql($cycle['future']['cycle_modified']) . ',
			channel_id = ' . (int)$cycle['future']['channel_id'] . ',
			active = 1
	';
	# $result = mysql_query($sql) or die(mysql_error());

	$cycle['future']['cycle_id'] = mysql_insert_id();
}
# future cycle now exists

# get valid horizon channel data
if (!empty($cycle['future']['cycle_modified'])) {
	$sql = '
		select
			id as channel_id,
			parent_id as channel_parent_id,
			value as channel_value,
			offset as channel_offset
		from
			' . $prefix . 'channel
		where
			modified < ' . to_sql($cycle['future']['cycle_modified']) . '
		order by
			modified desc
		limit
			1
	';
	$result = mysql_query($sql) or die(mysql_error());
	while ($row = mysql_fetch_assoc($result)) {
		# overwriting should overwrite with same values if they exist
		$cycle['horizon']['channel_id'] = $row['channel_id'];
		$cycle['horizon']['channel_parent_id'] = $row['channel_parent_id'];
		$cycle['horizon']['channel_value'] = $row['channel_value'];
		$cycle['horizon']['channel_offset'] = $row['channel_offset'];

		$cycle['horizon']['cycle_modified'] = date('Y-m-d', strtotime($cycle['future']['cycle_modified']) + $row['channel_offset']*86400);
	}
}
# ensure horizon cycle exists in db
if (empty($cycle['horizon']['cycle_id'])) {
	$sql = '
		insert into
			' . $prefix . 'cycle
		set
			modified = ' . to_sql($cycle['horizon']['cycle_modified']) . ',
			channel_id = ' . (int)$cycle['horizon']['channel_id'] . ',
			active = 1
	';
	# $result = mysql_query($sql) or die(mysql_error());

	$cycle['horizon']['cycle_id'] = mysql_insert_id();
}
# done with horizon cycle

######################
# done with cycle data

# get most recent renewal data
# renewal is more difficult than cycle because renewals are irregular
$sql = '
	select
		rnal.id as renewal_id,
		rnal.point_id,
		rnal.modified as renewal_modified
	from
		' . $prefix . 'renewal rnal,
		' . $prefix . 'cycle cce,
		' . $prefix . 'channel cnl
	where
		cce.id = rnal.cycle_id and
		cnl.id = cce.channel_id and
		cnl.parent_id = ' . (int)$lookup['channel_parent_id'] . ' and
		rnal.user_id = ' . (int)$_SESSION['login']['login_user_id'] . ' and
		rnal.modified >= ' . (int)$cycle['current']['modified'] . ' and
		rnal.active = 1 and
		cce.active = 1 and
		cnl.active = 1
	order by
		rnal.modified desc
	limit
		1
';
$result = mysql_query($sql) or die(mysql_error());
while ($row = mysql_fetch_assoc($result)) {
	$renewal['future'] = $row;
}

# todo get renewal horizon
# renewal horizon is neglected in the below logic
########################

# have to deal with 2 issues:
# renewal start
# S-------C/E
# ----C-------C/E

# current membership
$b1 = 2; # insert start? start_point
$b2 = 2; # insert continue/end? continue_point
$b3 = 2; # insert continue/end? end_point
if (!empty($renewal['future'])) {
	# point_id
	$i2 = 3;
	if (!empty($lookup['autorenew']))
		$i2 = 2;
	# future cycle?
	$i1 = strtotime($renewal['modified']);
	if ($i1 >= strtotime($cycle['future']['modified'])) {
		$sql = '
			update
				' . $prefix . 'renewal
			set
				modified = now(),
				point_id = ' . (int)$i2 . '
			where
				id = ' . (int)$renewal['id'] . '
			limit
				1
		';
		# mysql_query($sql) or die(mysql_error());
	}
	# current cycle
	if ($i1 < strtotime($cycle['future']['modified']))
	if ($i1 >= strtotime($cycle['current']['modified'])) {
		$b1 = 2;
		$b2 = 1;
	}
}
# new member / expired membership (previous cycle or earlier)
if ($b1 == 1 || $b2 == 1) {
	# todo how to compute payment
	# base on start and finish of
	## 2 cycles ago?
	### ensures everyone start receiving payment from 3-4 cycle lengths
	# todo
	## 2 renewals ago?
	### ensures everyone starts receiving payment after exactly 3 variable cycle lengths

	# if less than 2 cycles
	# just use $cycle['future']['channel_value']
	$cycle['future']['computed_renewal_value'] = $cycle['future']['channel_value'];

	# todo compute value from:
	# $cycle['future']['channel_value'];
	# $cycle['future']['my_rating_value']
	$cycle['future']['computed_rating_value'] = 0;
}

if ($b1 == 1) {
	$sql = '
		insert into
			' . $prefix . 'renewal
		set
			point_id = 1
			user_id = ' . (int)$_SESSION['login']['login_user_id'] . ',
			rating_value = ' . (int)$cycle['future']['computed_rating_value'] . ',
			value = ' . (double)$cycle['future']['computed_renewal_value'] . ',
			modified = now(),
			active = 1
		limit
			1
	';
}
if ($b2 == 1) {
	$sql = '
		insert into
			' . $prefix . 'renewal
		set
			point_id = 1
			user_id = ' . (int)$_SESSION['login']['login_user_id'] . ',
			rating_value = ' . (int)$cycle['future']['computed_rating_value'] . ',
			value = ' . (double)$cycle['future']['computed_renewal_value'] . ',
			modified = now(),
			active = 1
		limit
			1
	';
}

# todo hold funds charge transaction
## only know max price at this time discount will have to be calculated and refunded later

# set message
$message = 'made it to the end';
process_success(tt('element', 'transaction_complete') . ($email_sent ? ' : ' . tt('element', 'email_sent') : ''));
