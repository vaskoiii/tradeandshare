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

# Contents/Description: process file reads/writes
# Issue:
#  Currently just notes

function filer_read($id) {
	# starting out everything is public
	global $config;
	$sql = '
		SELECT
			id,
			path,
			user_id,
			extension,
			md5,
			byte
		FROM
			' . $config['mysql']['prefix'] . 'filer
		WHERE
			id=' . (int)$_GET['id'] . '
		LIMIT
			1
	';
	$result = mysql_query($sql) or die(mysql_error());
	while ($row = mysql_fetch_assoc($result)) {
		$file1 =
			$config['file_path'] .
			$row['path'] .
			substr($row['md5'], 0, 2) .
			'/' .
			substr($row['md5'], 2, 2) . 
			'/' .
			$row['md5'] .
			'.' .
			$row['extension']
		;
		$data = fread(fopen($file1, 'r'), $row['byte']);
		header('Content-Type: ' . filer_mime($row['extension']));
		header('Content-Length: ' . (int)$row['byte']);
		echo $data;
		exit;
	}
	header("HTTP/1.0 404 Not Found");
	exit;
}

# 0123456789abcdef0123456789abcdef
# >>
# 01/23/0123456789abcdef0123456789abcdef
function md5_path($md5) {
	# placeholder
	# return substr($md5, 0, 2) . '/' . substr($row['md5'], 2, 2) . '/' . $md5;
}

function filer_mime($extension) {
	switch(strtolower($extension)) {
		case 'png':
			return 'image/png';
		break;
		case 'jpg':
			return 'image/jpeg';
		break;
		default :
			return 'invalid/unknown/' . $extension;
		break;
	}
}

# todo write posted file
function filer_write($to) {
	exit; # placeholder
	global $_FILE;
	global $config;
	# not ready until placeholder faces are already setup

	$md5 = md5(fread(fopen($_FILES['file']['tmp_name'], "r"), $_FILES['file']['size']));
	$extension = 'Unknown';
	$a1 = explode('.', $_FILES['file']['name']);
	if (count($a1) > 0) {
		$extension = $a1[count($a1) - 1];
	}
	$s1 = $config['file_path'] . '/' . substr($md5, 0, 2);
	$s2 = $s1 . '/' . substr($md5, 2, 2);
	if (!file_exists($s1))
		mkdir($s1);
	if (!file_exists($s2))
		mkdir($s2);
	$file1 = $s2 . '/' . $md5 . '.' . $extension;
	$b1 = 1;
	if (!file_exists($file1)) {
		$b1 = 2;
		$fp = fopen($file1, 'wb');
		fwrite($fp, fread(fopen($_FILES['file']['tmp_name'], "r"), $_FILES['file']['size']));
		fclose($fp);
	}
}

# Upload
# move to filer from existing file
function filer_move($from, $to, $id = null) {
	# $id = allow updating a specific entry (placeholder) 2014-10-19 vaskoiii
	global $config;

	# $from = full_path
	# $to = path from ~/file  and excluding file name ie) home/v1
	$md5 = md5(fread(fopen($from, "r"), filesize($from)));
	$extension = 'Unknown';

	$a1 = explode('/', $from);
	if (count($a1) > 0) {
		$name = $a1[count($a1) - 1];
		$a2 = explode('.', $name);
		if (count($a2) > 0) {
			$extension = $a2[count($a2) - 1];

			$s1 = $config['file_path'] . $to . substr($md5, 0, 2);
			$s2 = $s1 . '/' . substr($md5, 2, 2);
			if (!file_exists($s1))
				mkdir($s1);
			if (!file_exists($s2))
				mkdir($s2);
			$file1 = $s2 . '/' . $md5 . '.' . $extension;


			$b1 = 1;
			if (!file_exists($file1)) {
				# todo chmod()?
				$b1 = 2;
				$fp = fopen($file1, 'wb');
				fwrite($fp, fread(fopen($from, "r"), filesize($from)));
				fclose($fp);
			}

			# assuming if file exists db entry exist too - decoupling should not be allowed
			if ($b1 == 2) {
				# read/write terminology vs list/edit?
				# todo edit/delete by id
				$sql = 
					($id ? 'update' : 'insert into') . '
						' . $config['mysql']['prefix'] . 'filer
					set
						md5 = ' . to_sql($md5) . ',
						user_id = ' . (int)$_SESSION['login']['login_user_id'] . ',
						path = ' . to_sql($to) . ',
						extension = ' . to_sql($extension) . ',
						`byte` = ' .  (int)filesize($from) . ',
						modified = now(),
						`default` = 1,
						`active` = 1
					' . ($id ? 'where id = ' . (int)$id . 'limit 1' : '')
				;
				$result = mysql_query($sql) or die(mysql_error());
				return 'written';
			}
			else {
				return 'exists';
			} 

		}
	}
	else
		return 'error';
}

# Client
/*
	// placeholder
	<script>
		var file = function(value, options) {
			if (value.match(/(.*)(\.png|\.jpg)$/ig)) // allow only pdf
				return true;
			return false;
		}
	</script>
*/

# Error
if (0) {
	# placeholder
	if (empty($_FILES['file']['size']))
		;
	if ($_FILES['file']['size'] > 999999)
		;
}
