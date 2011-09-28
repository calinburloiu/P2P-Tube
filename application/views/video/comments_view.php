<div id="video-comments">
	<h4><?php echo $this->lang->line('video_title_comment') ?>: </h4>

	<?php echo form_open("video/comment/$video_id") ?>
		<textarea name="comment" id="comment" rows="2" cols="56"></textarea>
		
		<div><input type="button" id="button-post" value="<?php echo $this->lang->line('video_submit_post_comment') ?>" /></div>
	</form>

  <?php if ($comments_count == 0): ?>
	<h4><?php echo $this->lang->line('video_title_no_comments') ?></h4>
  <?php else: ?>
	<h4><?php echo $this->lang->line('video_title_all_comments'). " ($comments_count): " ?></h4>

	<?php foreach ($comments as $comment): ?>
		<div class="comment-info"><span class="comment-user"><a href="<?php echo site_url("user/profile/{$comment['username']}") ?>"><?php echo $comment['username'] ?></a></span>
			(<span class="comment-time"><?php echo $comment['time'] ?></span>)
		</div>
		<div class="comment-content"><?php echo $comment['content'] ?></div>
	<?php endforeach ?>
  <?php endif ?>
</div>

<script type="text/javascript">
	$(function() {
		$('#button-post')
			.click(function() {
				$.post('<?php echo site_url("video/ajax_comment/$video_id") ?>',
					{comment: $('#comment').val(), 'video-id': <?php echo $video_id ?>},
					function(data) {
						$('#video-comments').html(data);
					});
			});
	});

</script>