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

# todo currenly used as an include but should not be used as such. Should be included in the header! 2012-02-02
?>

<script>
	function more_toggle(type_id_number) {
		var asearch = document.getElementById(type_id_number);
		if (asearch.style.display == 'none') {
			document.getElementById(type_id_number).style.display='block';
			if (!document.getElementById(type_id_number).innerHTML) // failsafe for crappy browsers ie) PSP
				window.location="./<?= get_q_query_modified($x['level'], array('asearch_on' => '1')); ?>"
			document.getElementById(type_id_number + '_toggle').innerHTML = '<?= tt('element', 'less'); ?>';
		}
		else {
			document.getElementById(type_id_number).style.display='none';
			if (!document.getElementById(type_id_number).innerHTML) // failsafe for crappy browsers ie) PSP
				window.location="./<?= get_q_query_modified($x['level'], array('asearch_on' => '')); ?>"
			document.getElementById(type_id_number + '_toggle').innerHTML = '<?= tt('element', 'more'); ?>';
		}
	}

	function even_more_toggle(id1, id2) {
		more_toggle(id1);
		more_toggle(id2);
	}

	<? # todo toggle_selection() for allowing a selection to have an action on it. but first all actions must be allowed per item! ?>
</script>
