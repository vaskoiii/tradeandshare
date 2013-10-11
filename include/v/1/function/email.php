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

# Contents/Description: Email sendinging functions

function get_email_boundary() {
	return md5(date('r', time()));
}

# obsolete
function get_email_header($feature_minnotify = 1, $boundary = '') {
	die('use get_tsmail_header()');
}
function get_email_subject($listing_type, $feature_minnotify = 1) {
	die('use get_tsmail_subject()');
}
function get_email_body($page_type, $feature_minnotify = 1, $boundary = '') {
	die('use get_tsmail_body()');
}

function get_tsmail_header(& $tsmail = null) {
	global $config;

	$minnotify = 1;
	if (!empty($tsmail)) {
	if ($tsmail['data']['search']['response']['search_miscellaneous']['feature_minnotify'] != 1) {
	if (!empty($tsmail['data']['search']['response']['search_miscellaneous']['email_boundary'])) {
		$minnotify = 2;
	} } }


	# gmail didnt like the charset=utf-8 if $minnotify == 2 2012-04-24 vaskoiii
	return 
		'From: ' . $config['email_from'] . "\r\n" .
		'Reply-To: ' . $config['email_from'] . "\r\n" . (
		$minnotify == 2
			? 'MIME-Version: 1.0' . "\r\n" . 'Content-Type: multipart/mixed; boundary="PHP-mixed-' . $tsmail['data']['search']['response']['search_miscellaneous']['email_boundary'] . '"' . "\r\n" 
			: 'Content-type:text/plain; charset=utf-8' . "\r\n"
		) .
		'X-Mailer: PHP/' . phpversion() . "\r\n";
}

function get_tsmail_subject(& $tsmail) {
	global $config;

	# Since we don't have language on user profile we have to send with the current user's language 2012-04-06 vaskoiii
	$dialect_id = $_SESSION['dialect']['dialect_id'];

	// TODO: start_engine() should set the count.
	$tsmail['data']['list']['result']['total'] = count($tsmail['data']['list']['result']['listing']);

	$email_subject = $config['title_prefix'];

	$minnotify = 1;
	if (!empty($tsmail)) {
	if ($tsmail['data']['search']['response']['search_miscellaneous']['feature_minnotify'] != 1) {
	if (!empty($tsmail['data']['search']['response']['search_miscellaneous']['email_boundary'])) {
		$minnotify = 2;
	} } }
	if ($minnotify == 1) { # only ASCII chars (means no translations)
		$email_subject .= ucfirst($listing_type) . ' Link' .  $config['spacer'] . $_SESSION['login']['login_user_name'];
	}
	else {
		# setting the subject!
		$sender_contact_name = $tsmail['key']['user_id']['result'][ $_SESSION['login']['login_user_id'] ]['contact_name'];
		$email_subject .= tt('page', $tsmail['x']['load']['list']['type'] . '_list', 'translation_name', $tsmail['translation']) . $config['spacer'];
		$email_subject .= $sender_contact_name ? $sender_contact_name : ' <' . $_SESSION['login']['login_user_name'] . '>';
	}
	if ($tsmail['data']['list']['result']['total'] > 1)
		$email_subject .= $config['spacer'] . $tsmail['data']['list']['result']['total'];
	return $email_subject;
}

