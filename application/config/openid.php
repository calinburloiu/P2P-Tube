<?php if (!defined('BASEPATH')) exit('No direct script access allowed');

$config['openid_storepath'] = './tmp';
$config['openid_required'] = array('nickname');
$config['openid_optional'] = array('fullname', 'email');
$config['openid_pape_policies'] = array();

// OBSOLETE
$config['openid_policy'] = 'test/policy';
$config['openid_request_to'] = 'test/check';

?>
