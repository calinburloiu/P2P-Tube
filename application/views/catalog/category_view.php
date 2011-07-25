<div id="body">
<div id="content">

<h1><?php echo $category ?></h1>

<div class="video-list">
<?php foreach($videos as $video):
	$thumb_src = $video['thumbs'][ $video['default_thumb'] ];
	?>
	<div class="video-icon">
		<a href="<?php echo $video['video_url'] ?>">
			<div class="video-thumb">
				<img src="<?php echo $thumb_src ?>" />
				<div class="video-duration"><?php echo $video['duration'] ?></div>
			</div>
		</a>
		<div class="video-title">
			<a href="<?php echo $video['video_url'] ?>">			
			<?php echo $video['shorted_title'] ?></a>
		</div>		
		<div class="video-views"><?php echo $video['views'] . ' views' ?></div>
		<!--<div class="video-username"><?php echo 'TODO: print user name' ?></div>-->
	</div>
<?php endforeach ?>
</div>

</div>
</div>
