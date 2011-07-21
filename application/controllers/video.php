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
		$data['plugin'] = ($plugin === NULL ? 'auto' : $plugin);
		
		// Display page.
		$params = array(	'title' => $data['video']['title'] . ' -- '
								. $this->config->item('site_name'),
							'stylesheets' => array('jquery-ui.css', 'NextShare_VLC_plugin.css'),
							'javascripts' => array('jquery.min.js', 'jquery-ui.min.js', 'NextShare_VLC_plugin.js', 'video.js'),
							//'metas' => array('description'=>'','keywords'=>'')
							);
		$this->load->library('html_head_params', $params);
		$this->load->view('html_begin', $this->html_head_params);
		$this->load->view('header');
		
		$this->load->view('video/watch_view', $data);
		
		$this->load->view('footer');
		$this->load->view('html_end');
	}
	
	/**
	 * AJAX page which retrieves a video plugin.
	 *
	 * The associated with this controller should be parameter type
	 * concatenated with '_plugin_view' and must be located in
	 * 'application/views/video'.
	 *
	 * @param	string $type	'ns-vlc', 'ns-html5'
	 */
	public function plugin($type)
	{
		$data['url'] = $this->input->post('url', TRUE);
		
		$this->load->view('video/'. $type . '_plugin_view', $data);
	}
}

/* End of file video.php */
/* Location: ./application/controllers/video.php */
