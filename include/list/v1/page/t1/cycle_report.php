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
# only focus 1 channel to start

if (!empty($data['user_report']['premature_channel_list'])) { ?> 
	<div class="content_box"><?
	foreach($data['user_report']['premature_channel_list'] as $k1 => $v1) {
		echo '<pre>'; print_r($data['user_report']['premature_channel_list']['info']); echo '</pre>';
		$s1 = $key['channel_id']['result'][$v1['info']['channel_parent_id']]['channel_name'];
		$s1 .= ' : ';
		$s1 .= (isset_gp('cycle_id') ? (int)get_gp('cycle_id') : (int)$vc1['info']['cycle_id']);
		echo '<h3>' . $s1 . '</h3>'; ?>
		<p>cycle not ready for payout</p><?
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
	$channel = & $channel_list[$kc1];
	$payout = & $channel['payout']; ?>
	<div id="channel_<?= (int)$kc1; ?>" class="content_box"><?
	$s1  = $vc1['info']['name'];
	$s1 .= ' : ';
	$s1 .= (isset_gp('cycle_id') ? (int)get_gp('cycle_id') : (int)$vc1['info']['cycle_id']);
	if (isset($vc1['info']['cycle_id']))
		$s1 .= ($vc1['info']['cycle_id'] == get_latest_payout_cycle_id($kc1) ? ' : latest' : '');
	echo '<h3>' . $s1 . '</h3>'; ?>
	<ul style="margin-top: 0px;">
		<li>Average Like Weight: <?= $channel['computed_weight']['aggregate']['info']['average']; ?></li>
	</ul>
	<p style="margin-top: 0px;">
		<a href="cycle_list/<?= ff('channel_parent_id=' . (int)$kc1); ?>">View All Cycles</a>*
		&gt;&gt; <a href="#" id="channel_<?= (int)$k1; ?>_summary_toggle" onclick="more_toggle('channel_<?= (int)$k1; ?>_summary'); return false;"><?= tt('element', 'more'); ?></a>
	</p>
	<div id="channel_<?= (int)$k1; ?>_summary" style="display: none;">
	
	<h3>About Likes/Dislikes</h3>
	<p>You do not get any credit for liking yourself</p>
	<p>Only net likes to a user are counted. ie) 3 likes and 1 dislike has a net value of 2</p>
	<p>Disliking a person can only go as far as bringing their net likes down to 0. ie) 2 dislikes and 1 likes is 0 (not -1)</p>
	<h3>Notes</h3>
	<ul>
		<li>No caps on payout based on time in cycle</li>
		<li>No credit for "voting"</li>
		<li>Equal payout for everyone with equal marks and equal time in cycle</li>
	</ul>
	<h3>Definitions</h3>
	<dl>
		<dt>Like</dt>
			<dd>+1</dd>
		<dt>Dislike</dt>
			<dd>-1</dd>
		<dt>Diminishing Carry</dt>
			<dd>1/(2^offset)</dd>
			<dd>Marks from the previous 3 cycles are carried over with this formula</dd>
		<dt>Freebie</dt>
			<dd>50% of the time you have in a cycle</dd>
			<dd>Equality part in the big tradeoff of economics</dd>
	</dl>
	<h3>Cycle Data</h3>
	<dl>
		<dt>Length</dt>
		<dd><?= to_html($vc1['info']['time']/86400); ?>  Days</dd><?
		# todo check that before and after cycle cost is handled correctly
		# todo ie) is the member paying the correct amount for their renewal
		# before and after cost is relative to the members ( since renewal can happen mid cycle )
		# the values are needed for computation ?> 
		<dt>Renewal Max Cost Before Cycle Start</dt>
			<dd>
				<?= nod() . to_html($vc1['info']['before_cost']); ?>
			</dd>
		<dt>Renewal Max Cost After Cycle Start</dt>
			<dd>
				<?= nod() . to_html($vc1['info']['after_cost']); ?>
			</dd><?
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
		<dt>Renewal Total</dt>
		<dd>
			<?= nod() . to_html($channel['renewal']['total']); ?>
		<dd>
		<dt>Sponsor Total</dt>
		<dd>
			<?= nod() . to_html($channel['sponsor']['total']); ?>
		<dd>
		<dt>Channel Grand Total</dt>
		<dd>
			<?= nod() . to_html($payout['total']); ?>
		</dd>
		<dt>Host Fee (<?= to_html($config['hostfee_percent']); ?>% of Channel Total)</dt>
		<dd>
			<?= nod() . to_html($payout['hostfee']); ?>
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
		<dd>
			<?= nod() . to_html($payout['missionfee']); ?> (<?= to_html($channel['info']['user_name']); ?>)
		</dd>
		<dt>Remaining to be distributed</dt>
		<dd>
			<?= nod() . to_html($payout['remainder']); ?>
		</dd>
		<dt>Multiplier</dt>
		<dd><?= !empty($payout['multiplier']) ? to_html($payout['multiplier']) : 'can not compute - no weighted credit'; ?></dd>
	</dl>
	<p>For breakdown please see public score list of members</p>
	<h3>Data Dump</h3>
	<pre>
		<? print_r($channel); ?>
	</pre>
	</div><?
	# allow increasing sponsor but never decreasing
	# as such there can be many sponsors for the same cycle
	# $channel['donate_user_id'] built in the controller

	print_break_close();
	print_break_open('Sponsor');
		if (!empty($channel['donate_value'])) {
		foreach ($channel['donate_value']['user_id'] as $ks1 => $vs1) { ?> 
			<h4>
				<?= (int)$ks1; ?>:
				<?= to_html($key['user_id']['result'][$ks1]['contact_name']); ?>
				(<?= to_html($key['user_id']['result'][$ks1]['user_name']); ?>)
			</h4>
			<p><?= nod() . '-' . (double)number_format($vs1, 2); ?></p><?
		} }
		else { ?> 
			<p>No sponsors this cycle =(</p><?
		}
	print_break_close();
	print_break_open('Member');
	if (!empty($channel['destination_user_id']))
	foreach ($channel['computed_weight']['aggregate']['weighted_credit'] as $kd1 => $vd1) {
		$kid = & $channel['destination_user_id'][$kd1]; # alias ?> 
		<h3><?= print_key_user_id($kd1); ?></h3><? 
		if (1) { ?> 
				Time Weight:
				<?= $channel['source_user_id'][$kd1]['before']['time_weight'] + $channel['source_user_id'][$kd1]['after']['time_weight']; ?>
			<br />
				Like Weight:
				<?= $channel['computed_weight']['aggregate']['weight_sum'][$kd1]; ?>
			<br />
				<strong>Payout</strong>:
				<?= nod() . to_html($payout['user_id'][$kd1]); ?><?
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
					<?= ($channel['computed_weight']['aggregate']['nmath'][$kd1]); ?>
					<!-- <span style="color: #777;"><?= ($channel['computed_weight']['aggregate']['weighted_credit_math'][$kd1]); ?></span> -->
					<span style="color: #777;"><?= ($channel['source_user_id'][$kd1]['combined']['time_weight'])*$equalizer; ?></span>
				</dd>
		</dl><?
	}
	print_break_close();
	print_break_open('Mission');
		# will only have multiple  people if master slave channels are implemented
		if (!empty($payout['missionfee'])) { ?> 
			<h4>
				<?= to_html($channel['info']['user_id']); ?>:
				<?= to_html($key['user_id']['result'][$channel['info']['user_id']]['contact_name']); ?>
				(<?= to_html($channel['info']['user_name']); ?>)
			</h4>
			<p><?= nod() . to_html(number_format($payout['missionfee'], 2)); ?></p><?
		}
		else { ?> 
			<p>No mission fee this cycle</p><?
		}
	print_break_close();
	print_break_open('Hosting');
		# will only have multiple people if hosts can be linked together (decentralized)
		if (!empty($payout['hostfee'])) { ?> 
			<h4>
				<?= (int)$config['hostfee_user_id']; ?>:
				<?= to_html($key['user_id']['result'][$config['hostfee_user_id']]['contact_name']); ?>
				(<?= to_html($key['user_id']['result'][$config['hostfee_user_id']]['user_name']); ?>)
			</h4>
			<p><?= nod() . to_html($payout['hostfee']); ?></p><?
		}
		else { ?> 
			<p>No hosting fee this cycle =)</p><?
		}
	print_break_close(); ?>
	</div><?
}
# hacked div to go with .content_box so that all divs have matching open/close ?> 
<div>

