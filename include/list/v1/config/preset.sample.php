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

# Contents/Description: global configuration settings! (for all of List!)
$config = array();

# Change Every Upload
date_default_timezone_set('UTC');

$config['hostfee_user_id'] = 111; # receives the % of fees for hosting
$config['hostfee_percent'] = 0; # percent of membership fees that go to you!
$config['version'] = 'YYYY-MM-DD | Version Name'; 
$config['debug'] = 1;
$config['write_protect'] = 2; # always 2 (set in individual scripts to 1 if needed)
$config['email_from'] = 'List <noreply@localhost>';
$config['file_path'] = '/www/site/list/file/'; # end with /
$config['public_path'] = '/www/site/list/public/'; # end with /
$config['include_path'] = '/www/site/list/include/'; # end with /

# Main
$config['start_page'] = '/main/';
$config['website_name'] = 'Local List';
$config['title_prefix'] = 'List '; # Used on Email & Feeds & Page Title

# Database Connect
$config['mysql'] = array(
	'host' => 'localhost',
	'user' => 'list',
	'password' => '',
	'database' => 'list',
	'prefix' => 'list_'
);
$config['mysql_resource']; # gets set from Database Connect

# Important IDs
# todo make default setup with all important ids = 1
$config['server_id'] = 1;
$config['everyone_team_id'] = 103; # <|*|>
$config['root_tag_id'] = 1; # <|!|> 
$config['main_location_id'] = 1; # <|?|>
$config['autocreation_user_id'] = '111'; # |root|
# drop < and > from user_name as as every user_name is reserved

$config['max_tag_depth'] = 5; # safe recursive limit on how deep tags can go 2012-02-23 vaskoiii

# command line utilities
$config['utility_convert'] = '/usr/local/bin/convert'; # part of imagemagick

# Important Dividers 
# /faq_doc/#General_Notation
# http://en.wikipedia.org/wiki/Email_address#Syntax

# Suggested Characters to use ! # $ % & ' * + - / = ? ^ _ ` { | } ~
$config['system_prefix'] = '|';
$config['system_suffix'] = '|';
# No known character limitations
$config['reserved_prefix'] = '<';
$config['reserved_suffix'] = '>';
# Any Character Except < > as Feed Readers Have Poor Support on Author Name
$config['unabstracted_prefix'] = '(';
$config['unabstracted_suffix'] = ')';

$config['spacer'] = ' - '; # Used on output display to separate elements 
$config['mark'] = '~'; # Special charter used for parsing of URL variable names (choose from: ~-ABCDEFGHIJKLMNOPQRSTUVWXYZ)
$config['category_exploder'] = '<>'; # NEEDED! (dont comment out) # todo change to "path_exploder"

# Template 1
$config['t1/']['result_amount_per_page'] = 10;
