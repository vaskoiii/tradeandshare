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
?> 
body {
	word-wrap: break-word; /* why is this NOT default! */
	background: transparent url("/<?= $x['site']['p']; ?>theme/<?= str_replace('theme_', '', $_SESSION['theme']['background_theme_name']); ?>/background.jpg") repeat;
	margin: 0px auto;
	padding: 0px;
	<?= $data['css']['body_width_percentage']; ?>
	<?= $data['css']['max_width']; ?>
	}
/*show style in vim hack <style>*/
.keyword_part {
	line-height: 35px;
	}
#ts_focus {
	/* color: #555555; */
	}
h1 {
	display: inline;
	font-size: 22pt;
	}
h3 {
	text-align: left;
	font-size: 14pt;
	margin: 0px;
	padding-bottom: 10px;
	}
h4 {
	text-align: left;
	font-size: 14pt;
	margin: 5px 0px 5px 0px;
	}
p {
	margin: 15px 0px;
	padding: 0px;
	}
hr {
	margin: 0px;
	padding: 0px;
	}
h2 {
	display: inline;
	text-align: left;
	font-size: 18pt;
	margin: 0px 0px 0px 13px;
	}
dd {
	margin-bottom: 5px;
	}
form {
	margin: 0px;
	padding: 0px;
	}
img {
	border: none;
	padding: 0px;
	margin: 0px;
	}
a:link {
	color: #000000;
	text-decoration: underline;
	}
a:visited {
	color: #000000;
	text-decoration: underline;
	}
a:hover {
	color: #000000;
	text-decoration: none
	}
textarea {
	padding: 0px;
	margin: 0px;
	resize: none; 
	overflow: hidden; 
	border: 2px inset #ffffff; 
	height: 55px;
	}
.textarea {
	height: 40px;
}
div {
	padding: 0px;
	margin: 0px;
	}
.more_solo {
	margin-left: 20px;
	}
#mediaspace {
	margin: 10px 0px; text-align: center;
	}
#main_intro {
	margin: 0px;
	margin-top: 10px;
	padding: 0px;
	margin-bottom: 25px;
	}
#main_add {
	margin: 0px
	padding: 0px;
	margin-top: 10px;
	margin-bottom: 15px
	}
.description_input {
	<?= $data['css']['description_width']; ?>
	}
#quick_link {
	margin-top: 15px;
	}
.title .result_add {
	display: inline;
	margin: 5px 10px 0px 10px;
	}
/* main search */
.main_keyword_box {
	margin-bottom: 20px;
}
.keyword_box {
	margin-top: 5px;
	margin-right: 15px;
	margin-bottom: 0px;
	margin-left: 15px;
	padding: 0px;
	display: inline-table;
	text-align: left;
	vertical-align: top;
	}
.lock_box,
.lock_box,
.nonlock_box,
.soption_box {
	display: inline-table;
	margin: 10px;
	margin-bottom: 0px;
	padding: 0px;
	text-align: left;
	vertical-align: top;
	}
/* override for main page */
#main_only {
	margin: 5px 10px 5px 10px;
	font-size: 11pt;
	}
#main_only a {
	color: #373737;
	}
#main_search .nonlock_box,
#main_search .lock_box,
#main_search .soption_box{
	display: inline-table;
	text-align: left;
	}
#main_search .soption_box{
	display: inline-table;
	}

.doc_box {
	margin: 0px;
	padding: 10px;
	}
.doc_box p, 
.doc_box dl {
	margin: 10px;
	padding: 0px;
	}
.doc_box ul {
	margin: 10px 25px;
	padding: 0px;
	}
.k {
	margin: 0px;
	padding: 0px;
	font-size: .95em;
	margin-left: 20px;
	}
.v {
	margin: 0px;
	padding: 0px;
	margin-left: 40px;
	margin-bottom: 5px;
	}
.new_box {
	float: left;
	width: 200px;
	margin: 0px;
	margin: 10px;
	padding: 0px;
	margin-top: -10px;
	}
.delete_table {
	margin: 0px auto;
	padding: 10px;
	}
.title {
	background: <?= $data['css']['bg1']; ?>;
	border: 1px solid #ffffff;
	padding: 5px 0px 5px 0px;
	}
