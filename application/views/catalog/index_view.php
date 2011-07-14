<div id="body">
	<?php foreach($videos as $video):
		$img_src = $video['thumbs'][ $video['default_thumb'] ];
		?>
		<div class="video-icon">
			<img src="<?php echo $img_src ?>" />
			<a href="<?php echo $video['video_url'] ?>">
				<div class="video-icon_title"><?php echo $video['title'] ?></div>
			</a>
			<div class="video-icon_duration"><?php echo $video['duration'] ?></div>
			<div class="video-icon_views"><?php echo $video['views'] . ' views' ?></div>
			<!--<div class="video-icon_user"><?php echo 'TODO: print user name' ?></div>-->
			<br />
	<?php endforeach ?>
</div>
