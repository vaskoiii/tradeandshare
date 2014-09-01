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

# Contents/Description: Placeholder for user_report


?> 
<div class="title">
	<h2><?= tt('page', 'user_report'); ?></h2>
</div>
<div class="content">

<div class="content_box ">
	<p> This page is not intended to be in production it is just an attempt at a proof of concept.</p>
	<p> Get the average of the average viewable source user rating on the destination user for "source user" members only.</p>
	<p> When practically computing ratings the following values are used (can't make people pay if they have a negative rating)</p>
	<p> To start only 1 "channel" is looked at and only member's user_ids are arbitrarily specified as there is not yet a member list</p>
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
	</table><?

foreach ($channel as $kc1 => $vc1) { ?> 
	<h3>Member List</h3>
	<p><?= implode(', ', $channel[$kc1]['member_list']); ?></p>
	<p>
		Total: <?= count($channel[$kc1]['member_list']); ?> 
	</p>
	<h3>Cost</h3>
	<p>
		Cost per Channel: $<?= $channel[$kc1]['info']['cost']; ?> per <?= $channel[$kc1]['info']['name']; ?>
		<br />
		Channel Total: $<?= $d1 = count($channel[$kc1]['member_list']) * $channel[$kc1]['info']['cost']; ?>
		<br />
		TS Cut (10% of Channel Total): $<?= .1 * $d1; ?>
		<br />
		Multiplier: <?= $d2 = (.9 * $d1 ) / array_sum($channel[$kc1]['average_sum']) ; ?> 
	</p>
	<p>For breakdown please see public rating list of members</p><?
	foreach ($channel[$kc1]['destination_user_id'] as $kd1 => $vd1) {
		# alias
		$kid = & $channel[$kc1]['destination_user_id'][$kd1]; ?>  

		<hr style="margin-bottom: 20px;" />
		<h3><?= $kd1; ?> - Average Rating per Member</h3>
		<p><?
			foreach($kid['source_user_id_rating_average'] as $k1 => $v1) { ?> 
				<?= $k1; ?> = <?= $v1; ?><br /><?
			} ?> 
		</p>
		<p>
			&sum;
			/
			<?= count($kid['source_user_id_rating_average']); ?>
			=
			<?= $channel[$kc1]['average_average'][$kd1]; ?>
		</p>
		<p>
			$<?= $channel[$kc1]['average_average'][$kd1] * $d2 * count($kid['source_user_id_rating_average']); ?> 
		</p><?
	}
} ?> 
</div>

<div class="menu_1">
</div>

<div class="menu_2">
</div>

</div>
