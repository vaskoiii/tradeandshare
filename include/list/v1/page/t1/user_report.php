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

# Contents/Description: display member ratings (doesn't compute all ratings)

# todo if this is so useful should be moved to function.php
function print_key_user_id($k1) {
	global $config;
	global $key;

	if ($config['debug'] == 1) {
		echo $k1 . ': ';
	}
	echo to_html($key['user_id']['result'][$k1]['contact_name']);
	if (!empty($key['user_id']['result'][$k1]['user_name'])) {
		echo ' &lt;';
		echo to_html($key['user_id']['result'][$k1]['user_name']);
		echo '&gt;';
	}
}



print_break_open('user_report', 'page');
?>
<p>For a given cycle calculate partial credit from each member:</p>
<ul>
	<li>multiply the <strong>average</strong> of average unique-source-user ratings on the destination user</li>
	<li>by the <strong>weight</strong> of that average (1/number of unique members rated by the source user)</li>
	<li>by the <strong>time</strong> with membership for the destination user (before and after a midcycle renewal)</li>
</ul>
<p>To calculate full credit for each member sum all partial credit.</p>
<p>After calculating full credit for each member above, a multiplier is needed to calculate payout:</p>
<ul>
	<li>take the total cost of the cycle (minus a % for TS)</li>
	<li>divide by the sum of all credit</li>
</ul>
<p>The payout for indivuals is then just the multiplier times their credit.</p>
<p>
	<strong>Merit Key:</strong>
	none=0
	|
	quarter=1
	|
	half=2
	|
	triquarter=3
	|
	full=4
</p><?
print_break_close();
print_break_open('Premature'); 
if (!empty($data['user_report']['premature_channel_list'])) { ?> 
	<ul><?
	foreach($data['user_report']['premature_channel_list'] as $k1 => $v1) { ?> 
		<li style="display: inline; margin-right: 10px;"><?= $key['channel_id']['result'][$k1]['channel_name']; ?></li><?
	} ?> 
	</ul><?
}
else { ?>
	<p>No premature channels</p><?
}
print_break_close();
foreach ($channel as $kc1 => $vc1) {
	print_break_open($vc1['info']['name']); ?> 
	<dl>
		<dt>Length</dt>
		<dd><?= to_html($vc1['info']['time']); ?>  Days</li></dd><?
		# before and after cost is relative to the members ( since renewal can happen mid cycle )
		# the values are needed for computation ?> 
		<dt>Renewal Max Cost Before Cycle Start</dt>
			<dd>$<?= to_html($vc1['info']['before_cost']); ?></dd>
		<dt>Renewal Max Cost After Cycle Start</dt>
			<dd>$<?= to_html($vc1['info']['after_cost']); ?></dd><?
		if ($vc1['cycle_restart']['yyyy-mm-dd-1x']) { ?> 
			<dt>Cycle Start</dt>
				<dd><?= $vc1['cycle_restart']['yyyy-mm-dd-2x']; ?></dd><?
		}
		if ($vc1['cycle_restart']['yyyy-mm-dd-2x']) { ?> 
			<dt>Cycle End<dt>
				<dd><?= $vc1['cycle_restart']['yyyy-mm-dd-1x']; ?></dd><?
		} ?> 
	</dl>
	<h3>Member List</h3>
	<p><?
		if (!empty($channel[$kc1]['member_list'])) {
			foreach ($channel[$kc1]['member_list'] as $k2 => $v2) {
				print_key_user_id($k2);
				echo '<br />';
			}
		}
		else
			echo 'No Members'; ?> 
	</p>
	<p>
		Total: <?= count($channel[$kc1]['member_list']); ?> 
	</p>
	<h3>Computation</h3>
	<dl>
		<dt>Channel Total</dt>
		<dd>$<?
			$d1 = 0;
			if (!empty($channel[$kc1]['computed_cost']['combined']))
				$d1 = array_sum($channel[$kc1]['computed_cost']['combined']);
			echo to_html($d1); ?> 
		</dd>
		<dt>TS Cut (10% of Channel Total)</dt>
		<dd>$<?= .1 * $d1; ?></dd>
		<dt>Remaining to be distributed</dt>
		<dd>$<?= (.9 * $d1); ?></dd>
		<dt>Average Weight Sum</dt>
		<dd><?= array_sum($channel[$kc1]['average_weight_sum']); ?></dd><?
		if (array_sum($channel[$kc1]['average_weight_sum']) != 0) { ?> 
			<dt>Multiplier</dt>
			<dd><?
				$d2 = (.9 * $d1 ) / array_sum($channel[$kc1]['average_weight_sum']) ;
				
			?></dd><?
		}
		else { ?>
			<dt>Multiplier</dt>
			<dd>can not compute - No ratings</dd><?
		} ?> 
	</dl>
	<p>For breakdown please see public rating list of members</p><?
	if (!empty($channel[$kc1]['destination_user_id']))
	foreach ($channel[$kc1]['destination_user_id'] as $kd1 => $vd1) {
		$kid = & $channel[$kc1]['destination_user_id'][$kd1]; # alias ?> 
		<hr style="margin-bottom: 20px;" />
		<h3><?= print_key_user_id($kd1); ?></h3>
		<dl><?
			if (!empty($kid['source_user_id_rating_average']))
			foreach($kid['source_user_id_rating_average'] as $k1 => $v1) {
				$kis = & $channel[$kc1]['source_user_id'][$k1];
				$kisb = & $kis['before'];
				$kisa = & $kis['after'];
				?>  
				<dt><?
					print_key_user_id($k1); ?> 
				</dt>
				<dd>
					<table>
						<tr>
							<td> </td>
							<td> <?= $kid['source_user_id_rating_weight_math_before'][$k1]; ?> </td>
						</tr>
						<tr>
							<td style="padding-right: 10px;">+</td>
							<td style="border-bottom: 1px solid #000; padding-bottom: 10px;"> <?= $kid['source_user_id_rating_weight_math_after'][$k1]; ?> </td>
						</tr>
						<tr>
							<td> </td>
							<td style="padding-top: 10px;"><?= $kid['source_user_id_rating_weight'][$k1]; ?></td>
						</tr>
					</table>
				</dd><?
			}
			else
				echo 'No ratings'; ?> 
		</dl><?
		if (!empty($kid['source_user_id_rating_average'])) { ?> 
			<p>
				Total:
				<?= count($kid['source_user_id_rating_average']); ?>
			</p>
			<p>
				Credit:
				<?= $channel[$kc1]['average_weight_sum'][$kd1]; ?>
			</p>
			<p>
				Payout:
				<? /*$channel[$kc1]['average_average'][$kd1] * $d2 * count($kid['source_user_id_rating_average']); */ ?> 
				$<?= round($d2 * $channel[$kc1]['average_weight_sum'][$kd1], 2); ?>
			</p><?
		}
	}
	print_break_close();
}

