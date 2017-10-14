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

# Description: Display Fast Content
# Issue: 

# todo dont repeat logic
# exact same file as fast.php but with fast and f replaced with quick and q

foreach ($data['new_report']['page_id'] as $k1 => $v1) {
if (!empty($v1['page_id'])) {
foreach ($v1['page_id'] as $k2 => $v2) {
	// if (str_match('_list', $v2['page_name'])) {
		# echo $v2['page_name'];
		switch ($v2['page_name']) {
			case 'item_list':
			case 'offer_list':
				$s1 = str_replace('_list', '', $v2['page_name']);
				$s2 = '/' . $s1 . '_list/';
				if (!empty($x['load']['view']['name']))
					$s2 = to_html(ffm('page=&list_name=list&list_type=' . $s1 . '&focus=&expand%5B0%5D=', 0));

				# todo dont let the action steal focus from quick
				$b1 = 2;
				# todo better way to integrate?
				if ($v2['page_name'] == 'offer_list')
				if (isset_gp('quick_offer_offer_name'))
					$b1 = 1;
				if (isset($_GET['focus'])){
					if ($_GET['focus'] == 'quick')
					if (isset($_SESSION['process']['form_info']['type']))
					if ($_SESSION['process']['form_info']['type'] == $s1)
						$b1 = 1;
				} ?> 
				<div id="<?= $s1; ?>_q_box" style="display: <?= ($b1 == 1 ? 'block' : 'none'); ?>;">
					<div class="title">
						<span class="go_back"><?= print_go_back('&lt;&lt;'); ?></span>
						<h2><a href="<?= $s2; ?>"><?= tt('page', $s1 . '_list'); ?></a>*</h2>
						<div class="result_add">
							<a id="<?= $s1; ?>_q_focus" href="#" onclick="javascript: more_toggle_swap('<?= to_html($s1 . '_q'); ?>'); return false;">Menu</a>*
						</div>
					</div><?
					if (
						isset($_SESSION['process']['form_info']) && 
						$s1 == $_SESSION['process']['form_info']['type']
					) {

						# focus on error (not standard with id="ts_focus" ?> 
						<script> document.getElementById('<?= to_html($s1); ?>_q_focus').focus(); </script><?

						# todo message bar has to print for all content box types! maybe ok if autoexpand
						if ($x['preload']['focus'] == 'quick')
							print_message_bar();
						if ($_GET['focus'] == 'quick') # this case because lazy to fix structure
							print_message_bar();
						
					} ?> 

					<div class="content"><?

						# todo autoexpand/focus quick box start
						/* <span id="quick_el_box" style="display: <?= get_action_style_display(); ?>;"> */
						?> 
						<span id="quick_<?= to_html($s1); ?>_box">
						<form name="edit_process" action="/index.php" method="POST">
							<input type="hidden" name ="x" value="<?= to_html($x['.']); ?>edit_process/" />
							<input type="hidden" name="q" value="<?= to_html(ff('', 1)); ?>" />
							<input type="hidden" name="load" value="quick" />
							<input type="hidden" name="type" value="<?= $s1; ?>" />

							<div class="table">
								<div id="quick_content_1"><?
									switch ($s1) {
										# limit printed boxes (make sure to match with the engine)
										case 'item':
										case 'offer':

		# todo print a separate array unless the session error was for this spot. Then print the quick array
		if (
			isset($_SESSION['process']['form_info']) && 
			$s1 == $_SESSION['process']['form_info']['type'] &&
			$_SESSION['process']['form_info']['load'] == 'quick'
		) {
			print_container($quick_content_1, $empty_listing, $key, $translation, 'quick', $option);
			if(!empty($quick_content_2)) { ?> 
			<p class="more_solo">
				&gt;&gt; <a id="<?= to_html($s1); ?>_quick_2_toggle" style="display: inline;" href="#" onclick="more_toggle('<?= to_html($s1); ?>_quick_2'); return false;"><?= tt('element', 'more'); ?></a>
			</p><?
			} ?> 

			<div id="<?= to_html($s1);?>_quick_2" style="margin-left: 20px; margin-bottom: 15px; display: none;"><?
				print_container($quick_content_2, $empty_listing, $key, $translation, 'quick', $option);
		}
		else {
			# note the $load parameter appending $s1
			print_container($edit[$s1]['content_1'], $empty_listing, $key, $translation, 'quick_' . $s1, $option);

			if(!empty($edit[$s1]['content_2'])) { ?> 
			<p class="more_solo">
				&gt;&gt; <a id="<?= to_html($s1); ?>_quick_2_toggle" style="display: inline;" href="#" onclick="more_toggle('<?= to_html($s1); ?>_quick_2'); return false;"><?= tt('element', 'more'); ?></a>
			</p><?
			} ?> 
			<div id="<?= to_html($s1);?>_quick_2" style="margin-left: 20px; margin-bottom: 15px; display: none;"><?
				print_container($edit[$s1]['content_2'], $empty_listing, $key, $translation, 'quick', $option);
		} ?> 
											</div><?
										break;
									} ?> 
								</div>

							</div>
							<div class="menu_1">
							<ul><?
								switch($s1) {
									case 'item': ?> 
										<input type="submit" name="<?= $k1; ?>" value="<?= tt('element', 'add'); ?>" /><?
									break;
									case 'offer': ?> 
										<input type="submit" name="<?= $k1; ?>" value="<?= tt('element', 'add'); ?>" /><?
									break;
								} ?> 
							</ul>
							</div>
							<div class="menu_2"></div>
						</form>
						</span>
					</div>
				</div><?
			break;
		}
	// }
} } }
