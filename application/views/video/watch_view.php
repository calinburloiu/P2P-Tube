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
		
		<ul>
			<li><a id="a_ns-vlc" href="javascript: void(0)">VLC</a></li>
		
			<li><a id="a_ns-html5" href="javascript: void(0)">HTML5</a></li>
		</ul>
		
		<div id="video_plugin"><?php echo $plugin_content ?></div>
		
		<!--TODO user name-->
		<!--TODO change format controls-->
		<div id="video_date"><?php echo $video['date'] ?></div>
		<div id="video_views">
			<?php echo $video['views'] . ' '
				. ($video['views'] == 1 ? 
					$this->lang->line('ui_view') : 
					$this->lang->line('ui_views') );
			?>
		</div>
		<div id="video_likes">
			<?php echo $video['likes'] . ' '
				. ($video['likes'] == 1 ? 
					$this->lang->line('ui_like') : 
					$this->lang->line('ui_likes') );
			?>
		</div>
		<div id="video_dislikes">
			<?php echo $video['dislikes'] . ' '
				. ($video['dislikes'] == 1 ? 
					$this->lang->line('ui_dislike') : 
					$this->lang->line('ui_dislikes') );
			?>
		</div>
		<div id="video_description"><?php echo $video['description'] ?></div>
		<div id="video_category">
			<?php echo ucwords($this->lang->line('ui_category'))
				. ': '. $video['category_title'] ?>
		</div>
		<div id="video_tags">
			<?php echo ucwords($this->lang->line('ui_tags')). ': ' ?>
			<?php if (isset($video['tags'])): 
			foreach ($video['tags'] as $tag => $score): ?>
			<a href="<?php site_url('catalog/search/'. $tag) ?>">
				<?php echo "$tag($score)" ?>
			</a>
			<?php endforeach; endif ?>
		<div id="video_license">
			<?php echo ucwords($this->lang->line('ui_license'))
				. ': '. $video['license'] ?>
		</div>
		

	<?php endif // if (isset($video['err'])): ?>
</div>

<?php // Javascript bindings when document is ready ?>
<script type="text/javascript">
	$(document).ready(function() {
		$('#a_ns-vlc').click(function() {
			// TODO video definition
			retrieveNsVlcPlugin('<? echo $video['url'][0] ?>');
		});
		
		$('#a_ns-html5').click(function() {
			// TODO video definition
			retrieveNsHtml5Plugin('<?php echo $video['url'][0] ?>')
		});
	});
</script>