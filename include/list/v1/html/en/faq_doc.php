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

# Contents/Description: Frequently Asked Questions

up_date('2012-03-31');

add_translation('element', 'remember');
add_translation('element', 'submit');
add_translation('element', 'access_public_web');
add_translation('element', 'access_team_intra');
add_translation('element', 'access_user_all');
add_translation('element', 'access_user_author');
add_translation('element', 'access_user_inter');
add_translation('element', 'access_user_intra');
add_translation('element', 'add');
add_translation('element', 'contact_name');
add_translation('element', 'edit');
add_translation('element', 'feature_lock');
add_translation('element', 'feature_minnotify');
add_translation('element', 'location_name');
add_translation('element', 'more');
add_translation('element', 'offer_description');
add_translation('element', 'offer_name');
add_translation('element', 'tag_name');
add_translation('element', 'team_name');
add_translation('element', 'user_name');
add_translation('meritype', 'meritype_bad');
add_translation('meritype', 'meritype_good');
add_translation('meritype', 'meritype_identity');
add_translation('meritype', 'meritype_monetary');
add_translation('page', 'host_portal');
add_translation('page', 'category_list');
add_translation('page', 'config_report');
add_translation('page', 'load_set');
add_translation('page', 'location_list');
add_translation('page', 'meripost_list');
add_translation('page', 'meritopic_list');
add_translation('page', 'metail_list');
add_translation('page', 'offer_list');
add_translation('page', 'profile_edit');
add_translation('page', 'team_list');
add_translation('page', 'user_edit');
add_translation('page', 'user_list');
add_translation('status', 'status_available');
add_translation('status', 'status_neutral');
add_translation('status', 'status_wanted');

do_translation($key, $translation, $_SESSION['dialect']['dialect_id'], $_SESSION['login']['login_user_id']);

 // LATEST ?> 
<div class="notice" style="margin: 0px -18px; margin-top: -10px;">
	Latest Modifications...
</div>

<div class="doc_box">
	<h3>Web Feeds do NOT work in Mozilla Thunderbird?</h3>
	<p>Web Feeds actually work fine in Thunderbird but Thunderbird will fail (even fail silently) so it will appear that the TS Web Feeds do not work. This is because Thunderbird requires valid certificates and if they are not valid the domain of this website [https://<?= to_html($_SERVER['HTTP_HOST']); ?>]  must be manually added to Thunderbird in the Advanced and then Certificates section. After an exception is added Web Feeds will work in Thunderbird.</p>
</div>


<div class="doc_box">
	<h3>Tab Navigation in Firefox?</h3>
	<p>In Firefox press "Tab" and "Shift + Tab" to easily navigate through TS links and forms by using the keyboard. Additionally pressing enter will submit your form you are on or follow the hyperlink you have selected.</p>
	<p>With the exception of the landing page when logged in, all pages have a default tab focus to help minimize the times of pressing "Tab" to get to where you want to be.</p>
</div>

<div class="doc_box">
	<h3>Special Category Notation?</h3>
	<dl>
		<dt>&lt;&gt;</dt>
		<dd>Path separator. ie) Level1&lt;&gt;Level2 WHERE "Level1" is the parent of "Level2"</dd>
		<dt>&lt;|!|&gt;</dt>
		<dd>Root category. All other categories are children of this category. Unless specifying the root category it is safe to omit &lt;|!|&gt; when entering a tag path</dd>
	</dl>
</div>

<div class="doc_box">
	<h3>What is the significance of <?= tt('element', 'tag_name'); ?></h3>
	<p><?= tt('element', 'tag_name'); ?> helps enforce correct placement of items. Ideally <?= tt('element', 'tag_name'); ?> is only in English. From there these English "parts" can be translated to many different languages. <?= tt('element', 'tag_name'); ?> is also intended used in the computation of supply and demand.</p>
	<p>It should also be noted that the parent of <?= tt('element', 'tag_name'); ?> is essentially the "context" of the corresponding <?= tt('element', 'tag_name'); ?>. ie) parent of <?= tt('element', 'tag_name'); ?> = [Skateboard] and <?= tt('element', 'tag_name'); ?> = [Wheel]</p>
</div>

<div class="doc_box">
	<h3>Why would I want to add goods/services here instead of on another website?</h3>
	<p>TS is a unique social experiment and evolving entity to see if an Identity-Based Non-Monetary Merit System (IBNMMS) is feasible on the internet. A more familiar IBNMMS to some extent is the social structure called a family.</p>
