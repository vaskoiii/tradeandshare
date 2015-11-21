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

# Contents/Description: Landing page for analyzing merit

up_date('2009-11-22'); ?> 

<div class="doc_box">
	<h3>Introduction</h3>
	<p>Different systems of merit are listed below:</p>
	<dl>
		<dt><?= tt('meritype', 'meritype_good'); ?></dt>
		<dd>Good. NOT necessarily realistic.</dd>
		<dt><?= tt('meritype', 'meritype_bad'); ?></dt>
		<dd>Bad. NOT necessarily realistic.</dd>
		<dt><?= tt('meritype', 'meritype_monetary'); ?></dt>
		<dd>Monetary Based. Used by most of the world.</dd>
		<dt><?= tt('meritype', 'meritype_identity'); ?></dt>
		<dd>Identity Based. Theoretical NON-monetary system used by TS.  More details can be found <a href="/about_doc/">HERE</a>.</dd>
	</dl>
	<p>
		These systems of merit are compared against certain criteria <a href="/meritopic_list/">HERE</a>.
	</p>
</div>
