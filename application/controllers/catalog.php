<?php

/**
 * Class Catalog controls video hierarchy and searching
 *
 * @category	Controller
 * @author		Călin-Andrei Burloiu
 */
class Catalog extends CI_Controller {
	
	public function __construct()
	{
		parent::__construct();
		
		//$this->lang->load('catalog');
	}	
	
	public function index()
	{
		// **
		// ** LOADING MODEL
		// **
		// Retrieve videos summary.
		$this->load->model('videos_model');
		foreach ($this->config->item('categories') as $id => $name)
		{
			// Videos
			$vs_data['videos'] = $this->videos_model->get_videos_summary(
				$id, 0, $this->config->item('videos_per_row'));
			
			// Category
			$vs_data['category_title'] = $name ?
				$this->lang->line("ui_categ_$name") : $name;
			$vs_data['category_id'] = $id;
			
			// Pagination (not required)
			$vs_data['pagination'] = '';
			
			$data['videos_summaries'][] = 
				$this->load->view('catalog/videos_summary_view', 
				$vs_data, TRUE);
		}
		
		$params = array(	'title' => $this->config->item('site_name'),
							'css' => array(
								'catalog.css'
							),
							//'js' => array(),
							//'metas' => array('description'=>'','keywords'=>'')
							);
		$this->load->library('html_head_params', $params);
		
		// **
		// ** LOADING VIEWS
		// **
		$this->load->view('html_begin', $this->html_head_params);
		$this->load->view('header', array('selected_menu' => 'home'));
		
		$main_params['content'] = $this->load->view('catalog/index_view', $data, TRUE);
		$main_params['side'] = $this->load->view('side_default.php', NULL, TRUE);
		$this->load->view('main', $main_params);
		
		$this->load->view('footer');
		$this->load->view('html_end');
	}
	
	public function test($page = 0)
	{
		$params = array(	'title' => 'Test - '. $this->config->item('site_name'),
							//'css' => array(),
							//'js' => array(),
							//'metas' => array('description'=>'','keywords'=>'')
			);
		$this->load->library('html_head_params', $params);
		
		// **
		// ** LOADING VIEWS
		// **
		$this->load->view('html_begin', $this->html_head_params);
		$this->load->view('header', array('selected_menu' => 'home'));
		
		$this->load->view('echo', array('output'=>'Test Page', 'clear'=>TRUE));
		
		$this->load->view('footer');
		$this->load->view('html_end');
	}
	
	public function category($category_name, $offset = 0)
	{
		// **
		// ** LOADING MODEL
		// **
		// Video Category
		$categories = $this->config->item('categories');
		$category_id = array_search($category_name, $categories);
		$vs_data['category_name'] = $category_name;
		$vs_data['category_id'] = $category_id;
		$vs_data['category_title'] = $category_name ?
		$this->lang->line("ui_categ_$category_name") : $category_name;		
		
		// Retrieve videos summary.
		$this->load->model('videos_model');
		$vs_data['videos'] = $this->videos_model->get_videos_summary(
			$vs_data['category_id'], intval($offset),
			$this->config->item('videos_per_page'));
		
		// Pagination
		$this->load->library('pagination');
		$pg_config['base_url'] = site_url("catalog/category/$category_id/");
		$pg_config['uri_segment'] = 4;
		$pg_config['total_rows'] = $this->videos_model->get_videos_count(
			$category_id);
		$pg_config['per_page'] = $this->config->item('videos_per_page');
		$this->pagination->initialize($pg_config);
		$vs_data['pagination'] = $this->pagination->create_links();
		
		// Video Summary
		$data['video_summary'] = $this->load->view('catalog/videos_summary_view',
			$vs_data, TRUE);
		
		$params = array(	'title' => $this->config->item('site_name'),
							'css' => array(
								'catalog.css'
							),
							//'js' => array(),
							//'metas' => array('description'=>'','keywords'=>'')
							);
		$this->load->library('html_head_params', $params);
		
		// **
		// ** LOADING VIEWS
		// **
		$this->load->view('html_begin', $this->html_head_params);
		$this->load->view('header');
		
		$main_params['content'] = $this->load->view('catalog/category_view', $data, TRUE);
		$main_params['side'] = $this->load->view('side_default.php', NULL, TRUE);
		$this->load->view('main', $main_params);
		
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
