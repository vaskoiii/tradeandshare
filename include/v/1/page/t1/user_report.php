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
?> 
<div class="title">
	<h2><?= tt('page', 'user_report'); ?></h2>
</div>
<div class="content">

<div class="content_box ">
	<p> This page is not intended to be in production it is just an attempt at a proof of concept.</p>
	<p> Calculate credit for each user (members only) by multiplying:</p>
	<ul>
		<li>source user average</li>
		<li>1 / source user rating on unique users</li>
		<li>source user membership cost as a percent of max membership cost</li>
		<li>source user timeframe percentage for current membership period</li>
	</ul>
	<p> From there add up all the calculated weighted credit for each member and distribute accordingly </p>
	<p> You can not get negative credit from ratings so no money will be owed beyond membership</p>
	<p> To start only 1 "channel" is looked at and member user_ids are arbitrarily specified</p>
	<style>
		td {
			padding-left: 20px;
			}
		table {
			margin-bottom: 20px;
			}
	</style>
	<table>
		<tr>
			<td>merit_none</td>
			<td>0</td>
		</tr>
		<tr>
			<td>merit_quarter</td>
			<td>1</td>
		</tr>
		<tr>
			<td>merit_half</td>
			<td>2</td>
		</tr>
		<tr>
			<td>merit_triquarter</td>
			<td>3</td>
		</tr>
		<tr>
			<td>merit_full</td>
			<td>4</td>
		</tr>
	</table>
	<h3>Cycle</h3>
	<p>
		<?= $data['user_report']['cycle_restart']['yyyy-mm-dd']; ?>
		to
		<?= $data['user_report']['cycle_restart']['previous_restart']; ?>
	</p>
	<!--
		<p>
			TODO: Previous Pot: $0 (only non-zero if no member to member ratings)
		</p>
	--><?

foreach ($channel as $kc1 => $vc1) { ?> 
	<h3>Member List</h3>
	<p><?= implode(', ', $channel[$kc1]['member_list']); ?></p>
	<p>
		Total: <?= count($channel[$kc1]['member_list']); ?> 
	</p>
	<h3>Cost</h3>
	<p>
		Max Cost per Channel: $<?= $channel[$kc1]['info']['cost']; ?> per <?= $channel[$kc1]['info']['name']; ?>
		<br />
		Computed Channel Total: $<?= $d1 = array_sum($channel[$kc1]['weight_cost']); ?>
		<br />
		TS Cut (10% of Channel Total): $<?= .1 * $d1; ?>
		<br />
		Remaining to be distributed: <?= (.9 * $d1); ?>
		<br />
		Average Weight Sum: <?= array_sum($channel[$kc1]['average_weight_sum']); ?>
		<br /><?
		if (array_sum($channel[$kc1]['average_weight_sum']) != 0) { ?> 
			Multiplier: <?= $d2 = (.9 * $d1 ) / array_sum($channel[$kc1]['average_weight_sum']) ; ?><?
		}
		else
			echo 'Multiplier: can not compute - No ratings'; ?> 
	</p>
	<p>For breakdown please see public rating list of members</p><?
	foreach ($channel[$kc1]['destination_user_id'] as $kd1 => $vd1) {
		$kid = & $channel[$kc1]['destination_user_id'][$kd1]; # alias ?> 
		<hr style="margin-bottom: 20px;" />
		<h3><?= $kd1; ?> - Average Rating per Member</h3>
		<dl><?
			if (!empty($kid['source_user_id_rating_average']))
			foreach($kid['source_user_id_rating_average'] as $k1 => $v1) {
				$kis = & $channel[$kc1]['source_user_id'][$k1]; ?>  
				<dt>
					<?= $k1; ?>
					=&gt;
					<?= $v1 * $kis['count_weight'] * $kis['cost_weight'] * $kis['time_weight']; ?></dt>
				</dt>
				<dd>
					<?= $v1; ?>
					*
					<?= $kis['count_weight']; ?>
					*
					<?= $kis['cost_weight']; ?>
					*
					<?= $kis['time_weight']; ?>
				</dd><?
			}
			else
				echo 'No ratings'; ?> 
		</dl><?
			if (!empty($kid['source_user_id_rating_average'])) { ?> 
		<p>
			&sum;
			/
			<?= count($kid['source_user_id_rating_average']); ?>
			=
			<?= $channel[$kc1]['average_weight_sum'][$kd1]; ?>
		</p>
		<p>
			<? /*$channel[$kc1]['average_average'][$kd1] * $d2 * count($kid['source_user_id_rating_average']); */ ?> 
			$<?= $d2 * $channel[$kc1]['average_weight_sum'][$kd1]; ?>
		</p><?
			}
	}
} ?> 
</div>

<div class="menu_1">
</div>

<div class="menu_2">
</div>

</div>
