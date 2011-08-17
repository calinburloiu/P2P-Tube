<?php //Javascript initializations, globals ?>
<script type="text/javascript">
	siteUrl = '<?php echo site_url() ?>';
</script>

<div id="body">
	<?php // Invalid name in URL ?>
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
		
	<?php // Correct URL ?>
	<?php else: ?>
		<h1><?php echo $video['title'] ?></h1>
		
		<div id="video-widget-tabs">
			<ul>
				<li>
					<a id="switch-to-ns-html5" href="#video-widget">HTML5</a>
				</li>
				<li>
					<a id="switch-to-ns-vlc" href="#video-widget">VLC</a>
				</li>
			</ul>
			
			<div id="video-widget"></div>
		</div>
		
		<!--TODO user name-->
		<div id="video-date"><?php echo $video['date'] ?></div>
		<div id="video-views">
			<?php echo $video['views'] . ' '
				. ($video['views'] == 1 ? 
					$this->lang->line('ui_view') : 
					$this->lang->line('ui_views') );
			?>
		</div>
		<div id="video-likes">
			<?php echo $video['likes'] . ' '
				. ($video['likes'] == 1 ? 
					$this->lang->line('ui_like') : 
					$this->lang->line('ui_likes') );
			?>
		</div>
		<div id="video-dislikes">
			<?php echo $video['dislikes'] . ' '
				. ($video['dislikes'] == 1 ? 
					$this->lang->line('ui_dislike') : 
					$this->lang->line('ui_dislikes') );
			?>
		</div>
		<div id="video-description"><?php echo $video['description'] ?></div>
		<div id="video-category">
			<?php echo ucwords($this->lang->line('ui_category'))
				. ': '. $video['category_title'] ?>
		</div>
		<div id="video-tags">
			<?php echo ucwords($this->lang->line('ui_tags')). ': ' ?>
			<?php if (isset($video['tags'])): 
			foreach ($video['tags'] as $tag => $score): ?>
			<a href="<?php echo site_url('catalog/search/'. $tag) ?>">
				<?php echo "$tag($score)" ?>
			</a>
			<?php endforeach; endif ?>
		<div id="video-license">
			<?php echo ucwords($this->lang->line('ui_license'))
				. ': '. $video['license'] ?>
		</div>
		

	<?php endif // if (isset($video['err'])): ?>
</div>

<?php // Javascript bindings when document is ready ?>
<script type="text/javascript">
	$(function() {
		// TODO remove this 2 bindings
		$('#a_ns-vlc').click(function() {
			//retrieveNsVlcPlugin('<? //echo $video['url'][0] ?>');
		});		
		$('#a_ns-html5').click(function() {
			//retrieveNsHtml5Plugin('<?php //echo $video['url'][0] ?>')
		});
		
		// Switch video plugin facilities
		$('#video-widget-tabs').tabs();
		$('#switch-to-ns-html5')
			.click(function() {
				$('#video-widget')
					.nsvideo('type', 'ns-html5');
			});
		$('#switch-to-ns-vlc')
			.click(function() {
				$('#video-widget')
					.nsvideo('type', 'ns-vlc');
			});
		
		// Video widget
		$('#video-widget')
			.nsvideo({
				type: "<?php echo $plugin_type ?>",
				src: <?php echo json_encode($video['assets']) ?>,
				//width: videoWidth,
				//height: videoHeight
				minWidth: 1200,
				maxWidth: 1400
			})
			.resize(function(e) {
				$('#video-widget-tabs')
					.css('width', $('#video-widget').css('width'));
				console.log($('#video-widget').css('width'));
			});
			
		$('#video-widget-tabs')
					.css('width', $('#video-widget').css('width'));
	});
</script>