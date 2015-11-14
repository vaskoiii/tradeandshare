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
	foreach($data['user_report']['premature_channel_list'] as $k1 => $v1) {
		echo '<pre>'; print_r($data['user_report']['premature_channel_list']['info']); echo '</pre>';
		$s1 = $key['channel_id']['result'][$v1['info']['channel_parent_id']]['channel_name'];
		$s1 .= ' : ';
		$s1 .= (isset_gp('cycle_id') ? (int)get_gp('cycle_id') : (int)$vc1['info']['cycle_id']);
		echo '<h3>' . $s1 . '</h3>'; ?>
		<p>cycle not ready for payout</p>
		<? # todo use ffm() ?>
		<p>please see the <a href="<?= ffm('cycle_id=', 0); ?>">latest cycle for payout</a><?
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
	$s1  = $vc1['info']['name'];
	$s1 .= ' : ';
	$s1 .= (isset_gp('cycle_id') ? (int)get_gp('cycle_id') : (int)$vc1['info']['cycle_id']);
	if (isset($vc1['info']['cycle_id']))
		$s1 .= ($vc1['info']['cycle_id'] == get_latest_payout_cycle_id($kc1) ? ' : latest' : '');
	echo '<h3>' . $s1 . '</h3>'; ?>
	<ul style="margin-top: 0px;">
		<li>Average Credit: <?= $channel['computed_weight']['aggregate']['info']['average']; ?></li>
		<li>Range of Difference: 
			<?= $channel['computed_weight']['aggregate']['info']['lowest']; ?>
			 to 
			<?= $channel['computed_weight']['aggregate']['info']['highest']; ?>
		</li>
		<li>Warning: Current algorithm only works if "Time in Cycle" is 1 for everyone</li>
	</ul>
	<p style="margin-top: 0px;">
		<a href="cycle_list/<?= ff('channel_parent_id=' . (int)$kc1); ?>">View All Cycles</a>*
		&gt;&gt; <a href="#" id="channel_<?= (int)$k1; ?>_summary_toggle" onclick="more_toggle('channel_<?= (int)$k1; ?>_summary'); return false;"><?= tt('element', 'more'); ?></a>
	</p>
	<div id="channel_<?= (int)$k1; ?>_summary" style="display: none;">
	
	<h3>Pending Modifications with Likes/Dislikes</h3>
	<p><strong>Merit Key:</strong> Dislike = -1 and Like = 1</p>
	<p>You do not get any credit for liking yourself</p>
	<p>Only net likes to a user are counted. ie) 3 likes and 1 dislikes has a net value of 2</p>
	<p>When liking a user the source user difference from the average should remain the same</p>
	<p>Liking a person is essentially increasing that one person's payout while increasing payout for everyone else except for you.</p>
	<p>Disliking a person can only go as far as bringing your net likes down to 0. ie) 11 dislike and 2 likes is 0 (not -9)</p>
	<p>A like can be thought of as putting weight on the liked user and taking that same weight equally from all of the other users such that the liking user's difference from the average will change by 0</p>
	<dl>
		<dt>Minimum Offset (freebie for everyone) occurs if a user received no likes:</dt>
		<dd>-1 - 1/(n-2)</dd>
		<dt>Offset Equalizer is applied to users that are not the source or destination user:</dt>
		<dd>-1/(n-2)</dd>
		<dt>The previous 3 cycle scores are be carried over with a diminishing weight of:</dt>
		<dd>1/(2^previous_cycle_offset)</dd>
		<dt>Maximum Offset occurs if all users in a channel like only the same single user in a single cycle:</dt>
		<dd>number_of_users - 1</dd>
	</dl>
	<p>To facilitate easier calculation it may be better to calculate with just the source and destination users and then getting the average and difference from the average last</p>
	<h3>Additional Todo</h3>
	<p>Time of the destination user in the cycle needs to be accounted for</p>
	<hr />
	<h3>Cycle Data</h3>
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
		<dt>Cycle Carry</dt>
			<dd><?= $config['cycle_carry']; ?></dd>
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
		<dd>$<?= $d2 = $config['hostfee_percent'] / 100 * $d1; ?>
		       	(<?= 
			get_db_single_value('
					name
			 	from
					' . $config['mysql']['prefix'] . 'user
				where
					id = ' . (int)$config['hostfee_user_id']
				, 0); ?>)
		</dd>
		<dt>Mission Cut (<?= (int)$channel['info']['percent']; ?>% of Remaining Total)</dt>
		<dd>$<?= $d4 = ($d1 - $d2) * (int)$channel['info']['percent'] * .01; ?> (<?= to_html($channel['info']['user_name']); ?>)</dd>
		<dt>Remaining to be distributed</dt>
		<dd>$<?= $d5 = $d1 - $d2 -$d4; ?></dd><?
		if (array_sum($channel['computed_weight']['aggregate']['weight_sum']) != 0) { ?> 
			<dt>Multiplier</dt>
			<dd><?= 
				$d3 = ( $d5 ) / array_sum($channel['computed_weight']['aggregate']['weighted_credit']);
			?></dd><?
		}
		else { ?>
			<dt>Multiplier</dt>
			<dd>can not compute - No scores</dd><?
		} ?> 
	</dl>
	<p>For breakdown please see public score list of members</p>
	<pre>
		<? print_r($channel); ?>
	</pre>
	</div><?
	if (!empty($channel['destination_user_id']))
	foreach ($channel['computed_weight']['aggregate']['weighted_credit'] as $kd1 => $vd1) {
	# foreach ($channel['computed_weight']['aggregate']['weight_sum'] as $kd1 => $vd1) {
		$kid = & $channel['destination_user_id'][$kd1]; # alias ?> 
		<hr style="margin-bottom: 20px;" />
		<h3><?= print_key_user_id($kd1); ?></h3><? 
		if (1) { ?> 
				Time in Cycle:
				<?= $channel['source_user_id'][$kd1]['before']['time_weight'] + $channel['source_user_id'][$kd1]['after']['time_weight']; ?>
			<br />
				Average Difference:
				<?= $channel['computed_weight']['aggregate']['average_difference'][$kd1]; ?>
			<br />
				Weighted Credit: <?
					$d1 = $channel['computed_weight']['aggregate']['weighted_credit'][$kd1];
					echo !empty($d1) ? $d1 : '0';
				?> 
			<br />
				<strong>Payout</strong>:
				$<?= round($d3 * $channel['computed_weight']['aggregate']['weighted_credit'][$kd1], 2); ?><?
		} ?> 
		<dl><?
			$a1p = array(); # todo fix looping so this is not needed
			if (!empty($kid['score_offset'])) {
			foreach($kid['score_offset'] as $k0 => $v0)
				if (!empty($v0['score_average']))
				foreach($v0['score_average'] as $k1 => $v1) {
					$kis = & $channel['source_user_id'][$k1];
					$kisb = & $kis['before'];
					$kisa = & $kis['after'];
					if (empty($a1p[$k1])) {
					$a1p[$k1] = $k1; # todo fix looping so marking for repeats is not necessary
					?>  
					<dt><?
						print_key_user_id($k1); ?> 
					</dt>
					<dd><?
						for ($i1 = 0; $i1 <= $config['cycle_carry']; $i1++) {
							$s1 = $kid['score_offset'][$i1]['score_weight_math'][$k1];
							if (!empty($s1)) { ?> 
								<?= $s1; ?><br /><?
							}
						}
						echo '<p>'; print_r($kid['aggregate']['this_score_weight_math'][$k1]); echo '</p>';
						if (!empty($kid['aggregate']['this_score_weight_math'][$kd1]))
							echo $kid['aggregate']['this_score_weight_math'][$kd1]  . '<br />'; ?> 
					</dd><?
					}
				}
			}
			else
				echo 'No scores'; ?> 
			<dt>System</dt>
				<dd>
					<?= to_html($channel['computed_weight']['aggregate']['nmath'][$kd1]); ?>
					<span style="color: #777;"><?= to_html($channel['computed_weight']['aggregate']['weighted_credit_math'][$kd1]); ?></span>
				</dd>
		</dl><?
	}
	print_break_close(); ?>
	</div><?
}
# hacked div to go with .content_box so that all divs have matching open/close ?> 
<div>

