<?php

/**
 * Class Article typically controls static pages.
 * Their content depends on the language.
 *
 * The page views are located in "application/views/article/$language/$method".
 *
 * @category	Controller
 * @author		Călin-Andrei Burloiu
 */
class Article extends Article_Controller {
	
	public function __construct()
	{
		parent::__construct();
	}

	public function _remap($method, $params = array())
	{	
		parent::_remap($method, $params);
		
		// **
		// ** DATA
		// **
		
		$html_params = array('title' => $this->title.' &ndash; '
									. $this->config->item('site_name'),
							'css' => $this->css,
							'js' => $this->js,
							'metas' => 
								array('description'=>$this->metaDescription)
							);
		$this->load->library('html_head_params', $html_params);

		// **
		// ** LOADING VIEWS
		// **
		$this->load->view('html_begin', $this->html_head_params);
		$this->load->view('header', array('selected_menu' => $method));
		
		$main_params['content'] = $this->_load($method, $params);
		$main_params['side'] = $this->load->view('side_default.php', NULL, TRUE);
		$this->load->view('main', $main_params); 
				
		$this->load->view('footer');
		$this->load->view('html_end');
	}
}

/* End of file article.php */
/* Location: ./application/controllers/article.php */
