<div id="body">
<div id="content">

<div class="video-list">
<h1><?php echo $category ?></h1>

<?php echo $pagination ?>

<?php foreach($videos as $video):
	$thumb_src = $video['thumbs'][ $video['default_thumb'] ];
	?>
	<div class="video-icon">
		<div class="video-thumb">
			<a href="<?php echo $video['video_url'] ?>">
				<img src="<?php echo $thumb_src ?>" />
				<div class="video-duration"><?php echo $video['duration'] ?></div>
			</a>
		</div>
		<div class="video-title">
			<a href="<?php echo $video['video_url'] ?>">			
			<?php echo $video['shorted_title'] ?></a>
		</div>		
		<div class="video-views"><?php echo $video['views'] . ' views' ?></div>
		<!--<div class="video-username"><?php echo 'TODO: print user name' ?></div>-->
	</div>
<?php endforeach ?>

<?php echo $pagination ?>

</div>

</div>
</div>
