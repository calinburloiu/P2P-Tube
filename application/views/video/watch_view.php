<div id="body">
	<!-- Invalid name in URL-->
	<?php if (isset($video->err) && $video->err == 'INVALID_NAME'): 
		$suggestion = site_url(sprintf("video/watch/%d/%s", $video->id, 
			$video->name))
		?>
		<p>Invalid URL <em><?php echo current_url() ?></em> .</p>
		<p>Did you mean <a href="<?php echo $suggestion ?>">
			<?php echo $suggestion ?></a> ?</p>
	<!-- Correct URL-->
	<?php else: ?>
		<h1><?php echo $video->title ?></h1>
	<?php endif ?>
</div>