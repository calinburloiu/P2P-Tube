<?php

/**
 * Class Catalog controlls video hierarchy and searching
 *
 * @category	Controller
 * @author		Călin-Andrei Burloiu
 */
class Catalog extends CI_Controller {
	
	public function __construct()
	{
		parent::__construct();
	}	
	
	public function index()
	{
		// Retrieve videos summary.
		$data['categories'] = $this->config->item('categories');
		$this->load->model('videos_model');
		foreach ($data['categories'] as $id => $name)
		{
			$data['videos'][$id] = $this->videos_model->get_videos_summary(
				$id, 0, $this->config->item('videos_per_row'));
		}
		
		$params = array(	'title' => $this->config->item('site_name'),
							'css' => array('catalog.css'),
							//'js' => array(),
							//'metas' => array('description'=>'','keywords'=>'')
							);
		$this->load->library('html_head_params', $params);
		$this->load->view('html_begin', $this->html_head_params);
		$this->load->view('header');
		
		$this->load->view('catalog/index_view', $data);
		
		$this->load->view('footer');
		$this->load->view('html_end');
	}
	
	public function test($page = 0)
	{
		$this->load->helper('url');
		$this->load->library('pagination');
		
		$config['base_url'] = site_url('catalog/test/');
		$config['total_rows'] = '160';
		$this->pagination->initialize($config);
		echo $this->pagination->create_links();
	}
	
	public function category($category_id, $offset = 0)
	{
		// Retrieve videos summary.
		$this->load->model('videos_model');
		$data['videos'] = $this->videos_model->get_videos_summary(
			$category_id, intval($offset),
			$this->config->item('videos_per_page'));
		$categories = $this->config->item('categories');
		$data['category'] = $categories[$category_id];
		$data['category_id'] = $category_id;
		
		// Pagination
		$this->load->library('pagination');
		$pg_config['base_url'] = site_url("catalog/category/$category_id/");
		$pg_config['uri_segment'] = 4;
		$pg_config['total_rows'] = $this->videos_model->get_videos_count(
			$category_id);
		$pg_config['per_page'] = $this->config->item('videos_per_page');
		$this->pagination->initialize($pg_config);
		$data['pagination'] = $this->pagination->create_links();
		
		$params = array(	'title' => $this->config->item('site_name'),
							'css' => array('catalog.css'),
							//'js' => array(),
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
