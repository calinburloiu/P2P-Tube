<div id="body">
<div id="content">

<?php foreach ($categories as $category_id => $category_name): ?>
<div class="video-list">
<h1>
	<a href="<?php echo site_url("catalog/category/$category_id") ?>">
		<?php echo $category_name ?>
	</a>
</h1>

<?php foreach($videos[$category_id] as $video):
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

<div style="clear: both"></div>
</div>
<?php endforeach ?>

</div>
</div>
