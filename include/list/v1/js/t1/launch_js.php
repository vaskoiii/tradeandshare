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

# Contents/Description: Javascript functionality for a launcher with a hotkey:
#  Pager: [ctrl + shift + ,] 
#  Peopler: [ctrl + shift + .] 
#  Original Compatibility: [ctrl + shift + space] 

# Hardcoded
# 	lbW = '300';
# 	lbH = '200';

echo <<<JAVASCRIPT
function simple_hide(s1) {
	document.getElementById(s1).style.display = 'none';
}
function set_cookie(name, value, expires, path, domain, secure) {
	var today = new Date();
	today.setTime( today.getTime() );
	if ( expires ) {
		expires = expires * 1000 * 60 * 60 * 24;
	}
	var expires_date = new Date( today.getTime() + (expires) );
	document.cookie = name + "=" +escape( value ) +
		( ( expires ) ? ";expires=" + expires_date.toGMTString() : "" ) + 
		( ( path ) ? ";path=" + path : "" ) + 
		( ( domain ) ? ";domain=" + domain : "" ) +
		( ( secure ) ? ";secure" : "" );
}
function remember(tsType, value, display) {
	var my_link;
	if (tsType == 'peopler') {
		my_link = 'https://{$_SERVER['HTTP_HOST']}/portal_process/?type=contact&contact_user_mixed=' + encodeURI(value);
		set_cookie('launch[peopler][value]', (value), 365, '/');
		set_cookie('launch[peopler][display]', (display), 365, '/');
	}
	else {
		switch (value) {
			// placeholders if we want to do this kind of launching?
			case 'page_parent':
			case 'page_next':
			case 'page_previous':
			case 'page_first':
			case 'page_last':
				// easier to deal with xx and qq as custom and start from scratch
				my_link = 'https://{$_SERVER['HTTP_HOST']}/portal_process/?type=go&where=' + value + '&xx=' + encodeURIComponent(document.location.pathname) + '&qq=' + encodeURIComponent(document.location.search);
				set_cookie('launch[pager][value]', value, 365, '/');
				set_cookie('launch[pager][display]', display, 365, '/');
			break;
			default:
				// todo forward to the portal_process
				my_link = 'https://{$_SERVER['HTTP_HOST']}/' + value + '/';
				set_cookie('launch[pager][value]', value, 365, '/');
				set_cookie('launch[pager][display]', display, 365, '/');
				// ADD LOCK
				if (value.match(RegExp('_list', '')) || value.match(RegExp('_edit', '')) || value.match('main') || value.match(''))
					my_link += '{$data['lock_query']}';
			break;
		}
	}
	window.open(my_link, '_top');
} 
function iii_clientWidth() {
	// Most important one for accuracy
	return iii_getLowestGTZ ( [
		document.documentElement ? document.documentElement.clientWidth : 0,
		window.innerWidth ? window.innerWidth : 0,
		document.documentElement.offsetWidth ? document.documentElement.offsetWidth : 0,
	] );
}
function iii_scrollLeft() {
	return iii_getLowestGTZ ( [
		window.pageXOffset ? window.pageXOffset : 0,
		document.documentElement ? document.documentElement.scrollLeft : 0,
		document.body ? document.body.scrollLeft : 0,
	] );
}
function iii_scrollTop() {
	return iii_getLowestGTZ ( [
		window.pageYOffset ? window.pageYOffset : 0,
		document.documentElement ? document.documentElement.scrollTop : 0,
		document.body ? document.body.scrollTop : 0,
	] );
}
function iii_clientHeight() {
	return iii_getLowestGTZ ( [
		window.innerHeight ? window.innerHeight : 0,
		document.documentElement ? document.documentElement.clientHeight : 0,
		document.body ? document.body.clientHeight : 0,
	] );
}
function iii_getLowestGTZ(myArray) {
	// GTZ Greater Than Zero
	myArray.sort;
	for (var i in myArray) {
		if (myArray[i] > 0) {
			return myArray[i];
		}
	}
	return 0;
}
function radicalize(tsType) {
	var lb = document.getElementById(tsType + '_box');
	lbW = '300';
	lbH = '220';
	var topPx = iii_scrollTop() + (iii_clientHeight() / 3) - (lbH / 2);
	if (topPx <= 0) {
		topPx = 0;
	}
	var leftPx = (iii_scrollLeft() + (iii_clientWidth() / 2)) - (lbW / 2);
	if (leftPx <= 0) {
		leftPx = 0;
	}
	lb.style.top = topPx + 'px';
	lb.style.left = leftPx + 'px';
}
function launch(tsType) {
	var tsOtherType;
	var tsanotherType;
	if (tsType == 'pager') {
		tsOtherType = 'peopler';
		tsAnotherType = 'scanner';
	}
	if (tsType == 'peopler') {
		tsOtherType = 'pager';
		tsAnotherType = 'scanner';
	}
	if (tsType == 'scanner') {
		tsOtherType = 'peopler';
		tsAnotherType = 'pager';
	}
	var o1 = document.getElementById(tsType + '_box');
	var o2 = document.getElementById(tsOtherType + '_box');
	var o3 = document.getElementById(tsAnotherType + '_box');
	if (o1.style.display == 'block')
		o1.style.display = 'none';
	else {
		o1.style.display = 'block';
		radicalize(tsType);
		document.getElementById(tsType + '_input').focus();
	}
	o2.style.display = 'none';
	o3.style.display = 'none';
	return true; // if not can't type.
	// after calling launch(); return false; if not launcher position is wrong!
}
function checkIt(event) {
	// comma 188 ,
	// space 32 deprecated
	if (event.keyCode == 32 | event.keyCode == 188) {
		if ( event.ctrlKey | (
			event.shiftKey &
			(event.metaKey | event.altKey | event.ctrlKey)
		) ) {
			launch('pager');
			return false;
		}
	}
	// period 190 .
	else if (event.keyCode == 190) {
		if ( event.ctrlKey | (
			event.shiftKey &
			(event.metaKey | event.altKey | event.ctrlKey)
		) ) {
			launch('peopler');
			return false;
		}
	}
	// slash 191
	else if (event.keyCode == 191) {
		if ( event.ctrlKey | (
			event.shiftKey &
			(event.metaKey | event.altKey | event.ctrlKey)
		) ) {
			launch('scanner');
			return false;
		}
	}
}
function getMatchArray(tsType, myString) {
	switch(tsType) {
		case 'peopler':
			var tsl = JSON.parse('{$peopleJson}');
		break;
		case 'pager':
			var tsl = JSON.parse('{$pageJson}');
		break;
	}
	var tsl_match = new Array();
	var tsl_limit = 5;
	var tsl_matched = 0;
	var sLength = myString.length;
	var suggest_even_more_html = '';
	myString = RegExp(myString, 'i');
	for (var k1 in tsl) {
		if (tsl_matched < tsl_limit ) {
			if (tsl[k1].display.slice(0,sLength).match(myString)) {
				tsl_match[tsl[k1].value] = tsl[k1].display;
				tsl_matched += 1;
			}
		}
		else {
			suggest_even_more_html = '<li>&gt;&gt;</li>';
		}
	}
	var suggest_more_html = '';
	// to print a message with no result put it here
	if (tsl_matched == 0)
		suggest_more_html = getSuggestMoreNoResult(tsType);
	var i = 0;
	var tsl_0_href;
	for (var k1 in tsl_match) {
		if (i == 0)
			tsl_0_href = k1;
		// todo figure out how to display correctly for now we are just hacking certaing chars 
		// var s1 = decodeURIComponent(tsl_match[k1]);
		// todo make this work
		// s1 = s1.replace('/\+/g', ' ');
		var s1 = tsl_match[k1];
		suggest_more_html += '<li id="' + tsType + '_li_' + i + '"><a style="color: black;" id="' + tsType+ '_' + i +
			'" href="javascript:remember(\'' + tsType + '\', \'' + k1 + '\',\'' + tsl_match[k1] + '\');">' +
			s1 + '</a></li>';
		i++;
	}
	suggest_more_html += suggest_even_more_html;
	var tsl_suggest_more = document.getElementById(tsType + '_suggest_more');
	if (tsl_suggest_more) {
			tsl_suggest_more.innerHTML = suggest_more_html;
		var tsl_0 = document.getElementById(tsType + '_0');
		if (tsl_0) {
			var tsl_suggest_one = document.getElementById(tsType + '_suggest_one');
			if (tsl_suggest_one) {
				tsl_suggest_one.innerHTML = tsl_0.innerHTML;
				tsl_suggest_one.href = tsl_0.href;
			}
			var tsl_li_0 = document.getElementById(tsType + '_li_0');
			if (tsl_li_0) {
				tsl_li_0.style.display = 'none';
			}

			set_cookie('launch[' + tsType + '][value]', tsl_0_href, 365, '/')
			set_cookie('launch[' + tsType + '][display]', tsl_0.innerHTML, 365, '/')
		}
	}
}
// If no result is found from input text
function getSuggestMoreNoResult(tsType) {
	switch(tsType) {
		case 'peopler':
			return '<p>No Results</p><p><a href="/contact_edit/">Add a Contact</a></p>';
		break;
		case 'pager':
			return '<p>No Results</p><p><a href="/sitemap_doc/">See Sitemap</a></p>';
		break;
		case 'scanner':
			// no dynamic content
		break;
	}
	return '';
}
// empty input | first launch
function getSuggestMoreEmptyInput(tsType) {
	switch(tsType) {
		case 'peopler':
			return '{$data['launch']['peopler']['empty']}';
		break;
		case 'pager':
			return '{$data['launch']['pager']['empty']}';
		break;
		case 'scanner':
			return '{$data['launch']['scanner']['empty']}';
		break;
	}
}
function showHint(tsType, s1) {
	if (s1.length == 0) { 
		document.getElementById(tsType + '_suggest_more').innerHTML = getSuggestMoreEmptyInput(tsType);
		return;
	}
	else {
		getMatchArray(tsType, s1);
	}
}
JAVASCRIPT;
