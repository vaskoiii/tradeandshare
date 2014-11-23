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

# cycle length
# - min = 1 day?
# - max = 1 year? ( probably people wont use )
# - target = 30 days

# plan
# get the variable cycle data first
# future
# current
# previous
# then modifying renewals will be possible

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

# custom code
# echo '<pre>'; print_r($_POST); echo '</pre>';

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

# if (empty($message))
# 	$message = 'todo: too abstract for the generic adder? - most checks are going to be custom';
process_failure($message);

# do it renewal (manual)
########################


# easier to start
# already a member
$b1 = 2;
if (get_db_single_value('
		rnwl.user_id
	from
		' . $prefix . 'renewal rnwl,
		' . $prefix . 'cycle cce,
		' . $prefix . 'channel cnl
	where
		cce.id = rnwl.cycle_id and
		cnl.id = cce.channel_id and
		cnl.id = ' . (int)$lookup['channel_parent_id'] . ' and
		rnwl.user_id = ' . (int)$_SESSION['login']['login_user_id'] . ' and
		rnwl.active = 1 and
		cnl.active = 1
', 0))
	$b1 = 1;
if ($b1 != 2) {
	$message = 'value found assume always autorenewing for now until there is more complicated logic';
	# change to continue if previously was end
	# copied from user report

	# find the future cycle start time
	# cycle length can change though!
	$data['edit_process']['cycle_point']['unix_time'] = time();

	$cycle_point = & $data['edit_process']['cycle_point']; # alias
	$cycle_point['unix_time-1x'] = date('Y-m-d', strtotime(date('Y-m-d')) - 30*86400);
	$cycle_point['unix_time-2x'] = date('Y-m-d', strtotime($cycle_restart['yyyy-mm-dd']) - 60*86400);
	$cycle_point['unix_time-3x'] = date('Y-m-d', strtotime($cycle_restart['yyyy-mm-dd']) - 90*86400);

	# get cycle length
	$sql = '
		select
			offset
		from
			' . $prefix . 'channel cnl
		where
			cnl.parent_id = ' . $lookup['channel_parent_id'] . '
	';
	# todo fix hardcode
	$todo_channel_offset = 30;
	# todo get offset for future cycle, current cycle, and previous cycle because offset can potentially change every cycle

	$sql = '
		select
			rnal.id,
			rnal.point_id,
			rnal.modified
		from
			' . $prefix . 'renewal rnal,
			' . $prefix . 'cycle cce,
			' . $prefix . 'channel cnl
		where
			cce.id = rnal.cycle_id and
			cnl.id = cce.channel_id and
			rnal.user_id = ' . (int)$_SESSION['login']['login_user_id'] . ' and
			cnl.parent_id = ' . (int)$lookup['channel_parent_id'] . '
		order by
			rnal.modified desc
		limit
			1
	';
	$todo_array_last_modification = array(
		'renewal_id' => 0,
		'renewal_point_id' => 0,
		'renewal_modified' => '0000-00-00 00:00:00',
	);

	# last mod cycle?
	# might be better to get the cycle here and then do changes accordingly

	# future?
	$i1 = $a1['renewal_modified'];
	# current?
	if ($i1 > time()) {
	}
	# previous?
	if ($i1 > time()) {
	}
	
	$i1 = get_db_single_value('
			rnal.id
		from
			' . $prefix . 'renewal rnal,
			' . $prefix . 'cycle cce,
			' . $prefix . 'channel cnl
		where
			cce.id = rnal.cycle_id and
			cnl.id = cce.channel_id and
			rnal.modified >= now() and
			rnal.user_id = ' . (int)$_SESSION['login']['login_user_id'] . ' and
			cnl.parent_id = ' . (int)$lookup['channel_parent_id']
	);
	if (!empty($i1)) {
		if (!empty($lookup['autorenew'])) {
			$sql = '
				update
					' . $prefix . 'renewal rnal
				set
					modified = now(),
					point_id = 2
			';
		}
		else {
			$sql = '
				update
					' . $prefix . 'renewal rnal
				set
					modified = now(),
					point_id = 3
			';
		}
	}
	#this cycle (last mod)
	else {
		# find last modification for the team since it wasn't a future mod
		$sql = '
			select
				rnal.id,
				rnal.point_id
			from
				' . $prefix . 'renewal rnal,
				' . $prefix . 'cycle cce,
				' . $prefix . 'channel cnl
			where
				cce.id = rnal.cycle_id and
				cnl.id = cce.channel_id and
				rnal.user_id = ' . (int)$_SESSION['login']['login_user_id'] . ' and
				cnl.parent_id = ' . (int)$lookup['channel_parent_id'] . '
			order by
				rnal.modified desc
			limit
				1
		';
		$todo_last_renewal_id = 0;

		if (empty($todo_last_renewal_id)) {

			# if nothing found insert start
			# rating_value is 0 and will be recalculated later
			# todo get the value from the channel that was set before the end of last cycle
			$todo_last_cycle_value = 0;
			$sql = '
				insert into
					' . $prefix . 'renewal rnal
				set
					point_id = 1,
					user_id = ' . (int)$_SESSION['login']['login_user_id'] . ',
					rating_value = 0,
					value = ' . (int)$todo_last_cycle_value . ',
					modified = now(),
					active = 1
			';
		}
		else {
			# if last modification is this cycle add another renewal for the next future cycle

			# if last modification was before this cycle assume everything was already handled correctly
			# do nothing

		}
	}
	
	$sql = '
		select
			cce.channel_id
		from
			' . $config['mysql']['prefix'] . 'renewal rnal,
			' . $config['mysql']['prefix'] . 'cycle cce
		where
			cce.id = rnal.cycle_id and
			rnal.modified >= ' . to_sql($cycle_restart['yyyy-mm-dd-3x']) . '
	';
}
if ($b1 == 2) {
	$message = 'no value found';
}


#



process_failure($message);
# do nothing

# not yet a member
# add user to the table


# todo todo todo

# check to find last renewal
## no need to renew like this if autorenew is set

# not already a member
## add start
## add continue or end depending if autorenew is wanted

# already a member
## if within 1 cycle ahead modify future values
## if not within 1 cycle ahead add an entry
## add continue or end depending if autorenew is wanted

# todo hold funds charge transaction
## only know max price at this time discount will have to be calculated and refunded later

# set message
# process success
process_success(tt('element', 'transaction_complete') . ($email_sent ? ' : ' . tt('element', 'email_sent') : ''));

