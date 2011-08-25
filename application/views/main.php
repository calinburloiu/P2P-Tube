<?php 
	if (! isset($content))
		$content = '';
	if (! isset($side))
		$side = '';
?>

<div style="clear: both"></div>
<div id="main">
	<div id="content">
		<?php echo $content ?>
	</div>
	<div id="side">
		<?php echo $side ?>
	</div>
	<div style="clear: both"></div>
</div>