</div>

<div class="doc_box">
	<h3>How can I receive alerts on my mobile phone?</h3>
	<p>First find your mobile phones email address. If you don't know if you can probably find it by following these instructions:</p>
	<ul>
		<li>1. Get your phone number ie) [1234567890]</li>
		<li>2. Get Provider SMS gateway (domain name) ie) [txt.att.net] - See: <a href="http://www.wikihow.com/Text-Message-Online#Email_to_SMS_Gateways">http://www.wikihow.com/Text-Message-Online#Email_to_SMS_Gateways</a></li>
		<li>3. Test your mobile phone's email (to SMS) address. ie) [1234567890@txt.att.net]</li>
	</ul>
	<p>Once you have your mobile phones email address, go to your profile at <a href="./profile_edit/<?= ff(); ?>"><?= tt('page', 'profile_edit'); ?></a> and from there you can update your email address and make sure to turn on <?= tt('element', 'feature_minnotify'); ?>.</p>
	<p>Please note that TS only allows you to use one email address for notifications so please choose what is best for you.</p>
</div>

<? // BEGINNER ?> 
<div class="notice" style="margin: 0px -18px; margin-top: -5px;">
	Below are beginner FAQs. Easier FAQ's closer to the top. 
</div>

<div class="doc_box">
	<h3>How do I send somebody a message from TS?</h3>
	<p>Click <span class="contact_name"><?= tt('element', 'contact_name'); ?></span> <span class="user_name">(<?= tt('element', 'user_name'); ?>)</span> wherever it may appear, and then click where it says <?= tt('page', 'offer_list'); ?> then click <?= tt('element', 'edit'); ?>.
	<p>Or <a href="/offer_edit/">add messages here</a> directly. Then fill out the fields on the resulting page:</p>
	<dl>
		<dt><span class="contact_name"><?= tt('element', 'contact_name'); ?></span> <span class="user_name">(<?= tt('element', 'user_name'); ?>)</span></dt>
		<dd>Just put the name of the person you want to send a message to here if it already isn't there.</dd>
		<dt><span class="offer_name"><?= tt('element', 'offer_name'); ?></span></dt>
		<dd>A title for the message. Intended to be the focus of the message.
		<dt><span class="offer_description"><?= tt('element', 'offer_description'); ?></span></dt>
		<dd>This is the message you want to send to the person.</dd>
	</dl>
	<p>A success message will print in green on success.</p>
	
</div>

<div class="doc_box">
	<h3>How do I add something on TS?</h3>
	<p>Just click on the GIANT + on main page.</p>
	<p>Or <a href="./item_edit/<?= ff(); ?>">add stuff here</a>.</p>
	<p>From there just fill out the form and click <?= tt('element', 'add'); ?>.</p>
	<p>Once again you will see a preview of your submission on the following page when you have completed adding an item successfully.</p>
</div>

<div class="doc_box">
	<h3>What if I get lost on TS?</h3>
	<p>Just click on the title at the top to return to the landing page.</p>
	<p>Alternatively you can just use the navigation which points to:</p>
	<ul>
		<li><?= tt('page', 'item_list'); ?></li>
		<li><?= tt('page', 'offer_list'); ?></li>
		<li><?= tt('page', 'sitemap_doc'); ?></li>
</div>

<div class="doc_box">
	<h3>Incremental learning?</h3>
	<p>In order to make TS easier to use for first time users by default only the minimal amount of information is displayed to prevent confusion/frustration. From here different links can be clicked to expand the possible actions such as:</p>
	<ul>
		<li><?= tt('element', 'more'); ?></li>
	</ul>
	<p>It is also intended to focus on just <?= tt('page', 'item_list'); ?> and <?= tt('page', 'offer_list'); ?> to not get so overwhelmed.
</div>

<div class="doc_box">
	<h3>How can I make a link in my post?</h3>
	<p>Links are automatically generated in ANY description field.</p>
	<p>Also links will automatically be created for any protocol. ie) mailto: http: callto: skype:
</div>

