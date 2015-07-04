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

# Contents/Description: All about Trade and Share

up_date('2014-11-11'); 

# LATEST ?> 
<!--
<div class="doc_notice notice" style="margin: 0px -18px; margin-top: -10px;">
	Latest Modifications...
</div>

<div class="notice" style="margin: 0px -18px; margin-top: -5px;">
	Read below for more on what TS is all about...
</div>
-->

<div class="doc_box">
	<h3>Welcome</h3>
	<p>Trade and Share (TS) is a gifting/sharing site. Trading is of course possible but mostly in the sense of gifting/sharing.</p>
	<p>Currently TS is similar to most classified ads in that is presents several listings of goods and services to browse through with the ability to contact the poster.  Listings can be designated as wanted, available, or neutral depending on what is most appropriate.  Users can do "relative" and customized searches of these listings based on their friends and families or whoever, and many times users can get things that are available for free. You can even specify who will be able to see your listings!</p>
	<p>Probably the biggest difference with TS and classified ads is that TS takes the focus off of money and onto the wants and needs of its users and communities. How it does this is further illustrated below.</p>
</div>

<div class="doc_box">
	<h3>TS Explained (Easy)</h3>
	<p>Gifting, trading, and sharing on TS is proactive rather than passive. ie) You have to actually find what you have and post it on TS instead of just letting it sit there until someone finds it.</p>
	<p>Sometimes new users do not recognize that they have many goods/services to add. However, if you "want anything" or "have anything" you already have stuff to add to TS. The following should provide a basic understanding of what happens if you do choose to add this stuff to TS:</p>
	<ul>
		<li>When you put up something that you want on TS, people that like you will see it. They will want to help you out and will provide you with what you want.</li>
		<li>When you put something that you do NOT want on TS, people that want what you don't will contact you. You decide who to give it to and what you want in return.</li>
		<li>When you put something up that you have on TS, you are able to share it with others that you like.</li>
	</ul>
	<p>The above is only a slightly more insightful explanation of the slogan available on the footer of every page, which reads: "Want it? Get it! Have it? Share it! Don't want it? Trade it!" This is also recapped below in the [Goods Scenarios] and [Services Scenarios] below...</p>
	
</div>

<div class="doc_box">
	<h3>TS Explained (Detailed)</h3>
	<p>TS is an economic system of gifting goods and services based on a mix of personal and publicly-available-collective knowledge of individuals to those who are more deserving. It is a kind of donation based economy. TS will used the term [Identity-Based Merit System] to refer to this kind of system. The end goal is a self-regulating donation oriented economy.</p>
	<p>One concern in an [Identity-Based Merit System] is that collective knowledge of an individual is shared to some extent to allow identity to be validated (resulting in some loss of privacy). In a centralized Identity-Based Merit System identity theft can be a big concern because many people are unknown. However, with smaller communities and a more decentralized P2P Identity-Based Merit system identity theft should be less of a concern as it will be harder to falsify one's identity. See also: <a href="https://en.wikipedia.org/wiki/Monkeysphere">https://en.wikipedia.org/wiki/Monkeysphere</a></p>
	<p>The current architecture of TS is a centralized model (designed as a proof of concept) but is intended to be moved to a decentralized model. In this way the would be no central points to amass too much information/power. See also: <a href="https://en.wikipedia.org/wiki/Decentralized">https://en.wikipedia.org/wiki/Decentralized</a></p>
	<p>TS could also be described as a continual voting system when specifying your wants and needs. This contrasts with a traditional poll in that TS allows you to change your vote (or what you want) at any time, whereas as in a traditional poll you can no longer change your vote once it is cast. The goal with TS as a continual voting system is to have things that people want or don't want constantly evaluated directly rather than being abstractly worked out through monetary means.</p>
	<p>Since TS is an [Identity-Based Merit System] rather than a [Monetary-Based Merit System], it is unique from other websites out there including ebay, craigslist, etc. because TS emphasizes "who you are" translating to what you deserve rather than "how much money you have" translating to what you deserve.</p>
	<p>Some of the main ideas that are used by TS are not entirely unique, but when used together they do present a medium that is truly different. These ideas include:</p>
	<ul>
		<li>Identity</li>
		<li>Personal Communities</li>
		<li>Non-monetary means</li>
		<li>Potential</li>
	</ul>
	<p>These 4 items will be discussed below along with how they tie into the system and how the system works.</p>
