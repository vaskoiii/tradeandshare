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

# Contents/Description: show the data structures - show all variable spaces
if ($config['debug'] == 1) { ?> 
<div id="debugger">
<div class="content">
	<? # phpinfo(); ?> 

	<center><h2>echo to_html(print_r($array, 1));</h2></center>
	<br />
	<br /><?
	# rearrange here to change debug order
	$a1 = array(
		# global
		'global' => 'block',
		'process' => 'block',
		'interpret' => 'block',
		'x' => 'none',
		'q' => 'none',
		'config' => 'none',
		'_GET' => 'none',
		'_POST' => 'none',
		'_SERVER' => 'none',
		# instantiated
		'instantiated' => 'block',
		'_SESSION' => 'none',
		'_COOKIE' => 'none',
		# 'authentication' => 'none',
		'edit' => 'none', # added for edit structure on every page
		'data' => 'none', # possible to uncomment certain sections below ( todo use array )
		'option' => 'none',
		'key' => 'none',
		'translation' => 'none',
		# other (unused) 2012-03-02 vaskoiii
		'tsmail' => 'none',
		'other' => 'none',
		'_REQUEST' => 'none', # unused and should be same as $_COOKIE except on process files
	);
	if (isset($data['action']))
		$data['action']['inquiry']['todo'] = 'inquiry = "in" & response = "out"';
	if (1 || isset($authentication))
		$authentication['todo'] = 'used credentials, type of login, login_ip, last login, login method, etc...';
	foreach($a1 as $k1 => $v1)
	switch($k1) {
		case 'global':
		case 'instantiated': ?> 
			<center><h2><?= $k1; ?></h2></center><?
		break;
		case 'other': ?> 
			<center><h2>Other</h2></center><?
		break;
		case 'config': ?> 
			<div class="debug_variable">
				<h3>$config</h3>
				<p>Intentionally hidden as a security precaution.</p>
			</div><?
		break;
		case 'data':
			if (!empty($data))
			foreach($data as $k2 => $v2) {
				switch($k2) {
					# auto_open what debugs
					case 'score_report':
					case 'user_report': # needed because arrays reference old file
						$v1 = 'block';
					# nobreak;
					default: ?> 
						<div class="debug_variable">
							<h3><?= '$data[\'' . $k2 .'\']'; ?></h3>
							<a id="debug_<?= $k2; ?>_toggle" href="javascript: more_toggle('debug_<?= $k2; ?>');"><?= tt('element', 'more'); ?></a> 
							<pre id="debug_<?= $k2; ?>" style="display: <?= $v1; ?>;"><?
								echo to_html(print_r($data[$k2], 1)); ?> 
							</pre>
						</div><?
					break;
				}
			}
		break;
		case 'process':
		case 'interpret':
			if (!str_match('_process', $x['page']['name']))
				break;
		default: ?> 
			<div class="debug_variable">
				<h3>$<?= $k1; ?></h3>
				<a id="debug_<?= $k1; ?>_toggle" href="javascript: more_toggle('debug_<?= $k1; ?>');"><?= tt('element', 'more'); ?><a>
				<pre id="debug_<?= $k1; ?>" style="display: <?= $v1; ?>;"><?
				switch($k1) {
					case '_GET': echo to_html(print_r($_GET, 1)); break;
					case '_POST': echo to_html(print_r($_POST, 1)); break;
					case '_REQUEST': echo 'NOT used but php still assigns values<br />'; echo to_html(print_r($_REQUEST, 1)); break;
					case '_COOKIE': echo to_html(print_r($_COOKIE, 1)); break;
					case '_SESSION': echo to_html(print_r($_SESSION, 1)); break;
					default: echo to_html(print_r(${$k1}, 1)); break; # variable variables!
				} ?> 
				</pre>
			</div><?
		break;
	} ?> 

	<div class="debug_variable">
		<center><h2>Temporary</h2></center><br />
		<p>To help ensure consistency with temporary variable conventions! NONE of these variables should be assumed to have any scope outside of what is immediate/local to the current file/function.</p>
		<p><b>query</b>: $sql - $result - $row</p>
		<p><b>sql</b>: $select - $from - $prefix - $where - $where_x - $group_by</p>
		<p><b>key/value</b>: $k1 - $k2 - $k3 / $v1 - $v2 - $v3</p>
		<p><b>string/int/array/bool/reference</b>: $s1 - $s2 - $s3 / $i1 - $i2 - $i3 / $a1 - $a2 - $a3 / $b1 - $b2 - $b3 / $r1 - $r2 - $r3</p>
	</div>
</div>
</div><?
}
