<?php

/**
 * Class Article typically controls static pages.
 * Their content depends on the language.
 *
 * The page views are located in "application/views/article/$language/$method".
 * Article's name can be set in language file 'article_lang.php' by using an
 * entry named "article_$method". If not present "$method" is used as a name.
 * Article meta description has the entry "article_$method_description"
 *
 * @category	Controller
 * @author		Călin-Andrei Burloiu
 */
class Article extends Article_Controller {

	public function _remap($method, $params = array())
	{
		// **
		// ** DATA
		// **
		$this->lang->load('article');
		$title = $this->lang->line("article_$method");
		if ($title == FALSE)
			$title = $method;
		$descr = $this->lang->line("article_${method}_description");
		if ($descr == FALSE)
			$descr = '';
		
		$html_params = array(	'title' => 
								$title.' - '. $this->config->item('site_name'),
							'css' => array(
								'jquery-ui.css'
								),
							//'js' => array(),
							'metas' => array('description'=>$descr)
							);
		$this->load->library('html_head_params', $html_params);

		// **
		// ** LOADING VIEWS
		// **
		$this->load->view('html_begin', $this->html_head_params);
		$this->load->view('header', array('selected_menu' => $method));
		
		$this->load->view('echo', 
			array('output' => $this->_load($method, $params),
				'clear' => TRUE)
			);
		
		$this->load->view('footer');
		$this->load->view('html_end');
	}
}

/* End of file article.php */
/* Location: ./application/controllers/article.php */
