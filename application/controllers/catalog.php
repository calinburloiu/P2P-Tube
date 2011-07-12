<?php

/**
 * Class Catalog controlls video hierarchy and searching
 *
 * @category	Controller
 * @author		Călin-Andrei Burloiu
 */
class Catalog extends CI_Controller {
	
	public function index()
	{
		$this->load->view('html_begin');
		$this->load->view('header');
		
		$this->load->model('videos_model');
		$data['query'] = $this->videos_model->getVideosSummary();
		$this->load->view('catalog/index_view', $data);
		
		
		$this->load->view('footer');
		$this->load->view('html_end');
	}
}

/* End of file catalog.php */
/* Location: ./application/controllers/catalog.php */
