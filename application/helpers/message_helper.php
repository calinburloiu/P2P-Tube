<?php  if ( ! defined('BASEPATH')) exit('No direct script access allowed');

/**
 * Called from a controller to show a message page.
 * 
 * @param type $controller controller's object; pass $this from controller
 * @param type $type message type: 'info', 'error'
 * @param type $msg message text
 */
function show_msg_page($controller, $type, $msg)
{
	$params = array(
		'title'=> $controller->lang->line("message_title_{$type}")
			.' &ndash; '
			. $controller->config->item('site_name'),
		//'metas' => array('description'=>'')
	);
	$controller->load->library('html_head_params', $params);

	// **
	// ** LOADING VIEWS
	// **
	$controller->load->view('html_begin', $controller->html_head_params);
	$controller->load->view('header', array());

	$main_params['content'] =
		$controller->load->view("message/{$type}_view",
			array('msg'=> $msg), TRUE);

	$main_params['side'] = $controller->load->view('side_default', NULL, TRUE);
	$controller->load->view('main', $main_params);

	$controller->load->view('footer');
	$controller->load->view('html_end');
}

/**
 * Called from a controller to show an error message page.
 * 
 * @param type $controller controller's object; pass $this from controller
 * @param type $msg message text
 */
function show_error_msg_page($controller, $msg)
{
	show_msg_page($controller, 'error', $msg);
}

/**
 * Called from a controller to show an info message page.
 * 
 * @param type $controller controller's object; pass $this from controller
 * @param type $msg message text
 */
function show_info_msg_page($controller, $msg)
{
	show_msg_page($controller, 'info', $msg);
}

/* End of file message_helper.php */
/* Location: ./application/helpers/message_helper.php */