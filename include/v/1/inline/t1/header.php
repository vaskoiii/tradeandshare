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
?> 
<body onLoad="javascript: document.getElementById('ts_focus').focus();" onkeydown="return checkIt(event);">

<div id="header"><? # preload div must be coordinated with the launcher and also duplicated for the main page
	if (1) {
		# todo placeholder for removing iframes
		include('v/1/inline/t1/test.php');
	} ?> 
	<div style="background: url('/v/1/theme/<?= to_html(str_replace('theme_', '', $_SESSION['theme']['theme_name'])); ?>/ts_icon.png') no-repeat -9999px -9999px;"></div>
	<div class="title">
		<p id="topper"><?
			if ($_SESSION['login']['login_user_name']) {
				echo $_SESSION['login']['login_user_name'] . $config['spacer'];
				if ($_SESSION['feature']['feature_lock'] == 1 && $x['page']['name'] == 'lock_set') { ?> 
					<a href="<?= str_replace('_edit', '_list', $x['..']); ?><?= ffm(get_lock_query('preview%5B0%5D=&focus=&expand%5B0%5D='), -1); ?>"><?= 
						tt('element', 'go_back'); 
					?></a><?
				} else { ?> 
					<a href="<?= str_replace('_edit', '_list', $x['..']); ?><?= ffm('preview%5B0%5D=&focus=&expand%5B0%5D=', -1); ?>"><?=
						tt('element', 'go_back'); 
					?></a><?
				}
			} else { 
				if ($x['page']['name'] == 'login_set') {
					# if not logged in and you click back on login_set it will keep headering you back to login set! ?> 
					<a href="/<?= ff($q['active'][$x['level'] - 1], -1); ?>"><?= 
						tt('element', 'go_back'); 
					?></a><?
				} else { ?> 
					<a href="<?= $x['..']; ?><?= ff($q['active'][$x['level'] - 1], -1); ?>"><?= 
						tt('element', 'go_back'); 
					?></a><?
				} 
			} ?>
		</p><?
		if ($_SESSION['feature']['feature_lock'] == 1) { ?> 
			<h1 id="website_name"><a href="/<?= get_lock_query(); ?>"><nobr><?= $config['website_name']; ?></nobr></a></h1><?
		} else { ?> 
			<h1 id="website_name"><a href="/"><nobr><?= $config['website_name']; ?></nobr></a></h1><?
		} ?> 
	</div><?

	# todo include in x not here but the closing div!
	include('v/1/inline/t1/header_helper.php'); ?>

</div>
<!-- content -->
