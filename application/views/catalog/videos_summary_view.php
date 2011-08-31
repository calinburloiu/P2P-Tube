<div class="videos-summary">
	<h1 class="category-title">
		<a href="<?php echo site_url("catalog/category/$category_name") ?>">
			<?php echo $category_title ?>
		</a>
	</h1>

	<?php echo $pagination ?>

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
			<?php echo $this->lang->line('ui_from') . ' TODO' //TODO ?>
		</div>
	</div>
	<?php endforeach ?>

	<?php echo $pagination ?>
	
	<div style="clear: both"></div>

</div>