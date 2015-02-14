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
	<p>Proof of Concept: Calculate credit for each user (members only) by multiplying:</p>
	<ul>
		<li>source user average</li>
		<li>1 / source user rating on unique users</li>
		<li>source user membership cost as a percent of max membership cost</li>
		<li>source user timeframe percentage for current membership period</li>
	</ul>
	<p>From there add up all the calculated weighted credit for each member and distribute accordingly.</p>
	<h3>Merit</h3>
	<p>
		none: 0
		-
		quarter: 1
		-
		half: 2
		-
		triquarter: 3
		-
		full: 4
	</p>
	<hr style="margin: 20px 0px;" />
	<!--
		<p>
			TODO: Previous Pot: $0 (only non-zero if no member to member ratings)
		</p>
	--><?
	if (empty($channel)) { ?> 
		<p>All channels are premature</p><?
	}

foreach ($channel as $kc1 => $vc1) { ?> 
	<h3>Channel</h3>
	<dl>
		<dt>Name</dt>
		<dd><?= to_html($vc1['info']['name']); ?></dd>
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
				<dd><?= $vc1['cycle_restart']['yyyy-mm-dd-1x']; ?></dd><?
		}
		if ($vc1['cycle_restart']['yyyy-mm-dd-2x']) { ?> 
			<dt>Cycle End<dt>
				<dd><?= $vc1['cycle_restart']['yyyy-mm-dd-2x']; ?></dd><?
		} ?> 
	</dl>
	<h3>Member List</h3>
	<p><?
		if (!empty($channel[$kc1]['member_list']))
			echo implode(', ', $channel[$kc1]['member_list']);
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
			<dd><?= $d2 = (.9 * $d1 ) / array_sum($channel[$kc1]['average_weight_sum']) ; ?></dd><?
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
		<h3><?= $kd1; ?> - Average Rating per Member</h3>
		<dl><?
			if (!empty($kid['source_user_id_rating_average']))
			foreach($kid['source_user_id_rating_average'] as $k1 => $v1) {
				$kis = & $channel[$kc1]['source_user_id'][$k1];
				$kisb = & $kis['before'];
				$kisa = & $kis['after'];
				?>  
				<dt>
					<?= $k1; ?>
					=&gt;
					<?= $v1; ?>
				</dt>
				<dd><?
				echo

					' ( ' . 
						$v1 . ' * ' . 
						$kis['count_weight'] . ' * ' . 
						$kisb['cost_weight'] . ' * ' . 
						$kisb['time_weight'] .
					' )  + ' .
					' ( ' . 
						$v1 . ' * ' . 
						$kis['count_weight'] . ' * ' . 
						$kisa['cost_weight'] . ' * ' . 
						$kisa['time_weight'] .
					' ) '
				; ?>  = <?=
					$kid['source_user_id_rating_weight'][$k1]; ?> 
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
				&sum;
				=
				<?= $channel[$kc1]['average_weight_sum'][$kd1]; ?>
			</p>
			<p>
				<? /*$channel[$kc1]['average_average'][$kd1] * $d2 * count($kid['source_user_id_rating_average']); */ ?> 
				$<?= $d2 * $channel[$kc1]['average_weight_sum'][$kd1]; ?>
			</p><?
		}
	}
}

if (!empty($data['user_report']['premature_channel_list'])) { ?> 
	<hr />
	<p>Premature channel ids</p>
	<ul><?
	foreach($data['user_report']['premature_channel_list'] as $k1 => $v1) { ?> 
		<li><?= $k1; ?></li><?
	} ?> 
	</ul><?
}

?> 
</div>

<div class="menu_1">
</div>

<div class="menu_2">
</div>

</div>
