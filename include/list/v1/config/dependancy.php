<?
# Notes: Separate this later into different dependencies depending on the template if we need to boost performance
# many functions can be loaded later through the corresponding "x file" ie) include/list/v1/x/layer.php
# for now it is easier to keep everything in one place

# CONFIG
$x = array();
require('list/v1/config/preset.php');

# FUNCTION
require('list/v1/function/main.php');
require('list/v1/function/circular_distance.php');
require('list/v1/function/q.php');
require('list/v1/function/key.php');
require('list/v1/function/option.php');
require('list/v1/function/php_database.php');
require('list/v1/function/arrangement.php');
require('list/v1/function/search.php');
require('list/v1/function/custom.php');
require('list/v1/function/link.php');
require('list/v1/function/engine.php');
require('list/v1/function/template.php');
require('list/v1/function/lock.php');
require('list/v1/function/css.php');
require('list/v1/function/file.php');
require('list/v1/function/member.php');
require('list/v1/function/payout.php');

# form processing
require('list/v1/function/process.php');
require('list/v1/function/email.php');

# DB CONNECT
require('list/v1/inline/mysql_connect.php');

# X
require('list/v1/config/x.php');
