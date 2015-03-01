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

# Contents/Description: Miscellaneous Javascript that should be included in ts.

$s1 = tt('element', 'more');
$s2 = tt('element', 'less');
$s3 =  get_q_query_modified($x['level'], array('t1_box_on' => '1'));

echo <<<JAVASCRIPT
function did(s1) {
	return document.getElementById(s1);
}
function more_toggle_swap(t1, refocus) {
	// have to supply a focus on the dom!
	if (refocus)
		var t1_focus = did(refocus);
	else
		var t1_focus = did(t1 + '_focus');
	var t1_box = did(t1 + '_box');
	var t1_swap1 = did(t1 + '_swap1');
	var t1_swap2 = did(t1 + '_swap2');
	if (t1_swap1.style.display == 'none') {
		t1_swap1.style.display='inline';
		t1_swap2.style.display='none';
	}
	else {
		t1_swap1.style.display='none';
		t1_swap2.style.display='inline';
	}
	if (t1_box.style.display == 'none') {
		t1_box.style.display='block';
		t1_focus.focus();
	}
	else {
		t1_box.style.display='none';
		t1_swap1.focus();
	}
}
function more_toggle(t1) {
	var t1_box = did(t1); // not + '_box'
	var t1_toggle = did(t1 + '_toggle');
	if (t1_box.style.display == 'none') {
		t1_box.style.display='block';
		if (!t1_box.innerHTML) // failsafe for crappy browsers ie) PSP
			window.location='./$s3';
		t1_toggle.innerHTML = '$s2';
	}
	else {
		t1_box.style.display='none';
		if (!t1_box.innerHTML) // failsafe for crappy browsers ie) PSP
			window.location='./$s3';
		t1_toggle.innerHTML = '$s1';
	}
}
function even_more_toggle(id1, id2) {
	more_toggle(id1);
	more_toggle(id2);
}
function simple_show_hide(s1, s2) {
	did(s1).style.display = 'block';
	did(s2).style.display = 'none';
	did(s1 + '_focus').focus();
}
JAVASCRIPT;
