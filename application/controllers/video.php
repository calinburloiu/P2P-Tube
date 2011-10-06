<?php

/**
 * Class Video controls video items handling: watching, commenting, rating,
 * adding etc.
 *
 * @category	Controller
 * @author		Călin-Andrei Burloiu
 */
class Video extends CI_Controller {

	public function __construct()
	{
		parent::__construct();
		
		$this->lang->load('video');
	}
	
	public function index()
	{
		
	}
	
	public function test($video_id)
	{
		// Display page.
		$params = array(	'title' => $this->config->item('site_name'),
									'css' => array(
										'video.css'
		),
									'js' => array(
										'jquery.ui.ajax_links_maker.js'
		),
		//'metas' => array('description'=>'','keywords'=>'')
		);
		$this->load->library('html_head_params', $params);
		
		// **
		// ** LOADING VIEWS
		// **
		$this->load->view('html_begin', $this->html_head_params);
		$this->load->view('header');
		
		$main_params['content'] =
			$this->load->view('echo', array('output'=> 
				$this->_ajax_comment(TRUE, $video_id)),
			TRUE);
		$main_params['side'] = $this->load->view('side_default', NULL, TRUE);
		$this->load->view('main', $main_params);
		
		$this->load->view('footer');
		$this->load->view('html_end');
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
		// **
		// ** LOADING MODEL
		// **
		// Retrieve video information.
		$this->load->model('videos_model');
		$this->videos_model->inc_views($id);
		$data['video'] = $this->videos_model->get_video($id, $name);
		$categories = $this->config->item('categories');
		$data['video']['category_name'] = 
			$categories[ $data['video']['category_id'] ];
		$data['plugin_type'] = ($plugin === NULL ? 'auto' : $plugin);
		$data['user_id'] = $this->session->userdata('user_id');
		if ($data['user_id'] === FALSE)
			$data['user_id'] = '';
		
		// Display page.
		$params = array(	'title' => $data['video']['title'] . ' &ndash; '
								. $this->config->item('site_name'),
							'css' => array(
								'jquery.ui.nsvideo.css',
								'video.css'
							),
							'js' => array(
								'jquery.ui.nsvideo.js',
								'jquery.ui.ajax_links_maker.js'
							),
							//'metas' => array('description'=>'','keywords'=>'')
							);
		$this->load->library('html_head_params', $params);
		
		// Preloading video plugin.
		// TODO plugin auto: type and format
		if ($data['plugin_type'] == 'auto')
			$data['plugin_type'] = 'ns-html5';
		$data['asset_index'] = 0;
		
		// TODO remove old AJAX plugin content
// 		$data['plugin_content'] = $this->_plugin('ns-html5', 
// 			$data['video']['url'][0], TRUE);

		// Comments
		$data['comments'] = $this->_ajax_comment(TRUE, $id);
		
		// **
		// ** LOADING VIEWS
		// **
		$this->load->view('html_begin', $this->html_head_params);
		$this->load->view('header');
		
		//$main_params['content'] = $this->load->view('video/watch_view', $data, TRUE);
		$this->load->view('video/watch_view', $data);
		
		$this->load->view('footer');
		$this->load->view('html_end');
	}
	
	/**
	* Increments (dis)likes count for video with the specified id and returns to
	* the client as plain text the number if likes.
	*
	* @param string $action	'like' or 'dislike'
	* @param string $video_id
	* @param string $user_id
	*/
	public function ajax_vote($action, $video_id)
	{
		$video_id = intval($video_id);
		$user_id = $this->session->userdata('user_id');
		$this->load->model('videos_model');
	
		$res = $this->videos_model->vote($video_id, $user_id,
			(strcmp($action, 'like') == 0 ? TRUE : FALSE));
	
		if ($res !== -1)
			echo $res;
	}
	/**
	 * Increments (dis)likes count for a comment with a specified id and returns
	 * to the client as plain text the number if likes. 
	 * 
	 * @param string $action	'like' or 'dislike'
	 * @param string $comment_id
	 */
	public function ajax_vote_comment($action, $comment_id)
	{
		$comment_id = intval($comment_id);
		$user_id = $this->session->userdata('user_id');
		$this->load->model('videos_model');
		
		$res = $this->videos_model->vote_comment($comment_id, $user_id,
			(strcmp($action, 'like') == 0 ? TRUE : FALSE));
		
		if ($res !== -1)
			echo $res;
	}
	
	public function ajax_comment($video_id,
			$ordering = 'newest', $offset = '0')
	{
		$this->_ajax_comment(FALSE, $video_id, $ordering, $offset);
	}
	
	public function _ajax_comment($return_output, $video_id,
			$ordering = 'newest', $offset = '0')
	{
		$video_id = intval($video_id);
		
		$this->load->library('form_validation');
		$this->form_validation->set_error_delimiters('<span class="error">',
					'</span>');
		
		if ($this->form_validation->run('comment_video'))
		{
			$this->load->model('videos_model');
			$user_id = intval($this->session->userdata('user_id'));
			$comment = $this->input->post('comment');
			
			$this->videos_model->comment_video($video_id, $user_id, $comment);
		}
		
		// **
		// ** MODEL **
		// **		
		$this->load->model('videos_model');
		$data['comments'] = $this->videos_model->get_video_comments($video_id,
			$offset, $this->config->item('video_comments_per_page'), $ordering);
		$data['comments_count'] =
			$this->videos_model->get_video_comments_count($video_id);
		$data['hottest_comments'] = $this->videos_model->get_video_comments(
			$video_id, 0, 2, 'hottest');
		$data['video_id'] = $video_id;
		$data['user_id'] = $this->session->userdata('user_id');
		
		// Pagination
		$this->load->library('pagination');
		$pg_config['base_url'] = site_url("video/ajax_comment/$video_id/$ordering/");
		$pg_config['uri_segment'] = 5;
		$pg_config['total_rows'] = $data['comments_count'];
		$pg_config['per_page'] = $this->config->item('video_comments_per_page');
		$this->pagination->initialize($pg_config);
		$data['comments_pagination'] = $this->pagination->create_links();
		
		// **
		// ** VIEWS **
		// **
		$output = $this->load->view('video/comments_view',
			$data, $return_output);
		
		if ($return_output)
			return $output;
	}
	
	public function _is_user_loggedin($param)
	{
		if (! $this->session->userdata('user_id'))
			return FALSE;
		
		return TRUE;
	}
	
	public function _do_comment($comment)
	{
		// Note: Videos_model must be already loaded.
		$this->load->model('videos_model');
		
		$video_id = intval($this->input->post('video-id'));
		$user_id = intval($this->session->userdata('user_id'));
		
		$this->videos_model->comment_video($video_id, $user_id, $comment);
	}
	
	/**
	 * OBSOLETE: AJAX page which retrieves a video plugin.
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
	 * OBSOLETE: Video plugin controller
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
