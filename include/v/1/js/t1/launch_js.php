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

# Contents/Description: Javascript functionality for a launcher with [ctrl + shift + space] to launch any standalone TS page.

echo <<<JAVASCRIPT

function set_cookie( name, value, expires, path, domain, secure ) {
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
	if (tsType == 'tslPeople') {
		my_link = 'https://{$_SERVER['HTTP_HOST']}/portal_process/?type=contact&contact_user_mixed=' + encodeURI(value);
		set_cookie('launch[tslPeople][value]', (value), 365, '/');
		set_cookie('launch[tslPeople][display]', (display), 365, '/');
	}
	else {
		// todo forward to the portal_process
		my_link = 'https://{$_SERVER['HTTP_HOST']}/' + value + '/';
		set_cookie('launch[tsl][value]', value, 365, '/');
		set_cookie('launch[tsl][display]', display, 365, '/');
		// ADD LOCK
		if (value.match(RegExp('_list', '')) || value.match(RegExp('_edit', '')) || value.match('main') || value.match(''))
			my_link += '{$data['lock_query']}';
	}
	window.open(my_link, '_top');
} 

function showtsl_iframe(tsType) {

	var tsl_iframe = document.getElementById(tsType + '_iframe');

	var tsl_iframewidth = tsl_iframe.offsetWidth;
	var tsl_iframeheight = tsl_iframe.offsetHeight;
	var topposition = iii_scrollTop() + (iii_clientHeight() / 3) - (tsl_iframeheight / 2);
	if (topposition <= 0) {
		topposition = 0;
	}
	var leftposition = iii_scrollLeft() + (iii_clientWidth() / 2) - (tsl_iframewidth / 2);
	if (leftposition <= 0) {
		leftposition = 0;
	}
	tsl_iframe.style.top = topposition + "px";
	tsl_iframe.style.left = leftposition + "px";
	tsl_iframe.style.visibility = "visible";
	
	if (tsType == 'tslPeople')
		setTimeout("gettsl_idocument('tslPeople').getElementById('tsl_input').focus()", 0);
	else
		setTimeout("gettsl_idocument('tsl').getElementById('tsl_input').focus()", 0);
}

