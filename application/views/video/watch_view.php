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
		<script type="text/javascript"> displayNextSharePC("<?php 
		//echo $video['torrents'][0]; 	// VLC
		echo 'tribe://'. $video['torrents'][0];		// HTML5
		?>", true); </script>
	<?php endif ?>
</div>