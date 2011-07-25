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
		// Retrieve videos summary.
		$this->load->model('videos_model');
		$data['videos'] = $this->videos_model->get_videos_summary();
		
		$params = array(	'title' => $this->config->item('site_name'),
							//'stylesheets' => array(),
							//'javascripts' => array(),
							//'metas' => array('description'=>'','keywords'=>'')
							);
		$this->load->library('html_head_params', $params);
		$this->load->view('html_begin', $this->html_head_params);
		$this->load->view('header');
		
		$this->load->view('catalog/index_view', $data);
		
		$this->load->view('footer');
		$this->load->view('html_end');
	}
	
	public function test()
	{
		$this->load->helper('url');
		
		echo '<a href="/">link</a>';
	}
	
	public function category($category_id)
	{
		// Retrieve videos summary.
		$this->load->model('videos_model');
		$data['videos'] = $this->videos_model->get_videos_summary($category_id);
		$categories = $this->config->item('categories');
		$data['category'] = $categories[$category_id];
		$data['category_id'] = $category_id;
		
		$params = array(	'title' => $this->config->item('site_name'),
							'stylesheets' => array('catalog.css'),
							//'javascripts' => array(),
							//'metas' => array('description'=>'','keywords'=>'')
							);
		$this->load->library('html_head_params', $params);
		$this->load->view('html_begin', $this->html_head_params);
		$this->load->view('header');
		
		$this->load->view('catalog/category_view', $data);
		
		$this->load->view('footer');
		$this->load->view('html_end');
	}
	
	public function search($query_str)
	{
		echo $query_str;
	}
}

/* End of file catalog.php */
/* Location: ./application/controllers/catalog.php */
