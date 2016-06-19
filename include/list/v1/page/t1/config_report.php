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

# Contents/Description: Most important configuration options go at the top. ?> 

<div class="content_box"><?
	if ($_SESSION['login']['login_user_name']) { ?> 
		<div class="doc_box">
			<h3>Action</h3>
			<ul>
				<li style="margin-bottom: 10px;">
					<a href="login_unset_process/"><span class="edit"><?=
						tt('page', 'login_unset_process')
						. ' (logout)'
					?></a>
				</li>
				<li>
					<a href="guest_portal/<?= ff(); ?>"><span class="edit"><?=
						tt('page', 'guest_portal')
						. ' ' . tt('element', 'print') 
					?></span></a>
				</li>
			</ul>
		</div>
		<div class="doc_box">
			<h3><?= tt('page', 'profile_edit'); ?></h3>
			<p>
			<p><?
				# avatar? ?> 
				<a href="profile_edit/<?= ff(''); ?>"><span class="edit"><?= tt('element', 'edit'); ?></span></a>
			</p>
		</div>
		<div class="doc_box">
			<h3><?= tt('page', 'item_list'); ?></h3>
			<p>
				<span class="user_name">(<?= to_html($_SESSION['login']['login_user_name']); ?>)</span>
				-
				<a href="user_view/?list_name=list&list_type=item&lock_user_id=<?= (int)$_SESSION['login']['login_user_id']; ?>"><span class="edit"><?= tt('element', 'edit'); ?></span></a>
			</p>
		</div>
		<div class="doc_box">
			<h3><?= tt('element', 'team_name'); ?></h3>
			<p>
				<span class="team_name"><?= to_html($my_team_name); ?></span>
				-
				<a href="./team_view/?team_id=<?= (int)$my_team_id; ?>&lock_team_id=<?= (int)$my_team_id; ?>"><span class="edit"><?= tt('element', 'edit'); ?></span></a>
			</p>
		</div><?
		if ($_SESSION['feature']['feature_lock'] == 1) { ?> 
		<div class="doc_box">
			<h3><?= tt('page', 'lock_set'); ?></h3>
			<p>
				<a href="./lock_set/<?= ff(); ?>"><span class="edit"><?= tt('element', 'edit'); ?></span></a>
			</p>
		</div><?
		}
	} ?> 

	<div class="doc_box">
		<h3><?= tt('page', 'theme_set'); ?></h3>
		<p><?
		$a1 = get_action_content_1('theme', 'set');
		foreach ($a1 as $k1 => $v1) { ?> 
			<span class="<?= $k1; ?>"><?= tt('theme', $_SESSION['theme'][$k1]); ?></span> <?= $config['spacer']; ?><?
		} ?> 
		<a href="./theme_set/<?= ff(); ?>"><span class="edit"><?= tt('element', 'edit'); ?></span></a>
		</p>
	</div>
	<div class="doc_box">
		<h3><?= tt('page', 'display_set'); ?></h3>
		<p>
			<span class="display_name"><?= tt('display', $_SESSION['display']['display_name']); ?></span>
			 <?= $config['spacer'];?> <a href="display_set/<?= ff(); ?>"><span class="edit"><?= tt('element', 'edit'); ?></span></a>
		</p>
	</div>
	<div class="doc_box">
		<h3><?= tt('page', 'dialect_set'); ?></h3>
		<p>
			<span class="dialect_name"><?= to_html($_SESSION['dialect']['dialect_name']); ?></span>
			 <?= $config['spacer'];?> <a href="./dialect_set/<?= ff(); ?>"><span class="edit"><?= tt('element', 'edit'); ?></span></a>
		</p>
	</div>
	<div class="doc_box">
		<h3><?= tt('page', 'load_set'); ?></h3>
		<p>
			<span class="load_javascript"><?= tt('element', 'load_javascript'); ?>: <?= $_SESSION['load']['load_javascript'] == 1 ? tt('boolean', 'true') : tt('boolean', 'false'); ?></span>
			 <?= $config['spacer'];?> <a href="load_set/<?= ff(); ?>"><span class="edit"><?= tt('element', 'edit'); ?></span></a>
		</p>
	</div>
</div>

<div class="menu_1">
</div>

<div class="menu_2">
</div>
