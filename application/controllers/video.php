<?php

/**
 * Class Video controls video items handling: watching, commenting, rating,
 * adding etc.
 *
 * @category	Controller
 * @author		Călin-Andrei Burloiu
 */
class Video extends CI_Controller {

	protected $uploaded_file;
	protected $av_info;
	
	public function __construct()
	{
		parent::__construct();
		
		$this->lang->load('video');
	}
	
	public function index()
	{
		//phpinfo();
	}
	
	public function test()
	{
		$this->load->model('videos_model');
		
		$videos = $this->videos_model->get_videos_summary(1, NULL, 0, 10,
				'alphabetically', TRUE);
		
		var_dump($videos);
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
		$this->load->model('videos_model');
		
		$data['user_id'] = $this->session->userdata('user_id');
		if ($data['user_id'] === FALSE)
			$data['user_id'] = '';
		else
			$data['user_id'] = intval($data['user_id']);
		$user_roles = intval($this->session->userdata('roles'));
//		echo USER_ROLE_ADMIN . ' / ';
//		var_dump($user_roles);
//		var_dump($user_roles | USER_ROLE_ADMIN);
//		die();
		
		// Retrieve video information.
		$data['video'] = $this->videos_model->get_video($id, $name);
		if ($data['video'] === FALSE)
		{	
			$this->load->helper('message');
			show_error_msg_page($this, 
				$this->lang->line('video_msg_no_video'));
			return;
		}
		
		// Video is being processed by CIS.
		if ($data['video']['activation_code']
				&& !$data['video']['content_ingested'])
		{
			$this->load->helper('message');
			show_error_msg_page($this, 
				$this->lang->line('video_msg_video_not_ready'));
			return;
		}
		
		// Unlogged in user can't see unactivated videos.
		if (empty($data['user_id']))
			$allow_unactivated = FALSE;
		else
		{
			if (($user_roles & USER_ROLE_ADMIN) == 0
					&& $data['user_id'] != $data['video']['user_id'])
				$allow_unactivated = FALSE;
			else
				$allow_unactivated = TRUE;
		}
		
		// Video is not activated; can be seen by owner and admin.
		if ($data['video']['activation_code'] && !$allow_unactivated)
		{
			$this->load->helper('message');
			show_error_msg_page($this, 
				$this->lang->line('video_msg_video_unactivated'));
			return;
		}			
		
		$categories = $this->config->item('categories');
		$data['video']['category_name'] = 
			$categories[ $data['video']['category_id'] ];
		$data['plugin_type'] = ($plugin === NULL ? 'auto' : $plugin);
		
		// Increment the number of views for the video.
		$this->videos_model->inc_views($id);
		
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
		
	public function upload()
	{
		$user_id = $this->session->userdata('user_id');
		
		// Action not possible if an user is not logged in.
		if (!$user_id)
		{
			$this->load->helper('message');
			show_error_msg_page($this, 
				$this->lang->line('ui_msg_login_restriction'));
			return;
		}
		
		$this->load->library('form_validation');

		$this->form_validation->set_error_delimiters('<span class="error">',
				'</span>');
		
		if ($this->form_validation->run('upload') === FALSE)
		{
			$params = array('title' =>
								$this->lang->line('ui_nav_menu_upload')
									.' &ndash; '
									. $this->config->item('site_name'),
							//'metas' => array('description'=>'')
			);
			$this->load->library('html_head_params', $params);

			// **
			// ** LOADING VIEWS
			// **
			$this->load->view('html_begin', $this->html_head_params);
			$this->load->view('header',
					array('selected_menu' => 'upload'));

			$main_params['content'] = $this->load->view(
					'video/upload_view', array(), TRUE);
			$main_params['side'] = $this->load->view('side_default', NULL, TRUE);
			$this->load->view('main', $main_params);

			$this->load->view('footer');
			$this->load->view('html_end');
		}
		else
		{
			$this->load->model('videos_model');
			$this->load->helper('video');
			$this->config->load('content_ingestion');
			
			$name = urlencode(str_replace(' ', '-',
					$this->input->post('video-title')));
			$category_id = $this->input->post('video-category');
			
			// Prepare formats
			$formats = $this->config->item('formats');
			$prepared_formats = prepare_formats($formats, $this->av_info,
					$this->config->item('elim_dupl_res'));
			
			// Add video to DB.
			$activation_code = $this->videos_model->add_video($name,
					$this->input->post('video-title'),
					$this->input->post('video-description'),
					$this->input->post('video-tags'),
					$this->av_info['duration'],
					$prepared_formats['db_formats'], $category_id, $user_id,
					$this->uploaded_file);
			
			// Send a content ingestion request to
			// CIS (Content Ingestion Server).
			$this->_send_content_ingestion($activation_code,
					$this->uploaded_file,
					$name, $this->av_info['size'],
					$prepared_formats['transcode_configs']);
			
			$this->load->helper('message');
			show_info_msg_page($this, 
				$this->lang->line('video_msg_video_uploaded'));
		}
	}
	
	public function cis_completion($activation_code)
	{
		$this->load->model('videos_model');
		
		if ($this->config->item('require_moderation'))
			$this->videos_model->set_content_ingested($activation_code);
		else
			$this->videos_model->activate_video($activation_code);
		
//		log_message('info', "cis_completion $activation_code");
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
	
	/**
	 * Request content_ingest to the CIS in order to start the content
	 * ingestion process.
	 * 
	 * @param string $activation_code
	 * @param string $raw_video_fn uploaded video file name
	 * @param string $name
	 * @param int $raw_video_size uploaded video file size in bytes
	 * @param array $transcode_configs dictionary which must be included in
	 * the JSON data that needs to be sent to CIS
	 * @return mixed return the HTTP content (body) on success and FALSE
	 * otherwise
	 */
	protected function _send_content_ingestion($activation_code, $raw_video_fn,
			$name, $raw_video_size, $transcode_configs)
	{
		$this->config->load('content_ingestion');
		
		$url = $this->config->item('cis_url') . 'ingest_content';
		$data = array(
			'code'=>$activation_code,
			'raw_video'=>$raw_video_fn,
			'name'=>$name,
			'weight'=>$raw_video_size,
			'transcode_configs'=>$transcode_configs,
			'thumbs'=>$this->config->item('thumbs_count')
		);
		$json_data = json_encode($data);
		
		// Send request to CIS.
		$r = new HttpRequest($url, HttpRequest::METH_POST);
		$r->setBody($json_data);
		try
		{
			$response = $r->send()->getBody();
		}
		catch (HttpException $ex) 
		{
			return FALSE;
		}
		
		return $response;
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
	
	public function _valid_tags($tags)
	{
		$tok = strtok($tags, ',');
		while ($tok != FALSE)
		{
			$tok = trim($tok);
			if (!ctype_alnum($tok))
				return FALSE;
			
			$tok = strtok(',');
		}
		
		return TRUE;
	}
	
	public function _valid_upload($file)
	{
		if ($_FILES['video-upload-file']['tmp_name'])
		{
			// Upload library
			$config_upload = array();
			$config_upload['upload_path'] = './data/upload';
			$config_upload['allowed_types'] = '*';
			$this->load->library('upload', $config_upload);

			if ($this->upload->do_upload('video-upload-file'))
			{
				$upload_data = $this->upload->data();
				$this->uploaded_file = $upload_data['file_name'];
				
				$this->load->helper('video');
				$this->av_info = get_av_info($upload_data['full_path']);
				if (!$this->av_info)
					return FALSE;
				
				return TRUE;
			}
			else
			{
				$this->form_validation->set_message('_valid_upload',
						$this->upload->display_errors('<span class="error">',
								'</span>'));
				return FALSE;
			}
		}
		
		$this->form_validation->set_message('_valid_upload',
				$this->lang->line('_required_upload'));
		return FALSE;
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
