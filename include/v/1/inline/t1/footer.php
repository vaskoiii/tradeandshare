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
<!-- /content -->
</div>
</div>

<div id="footer">
	<div class="title">
		<span class="footer_title"><nobr><small><?= $config['version']; ?></small></nobr></span>
	</div>
	<div class="content">
		<p class="top_foot">
		<i><? # todo potential translation_description of the website name ?> 
			<nobr>"Want it? Get it!</nobr>
			<nobr>Have it? Share it!</nobr>
			<nobr>Don't want it? Trade it!"</nobr>
		</i>
		</p>
		<p class="middle_foot">
			<a href="./sitemap_doc/"><?= tt('page', 'sitemap_doc'); ?></a><?
			if ($_SESSION['load']['load_javascript'] == 1) { ?>
				|
				<a href="#" onclick="javascript: launch(event); return false;">Launcher</a>: <nobr>ctrl+shift+space</nobr><?
			} ?> 
		</p>
		<p class="bottom_foot">
			<nobr>&copy; 2003-<?= to_html(date('Y')); ?> Trade and Share</nobr>
		</p>
		<div class="menu_1">
			<ul>
				<li><a href="/contact_list/"><?= tt('page', 'contact_list'); ?></a>/<a href="/note_list/"><?= tt('page', 'note_list'); ?></a></li>
				<li><a href="/user_list/"><?= tt('page', 'user_list'); ?></a>/<a href="/metail_list"><?= tt('page', 'metail_list'); ?></a></li>
			</ul>
		</div>
		<div class="menu_2" style="margin-bottom: 0px;">
		</div>
	</div>
</div><? 

	if ($config['debug'] == 1)
		include($x['site']['i'] . '/inline/t1/debug.php'); ?> 

</body>
</html><?

# clear garbage left by form processing
unset($_SESSION['process']);
unset($_SESSION['interpret']);
