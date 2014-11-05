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

# Contents/Description: All list pages and some other key pages
# Known Issues: English ONLY =(

up_date('2010-07-05');

include('list/v1/inline/site_map.php');

function print_sitemap_link($page_name) {
	global $x;
	global $q;
	global $key;
	global $translation;

	if ($x['load']['view']['type']) { 
		$e1 = explode('_', $page_name); ?> 
		<dt><a href="./<?= ffm('list_name=' . $e1[1] . '&list_type=' . $e1[0], 0); ?>"><?= tt('page', $page_name); ?></a>*</dt><?
	} else { ?> 
		<dt><a href="./<?= $page_name; ?>/<?= ffm('', 0); ?>"><?= tt('page', $page_name); ?></a></dt><?
	}
}

foreach($data['new_report']['page_id'] as $k1 => $v1) {
if (!empty($v1['page_id'])) {
	add_translation('page', $v1['page_name']);
	foreach($v1['page_id'] as $k2 => $v2) {
		add_translation('page', $v2['page_name']);
	}
} }

add_translation('page', 'main');
add_translation('page', 'new_area');
add_translation('page', 'top_report');
add_translation('page', 'new_report');
add_translation('page', 'search_report');
add_translation('element', 'access_mixed');
add_translation('element', 'access_team_intra');
add_translation('element', 'access_user_all');
add_translation('element', 'access_user_inter');
add_translation('element', 'access_user_author');
add_translation('element', 'access_public_web');
add_translation('element', 'access_user_intra');

do_translation($key, $translation, $_SESSION['dialect']['dialect_id'], $_SESSION['login']['login_user_id']);

# todo change to engine and template! we will also be able to translate things in this case! 2012-02-16 vaskoiii
# ie) TODO rework so that this automatically pics up all page_descriptions in the database! 2012-02-16 vaskoiii

# uncomment for ability to show/hide certain sections (never used)
/*
<script>
function hide_other_area(string, showall) {

	var sitemap_display = 'none';
	if (showall == 1) {
		sitemap_display = 'block';
	}

	document.getElementById('sitemap_ts_area').style.display = sitemap_display;
	document.getElementById('sitemap_new_area').style.display = sitemap_display;
	document.getElementById('sitemap_home_area').style.display = sitemap_display;
	document.getElementById('sitemap_contact_area').style.display = sitemap_display;
	document.getElementById('sitemap_doc_area').style.display = sitemap_display;
	document.getElementById('sitemap_people_area').style.display = sitemap_display;
	document.getElementById('sitemap_control_area').style.display = sitemap_display;
	document.getElementById('sitemap_other_area').style.display = sitemap_display;
	document.getElementById(string).style.display = 'block';
}
</script>

<div class="notice" style="margin: 0px -18px; margin-top: -5px;">
	This sitemap lists all list pages (beginning and advanced).
</div>

<div class="doc_box">
	<a href="javascript: hide_other_area('sitemap_ts_area', 1);">Everything</a> - 
	<a href="javascript: hide_other_area('sitemap_ts_area');">TS</a> - 
	<a href="javascript: hide_other_area('sitemap_new_area');">New</a> - 
	<a href="javascript: hide_other_area('sitemap_home_area');">Home</a> - 
	<a href="javascript: hide_other_area('sitemap_contact_area');">Contact</a> - 
	<a href="javascript: hide_other_area('sitemap_doc_area');">Doc</a> - 
	<a href="javascript: hide_other_area('sitemap_people_area');">People</a> - 
	<a href="javascript: hide_other_area('sitemap_control_area');">Control</a> - 
	<a href="javascript: hide_other_area('sitemap_other_area');">Other</a>
</div>
*/ ?> 

<div id="sitemap_ts_area" class="doc_box">
	<h3><?= tt('page', 'ts_area'); ?></h3>
	<p><?= tt('page', 'ts_area', 'translation_description'); ?></p>
	<dl>
		<dt><a href="/"><?= tt('page', 'main'); ?></a></dt>
			<dd><?= tt('page', 'main', 'translation_description'); ?></dd>
	</dl>
</div>