</div>
<div class="doc_box">
	<h3>Identity</h3>
	<p>Basically your identity is who you are. It is a mix of public and private knowledge about you. Private knowledge will come from personal relationships and information NOT listed on TS. Public knowledge is intended to be suggestive and not as an absolute. It will come from information on TS such as:</p>
	<ul>
		<li>One's Score (Likes/Dislikes)</li>
		<ul>
		<li>Warning: Score and membership are still evolving at <a href="./score_report"><?= tt('page', 'score_report'); ?></a>.</li>
			<li>Each user is allowed to score other users as many times as they like.</li>
			<li>You can specify who sees the scores you make.</li>
 			<li>Users can change their scores of other users at any time unless they are committed to the log used in calculating membership payouts</li>
			<li>A review period is provided for scores that will be permanent.</li>
			<li>Applicable scores come from a specific channel (community).</li>
			<li>Used to distribute wealth.</li>
			<li>
				Rating computations can be found by examining the source code at:
				<br />
				<a href="https://github.com/vaskoiii/tradeandshare/blob/master/include/list/v1/page/score_report.php">github (engine)</a>
				<br />
				<a href="https://github.com/vaskoiii/tradeandshare/blob/master/include/list/v1/page/t1/score_report.php">github (template)</a>
			</li>
			<li>Physics involved that supply data for the computation are spreadout through: Rating, Channel, Cycle, and Renewal.</li>
			<li>A user's report was originally intended to be the average of the average viewable source user scores on the destination user.</li>
		</ul>
		<li>One's Assets</li>
		<ul>
			<li>Important to be tied to one's identity alongside one's scores.</li>
			<li>Extensions of who a person is.</li>
			<li>What goods/services you have listed on TS.</li>
		</ul>
		<li>More</li>
		<ul>
			<li>Any action you perform on TS that will directly affect other users.</li>
			<li>Other miscellaneous transactions associated with your identity.</li>
			<li>Other public knowledge of you from sources other than TS.</li>
		</ul>
	</ul>
	<p>Public knowledge serves as an important and portable means of demonstrating merit, especially to those who do NOT know you or to those you THINK you know.  It should also be noted that the public knowledge contained on TS is made to serve ONLY as a suggested evaluation of one's merit... NOT as an absolute.</p>
</div>
<div class="doc_box">
	<h3>Voting</h3>
	<p>Voting has also been integrated into TS so that it can be used as a democratic medium to like/dislike things amounting to approval/disapproval accordingly. Ultimately votes are intended to be tied to the users reciving the votes rather than the individual things that are being voted on. Voting is intended to be transparent and accountable (2 key factors in avoiding corruption). Note that it is possible to hide votes by limiting them to be viewed by only certain teams. However, in the event that a vote is not visible it is intended to not count as it can not be seen or verified. Voting is not an absolute but is intended to serve as a guide for what is best for the whole or a specific community. Voting is also intended to raise awareness that there are other more democratic systems that people may not be familiar with. See also: <a href="https://en.wikipedia.org/wiki/Voting">https://en.wikipedia.org/wiki/Voting</a></p>
	<p>The computation for calculating voting is still evolving but it is intended that each person has an equal and specific "weight" that they can distribute to the things and ultimately to the users relative to them. ie) with membership</p>
</div>
<div class="doc_box">
	<h3>Personal Communities</h3>
	<p>TS emphasizes personal relationships and building communities of your PEERS!  In this way your communities are actually tangible, meaning you know those involved and what is going on.</p>
	<p>You are also able to isolate only the people you want to look at by completely customizing groups and teams.  In fact every page in TS is designed with this in mind, so you can actually navigate from page to page seeing only information relative to the people you care about with ease.</p>
	<p>With this personal emphasis and ability to see your community's assets, sharing is greatly promoted in one's communities.</p>
	<p>On the contrary, NON-personal activity is NOT emphasized though it is expected. NON-personal activity is NOT emphasized because with unknown people it is likely that you do not know their motives or what is going on.</p>
</div>
<div class="doc_box">
	<h3>Non-Monetary Means</h3>
	<p>Because monetary systems are often mis-representative of merit, TS explores feasible alternatives for use in today's society. TS attempts to represent value in terms of real world goods/services (NOT worthless paper) as follows:</p>
	<ul>
		<li>Supply of a particular good/service is evaluated.</li>
		<li>Demand of a particular good/service is evaluated.</li>
		<li>Different ways of listing the same good/service is accounted for.</li>
		<li>A good/service's report is then represented as a cumulation of the above info.</li>
	</ul>
</div>
<div class="doc_box">
	<h3>Potential</h3>
	<p>This part is highly theoretical especially since money isn't involved.</p>
	<p>However, the idea is that your identity translates to your merit/assets. In the event that monetary value is no longer used, obviously you are NOT going to be able to take your assets everywhere you go, but you can take your identity everywhere you go. So then your identity (merit/assets) will translate to your ability to perform transactions or purchases if you will. This is similar to your purchasing power from your credit score in a monetary system.</p>
	<p>It should also be noted that identity is relative to how well you are known. In the event that you are an unknown person in a new community it is likely that your purchasing power will take a hit regardless of your identity in other communities. This is because your identity is not known to this new community you are in.  Of course this can be remedied by building a positive relationship with those in your new community. Compare this with modern society where money will get you most everything even when nobody knows who you are.</p>
	<p>The scope of TS and it's ideas is variable. It can be big enough to benefit the world or small enough to benefit a few people. TS can be utilized alongside the monetary system or without it (eliminating the need for monetary dependancy).</p>
	<p>At any rate, TS is a possible means of social and economic reform that aims to be more democratic than the current system.</p>
	<p>Few sites represent these ideas and fewer still to the degree done in TS.</p>