<a name="invite_system"></a>
<div class="doc_box">
	<h3>How does the invitation system work? How do I invite my friend?</h3>
	<p>The invitation system currently allows you to invite whoever you want. However, TS is intended for serious users. Who you invite is <a  href="./invited_list/">monitored</a>.</p>
	<p>The preferred option to invite someone is to:</p>
	<ul>
		<li>1. Sit down with your friend on TS.</li>
		<li>2. Log in with your user name.</li>
		<li>3. Navigate to <a href="./user_edit/"><?= tt('page', 'user_edit'); ?></a> and fill out the form.</li>
	</ul>
	<p>Alternatively you can just <a href="/invite_edit/">invite your friend</a> and have your friend click the link in the email they will receive and fill out the registration form from there.</p>
	<p>Please note the special registration link you get in the invitation email expires in 7 days.</p>
</div>

<div class="doc_box">
	<h3>Can I make TS easier to read/use on my handheld?</h3>
	<p>There are actually a variety of configuration options you can use. You can turn off certain things from loading to prevent unnecessary info from loading like "some"  javascript (Resulting in faster page loads).</p>
	<p>However, if you would like to still maintain a rich browsing experience there are also various <a href="./display_set/">display settings</a> that should accommodate almost ANY device</p>
	<p>TS should work on a wide range of devices and has been tested on many smartphones with no configuration needed.</p>
</div>

<div class="doc_box">
	<h3>How can I share things anonymously?</h3>
	<p>TS is NOT an anonymous site although if people do not know your username they might not know who you are. However, there are websites that are better equiped for anonymous sharing such as: <a href="http://www.craigslist.com">http://www.craigslist.com</a> or <a href="http://www.wikileaks.info">http://www.wikileaks.info</a></p>
</div>

<div class="doc_box">
	<h3>Is this a communism?</h3>
	<p>No, even though many of the resources are shared, users can still use disgression whether or not to share goods/services. If this site was a communism it would have to "advocate elimination of private property" (communism definition from www.m-w.com)</p>
</div>

<div class="doc_box">
	<h3>How will TS benefit me?</h3>
	<p>Users will be able to directly benefit from goods/services for trading/sharing in their communities, and as a user's community grows so will the user's assets.</p>
</div>

<div class="doc_box">
        <h3>How can I navigate quickly through TS?</h3>
	<p>If you are finding that using the menus are not fast enough you can use the launcher program with key combinations:</p>
	<ul>
		<li>Load up TS</a>.</li>
		<li>Click on the body of the page.</li>
		<li>Pager</li>
		<ul>
			<li>Press [ctrl + shift + ,]</li>
			<li>Type the page you want to visit and press enter.</li>
		</ul>
		<li>Peopler</li>
		<ul>
			<li>Press [ctrl + shift + .]</li>
			<li>Type the name of the contact you want (that you previously added) and press enter.</li>
		</ul>
		<li>Scanner</li>
		<ul>
			<li>Press [ctrl + shift + /]</li>
			<li>Type the ID number of the person you want and press enter.</li>
			<li>This method can be used if a person doesn't have their ID.</li>
		</ul>
	</ul>
	<p>Also, using the launcher requires you load javascript from <a href="./load_set/"><?= tt('page', 'load_set'); ?></a>.
</div>

<div class="doc_box">
	<h3>Can I send a link of my <?= tt('page', 'item_list'); ?> search results to a friend?</h3>
	<p>Yes, simply copy the address in your browser and send it in an email or whatever means you use. However, if you use a private <?= tt('element', 'contact_name'); ?> that is not tied to a user name your friend will not be able to see the corresponding listings.</p>
	<p>Also, your friend will need to login.</p>
</div>

<div class="doc_box">
	<h3>What do I put on the database?</h3>
	<p>Ideally everyone would have everything they owned or wanted on TS. Anything at all can be entered into the database such as: Things you want, Things you dream of, Things you have, Services you can offer, etc. People probably will NOT put everything on TS, but the more goods/services listed the better. For more specifics just do a search! </p>
</div>
<div class="doc_box">
	<h3>What does <?= tt('status', 'status_available'); ?>, <?= tt('status', 'status_neutral'); ?>, or <?= tt('status', 'status_wanted'); ?> mean?</h3>
	<dl>
		<dt><?= tt('status', 'status_available'); ?></dt>
			<dd>Available for trading or sharing.</dd>
		<dt><?= tt('status', 'status_neutral'); ?></dt>
			<dd>Medium between wanted and available. Choose <?= tt('status', 'status_neutral'); ?> if you can not decide which option to choose and still want to list something.</dd>
		<dt><?= tt('status', 'status_wanted'); ?></dt>
			<dd>You know you want it.</dd>
	</dl>
</div>

