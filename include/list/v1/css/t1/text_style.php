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

# Contents/Description: print the css defined in the engine

# possibly broke colorful emails when eliminating $data['css']['text_style']['all'] 2012-02-16 vaskoiii

# for vim css highlighting use:
# :set filetype=css
# the previous style hack messes up firebug
?>

<?= $data['css']['text_style']['message_box']; ?>

a:link {
	color: <?= $data['css']['text_style']['color']['link']; ?>;
	text-decoration: underline;
	}
a:visited {
	color: <?= $data['css']['text_style']['color']['link'] ; ?>;
	text-decoration: underline;
	}
a:hover {
	color: <?= $data['css']['text_style']['color']['link'] ; ?>;
	text-decoration: none
	}
#ts_helper_text {
	margin: 0px 0px -10px 0px;
	color: #333333;
	}
input, select, textarea {
	background: #fefefe;
	color: #010101;
}
.spacer {
	color: <?= $data['css']['text_style']['color']['spacer']; ?>;
	}
/* not really element */
.access_public_web {
	color: #cc0000;
	}
.access_user_all {
	color: #ee7700;
	}
.access_team_intra {
	color: #aaaa00;
	}
.access_user_inter {
	color: #330099;
	}
.access_user_author {
	color: #006600;
	}
.list_seen {
	color: #777777;
	}
.list_unseen {
	color: #777777;
	}
.list_new {
	}
.tag_translation_name, .tag_name, .feed_name, .thing_name, .news_name, .element_name, .translation_kind_name, .minder_kind_name, .kind_name {
	color: <?= $data['css']['text_style']['color']['thing_name']; ?>;
	}
.tag_path, .status_name, .grade_name, .phase_name, .right_name, .meritype_name, .kind_name_name {
	color: <?= $data['css']['text_style']['color']['status_name']; ?>;
	}
.login_user_name, .user_name, .source_user_name, .destination_user_name, .lock_user_name {
	color: <?= $data['css']['text_style']['color']['user_name']; ?>;
	}
.contact_user_mixed, .lock_contact_user_mixed {
	color: #cb5700;
	}
.contact_name, .lock_contact_name {
	color: <?= $data['css']['text_style']['color']['contact_name']; ?>;
	}
.tag_description {
	color: #585858;
	}
.modified {
	color: #445f44;
	}
.edit {
	color: #7f5d1b;
	}
.parent_tag_path, .parent_tag_name {
	color: #418783;
	}
.dialect_name {
	color: #cd3fd2;
	}
.parent_tag_name, .parent_tag_translation_name {
	color: #9201c2;
	}
.incident_name, .meritopic_name, .offer_name {
	color: <?= $data['css']['text_style']['color']['offer_name']; ?>;
	}
/* All Unique IDs */
.uid {
	color: #778866;
	}
/* parent_ids */
.incident_id, .meritopic_id {
	color: #999221;
	}
.group_name, .lock_group_name, .page_name {
	color: #5b0ee8;
	}
.team_name, .lock_team_name, .team_required_name {
	color: <?= $data['css']['text_style']['color']['team_name']; ?>;
	}
.location_name, .lock_location_name {
	color: #b22f69;
	}
.lock_range_name {
	color: #b7b527;
	}
.direction_name, .delete {
	color: <?= $data['css']['text_style']['color']['direction_name']; ?>;
	}
.like {
	color: #00ff00;
	}
.dislike {
	color: #ff0000;
	}
.keyword, .description, .incident_description, .feedback_description, .offer_description, .transfer_description, .note_description, .group_description, .team_description {
	color: #383838;
	}
.translation_name {
	color: #cc9900;
	}
/* LESS critical colors */
.extra {
	color: #aa9988;
	}
.remember_login {
	color: #00cc99;
	}
.location_latitude, .location_longitude {
	color: #11aa44;
	}
.translation_default_boolean_name, .known, .enabled, .default_boolean_name {
	/* color: #336666; */
	color: #669966;
	}
.login_user_password_unencrypted, .user_password_unencrypted, .user_password_unencrypted_again {
	color: #3300ff;
	}
.user_email {
	color: #cc9900;
	}
.invite_user_name {
	color: #996633;
	}
.translate, invite_password {
	color: #990066;
	}
.accept_usage_policy {
	color: #009900;
	}
.notify_offer_received, .notify_teammate_received {
	color: #9900cc;
	}
.feature_lock, .feature_minnotify {
	color: #cc6600;
	}
.display_name {
	color: #dd3333;	
	}
.background_theme_name, .launcher_theme_name, .theme_name {
	color: #33aa33;
	}
.load_javascript {
	color: #3333aa;
	}
.export {
	color: #6633cc;
	}
.import {
	color: #cc0099;
	}
.judge {
	color: #CC3300;
	}