#view_list,
#motion_content_1,
#action_content_1 {
	margin-top: 7px;
}
.content {
	<?= $data['css']['max_width_minus_2']; ?>
	margin: 0px auto;
	border-left: 1px solid #ffffff;
	border-right: 1px solid #ffffff;
	border-top: 0px;
	border-bottom: 0px;
	background: <?= $data['css']['bg4']; ?>;
	display: block;
	padding: 0px;
	padding-top: 10px;
	}
.view_content_box {
	margin-top: -15px;
	border: 1px solid red;
	}
.content_box {
	margin-top: 0px;
	padding: 0px 18px;
	text-align: left;;
	}
.splash_box {
	margin: 0px auto;
	padding: 0px 0px 0px 0px;
	text-align: center;
	}
.lr_border {
	border-left: 1px solid #ffffff;
	border-right: 1px solid #ffffff;
	}
.menu_1 {
	margin: 0px;
	margin-top: 20px;
	padding: 0px;
	background: transparent;
	}
.menu_1 ul {
	margin: 5px 15px 5px 15px;
	padding: 0px;
	}
.menu_1 input {
	margin: 2px;
	}
.menu_1 li {
	background: <?= $data['css']['bg2']; ?>;
	display: inline;
	border: 1px solid #ffffff;
	margin: 2px;
	padding: 3px 10px 3px 10px;
	line-height: 31px;
	}
.menu_2 {
	background: <?= $data['css']['bg3']; ?>;
	border-top: 1px solid #ffffff;
	border-bottom: 1px solid #ffffff;
	margin: 0px 0px 15px 0px;
	padding:5px;
	
	}
.menu_2 ul {
	margin: 0px 10px;
	padding: 0px;
	}
.menu_2 li {
	<?= $data['css']['max_element_width']; ?>
	background: <?= $data['css']['bg2']; ?>;
	display: inline-block;
	border: 1px solid #ffffff;
	margin: 2px;
	padding: 3px 10px 3px 10px;
	line-height: 25px;
	}
.message {
	border: 1px solid #ffffff; 
	border-top: 0px;
	}
.failure, .notice, .success {
	padding: .2em .8em; 
	margin: 0px; 
	border: 2px solid #ddd;
	}
	.failure    { background: #FBE3E4; color: #8a1f11; border-color: #FBC2C4; }
	.notice     { background: #FFF6BF; color: #514721; border-color: #FFD324; }
	.success    { background: #E6EFC2; color: #264409; border-color: #C6D880; }
	/* also has parts set in text_style but ONLY for the lofi version of the site! */
#header {
	<?= $data['css']['max_width']; ?>
	margin: 0px auto;
	margin-bottom: 15px;
	padding: 0px;
	}
#lock {
	margin: 0px;
	padding: 0px;
	width: 44px;
	height: 44px;
	}
#website_name {
	display: block;
	margin: 0px 0px 0px 0px;
	padding: 0px;
	padding-bottom: 0px;
	text-align: center;
	}
#topper {
	text-align: left;
	margin: 3px 10px 3px 10px;
	padding: 0px;
	font-size: 11pt;
	}
#topper a {
	color: #373737;
	}
#topper_mini {
	margin: 0px;
	padding: 0px;
	text-align: left;
	<?= $data['css']['max_width']; ?>
	}
#nav {
	<?= $data['css']['max_width']; ?>
	margin: 0px auto;
	padding: 0px;
	}
#search {
	<?= $data['css']['max_width']; ?>
	margin: 0px auto;
	padding: 0px;
	}
#result {
	<?= $data['css']['max_width']; ?>
	margin: 0px auto;
	padding: 0px;
	}
#footer {
	<?= $data['css']['max_width']; ?>
	background: <?= $data['css']['bg4']; ?>;
	margin: 0px auto;
	padding: 0px;
	text-align: center;
	}
#footer p {
	margin: 0px;
	padding: 0px;
	}
#footer .top_foot {
	margin-top: 0px;
	}
#footer .middle_foot {
	margin-top: 7px;
	}
#footer .bottom_foot {
	font-size: small;
	margin-top: 7px;
	margin-bottom: -10px
	}
#header_mini {
	<?= $data['css']['max_width']; ?>
	margin: 0px auto;
	padding: 0px;
	text-align: center;
	}
#flashcontent {
	text-align: center;
	}
#cal {
	height: 150px;
	position: absolute;
	width: 160px;
	}
#debug a {
	margin-left: 10px;
	}
#debug h3 {
	display: inline;
	}
.debug_variable {
	margin-left: 10px;
	padding-bottom: 20px;
	}
.description_input_row {
	height: 47px;
	}