<div class="doc_box">
	<h3>What's the deal with all the text?</h3>
	<p>It is easier to add stuff without worrying about getting a picture of every item that goes on here! Also, since the target audience is friends and family you might be able to see things on the database firsthand, which is even better than a picture. In the future, pictures may be tied into "tags" to help enforce correct supply and demand placement.</p>
	<p>If you are the kind of person who needs to see what something looks like ASAP, you can always try typing the appropriate text in "Google Images" which can be found at <a href="http://images.google.com">http://images.google.com</a></p>
	<p>ie) if you see an "HP Deskjet 722C" listed, a google image search would return <a href="http://images.google.com/images?q=HP%20Deskjet%20722C&amp;hl=en&amp;lr=&amp;sa=N&amp;tab=wi" >http://images.google.com/images?q=HP%20Deskjet%20722C&amp;hl=en&amp;lr=&amp;sa=N&amp;tab=wi</a></p>
</div>


<? // NOTICE ?> 
<div class="notice" style="margin: 0px -18px; margin-top: -5px;">
	Below are more advanced FAQs. Easier FAQ's are close to the top.
</div>

<div class="doc_box">
	<h3>How does the searching work on TS?</h3>
	<p>By default everything that you are permitted to see is shown. By specifying additional search parameters only matching results are shown.</p>
</div>

<div class="doc_box">
	<h3>What permission levels exist on TS?</h3>
	<p>There are a bunch of different permission levels that are user centric. These list pages with corresponding access levels can be seen from the <?= tt('page', 'top_report'); ?> page and the corresponding levels are described below.</p>
	<dl>
		<dt><?= tt('element', 'access_public_web'); ?></dt>
			<dd>Any one even if they are not a user of the site can see this information.</dd>
		<dt><?= tt('element', 'access_user_all'); ?></dt>
			<dd>Any user on the site can see this information.</dd>
		<dt><?= tt('element', 'access_team_intra'); ?></dt>
			<dd>Any user on the corresponding team can view this information.</dd>
		<dt><?= tt('element', 'access_user_inter'); ?></dt>
			<dd>Info shared between the sending user and the receiving user.</dd>
		<dt><?= tt('element', 'access_user_author'); ?></dt>
			<dd>Private info that only you can see.</dd>
	</dl>
	<p>Info in your user profile is a special case. Your profile is for the most part private. However, <?= tt('element', 'location_name'); ?> is viewable by all TS users.</p>
	<p><?= tt('element', 'contact_name'); ?> is also a special case. It shows up in your listings but not anyone else's. This is so you can help identify people on TS.</p>
</div>

<div class="doc_box">
	<h3>How do I get more options in the drop down menus?</h3>
	<p>On the appropriate listing click <?= tt('element', 'more'); ?> and then click <?= tt('element', 'remember'); ?> then click <?= tt('element', 'submit'); ?>. See:</p>
	<ul>
		<li><a href="/category_list/"><?= tt('page', 'category_list'); ?></a></li>
		<li><a href="/location_list/"><?= tt('page', 'location_list'); ?></a></li>
		<li><a href="/team_list/"><?= tt('page', 'team_list'); ?></a></li>
	</ul>
</div>

<div class="doc_box">
	<h3>How do I verify someone's identity?</h3>
	<p>Use the <a href="/host_portal/"><?= tt('page', 'host_portal'); ?></a> page to match someone's identity by to their user name using a photo of their face</p> 
	<p>Alternatively see the <a href="/metail_list/"><?= tt('page', 'metail_list'); ?></a> page for potential information that can be used to identify the corresponding user. ie) a link to a Facebook profile...</p>
</div>

<div class="doc_box">
	<h3>Configuration?</h3>
	<p>See <a href="/config_report/"><?= tt('page', 'config_report'); ?></a>. From there there are a several configuration options.</p>
	<p>Most of the configuration should be self-explanatory. It should be noted as well that logged in users have additional configuration options. Some of the non-self-explanatory configuration options are shown below:</p>
	<dl>
		<dt><?= tt('element', 'feature_minnotify'); ?></dt>
		<dd>Make it so that your email notification include only a subject and link (The bare minimum notification).</dd>
		<dt><?= tt('element', 'feature_lock'); ?></dt>
		<dd>Allow changing the focus of displayed results for the entire site by "locking" a specific interest group.</dd>
	</dl>
</div>