function iii_clientWidth() {
	// Most important one for accuracy
	//alert(document.documentElement.offsetHeight + ' - ' + window.innerWidth  + ' - ' + document.documentElement.clientWidth + ' - ' + document.body.clentWidth  ); 
	return iii_getLowestGTZ ( [
		document.documentElement ? document.documentElement.clientWidth : 0,
		window.innerWidth ? window.innerWidth : 0,
		//document.body ? document.body.clientWidth : 0,
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
/*
function iii_getMedian(myArray) {
	// Lots of differnt ways to try and make the browser display things in the right place.
	var middleOne = null;
	if (myArray.length == 0) {
		return null;
	}
 	else {
		myArray.sort;
		if (myArray.length%2) {
			middleOne = (myArray.length - 1) / 2;
		}
		else {
			middleOne = (myArray.length) / 2;
		}
	}
	return myArray[middleOne];
}

function iii_getFirstGTZ(myArray) {
	// GTZ Greater Than Zero
	for(var i in myArray) {
		if (myArray[i] > 0) {
			return myArray[i]
		}
	}
	return 0;
}
*/

function killtsl_iframe(tsType) {
	var tsl_iframe;
	if (tsType == 'tslPeople')
		tsl_iframe = document.getElementById('tslPeople_iframe');
	else
		tsl_iframe = document.getElementById('tsl_iframe');
	document.documentElement.removeChild(tsl_iframe);
	window.parent.focus();
}
function gettsl_idocument(tsType) {
	var tsl_iframe;
	if (tsType == 'tslPeople')
		tsl_iframe = document.getElementById('tslPeople_iframe');
	else
		tsl_iframe = document.getElementById('tsl_iframe');
	var tsl_idocument;
	if (tsl_iframe.contentDocument) {
		tsl_idocument = tsl_iframe.contentDocument;
	}
	else if (tsl_iframe.contentWindow) {
		tsl_idocument = tsl_iframe.contentWindow.document;
	}
	else if (window.frames[tsl_iframe.name]) {
		tsl_idocument = window.frames[tsl_iframe.name].document;
	}
	return tsl_idocument;
}
function launch(tsType, event) {
	if (!document.getElementById(tsType + '_iframe')) {
		createtsl_iframe(tsType);
		showtsl_iframe(tsType);
	} else {
		killtsl_iframe(tsType);
	}
	return true; // if not can't type.
	// after calling launch(); return false; if not launcher position is wrong!
}

function checkIt(event) {
	// comma 188 ,
	// space 32 deprecated
	if (event.keyCode == 32 | event.keyCode == 188) {
		if (event.shiftKey & (event.metaKey | event.altKey | event.ctrlKey)) {
			launch('tsl', event);
			return false;
		}
	}
	// period 190 .
	else if (event.keyCode == 190) {
		if (event.shiftKey & (event.metaKey | event.altKey | event.ctrlKey)) {
			launch('tslPeople', event);
			return false;
		}
	}
	// alert('shift=' + event.shiftKey + ' :: ctrl=' + event.ctrlKey + ' :: meta=' + event.metaKey + ' :: alt=' + event.altKey + ' :: code=' + event.keyCode);
}

function createtsl_iframe(tsType) {

	var tsl_iframe;
	tsl_iframe = document.createElement('iframe');

	if (tsType == 'tslPeople') {
		tsl_iframe.id = 'tslPeople_iframe';
		tsl_iframe.name = 'tslPeople_iframe';
	}
	else {
		tsl_iframe.id = 'tsl_iframe';
		tsl_iframe.name = 'tsl_iframe';
	}
	
	tsl_iframe.setAttribute('border', '0px');
	tsl_iframe.setAttribute('vspace', '0px');
	tsl_iframe.setAttribute('hspace', '0px');
	tsl_iframe.setAttribute('marginwidth', '0px');
	tsl_iframe.setAttribute('marginheight', '0px');
	tsl_iframe.setAttribute('scrolling', 'no');
	tsl_iframe.style.position = 'absolute';
	tsl_iframe.style.top = '-9999px';

	// http://en.wikipedia.org/wiki/Display_resolution
	// Smallest Display Listed is 320px x 200px
	tsl_iframe.style.width = '298px'; // 298 + (2) 1px borders = 300px
	tsl_iframe.style.height = '198px'; // 198 + (2) 1px borders = 200px
	tsl_iframe.style.border = '1px solid white';
	document.documentElement.appendChild(tsl_iframe);
	if (tsl_iframe) {
		var tsl_idocument = gettsl_idocument(tsType);
		if (tsl_idocument) {
			tsl_idocument.open(); // IE hack
			tsl_idocument.close(); // IE hack
			tsl_idocument.body.setAttribute('onKeyDown', 'return window.parent.checkIt(event, navigator.appName);');
			tsl_idocument.body.style.background = '{$data['css']['c0']}';

				var tsl_meat_box; // All Content
				tsl_meat_box = tsl_idocument.createElement('div');
				tsl_meat_box.id = 'tsl_meat_box';
				tsl_meat_box.style.height = '190px';
				tsl_idocument.body.appendChild(tsl_meat_box);

				var tsl_juice_box; // Fancy bottom Bar
				tsl_juice_box = tsl_idocument.createElement('div');
				tsl_juice_box.id = 'tsl_juice_box';
				tsl_juice_box.style.height = '10px';
				tsl_juice_box.style.borderTop = '1px solid #ffffff';
				tsl_juice_box.style.background = '{$data['css']['c2']}';
				tsl_idocument.body.appendChild(tsl_juice_box);

			// MEAT
			var tsl_main_box;
			tsl_main_box = tsl_idocument.createElement('div');
			tsl_main_box.id = 'tsl_main_box';
			tsl_main_box.style.padding = '5px 20px 0px 20px';
			tsl_main_box.style.background = '{$data['css']['c1']}';
			tsl_meat_box.appendChild(tsl_main_box); //[


			// placeholder
			if (tsType == 'placeholdeRtslPeople') {
				var tsl_form;
				tsl_form = tsl_idocument.createElement('form');
				tsl_form.id = 'tsl_form';
				tsl_form.style.position = 'relative';
				tsl_form.style.top = '0px';
				tsl_form.style.margin = '0px';
				tsl_form.style.padding = '0px';
				tsl_main_box.appendChild(tsl_form); //[
					
					var tsl_p;
					tsl_p = tsl_idocument.createElement('p');
					tsl_p.id = 'tsl_p';
					tsl_p.style.fontWeight = 'bold';
					tsl_p.innerHTML = 'TS Alternative Launcher Placeholder';
					tsl_p.style.color = 'black';
					tsl_form.appendChild(tsl_p);

				//]
			}
			else {
				var tsl_form;
				tsl_form = tsl_idocument.createElement('form');
				tsl_form.id = 'tsl_form';
				tsl_form.style.position = 'relative';
				tsl_form.style.top = '0px';
				tsl_form.style.margin = '0px';
				tsl_form.style.padding = '0px';
				tsl_form.setAttribute('onSubmit', 'window.parent.location=tsl_suggest_one.href;');
				tsl_main_box.appendChild(tsl_form); //[
					var tsl_x;
					tsl_x = tsl_idocument.createElement('a');
					tsl_x.id = 'tsl_x';

					// switch
					tsl_x.href = 'javascript:top.killtsl_iframe("' + tsType + '");';

					tsl_x.innerHTML = 'TS';
					tsl_x.style.marginRight = '10px';
					tsl_x.style.color = 'black';
					tsl_x.style.fontWeight = 'bold';
					tsl_x.style.fontSize = '+1.5em';
					tsl_form.appendChild(tsl_x);
					var tsl_suggest_one;
					tsl_suggest_one = tsl_idocument.createElement('a');
					tsl_suggest_one.id = 'tsl_suggest_one';
					tsl_suggest_one.target = '_top';

					// switch
					if (tsType == 'tslPeople') {
						tsl_suggest_one.href = 'javascript:top.remember("tslPeople", "{$_COOKIE["launch"]["tslPeople"]["value"]}", "{$_COOKIE["launch"]["tslPeople"]["display"]}");';
						tsl_suggest_one.innerHTML = '{$_COOKIE["launch"]["tslPeople"]["display"]}';
					}
					else {
						tsl_suggest_one.href = 'javascript:top.remember("tsl", "{$_COOKIE['launch']['tsl']['value']}", "{$_COOKIE['launch']['tsl']['display']}");';
						tsl_suggest_one.innerHTML = '{$_COOKIE['launch']['tsl']['display']}';
					}

					tsl_suggest_one.style.color = 'black';
					tsl_suggest_one.style.fontWeight = 'bold';
					tsl_suggest_one.style.fontSize = '+1.5em';
					tsl_form.appendChild(tsl_suggest_one);
					var tsl_br;
					tsl_br = tsl_idocument.createElement('br');
					tsl_form.appendChild(tsl_br);
					var tsl_input;
					tsl_input = tsl_idocument.createElement('input');
					tsl_input.id = 'tsl_input';
					tsl_input.style.width = '200px';
					tsl_input.style.background = '#fefefe';
					tsl_input.style.color = '#010101';
					tsl_input.type = "text";
					tsl_input.autocomplete = "off";

					//switch
					tsl_input.setAttribute('onkeyup', 'window.top.showHint("' + tsType + '", this.value);');

					tsl_input.style.background.color = '#ffffff';
					tsl_form.appendChild(tsl_input);
					var tsl_launch;
					tsl_launch = tsl_idocument.createElement('input');
					tsl_launch.id = 'tsl_launch';
					tsl_launch.type = "submit";
					tsl_launch.value = "!";
					tsl_launch.style.margin = '5px';
					tsl_launch.style.background = '#fefefe';
					tsl_launch.style.color = '#010101';
					tsl_input.type = "text";
					tsl_form.appendChild(tsl_launch);
					var tsl_hr; // border is not an option because the title can expand dynamically
					tsl_hr = tsl_idocument.createElement('hr');
					tsl_hr.style.background = '#ffffff';
					tsl_hr.style.border = 'none';
					tsl_hr.style.height = '2px';
					tsl_hr.style.margin = '0px -20px 0px -20px';
					tsl_form.appendChild(tsl_hr);
				//]
			// ]

			// JUICE
			var tsl_alternative_box;
			tsl_alternative_box = tsl_idocument.createElement('div');
			tsl_alternative_box.id = 'tsl_alternative_box';
			tsl_meat_box.appendChild(tsl_alternative_box); //[
				var tsl_suggest_more;
				tsl_suggest_more = tsl_idocument.createElement('ul');
				tsl_suggest_more.id = 'tsl_suggest_more';
				tsl_suggest_more.style.margin = '5px 40px 0px 40px';
				tsl_suggest_more.style.padding = '0px';	
				// used in 2x places (on creation and on input length of 0)
				tsl_suggest_more.innerHTML = getSuggestMoreEmptyInput(tsType);
				tsl_alternative_box.appendChild(tsl_suggest_more);
			//]
			}
		}
	}
}
function getMatchArray(tsType, myString) {

	if (tsType == 'tslPeople')
		var tsl = JSON.parse('{$peopleJson}');
	else
		var tsl = JSON.parse('{$pageJson}');

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
		suggest_more_html = getSuggestMoreNoResult();

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
		
		if (tsType == 'tslPeople') {
			suggest_more_html += '<li id="tsl_li_' + i + '"><a style="color: black;" id="tsl_' + i 
			+ '" href="javascript:window.parent.remember(\'tslPeople\', \'' + k1 + '\',\'' + tsl_match[k1] + '\');" target="_top">' + s1 + '</a></li>';
		}
		else {
			suggest_more_html += '<li id="tsl_li_' + i + '"><a style="color: black;" id="tsl_' + i 
			+ '" href="javascript:window.parent.remember(\'tsl\', \'' + k1 + '\',\'' + tsl_match[k1] + '\');" target="_top">' + s1 + '</a></li>';
		}
		i++;
	}
	suggest_more_html += suggest_even_more_html;

	if (tsType == 'tslPeople')
		tsl_iframe = document.getElementById('tslPeople_iframe');
	else
		tsl_iframe = document.getElementById('tsl_iframe');

	if (tsl_iframe) {
		tsl_idocument = gettsl_idocument(tsType);
		if (tsl_idocument) {
			tsl_idocument.getElementById('tsl_suggest_more').innerHTML = suggest_more_html;
			tsl_0 = tsl_idocument.getElementById('tsl_0');
			// Error her if there are no results [tsl_0 is null]
			tsl_idocument.getElementById('tsl_suggest_one').innerHTML = tsl_0.innerHTML;
			tsl_idocument.getElementById('tsl_suggest_one').href = tsl_0.href;

			if (tsType == 'tslPeople') {
				set_cookie('launch[tslPeople][value]', tsl_0_href, 365, '/')
				set_cookie('launch[tslPeople][display]', tsl_0.innerHTML, 365, '/')
			}
			else {
				set_cookie('launch[tsl][value]', tsl_0_href, 365, '/')
				set_cookie('launch[tsl][display]', tsl_0.innerHTML, 365, '/')
			}
			tsl_0.style.display = 'none'
			tsl_idocument.getElementById('tsl_li_0').style.display = 'none'; // For IE and FF
		}
	}
}
// If no result is found from input text
function getSuggestMoreNoResult() {
	return '';
}
// If empty input text | first launch
function getSuggestMoreEmptyInput(tsType) {
	if (tsType == 'tslPeople')
		return '{$data['launch']['tslPeople']['empty']}';
	else
		return '{$data['launch']['tsl']['empty']}';
}

function showHint(tsType, str) {
	// for some reason the next line fails for tslPeople
	tsl_iframe = document.getElementById(tsType + '_iframe');
	if (tsl_iframe) {
		tsl_idocument = gettsl_idocument(tsType);
		if (tsl_idocument) {
			if (str.length==0) { 
				tsl_idocument.getElementById('tsl_suggest_more').innerHTML=getSuggestMoreEmptyInput(tsType);
				return;
			}
			else {
				getMatchArray(tsType, str);
			}
		}
	}
}
//createElement();
JAVASCRIPT;
