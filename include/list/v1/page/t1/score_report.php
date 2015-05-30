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
		echo ' (';
		echo to_html($key['user_id']['result'][$k1]['user_name']);
		echo ')';
	}
}

# todo fix print_break_open to fix/accomodate the below div and !!! ts_focus
# global seach for [<div] to find the hacked divs
# print_break_open('user_report', 'page');
?>
<div class="content_box">

<div>
<form action="." method="GET">
	<select name="channel_id">
		<option></option><?
		foreach ($data['score_report']['channel'] as $k1 => $v1) { ?> 
			<option value="<?= (int)$v1['channel_id']; ?>" <?= get_gp('channel_id') == $k1 ? 'selected="selected"' : ''; ?>><?= to_html($v1['channel_name']); ?></option><?
		}
		?>
	</select>

	<input type="submit" value="!" />
	&gt;&gt;
	<a href="#" id="score_readme_toggle" onclick="more_toggle('score_readme'); return false;"><?= tt('element', 'more'); ?></a>
</form>
</div>
<div id="score_readme" style="display: none;">
<dd>todo: Allow channel owners to have a separate transparent and accountable fund to spend on facilities. Will change the dynamic of the channel significantly to have a shared fund controlled by the channel owner.</dd>
<p>todo: Use a diminishing score to help normalize ratings</p>
<ul>
	<li>todo: factor in the carried over score</li>
	<li>todo: rewrite payout based on score and carry</li>
</ul>
<p>todo: explain with words the new algorithym</p>
<p>
	<strong>Merit Key:</strong>
	none=0
	|
	quarter=1
</p>
</div>
<?
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
		<dt>Mission Cut (0% of Channel Total)</dt>
		<dd>$0</dd>
		<dt>Remaining to be distributed</dt>
		<dd>$<?= (.9 * $d1); ?></dd><?
		if (array_sum($channel[$kc1]['weighted_credit']) != 0) { ?> 
			<dt>Multiplier</dt>
			<dd><?=
				$d3 = (.9 * $d1 ) / array_sum($channel[$kc1]['weighted_credit']) ;
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
		<h3><?= print_key_user_id($kd1); ?></h3><? 
		if (1 || !empty($kid['source_user_id_rating_average'])) { ?> 
				Time in Cycle:
				<?= $channel[$kc1]['source_user_id'][$kd1]['before']['time_weight'] + $channel[$kc1]['source_user_id'][$kd1]['after']['time_weight']; ?>
			<br />
				Weighted Credit:
				<?= !empty($channel[$kc1]['weighted_credit'][$kd1]) ? $channel[$kc1]['weighted_credit'][$kd1] : '0'; ?>
			<br />
				<strong>Payout</strong>:
				$<?= round($d3 * $channel[$kc1]['weighted_credit'][$kd1], 2); ?> 
			<?
		} ?> 
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
					<?= $kid['source_user_id_rating_weight_math_before'][$k1]; ?>
				</dd><?
			}
			else
				echo 'No ratings'; ?> 
		</dl><?
	}
	print_break_close();
}
# hacked div to go with .content_box so that all divs have matching open/close ?> 
<div>

