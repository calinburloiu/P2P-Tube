<!--Javascript initializations-->
<script type="text/javascript">
	siteUrl = '<?php echo site_url() ?>';
</script>

<div id="body">
	<!-- Invalid name in URL-->
	<?php if (isset($video['err'])):
		if ($video['err'] == 'INVALID_NAME'):
			$suggestion = site_url(sprintf("video/watch/%d/%s", $video['id'], 
				$video['name']))
			?>
			<p>Invalid URL <em><?php echo current_url() ?></em> .</p>
			<p>Did you mean <a href="<?php echo $suggestion ?>">
				<?php echo $suggestion ?></a> ?</p>
		<?php elseif($video['err'] == 'INVALID_ID'): ?>
			<p>Invalid ID in URL.</p>
		<?php endif ?>
		
	<!-- Correct URL-->
	<?php else: ?>
		<h1><?php echo $video['title'] ?></h1>
		
		<ul>
			<li><a href="javascript: void(0)" onclick="retrieveNsVlcPlugin('<? echo $video['torrents'][0] ?>')">VLC</a></li>
		
			<li><a href="javascript: void(0)" onclick="retrieveNsHtml5Plugin('<?php echo 'tribe://'. $video['torrents'][0] ?>')">HTML5</a></li>
		</ul>
		
		<div id="video_plugin"></div>
		<!--TODO preload user preferred plugin-->
		<script type="text/javascript"> retrieveNsHtml5Plugin('<?php echo 'tribe://'. $video['torrents'][0] ?>') </script>
		
		<!--TODO user name-->
		<!--TODO change format controls-->
		<div id="video_date"><?php echo $video['date'] ?></div>
		<div id="video_views"><?php echo $video['views'] ?> views</div>
		<div id="video_likes"><?php echo $video['likes'] ?> likes</div>
		<div id="video_dislikes"><?php echo $video['dislikes'] ?> dislikes</div>
		<div id="video_description"><?php echo $video['description'] ?></div>
		<!-- TODO <div id="video_category">Category: <?php echo $video['category_name'] ?></div>-->
		<div id="video_tags">Tags:
		<?php print_r($video['tags']) ?>
		<?php if (isset($video['tags'])): 
		foreach ($video['tags'] as $tag => $score): ?>
			<a href="<?php site_url('catalog/search/'. $tag) ?>">
			<?php echo "$tag($score)" ?>
			</a>
		<?php endforeach; endif ?>
		<div id="video_license"><?php echo $video['license'] ?></div>
		

	<?php endif // if (isset($video['err'])): ?>
</div>