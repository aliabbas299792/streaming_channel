<?php
include 'include/logged_in_head.php';


$_SESSION['channel_name'] = "Streaming Channel";
$_SESSION['sub_channel_name'] = "room_1";
?>
		<body>
			<div id="stream_container">
				<video id="video" autoplay="" preload="auto">
					<source id="n" src="" type='video/mp4'>
				</video>
			</div>
			<div id="startStream">
				<span>Press here to start the stream</span>
			</div>
			<div id="loadingScreen"></div>
			<script src="scripts/stream_script.js"></script>
		</body>
	</html>