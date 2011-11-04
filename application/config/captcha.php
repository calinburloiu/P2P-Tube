<?php  if ( ! defined('BASEPATH')) exit('No direct script access allowed');

$config['captcha_params'] = array(
	'expiration' => 900,
	'img_path' => './img/captcha/',
	'img_url' => site_url('img/captcha/') . '/'
);