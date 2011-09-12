<?php

/**
 * Class User controls video hierarchy and searching
 *
 * @category	Controller
 * @author		Călin-Andrei Burloiu
 */
class User extends CI_Controller {
	
	public function __construct()
	{
		parent::__construct();
	
		$this->lang->load('user');
	}
	
	public function index()
	{
	}
	
	public function login()
	{		
		$this->load->library('form_validation');
		
		if ($this->form_validation->run() == FALSE)
		{
			$params = array(	'title' => $this->config->item('site_name'),
										'css' => array(
											'catalog.css'
										),
										//'js' => array(),
										//'metas' => array('description'=>'')
			);
			$this->load->library('html_head_params', $params);
			
			// **
			// ** LOADING VIEWS
			// **
			$this->load->view('html_begin', $this->html_head_params);
			$this->load->view('header', array('selected_menu' => 'login'));
			
			$this->load->view('user/login_view', array());
			
			$this->load->view('footer');
			$this->load->view('html_end');
		}
		else
		{
			header('Location: ' . site_url());
			return;
		}
	}
	
	public function _check_login($username, $password)
	{
		return TRUE;
	}
}

/* End of file user.php */
/* Location: ./application/controllers/user.php */