<div id="sitemap_new_area" class="doc_box">
	<h3><?= tt('page', 'new_area'); ?></h3>
	<p><?= tt('page', 'new_area', 'translation_description'); ?></p>

	<dl>
		<? print_sitemap_link('top_report'); ?> 
		<dd><?= tt('page', 'top_report', 'translation_description'); ?> - <?= tt('element', 'access_mixed'); ?></dd>
		<? print_sitemap_link('new_report'); ?> 
		<dd><?= tt('page', 'new_report', 'translation_description'); ?> - <?= tt('element', 'access_mixed'); ?></dd>
		<?= print_sitemap_link('search_report'); ?> 
		<dd><?= tt('page', 'search_report', 'translation_description'); ?> - <?= tt('element', 'access_mixed'); ?></dd>
	</dl>
</div>

<?
foreach ($data['new_report']['page_id'] as $k1 => $v1) {
if (!empty($v1['page_id'])) { ?> 
	<div id="sitemap_<?= to_html($v1['page_name']); ?>" class="doc_box">
		<h3><?= tt('page', $v1['page_name']); ?></h3><?
		echo tt('page', $v1['page_name'], 'translation_description'); ?> 
		<dl><?
		foreach ($v1['page_id'] as $k2 => $v2) {
			print_sitemap_link($v2['page_name']); ?> 
			<dd><?= tt('page', $v2['page_name'], 'translation_description'); ?><?
				echo '<span class="spacer">' . $config['spacer'] . '</span>';
				# todo make a function to grab the access type 2012-04-30 vaskoiii
				switch($v2['page_name']) {
					case 'item_list':
					case 'news_list':
					case 'metail_list':
					case 'rating_list':
					case 'transfer_list':
					case 'vote_list':
						echo '<span class="access_team_intra">' . tt('element', 'access_team_intra') . '</span>';
					break;
					case 'offer_list':
						echo '<span class="access_user_inter">' . tt('element', 'access_user_inter') . '</span>';
					break;
					case 'contact_list':
					case 'note_list':
					case 'group_list':
					case 'groupmate_list':
					# case 'feed_list': # uncomment if feeds are only seen by you 2012-04-04 vaskoiii
						echo '<span class="access_user_author">' . tt('element', 'access_user_author') . '</span>';
					break;
					case 'trailer_doc':
					case 'about_doc':
					case 'faq_doc':
					case 'sitemap_doc':
					case 'disclaimer_doc':
					case 'download_doc':
					case 'donate_doc':
						echo '<span class="access_public_web">' . tt('element', 'access_public_web') . '</span>';
					break;
					default:
						echo  '<span class="access_user_all">' . tt('element', 'access_user_all') . '</span>';
					break;
				} ?> 
			</dd><?
		} ?> 
		</dl>
	</div><?
} }

/*
<div class="doc_box">
	<h3><?= tt('page', 'special_area'); ?></h3>
	<p>Recover pages and Set pages. These should actually just be in the FAQ if necessary but they are pretty self explanatory.</p>
	<dl>
			<dt><a href="/rss_recover/"><?= tt('page', 'rss_recover'); ?></a></dt>
				<dd>Get your custom RSS feed link for TS sent to your email!</dd>
		<dt><a href="/login_recover/"><?= tt('page', 'login_recover'); ?></a></dt>
			<dd>Get your automatic login link sent to your email!</dd>
		<dt><a href="/login_set/"><?= tt('page', 'login_set'); ?></a></dt>
			<dd>Login!</dd> 
			<dt><a href="/lock_set/"><?= tt('page', 'lock_set'); ?></a></dt>
				<dd>Lock cetain fields to limit the data display!</dd>
		<dt><a href="/theme_set/"><?= tt('page', 'theme_set'); ?></a></dt>
			<dd>Choose a color!</dd>
		<dt><a href="/display_set/"><?= tt('page', 'display_set'); ?></a></dt>
			<dd>Set webpage to correct screen size!</dd>
		<dt><a href="/dialect_set/"><?= tt('page', 'dialect_set'); ?></a></dt>
			<dd>Change language!</dd>
	</dl>
</div>
*/
