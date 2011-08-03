<?php

/**
 * Class Video controlls video items handling: watching, commenting, rating,
 * adding etc.
 *
 * @category	Controller
 * @author		Călin-Andrei Burloiu
 */
class Video extends CI_Controller {

	public function __construct()
	{
		parent::__construct();
		
		//$this->lang->load('video');
	}
	
	public function index()
	{
		
	}
	
	/**
	 * The page used for watching a video
	 *
	 * @param	string $id	DB id of the video
	 * @param	string $name	`name` of the video from DB
	 * @param	string $plugin	video plugin ('ns-vlc', 'ns-html5'). If it's set 
	 * to NULL or 'auto', the plugin is automatically selected.
	 */
	public function watch($id, $name = NULL, $plugin = NULL)
	{
		$this->load->helper('url');
		
		// Retrieve video information.
		$this->load->model('videos_model');
		$data['video'] = $this->videos_model->get_video($id, $name);
		$data['plugin_type'] = ($plugin === NULL ? 'auto' : $plugin);
		
		// Display page.
		$params = array(	'title' => $data['video']['title'] . ' -- '
								. $this->config->item('site_name'),
							'css' => array(
								'jquery-ui-1.8.14.custom.css',
								'jquery.ui.nsvideo.css',
								'video.css'
							),
							'js' => array(
								'jquery-1.6.2.min.js',
								'jquery-ui-1.8.14.custom.min.js',
								'jquery.ui.nsvideo.js'
							),
							//'metas' => array('description'=>'','keywords'=>'')
							);
		$this->load->library('html_head_params', $params);
		$this->load->view('html_begin', $this->html_head_params);
		$this->load->view('header');
		
		// Preloading video plugin.
		// TODO plugin auto: type and format
		if ($data['plugin_type'] == 'auto')
			$data['plugin_type'] = 'ns-html5';
		$data['asset_index'] = 0;
		
		// TODO remove old AJAX plugin content
// 		$data['plugin_content'] = $this->_plugin('ns-html5', 
// 			$data['video']['url'][0], TRUE);
		
		$this->load->view('video/watch_view', $data);
		
		$this->load->view('footer');
		$this->load->view('html_end');
	}
	
	/**
	 * AJAX page which retrieves a video plugin.
	 *
	 * The view associated with this controller should be parameter type
	 * concatenated with '_plugin_view' and must be located in
	 * 'application/views/video'.
	 *
	 * @param	string $type	'ns-vlc', 'ns-html5'
	 */
	public function plugin($type)
	{
		$url = $this->input->post('url', TRUE);
		
		$this->_plugin($type, $url);
	}
	
	/**
	 * Video plugin controller
	 *
	 * See plugin function for details. If the second parameter is TRUE
	 * the output is return instead of being displayed (used in preloading).
	 */
	public function _plugin($type, $url, $return_output=FALSE)
	{	
		if ($type == 'ns-html5')
			$data['url'] = 'tribe://' . $url;
		else if ($type == 'ns-vlc')
			$data['url'] = $url;
		
		$output = $this->load->view('video/'. $type . '_plugin_view', $data, 
			$return_output);
		
		if ($return_output)
			return $output;
	}
	
}

/* End of file video.php */
/* Location: ./application/controllers/video.php */
