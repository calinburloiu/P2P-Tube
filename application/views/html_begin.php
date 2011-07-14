<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.1//EN"
"http://www.w3.org/TR/xhtml11/DTD/xhtml11.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">

<head>

<meta http-equiv="Content-Type" content="text/html;charset=UTF-8" />

<title><?php echo $title ?></title>

<?php foreach ($stylesheets as $stylesheet): ?>
<link rel="stylesheet" type="text/css" href="<?php echo $stylesheet ?>" />
<?php endforeach ?>

<?php foreach ($javascripts as $javascript): ?>
<script type="text/javascript" src="<?php echo $javascript ?>"></script>
<?php endforeach ?>

<?php foreach ($metas as $meta_name => $meta_content): ?>
<meta name="<?php echo $meta_name ?>" content="<?php echo $meta_content ?>" /> 
<?php endforeach ?>

</head>

<body>
