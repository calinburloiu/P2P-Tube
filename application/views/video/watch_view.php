<?php //Javascript initializations, globals ?>
<script type="text/javascript">
	siteUrl = '<?php echo site_url() ?>';
</script>

<div id="main">
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
	<h1><a href="<?php echo site_url('catalog/category/'. $video['category_name']) ?>"><?php echo $video['category_title'] ?></a> &rsaquo; <?php echo $video['title'] ?></h1>
	
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
	
	<div style="clear: both"></div>
	
	<div id="video-footer">
		<div id="video-info" style="clear: both">
			<div id="video-upload-info">
				<?php echo $this->lang->line('ui_uploaded_by') ?>
					<span id="video-user"><a href="<?php echo site_url("user/profile/{$video['username']}") ?>"><?php echo $video['username'] ?></a></span>
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
				
				<div><a class="link-vote" data-action="like" href="#"><?php echo $this->lang->line('video_like') ?></a>
					<a class="link-vote" data-action="dislike" href="#"><?php echo $this->lang->line('video_dislike') ?></a>
					<span id="video-likes"><?php echo $video['likes'] ?></span> <?php
						echo $this->lang->line('ui_likes') ?>,
					<span id="video-dislikes"><?php echo $video['dislikes'] ?></span> <?php
						echo $this->lang->line('ui_dislikes'); ?>
				</div>
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
			
			<dl id="video-torrents">
				<dt><?php echo $this->lang->line('ui_download_torrents') ?>: </dt>
			  <?php foreach ($video['assets'] as $asset): ?>
				<dd><a href="<?php echo $asset['src'] ?>"><?php echo $asset['def'] ?></a></dd>
			  <?php endforeach ?>
			</dl>
			
			<dl id="video-license">
				<dt><?php echo ucwords($this->lang->line('ui_license')).': ' ?></dt>
				<dd><?php echo $video['license'] ?></dd>
			</dl>
		</div>
	
		<div id="video-comments"><?php echo $comments ?></div>
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
		$('#video-widget-tabs')
			.tabs();
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

				showState: false,
				
				minWidth: 640,
				maxWidth: 1024,
				initialDuration: "<?php echo $video['duration'] ?>",
				
				resize: function() {
					$('#video-widget-tabs')
						.css('width', $('#video-widget').css('width'));
				}
			});
		
		$('.link-vote')
			.click(function(event) {
				var user_id = "<?php echo $user_id ?>";
				var action, idOutput;
				if ($(this).data('action') == 'like')
				{
					var action = 'like';
					var idOutput = '#video-likes';
				}
				else
				{
					var action = 'dislike';
					var idOutput = '#video-dislikes';
				}
				//alert(action + " " + user_id);
				
				event.preventDefault();
				
				if (user_id.length != 0)
				{
					$.ajax({
						type: "GET",
						url: "<?php echo site_url("video/ajax_vote") ?>/"
							+ action
							+ "<?php echo "/{$video['id']}" ?>",
						data: {t: ""+Math.random()},
						dataType: "text",
						success: function(text) {
							if (text)
								$(idOutput).html(text);
							else
								alert('<?php echo $this->lang->line('ui_msg_repeated_action_restriction') ?>');
						}
					});
				}
				else
					alert('<?php echo $this->lang->line('ui_msg_login_restriction') ?>');
			})
			.button();
		
		$('#link-like')
			.click(function() {
				user_id = "<?php echo $user_id ?>";
				
				if (user_id)
				{
					$.ajax({
						type: "GET",
						url: "<?php echo site_url("video/ajax_vote/like/{$video['id']}") ?>",
						dataType: "text",
						success: function(text) {
							if (text)
								$('#video-likes').html(text);
							else
								alert('<?php echo $this->lang->line('ui_msg_repeated_action_restriction') ?>');
						}
					});
				}
				else
					alert('<?php echo $this->lang->line('ui_msg_login_restriction') ?>');
			})
			.button();
		$('#link-dislike')
			.click(function() {
				user_id = "<?php echo $user_id ?>";
				
				if (user_id)
				{
					$.ajax({
						type: "GET",
						url: "<?php echo site_url("video/ajax_vote/dislike/{$video['id']}/$user_id") ?>",
						data: {t: ""+Math.random()},
						dataType: "text",
						success: function(text) {
							$('#video-dislikes').html(text);
						}
					});
				}
				else
					alert('<?php echo $this->lang->line('ui_msg_login_restriction') ?>');
			})
			.button();
	});
</script>