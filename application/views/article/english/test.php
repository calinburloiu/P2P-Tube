<?php
$src[] = site_url('data/thumbs/IndependentaRomaniei_t00.jpg');
$src[] = site_url('data/thumbs/IndependentaRomaniei_t01.jpg');
$src[] = site_url('data/thumbs/IndependentaRomaniei_t02.jpg');
$src[] = site_url('data/thumbs/IndependentaRomaniei_t03.jpg');

$json_src = json_encode($src);
?>

<img id="d" src="<?php echo site_url('data/thumbs/IndependentaRomaniei_t02.jpg') ?>"
	data-src='<?php echo $json_src ?>'>tra-la-la</img>

<script type="text/javascript">
	$('#d').thumbs({
		
	});
</script>