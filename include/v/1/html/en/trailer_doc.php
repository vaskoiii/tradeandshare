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

# Contents/Description: Video trailer made with blender - Assets in ~/asset/

# Video Browser Compatibility:
# http://www.w3schools.com/html/html5_video.asp
# https://en.wikipedia.org/wiki/HTML5_video#Browser_support

up_date('2014-03-22'); ?> 

<style>
	#lisTShareVideo {
		width: 256px;
		height: 192px;
		<?
		// background: url('/v/1/video/ts_give_to_billy_preview.jpg');
		// background-repeat: no-repeat;
		// background-size: 256px 192px;
		?> 
		text-align: center;
		}
</style>

<center>
	<div id="lisTShareVideo">
	
		<video width="256px" height="192px" controls>
			<source src="/v/1/video/list_share.mp4" type="video/mp4"><? # MP4 = H264 video / AAC audio ?> 
			<source src="/v/1/video/list_share.ogg" type="video/ogg"><? # Ogg = Theora video / Vorbis audio ?> 
			<source src="/v/1/video/list_share.webm" type="video/webm"><? # WebM = VP8 video / Vorbis audio ?> 
			Your browser does not support the video tag.
		</video> 

	</div>
	<p>
		<a href="/v/1/video/list_share.mp4">Right Click to Download</a>
	</p>
</center>
