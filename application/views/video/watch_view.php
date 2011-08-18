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
		
		<div id="video-info" style="clear: both">
		<div id="video-upload-info">
			<?php echo $this->lang->line('ui_uploaded_by') ?>
				<span id="video-date"><?php echo $video['user_name'] ?></span>
				<?php echo $this->lang->line('ui_on_date') ?>
				<span id="video-date"><?php echo $video['date'] ?></span>
		</div>
		
		<div id="video-popularity">
			<div id="video-views">
				<?php echo $video['views'] . ' '
					. ($video['views'] == 1 ? 
						$this->lang->line('ui_view') : 
						$this->lang->line('ui_views') );
				?>
			</div>
			
			<div><span id="video-likes">
				<?php echo $video['likes'] . ' '
					. ($video['likes'] == 1 ? 
						$this->lang->line('ui_like') : 
						$this->lang->line('ui_likes') );
				?></span>,
			<span id="video-dislikes">
				<?php echo $video['dislikes'] . ' '
					. ($video['dislikes'] == 1 ? 
						$this->lang->line('ui_dislike') : 
						$this->lang->line('ui_dislikes') );
				?>
			</span></div>
		</div>
		
		<div id="video-description"><?php echo $video['description'] ?></div>
		
		<dl id="video-category">
			<dt><?php echo ucwords($this->lang->line('ui_category'))
				. ': ' ?></dt>
			<dd><?php echo $video['category_title'] ?></dd>
		</dl>
		
		<dl id="video-tags">
			<dt><?php echo ucwords($this->lang->line('ui_tags')). ': ' ?></dt>
			<dd><?php if (isset($video['tags'])): 
			foreach ($video['tags'] as $tag => $score): ?>
			<a href="<?php echo site_url('catalog/search/'. $tag) ?>" class="video-tag">
				<?php echo "$tag " // TODO print score in future ?>
			</a>
			<?php endforeach; endif ?></dd>
		</dl>
		
		<dl id="video-license">
			<dt><?php echo ucwords($this->lang->line('ui_license')).': ' ?></dt>
			<dd><?php echo $video['license'] ?></dd>
		</dl>
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
				minWidth: 640,
				maxWidth: 1024,
				initialDuration: "<?php echo $video['duration'] ?>",
				
				resize: function() {
					$('#video-widget-tabs')
						.css('width', $('#video-widget').css('width'));
				}
			});
	});
</script>