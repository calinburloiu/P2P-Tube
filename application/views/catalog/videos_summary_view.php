<div class="videos-summary">
  <?php if (isset($title) && $title): ?>
	<h1>
		<?php echo $title ?>
	</h1>
  <?php endif ?>
	
	<?php
	if ( isset($ordering))
	{
		$ordering_opts = array(
			'hottest'=> $this->lang->line('ui_show_hottest'),
			'newest'=> $this->lang->line('ui_show_newest'),
			'alphabetically'=> $this->lang->line('ui_sort_alphabetically')
		);
		
		echo '<p>';
		echo form_dropdown('ordering', $ordering_opts, $ordering, 'id="ordering"');
		echo '</p>';
	}
	?>

	<?php echo $pagination ?>

  <?php if (count($videos) === 0): ?>
	<p><?php echo $this->lang->line('user_no_videos_uploaded') ?></p>
  <?php else: ?>
	<?php foreach($videos as $video):
		$thumb_src = $video['thumbs'][ $video['default_thumb'] ];
		?>
	<div class="video-icon">
		<div class="video-thumb ui-widget-content ui-corner-all">
			<a href="<?php echo $video['video_url'] ?>">
				<img src="<?php echo $thumb_src ?>" />
				<div class="video-duration"><?php echo $video['duration'] ?></div>
			</a>
		</div>
		<div class="video-title">
			<a href="<?php echo $video['video_url'] ?>">			
			<?php echo $video['shorted_title'] ?></a>
		</div>		
		<div class="video-views">
			<?php echo $video['views'] . ' '
				. ($video['views'] == 1 ? 
					$this->lang->line('ui_view') : 
					$this->lang->line('ui_views') );
			?>
		</div>
		<div class="video-username">
			<?php echo $this->lang->line('ui_from') ?> <a href="<?php echo site_url("user/profile/{$video['username']}") ?>"><?php echo $video['username'] ?></a>
		</div>
	</div>
	<?php endforeach ?>
  <?php endif ?>

	<?php echo $pagination ?>
	
	<div style="clear: both"></div>

</div>

<?php // TODO change ordering via AJAX ?>
<script type="text/javascript">
	$(function() {
		$('#ordering').change(function(e) {
			var uri = "<?php echo site_url("catalog/category/$category_name") ?>";
			
			// Default ordering
			if ($(this).val() != "hottest")
				uri += "/" + $(this).val();
				
			window.location = uri;
		});
	});

</script>