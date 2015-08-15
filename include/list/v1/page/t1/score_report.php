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

# Contents/Description: display member scores (doesn't compute all scores)

#alias
$channel_list = & $data['user_report']['channel_list'];

# todo if this is so useful should be moved to function.php
function print_key_user_id($k1) {
	global $config;
	global $key;

	if ($config['debug'] == 1) {
		echo $k1 . ': ';
	}
	echo to_html($key['user_id']['result'][$k1]['contact_name']);
	if (!empty($key['user_id']['result'][$k1]['user_name'])) {
		echo ' (';
		echo to_html($key['user_id']['result'][$k1]['user_name']);
		echo ')';
	}
}

# todo fix print_break_open to fix/accomodate the below div and !!! ts_focus
# global seach for [<div] to find the hacked divs
# print_break_open('user_report', 'page');
?>

<script>
	function score_report_focus(s1) { <?
		# hide everything
		foreach ($data['user_report']['channel'] as $k1 => $v1) {
			if (!isset($data['user_report']['premature_channel_list'][$v1['channel_id']])) { ?> 
				did('channel_<?= (int)$k1; ?>').style.display = 'none';<?
			}
		}
		# unhide 1 ?> 
		did(s1).style.display = 'block';
	}
</script>

<?
# only focus 1 channel to start
if (!empty($data['user_report']['premature_channel_list'])) { ?> 
	<div class="content_box"><?
	foreach($data['user_report']['premature_channel_list'] as $k1 => $v1) { ?> 
		<p><?= $key['channel_id']['result'][$k1]['channel_name']; ?> has no members eligible for payout</p><?
	} ?> 
	<? print_break_close(); ?>
	</div><?
}
elseif (empty($channel_list)) { ?>
	<div class="content_box">
		<p>No channel was selected</p>
	<? print_break_close(); ?>
	</div><?
}
else
foreach ($channel_list as $kc1 => $vc1) {
	# alias
	$channel = & $channel_list[$kc1]; ?>
	<div id="channel_<?= (int)$kc1; ?>" class="content_box"><?
	# todo only show data pertaining to the corresponding cycle
	$s1  = $vc1['info']['name'];
	$s1 .= ' : ';
	$s1 .= (isset_gp('cycle_id') ? (int)get_gp('cycle_id') : (int)$vc1['info']['cycle_id']);
	$s1 .= ($vc1['info']['cycle_id'] == $vc1['info']['latest_payout_cycle_id'] ? ' : latest' : '');
	echo '<h3>' . $s1 . '</h3>'; ?>

	<?  # timeline wanted? ie) get_next_cycle_id() and get_previous_cycle_id() ?>
	<p style="margin-top: 0px;">
		<a href="cycle_list/<?= ff('channel_parent_id=' . (int)$kc1); ?>">View All Cycles</a>*
		&gt;&gt; <a href="#" id="channel_<?= (int)$k1; ?>_summary_toggle" onclick="more_toggle('channel_<?= (int)$k1; ?>_summary'); return false;"><?= tt('element', 'more'); ?></a>
	</p>
	<div id="channel_<?= (int)$k1; ?>_summary" style="display: none;">
	<p>todo: Show active members that are not currently eligible for payout</p>
	<p>todo: Use a diminishing score to help normalize scores.</p>
	<p>todo: Factor in the carried over score.</p>
	<hr />
	<dl>
		<dt>If all users in a channel score only a single user with the same score the current expression is:</dt>
		<dd>score(number_of_users + 1)</dd>
	</dl>
	<p> <strong>Merit Key:</strong> Dislike = -1 and Like = 1 </p>
	<hr />
	<dl>
		<dt>Length</dt>
		<dd><?= to_html($vc1['info']['time']); ?>  Days</dd><?
		# before and after cost is relative to the members ( since renewal can happen mid cycle )
		# the values are needed for computation ?> 
		<dt>Renewal Max Cost Before Cycle Start</dt>
			<dd>$<?= to_html($vc1['info']['before_cost']); ?></dd>
		<dt>Renewal Max Cost After Cycle Start</dt>
			<dd>$<?= to_html($vc1['info']['after_cost']); ?></dd><?
		if ($vc1['cycle_restart']['yyyy-mm-dd-2x']) { ?> 
			<dt>Cycle End<dt>
				<dd><?= $vc1['cycle_restart']['yyyy-mm-dd-2x']; ?></dd><?
		}
		if ($vc1['cycle_restart']['yyyy-mm-dd-3x']) { ?> 
			<dt>Cycle Start</dt>
				<dd><?= $vc1['cycle_restart']['yyyy-mm-dd-3x']; ?></dd><?
		} ?> 
	</dl>
	<h3>Member List</h3>
	<p><?
		if (!empty($channel['member_list'])) {
			foreach ($channel['member_list'] as $k2 => $v2) {
				print_key_user_id($k2);
				echo '<br />';
			}
		}
		else
			echo 'No Members'; ?> 
	</p>
	<p>
		Total: <?= count($channel['member_list']); ?> 
	</p>
	<h3>Computation</h3>
	<dl>
		<dt>Channel Total</dt>
		<dd>$<?
			$d1 = 0;
			if (!empty($channel['computed_cost']['combined']))
				$d1 = array_sum($channel['computed_cost']['combined']);
			echo to_html($d1); ?> 
		</dd>
		<dt>Host Fee (<?= to_html($config['hostfee_percent']); ?>% of Channel Total)</dt>
		<dd>$<?= $d2 = .1 * $d1; ?></dd>
		<dt>Mission Cut (<?= (int)$channel['info']['percent']; ?>% of Remaining Total)</dt>
		<dd>$<?= $d4 = ($d1 - $d2) * (int)$channel['info']['percent'] * .01; ?></dd>
		<dt>Remaining to be distributed</dt>
		<dd>$<?= $d5 = $d1 - $d2 -$d4; ?></dd><?
		if (array_sum($channel['weighted_credit']) != 0) { ?> 
			<dt>Multiplier</dt>
			<dd><?=
				$d3 = ( $d5 ) / array_sum($channel['weighted_credit']) ;
			?></dd><?
		}
		else { ?>
			<dt>Multiplier</dt>
			<dd>can not compute - No scores</dd><?
		} ?> 
	</dl>
	<p>For breakdown please see public score list of members</p>
	</div><?
	if (!empty($channel['destination_user_id']))
	foreach ($channel['destination_user_id'] as $kd1 => $vd1) {
		$kid = & $channel['destination_user_id'][$kd1]; # alias ?> 
		<hr style="margin-bottom: 20px;" />
		<h3><?= print_key_user_id($kd1); ?></h3><? 
		if (1 || !empty($kid['source_user_id_score_average'])) { ?> 
				Time in Cycle:
				<?= $channel['source_user_id'][$kd1]['before']['time_weight'] + $channel['source_user_id'][$kd1]['after']['time_weight']; ?>
			<br />
				Weighted Credit:
				<?= !empty($channel['weighted_credit'][$kd1]) ? $channel['weighted_credit'][$kd1] : '0'; ?>
			<br />
				<strong>Payout</strong>:
				$<?= round($d3 * $channel['weighted_credit'][$kd1], 2); ?> 
			<?
		} ?> 
		<dl><?
			if (!empty($kid['source_user_id_score_average']))
			foreach($kid['source_user_id_score_average'] as $k1 => $v1) {
				$kis = & $channel['source_user_id'][$k1];
				$kisb = & $kis['before'];
				$kisa = & $kis['after'];
				?>  
				<dt><?
					print_key_user_id($k1); ?> 
				</dt>
				<dd>
					<?= $kid['source_user_id_score_weight_math_before'][$k1]; ?>
				</dd><?
			}
			else
				echo 'No scores'; ?> 
		</dl><?
	}
	print_break_close(); ?>
	</div><?
}
# hacked div to go with .content_box so that all divs have matching open/close ?> 
<div>

