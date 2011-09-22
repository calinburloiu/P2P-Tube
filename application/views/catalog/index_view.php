<?php foreach ($videos_summaries as $videos_summary): ?>
<h1 class="category-title">
	<a href="<?php echo site_url("catalog/category/{$videos_summary['category_name']}") ?>">
		<?php echo $videos_summary['category_title'] ?>
	</a>
</h1>
<?php echo $videos_summary['content']; ?>
<?php endforeach ?>