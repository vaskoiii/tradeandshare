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
</div>
<!-- /content -->

<div id="footer">
	<div class="title">
		<span class="footer_title"><nobr><small><?= $config['version']; ?></small></nobr></span>
		| <?
		if ($_SESSION['login']['login_user_name']) {
			# does it encourage looking at the version in the footer? ?> 
			<small><?= nod() . number_format((float)$_SESSION['login']['accounting_value_sum'], 2); ?></small><?
		}
		else 
			echo nod(); ?> 
	</div>
	<div class="content">
		<p class="top_foot">
		<i><? # todo potential translation_description of the website name ?> 
			<nobr>"Want it? Get it!</nobr>
			<nobr>Have it? Share it!</nobr>
			<nobr>Don't want it? Trade it!"</nobr>
		</i>
		</p>
		<p class="middle_foot"><?
			# placeholder ?> 
		</p>
		<p class="bottom_foot">
			<nobr>&copy; 2003-<?= to_html(date('Y')); ?> Trade and Share GPL</nobr>
		</p>
		<div class="menu_1">
			<ul>
				<li><a href="#" alt="/page_portal/" onclick="launch('pager'); return false;"><?= tt('page', 'page_portal'); ?></a></li>
				<li><a href="#" alt="/people_portal/" onclick="launch('peopler'); return false;"><?= tt('page', 'people_portal'); ?></a></li>
				<li><a href="#" alt="/host_portal/" onclick="launch('scanner'); return false;"><?= tt('page', 'host_portal'); ?></a></li>
			</ul>
		</div>
		<div class="menu_2" style="margin-bottom: 0px;">
		</div>
	</div>
</div><? 

	if ($config['debug'] == 1)
		include('list/v1/inline/t1/debug.php'); ?> 

</body>
</html><?

# clear garbage left by form processing
unset($_SESSION['process']);
unset($_SESSION['interpret']);