function get_tsmail_body(& $tsmail) {
	global $config;

	# shortcut
	$boundary = $tsmail['data']['search']['response']['search_miscellaneous']['email_boundary'];
	$email_steak = '';
	$email_body = '';
	# dont html encode the email link because it will also be displayed in plaintext 2012-04-10 vaskoiii
	$email_link   = 'https://' . $_SERVER['HTTP_HOST'] . '/user_view/?list_name=list&list_type=' . $tsmail['x']['load']['list']['type'] . '&lock_user_id=' . (int)$_SESSION['login']['login_user_id'];


	$tsmail['data']['list']['result']['listing'][0]['list_type'] = $tsmail['x']['load']['list']['type']; # Needed?


	$minnotify = 1;
	if (!empty($tsmail)) {
	if ($tsmail['data']['search']['response']['search_miscellaneous']['feature_minnotify'] != 1) {
	if (!empty($tsmail['data']['search']['response']['search_miscellaneous']['email_boundary'])) {
		$minnotify = 2;
	} } }

	if ($minnotify == 1)
		$email_message .= $email_link;
	else {
		# todo make functions?
		include('v/1/css/text_style.php'); # pretty text colors!
		include('v/1/css/email.php'); # picks a random theme could also be a function

		# set random background colors ;
		$data['css'] = array_merge(
				get_background(str_replace('theme_', '', $data['css']['email']['theme_name'])),
				$data['css']
		);

		// if certain variables are set in text_style.php we get the colorizing in email.

		# todo we dont want to duplicate data like this but we want all email info separate! 2012-04-06 vaskoiii
		# merged from above includes - perhaps they should be functions 2012-04-06 vaskoiii

		$tsmail['data']['css'] = $data['css'];

		ob_start(); # get some usage out of the output buffer tool
		foreach($tsmail['data']['list']['result']['listing'] as $k1 => $v1) {
			# todo simplify parameters of print_listing_template()
			print_listing_template(
				$tsmail['data']['list']['result']['listing'][$k1], # & $listing
				$tsmail['key'],
				$tsmail['translation'],
				'list', # $load
				'email', # $display
				'body', # $part
				$tsmail['_SESSION']['login']['login_user_id'], # $login_user_id
				$tsmail['data']['css'], # $style = null
				$tsmail['x']['load']['list']['type'] # $type
			);
		}
		$email_steak = ob_get_clean();

		# Email intentionally has several ways to define color since lots of email clients suck at display

#################### fold #######################
ob_start(); # Turn on output buffering ?> 

--PHP-mixed-<?= $boundary; ?> 
Content-Type: multipart/alternative; boundary="PHP-alt-<?= $boundary; ?>"

--PHP-alt-<?= $boundary; ?> 
Content-Type: text/plain; charset="utf-8"
Content-Transfer-Encoding: 8bit

<?= tt('page', $tsmail['x']['load']['list']['type'] . '_list', 'translation_name', $tsmail['translation']); ?> 

<?= html_entity_decode(strip_tags($email_steak)); // text not html! ?> 

<?= $email_link; // remember the trailing spaces! ?> 

--PHP-alt-<?= $boundary; ?> 
Content-Type: text/html; charset="utf-8"
Content-Transfer-Encoding: 8bit

<? # <table> and other unconventional inline notation is used to maximize compatibility ?>
<html>
<head>
	<meta content="text/html; charset=utf-8" http-equiv="Content-Type"><? # Needed? ?> 
	<meta name="viewport" content="width=480" /><? # Needed? Email client can squish the flexible design if necessary? ?> 
</head>

<body><table width="" bgcolor="#c0c0c0" cellpadding="0" cellspacing="1" border="0" style="word-wrap: break-word;"><tr><td>
	<table width="420" bgcolor="#fffffe" align="center" cellpadding="0" cellspacing="1" border="0">
		<tr><td bgcolor="<?= $tsmail['data']['css']['c1']; ?>">
		<table width="100%" bgcolor="" valign="" cellpadding="10" cellspacing="0" border="0">
			<tr><td>
				<font size="5" color="#000001">
				<a style="color:#000001;" href="<?= to_html($email_link); ?>"><b><span style="color: <?= $tsmail['data']['css']['text_style']['color']['link']; ?>;"><?= tt('page', $tsmail['x']['load']['list']['type'] . '_list', 'translation_name', $tsmail['translation']); ?></span></b></a>&nbsp;
				</font>
				<font size="3">
					<a style="color:#000001;" href="<?= to_html($email_link . '&expand%5B0%5D=action'); ?>"><span style="color: <?= $tsmail['data']['css']['text_style']['color']['link']; ?>;"><?= tt('element', 'edit', 'translation_name', $tsmail['translation']); ?></span></a><span style="color:#000000;">*</span>
				</font>
			</td></tr>
		</table>
		</td></tr>
		<tr><td bgcolor="<?= $tsmail['data']['css']['c0']; ?>">
			<table width="100%" align="" cellpadding="25" cellspacing="" border="0">
			<tr><td>
				<?= $email_steak; # already htmlified ?> 
			</td></tr>
			</table>
		</td></tr>
		<tr><td bgcolor="<?= $tsmail['data']['css']['c2']; ?>">
			&nbsp;
		</td></tr>
	</table>
</td></tr></table></body>
</html>

--PHP-alt-<?= $boundary; ?>--
--PHP-mixed-<?= $boundary; ?>--

<?

# Previously used in the footer with the more link.
/*
<table width="100%" bgcolor="" align="center" cellpadding="0" cellspacing="0" border="0">
	<tr><td>&nbsp;</td></tr>
	<tr><td align="center">
		<a href="<?= to_html($email_link); ?>"><b><span style="color: <?= $tsmail['data']['css']['text_style']['color']['link']; ?>;"><?= tt('element', 'more', 'translation_name', $tsmail['translation']); ?></span></b></a>
	</td></tr>
</table>
 */


$email_message = ob_get_clean(); # copy current buffer contents into $message variable and delete current output buffer
#################### unfold #######################

	}
	return $email_message;
}

function get_user_email_array($user_id, $user_more_extra_field = false) {
	// Typical Usage:
	//get_user_email_array($data['user_id'], 'notify_offer_received');
	global $config;
	$sql = '
		SELECT
			u.email,
			um.feature_minnotify' . 
				($user_more_extra_field 
					? ',	um.' . $user_more_extra_field
					: '' 
				)
				. '
		FROM
			' . $config['mysql']['prefix'] . 'user u,
			' . $config['mysql']['prefix'] . 'user_more um
		WHERE
			u.id = um.id AND
			u.id = ' . (int)$user_id  . ' AND
			u.active = 1 
		LIMIT
			1
	';
	$email_array = array();
	$result = mysql_query($sql) or die(mysql_error());
	while ($row = mysql_fetch_assoc($result)) {
		$email_array['email'] = $row['email'];
		if ($user_more_extra_field)
			$email_array[$user_more_extra_field] = $row[$user_more_extra_field];
		$email_array['feature_minnotify'] = $row['feature_minnotify'];
	}
	return $email_array;
}

// Needed for feeds to show the contact name of the feed owner relative to the feed owner
// TODO: could be eliminated if we include the $_SESSION['login']['login_user_id'] in the search under listing_key_translation();
function get_sender_contact_name($receiver_user_id) {
	global $config;
	global $_SESSION;
	return get_db_single_value('
			c.name
		FROM
			' . $config['mysql']['prefix'] . 'contact c,
			' . $config['mysql']['prefix'] . 'link_contact_user lcu
		WHERE
			lcu.contact_id = c.id AND
			lcu.user_id = ' . (int)$receiver_user_id . ' AND
			c.user_id = ' . (int)$_SESSION['login']['login_user_id'] . ' AND
			c.active = 1
	');
}