</div><?
# Miscellaneous Sections: ?> 
<div class="doc_box">
	<h3>Similar (But Very Different) Sites:</h3>
	<p><a href="http://thevenusproject.com" >http://thevenusproject.com</a> - Emphasizes a resource based economy although it doesn't seem to touch on merit.</p>
	<p><a href="http://gigoit.org" >http://gigoit.org</a> - GIGO is an acronym for Garbage In Garbage Out.  Emphasis is on reuse.</p>
	<p><a href="http://u-exchange.com"  >http://u-exchange.com</a> - Emphasizes specific trades.</p>
	<p><a href="http://craigslist.com" >http://craigslist.com</a> - Emphasizes anonymous transactions.</p>
	<p><a href="http://wishlist.com" >http://wishlist.com</a> -  Emphasizes purchasing "wishlist" items.</p>
	<p><a href="http://ebay.com" >http://ebay.com</a> - Emphasizes buying/auctioning items.</p>
	<p><a href="http://freecycle.org" >http://freecycle.org</a> - Emphasizes recycling.</p>
	<p><a href="http://swap.com" >http://swap.com</a> - Emphasizes swapping.</p>
</div>
<div class="doc_box">
	<h3>Effective Behaviors</h3>
	<p>Certain behaviors will help users be more successful in using TS. Improving such behaviors will increase the overall effectiveness of TS. Some helpful behaviors are listed below:</p>
	<ul>
		<li>Working Toward Sustainability</li>
		<ul>
			<li>Preferring something available (and possibly more functional) even if it is old, as opposed to preferring something new (and possibly less functional) to help combat hoarding and/or wasting resources.</li>
		</ul>
		<li>Patience</li>
		<ul>
			<li>Let users know what you have before getting rid of it...</li>
			<li>Let users know what you want before going to get it...</li>
		</ul>
		<li>Being Specific</li>
		<ul>
			<li>Words like "it" are ambiguous, and part of being specific includes restating the question in the answer.</li>
		</ul>
		<li>Being Inquisitive</li>
		<ul>
			<li>Trying to explore new parts/functionality of the site and asking questions.</li>
			<li>Helps generate valuable feedback for what users would like to see improved.</li>
		</ul>
		<li>Courage</li>
		<ul>
			<li>Users that do NOT feel inhibited, shy, embarrassed or otherwise are more likely to contribute to the community.</li>
		</ul>
	</ul>
</div>
<div class="doc_box">
	<h3>Purpose</h3>
	<p>To facilitate trading and sharing on the basis of MERIT rather than monetary means.</p>
</div>
<div class="doc_box">
	<h3>Philosophy</h3>
	<p>EVERYONE in the world has something to offer. TS is simply a means to help people less obtrusively advertise.</p>
</div>
<div class="doc_box">
	<h3>Requirements</h3>
	<p>Minimal: Web browser with support for HTML and COOKIES.</p>
	<p>Recommended: Current web browser with FULL JavaScript Support.</p>
	<p>Basically: Everything from smart phones to desktop computers.</p>
</div>
<div class="doc_box">
	<h3>Intended Audience</h3>
	<p>Ultimately everyone is intended to use TS, but for now:</ul>
	<ul>
		<li>Friends and family wanting things or wishing to loan or barter items:</li>
		<li>Basically anyone and everyone that would be willing to spend some time itemizing what they have or want.</li>
		<li>Anyone who would prefer to publicly express their wants/needs with their friends and family.</li>
		<li>People that don not want to throw stuff away or have items that they don not need that they would like to find a home for.</li>
		<li>Users that can use the internet.</li>
	</ul>
</div>
<div class="doc_box">
	<h3>Quotes</h3>
	<blockquote>"Strength in numbers."</blockquote>
	<blockquote>"Never underestimate the power of suggestion."</blockquote>
	<blockquote>"What's useless to one person can mean the world to another."</blockquote>
</div>
<div class="doc_box">
	<h3>Goods Scenarios</h3>
	<ol>
		<li>
			Many times people accumulate items that seem to just take up space.<br>
			These assets could be easily shared with one's community...<br>
			If only they knew you had them!!!
		</li>
		<li>
			Many times people think about items that they want.<br>
			Your community might be able to help you get what you want...<br>
			If only they knew what it was!!!
		</li>
		<li>
			Many times examining items you have or use regularly hint at the kind of person you might be or where you interests lie.<br>
			People might realize they have similar interests as you based on what items you had and you could exchange valuable information...<br>
			If only they could examine what items you had and used regularly!!!
		</li>
	</ol>
</div>
<div class="doc_box">
	<h3>Services Scenarios</h3>
	<ol>
		<li>
			Many times people have free time and valuable skills to offer.<br>
			These skills could easily benefit the community...<br>
			If only the community knew what you did!!!
		</li>
		<li>
			Many times people think about what services they would like.<br>
			Your community might be able to help you do what you need to get done...<br>
			If only they knew what you wanted done!!!
		</li>
		<li>
			Many times what services or trades you do hint at the kind of person you might be.<br>
			Your community might have similar trades as you and could give you valuable information on that trade...<br>
			If only they knew what you did!!!
		</li>
	</ol>
</div>
