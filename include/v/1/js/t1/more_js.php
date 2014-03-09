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

function more_toggle_swap(t1, refocus) {
   	refocus = typeof refocus !== 'undefined' ? true : false; // browser compatible default parameter
	if (refocus == false)
		var t1_focus = document.getElementById(t1 + '_focus');
	else
		var t1_focus = document.getElementById(refocus);

	var t1_box = document.getElementById(t1 + '_box');
	var t1_swap1 = document.getElementById(t1 + '_swap1');
	var t1_swap2 = document.getElementById(t1 + '_swap2');

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
	var t1_box = document.getElementById(t1); // not + '_box'
	var t1_toggle = document.getElementById(t1 + '_toggle');
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

JAVASCRIPT;