<div class="doc_box">
	<h3>How do some of the ideas presented in TS compare with other systems of merit?</h3>
	<p>It is likely that most merit systems will be hybridized but we try and compare different types of merit systems in their more true form including: </p>
	<dl>
		<dt><?= tt('meritype', 'meritype_good'); ?></dt>
		<dd>Good. NOT necessarily realistic.</dd>
		<dt><?= tt('meritype', 'meritype_bad'); ?></dt>
		<dd>Bad. NOT necessarily realistic.</dd>
		<dt><?= tt('meritype', 'meritype_monetary'); ?></dt>
		<dd>Predominantly Monetary Based. Used by most of the world.</dd>
		<dt><?= tt('meritype', 'meritype_identity'); ?></dt>
		<dd>Predominantly Identity Based. Theoretical NON-monetary system used by TS.</dd>
	</dl>
	<p>
		For an ongoing look at these systems of merit compared against certain criteria see:
		<ul>
			<li><a href="/meritopic_list/"><?= tt('page', 'meritopic_list'); ?></a></li>
			<li><a href="/meripost_list/"><?= tt('page', 'meripost_list'); ?></a></li>
		</ul>
	</p>
</div>

<div class="doc_box">
	<h3>Hiding certain things from users?</h3>
	<p>While TS does strive to be as open as possible, showing ALL your information with the world may NOT be practical.  Therefore, you can limit who can see certain posts on TS including items and news to just your friends if you like. This is currently possible with: <?= tt('page', 'item_list'); ?> and <?= tt('page', 'news_list'); ?> by specifying <?= tt('element', 'team_name'); ?></p>
</div>

<div class="doc_box">
	<h3>General Notation?</h3>
	<dl>
		<dt>TS</dt>
			<dd>Abbreviation for Trade and Share</dd>
		<dt>[]</dt>
			<dd>Anything in between [ and ] is used to denote an EXACT reference and it's meaning is similar to that of using quotes however brackets are intended to be more precise than quotes, and they also denote direction. This convention is NOT intended to be "stackable" either. ie) [[Guitar]] is acceptable syntax but would refer to "[Guitar]" rather than "Guitar" as the EXACT match. See also <a  href="http://en.wikipedia.org/wiki/Bracket">http://en.wikipedia.org/wiki/Bracket</a></dd>
		<dt>*</dt>
			<dd>Data is already propagated in the link.</dd>
		<dt>&gt;&gt; or &lt;&lt;</dt>
			<dd>Next or Previous page. In other uses it just demonstrates direction.</dd>
		<dt>&gt;&gt;| or |&lt;&lt;</dt>
			<dd>Last or First page.</dd>
		<dt>||</dt>
			<dd>System name enclosure. Reserved special names that were NOT automatically created. Meaningless when used outside &lt;&gt;.</dd>
		<dt>&lt;&gt;</dt>
			<dd>Reserved name enclosure. Characters are disallowed on name fields, but allowed on any description fields.</dd>
		<dt>()</dt>
			<dd>Unabstracted name enclosure. Potentially untranslated name. Nestable but only in the same sense as [].</dd>  
		<dt>(&lt;||&gt;)</dt>
			<dd>Potential unabstracted reserved system name enclosure. Demonstrates 3 level enclosure odering which may not be rearranged although enclosures may be omitted. Current implementation uses a maximum of 2 levels concurrently with (||) and &lt;||&gt; </dd>
	</dl>
</div>

<div class="doc_box">
	<h3>Special Location Notation?</h3>
	<dl>
		<dt>&lt;|?|&gt;</dt>
			<dd>Default location! Spherical coordinates are at the North Pole.</dd>
	</dl>
</div>

<div class="doc_box">
	<h3>Special Team Notation?</h3>
	<dl>
		<dt>Team &lt;|*|&gt;</dt>
			<dd>Special team that everyone is a part of.</dd>
		<dt>Team &lt;[0-9a-z]&gt;</dt>
			<dd>Author only team. [0-9a-z] is the regular expression to match any series of consecutive mixed digits and lowercase English letters corresponding to a <?= tt('element', 'user_name'); ?>.</dd>
		<dt>Team &lt;*[0-9a-z]&gt;</dt>
			<dd>User default team. AKA friends. Once again [0-9a-z] is the regular expression to match any series of consecutive mixed digits and lowercase English letters corresponding to a <?= tt('element', 'user_name'); ?>.</dd>
	</dl>
</div>

<div class="doc_box">
	<h3>User Notation?</h3>
	<dt>|root|</dt>
		<dd>Unabstracted Special Username - |root| is the initial user of TS.</dd>
</div>
