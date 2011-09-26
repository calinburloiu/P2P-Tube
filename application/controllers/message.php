<?php

/**
 * Class Message displays messages (info, error).
 * 
 * Messages are captured from 'msg' flash session variable.
 *
 * @category	Controller
 * @author		Călin-Andrei Burloiu
 */
class Message extends CI_Controller {
	
	private $msg;
	
	public function __construct()
	{
		parent::__construct();
		
		$this->msg = $this->session->flashdata('msg');
	}
	
	public function _remap($method, $params = array())
	{	
		if (! $this->msg)
			header('Location: '. site_url());

		$params = array(
			'title'=> $this->lang->line("message_title_{$method}")
				.' &ndash; '
				. $this->config->item('site_name'),
			//'metas' => array('description'=>'')
		);
		$this->load->library('html_head_params', $params);
		
		// **
		// ** LOADING VIEWS
		// **
		$this->load->view('html_begin', $this->html_head_params);
		$this->load->view('header', array());
		
		$main_params['content'] =
			$this->load->view("message/{$method}_view",
				array('msg'=> $this->msg), TRUE);
		
		$main_params['side'] = $this->load->view('side_default', NULL, TRUE);
		$this->load->view('main', $main_params);
		
		$this->load->view('footer');
		$this->load->view('html_end');
	}
}

/* End of file message.php */
/* Location: ./application/controllers/message.php */
