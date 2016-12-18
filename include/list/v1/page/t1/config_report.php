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
			<h3>My Stuff</h3>
			<ul>
				<li><a href="user_view/?list_name=list&list_type=feed&lock_user_id=<?= (int)$_SESSION['login']['login_user_id']; ?>"><span class="edit"><?= tt('page', 'feed_list'); ?></span></a></li>
				<li><a href="profile_edit/<?= ff(''); ?>"><span class="edit"><?= tt('page', 'profile_edit'); ?></span></a></li>
				<li><a href="user_view/?list_name=list&list_type=item&lock_user_id=<?= (int)$_SESSION['login']['login_user_id']; ?>"><span class="edit"><?= tt('page', 'item_list'); ?></span></a></li>
				<li><a href="./team_view/?team_id=<?= (int)$my_team_id; ?>&lock_team_id=<?= (int)$my_team_id; ?>"><span class="edit"><?= tt('page', 'team_list'); ?></span></a></li>
			</ul>
		</div><?
	} ?> 
	<div class="doc_box">
		<h3>Set</h3>
		<dl>
			<dt><?= tt('page', 'theme_set'); ?></dt>
			<dd><?
			$a1 = get_action_content_1('theme', 'set');
			foreach ($a1 as $k1 => $v1) { ?> 
				<span class="<?= $k1; ?>"><?= tt('theme', $_SESSION['theme'][$k1]); ?></span> <?= $config['spacer']; ?><?
			} ?> 
			<a href="./theme_set/<?= ff(); ?>"><span class="edit"><?= tt('element', 'edit'); ?></span></a>
			</dd>
			<dt><?= tt('page', 'dialect_set'); ?></dt>
			<dd>
				<span class="dialect_name"><?= to_html($_SESSION['dialect']['dialect_name']); ?></span>
				 <?= $config['spacer'];?> <a href="./dialect_set/<?= ff(); ?>"><span class="edit"><?= tt('element', 'edit'); ?></span></a>
			</dd><?
			if ($_SESSION['login']['login_user_name']) { ?> 
				<dt><?= tt('page', 'lock_set'); ?></dt>
				<dd>
					<a href="./lock_set/<?= ff(); ?>"><span class="edit"><?= tt('element', 'edit'); ?></span></a>
				</dd><?
			} ?> 
			<dt><?= tt('page', 'display_set'); ?></dt>
			<dd>
				<span class="display_name"><?= tt('display', $_SESSION['display']['display_name']); ?></span>
				 <?= $config['spacer'];?> <a href="display_set/<?= ff(); ?>"><span class="edit"><?= tt('element', 'edit'); ?></span></a>
			</dd>
			<dt><?= tt('page', 'load_set'); ?></dt>
			<dd>
				<span class="load_javascript"><?= tt('element', 'load_javascript'); ?>: <?= $_SESSION['load']['load_javascript'] == 1 ? tt('boolean', 'true') : tt('boolean', 'false'); ?></span>
			 <?= $config['spacer'];?> <a href="load_set/<?= ff(); ?>"><span class="edit"><?= tt('element', 'edit'); ?></span></a>
			</dd>
		</dl>
	</div><?
	if ($_SESSION['login']['login_user_name']) { ?> 
		<div class="doc_box">
			<h3>Action</h3>
			<ul>
				<li>
					<a href="guest_portal/<?= ff(); ?>"><span class="edit"><?=
						tt('page', 'guest_portal')
						. ' ' . tt('element', 'print') 
					?></span></a>
				</li>
				<li style="margin-bottom: 10px;">
					<a href="login_unset_process/"><span class="edit"><?=
						tt('page', 'login_unset_process')
						. ' (logout)'
					?></a>
				</li>
			</ul>
		</div><?
	} ?> 
</div>

<div class="menu_1">
</div>

<div class="menu_2">
</div>
