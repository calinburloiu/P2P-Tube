<h4><?php echo $this->lang->line('video_title_comment') ?>: </h4>

<?php echo form_open("video/comment/$video_id") ?>
	<textarea name="comment" id="comment" rows="4" cols="56"><?php 
		if (validation_errors()):
			echo set_value('comment', '');
		endif;
	?></textarea>
	
	<div><input type="button" id="button-post" value="<?php echo $this->lang->line('video_submit_post_comment') ?>" />
		<span id="comment-chars-left">512</span> <?php echo $this->lang->line('ui_chars_left') ?>
	</div>
	<div><?php echo form_error('comment') ?></div>
</form>

<?php if ($comments_count == 0): ?>

<h4><?php echo $this->lang->line('video_title_no_comments') ?></h4>

<?php else: ?>

  <?php if ($hottest_comments): ?>
	<h4><?php echo $this->lang->line('video_title_hottest_comments'). ": " ?></h4>

	<?php foreach ($hottest_comments as $hottest_comment): ?>
		<div class="comment-info"><strong class="comment-user"><a href="<?php echo site_url("user/profile/{$hottest_comment['username']}") ?>"><?php echo $hottest_comment['username'] ?></a></strong>
			(<span class="comment-time"><?php echo $hottest_comment['local_time'] ?></span>)
		</div>
		
		<div class="comment-content"><?php echo $hottest_comment['content'] ?></div>
		
		<div class="comment-popularity"><a class="link-vote-video-comment" data-action="like" data-commentid="<?php echo $hottest_comment['id'] ?>" href="#"><?php echo $this->lang->line('video_like') ?></a>
			&nbsp;<a class="link-vote-video-comment" data-action="dislike" data-commentid="<?php echo $hottest_comment['id'] ?>" href="#"><?php echo $this->lang->line('video_dislike') ?></a>
			&nbsp;&nbsp;&nbsp;&nbsp;<span id="<?php echo "video-comment-{$hottest_comment['id']}-likes" ?>"><?php echo $hottest_comment['likes'] ?></span> <?php
				echo $this->lang->line('ui_likes') ?>,
			<span id="<?php echo "video-comment-{$hottest_comment['id']}-dislikes" ?>"><?php echo $hottest_comment['dislikes'] ?></span> <?php
				echo $this->lang->line('ui_dislikes'); ?>
		</div>
	<?php endforeach ?>
  <?php endif ?>

<h4><?php echo $this->lang->line('video_title_all_comments'). " ($comments_count): " ?></h4>

<?php foreach ($comments as $comment): ?>
	<div class="comment-info"><strong class="comment-user"><a href="<?php echo site_url("user/profile/{$comment['username']}") ?>"><?php echo $comment['username'] ?></a></strong>
		(<span class="comment-time"><?php echo $comment['local_time'] ?></span>)
	</div>
	
	<div class="comment-content"><?php echo $comment['content'] ?></div>
	
	<div class="comment-popularity"><a class="link-vote-video-comment" data-action="like" data-commentid="<?php echo $comment['id'] ?>" href="#"><?php echo $this->lang->line('video_like') ?></a>
		&nbsp;<a class="link-vote-video-comment" data-action="dislike" data-commentid="<?php echo $comment['id'] ?>" href="#"><?php echo $this->lang->line('video_dislike') ?></a>
		&nbsp;&nbsp;&nbsp;&nbsp;<span id="<?php echo "video-comment-{$comment['id']}-likes" ?>"><?php echo $comment['likes'] ?></span> <?php
			echo $this->lang->line('ui_likes') ?>,
		<span id="<?php echo "video-comment-{$comment['id']}-dislikes" ?>"><?php echo $comment['dislikes'] ?></span> <?php
			echo $this->lang->line('ui_dislikes'); ?>
	</div>
<?php endforeach ?>

<?php echo $comments_pagination ?>
<?php endif ?>

<script type="text/javascript">
	$(function() {
		$('#button-post')
			.click(function() {
				$.post('<?php echo site_url("video/ajax_comment/$video_id") ?>',
					{comment: $('#comment').val()},
					function(data) {
						$('#video-comments').html(data);
					});
			});
		
		$('.pagination')
			.ajaxLinksMaker({
				linkSelectors: [
					'.pg-first',
					'.pg-prev',
					'.pg-next',
					'.pg-last',
					'.pg-num'
				],
				target: '#video-comments'
			});
			
		$('.link-vote-video-comment')
			.click(function(event) {
				var user_id = "<?php echo $user_id ?>";
				var action, idOutput, commentId;
				commentId = $(this).data('commentid');
				if ($(this).data('action') == 'like')
				{
					var action = 'like';
					var idOutput = '#video-comment-' + commentId + '-likes';
				}
				else
				{
					var action = 'dislike';
					var idOutput = '#video-comment-' + commentId + '-dislikes';
				}
				//alert(action + " " + user_id);
				
				event.preventDefault();
				
				if (user_id.length != 0)
				{
					$.ajax({
						type: "GET",
						url: "<?php echo site_url("video/ajax_vote_comment") ?>/"
							+ action
							+ "/" + commentId,
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
			});
		
		$('#comment')
			.bind('keyup paste drop change', function(event) {
				$textarea = $(this);
				
				if ($textarea.val().length >= 513)
					$textarea.val($textarea.val().substring(0, 512));

				$('#comment-chars-left').html('' + (512 - $textarea.val().length));
			});
	});
</script>