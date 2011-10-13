<?php

/**
 * Class User controls video hierarchy and searching
 *
 * @category	Controller
 * @author		Călin-Andrei Burloiu
 */
class User extends CI_Controller {

	private $import = FALSE;
	private $activated_account = TRUE;
	private $user_id = NULL;

	public function __construct()
	{
		parent::__construct();

		$this->lang->load('user');
		$this->load->model('users_model');
	}

	public function index()
	{
	}
	
	public function test($user_id = 1)
	{

	}

	/**
	* Login a user and then redirect it to the last page which must be encoded
	* in $redirect.
	*
	* @param string $redirect	contains the last page URI segments encoded
	* with helper url_encode_segments.
	*/
	public function login($redirect = '')
	{
		$this->load->library('form_validation');
		$this->form_validation->set_error_delimiters('<span class="error">',
			'</span>');
		
		// Normal or OpenID login?
		if ($this->input->post('openid') !== FALSE)
			$b_openid = TRUE;
		else
			$b_openid = FALSE;
		// Validate the correct form.
		$res_form_validation = FALSE;
		if (!$b_openid)
			$res_form_validation = $this->form_validation->run('login');
		else
			$res_form_validation = $this->form_validation->run('login_openid');

		if ($res_form_validation === FALSE)
		{
			$params = array(	'title' =>
									$this->lang->line('ui_nav_menu_login')
										.' &ndash; '
										. $this->config->item('site_name'),
								//'metas' => array('description'=>'')
			);
			$this->load->library('html_head_params', $params);
				
			// **
			// ** LOADING VIEWS
			// **
			$this->load->view('html_begin', $this->html_head_params);
			$this->load->view('header', array('selected_menu' => 'login'));

			$main_params['content'] = $this->load->view('user/login_view',
				array('redirect'=> $redirect), TRUE);
			$main_params['side'] = $this->load->view('side_default', NULL, TRUE);
			$this->load->view('main', $main_params);
				
			$this->load->view('footer');
			$this->load->view('html_end');
		}
		else
		{
			if ($b_openid)
			{
				$this->lang->load('openid');
				$this->load->library('openid');
				$this->config->load('openid');
				$request_to = site_url('user/check_openid_login');
				$req = $this->config->item('openid_required');
				$opt = $this->config->item('openid_optional');
				$policy = site_url('user/openid_policy');
				$pape_policy_uris = $this->config->item('openid_papa_policies');
				
				$this->openid->set_request_to($request_to);
				$this->openid->set_trust_root(base_url());
				$this->openid->set_args(null);
				$this->openid->set_sreg(true, $req, $opt, $policy);
				if (!empty($pape_policy_uris))
					$this->openid->set_pape(true, $pape_policy_uris);
				
				// Redirection to OP site will follow.
				$this->openid->authenticate($this->input->post('openid'));
				return;
			}
			
			// Without OpenID
			if (! $this->activated_account)
				header('Location: '
					. site_url("user/activate/{$this->user_id}"));
			else if (! $this->import)
			{
				// Redirect to last page before login. 
				header('Location: '. site_url(urldecode_segments($redirect)));
			}
			else
			{
				// Redirect to account page because an user authenticates here
				// for the first time with external authentication. The page
				// will display imported data.
				header('Location: '. site_url('user/account'));
			}
		}
	}
	
	public function check_openid_login()
	{
		$this->lang->load('openid');
		$this->load->library('openid');
		$this->config->load('openid');
		$request_to = site_url('user/check_openid_login');

		$this->openid->set_request_to($request_to);
		$response = $this->openid->get_response();

		switch ($response->status)
		{
		case Auth_OpenID_CANCEL:
			$this->load->helper('message');
			show_info_msg_page($this, $this->lang->line('openid_cancel'));
			break;
		case Auth_OpenID_FAILURE:
			$this->load->helper('message');
			show_error_msg_page($this,
					$this->_format_message('openid_failure',
							$response->message));
			break;
		case Auth_OpenID_SUCCESS:
			$openid = $response->getDisplayIdentifier();
			$esc_identity = htmlspecialchars($openid, ENT_QUOTES);

			$sreg_resp = Auth_OpenID_SRegResponse::fromSuccessResponse($response);
			$sreg = $sreg_resp->contents();
			
			// Get registration informations
		    $ax = new Auth_OpenID_AX_FetchResponse();
			$obj = $ax->fromSuccessResponse($response);

			//echo 'nickname('. $sreg_resp->get('nickname'). ')';
			echo var_dump($obj->data);
//			foreach ($sreg as $key => $value)
//			{
//				$data['success'] .= $this->_set_message('openid_content', array($key, $value), array('%s', '%t'));
//			}

			break;
		}
	}
	
	public function openid_policy()
	{
		$this->load->view('openid_policy_view');
	}
	
	/**
	 * Logout user and then redirect it to the last page which must be encoded
	 * in $redirect.
	 * 
	 * @param string $redirect	contains the last page URI segments encoded
	 * with helper url_encode_segments.
	 */
	public function logout($redirect = '')
	{
		$this->session->unset_userdata('user_id');
		$this->session->unset_userdata('username');
		$this->session->unset_userdata('auth_src');
		$this->session->unset_userdata('time_zone');
		
		header('Location: '. site_url(urldecode_segments($redirect)));
	}
	
	public function register($redirect = '')
	{
		$this->load->library('form_validation');
		$this->load->helper('localization');
		$this->load->helper('date');
			
		$this->form_validation->set_error_delimiters('<span class="error">',
					'</span>');
		$error_upload = '';

		if ($this->form_validation->run('register'))
		{
			$b_validation = TRUE;
			
			if ($_FILES['picture']['tmp_name'])
			{
				// Upload library
				$config_upload['upload_path'] = './data/user_pictures';
				$config_upload['file_name'] = 
					str_replace('.', '-', $this->input->post('username')) .'-';
				$config_upload['allowed_types'] = 'gif|jpg|png';
				$config_upload['max_size'] = '10240';
				$this->load->library('upload', $config_upload);
				
				$b_validation = $this->upload->do_upload('picture');
				$error_upload = 
					$this->upload->display_errors('<span class="error">',
							'</span>');
			}
		}
		else
			$b_validation = FALSE;
		
		if (! $b_validation)
		{
			// Edit account data if logged in, otherwise register.
			if ($user_id = $this->session->userdata('user_id'))
			{
				$userdata = $this->users_model->get_userdata(intval($user_id));
				$selected_menu = 'account';
			}
			else
			{
				$userdata = FALSE;
				$selected_menu = 'register';
			}
			
			$params = array('title' =>
								$this->lang->line('ui_nav_menu_register')
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
				array('selected_menu' => $selected_menu));
			
			$main_params['content'] = $this->load->view('user/register_view', 
				array('userdata'=> $userdata, 'redirect'=> $redirect,
					'error_upload'=> $error_upload),
				TRUE);
			$main_params['side'] = $this->load->view('side_default', NULL, TRUE);
			$this->load->view('main', $main_params);
		
			$this->load->view('footer');
			$this->load->view('html_end');
		}
		else
		{
			$user_id = $this->input->post('user-id');
			$data['email'] = $this->input->post('email');
			$data['first_name'] = $this->input->post('first-name');
			$data['last_name'] = $this->input->post('last-name');
			$data['birth_date'] = $this->input->post('birth-date');
			$data['country'] = $this->input->post('country');
			$data['locality'] = $this->input->post('locality');
			$data['ui_lang'] = $this->input->post('ui-lang');
			$data['time_zone'] = $this->input->post('time-zone');
			
			// Handle picture if one was uploaded.
			if ($_FILES['picture']['tmp_name'])
			{
				$upload_data = $this->upload->data();
				$this->load->library('image');
				$this->image->load($upload_data['full_path']);
				// Resize original to a maximum size.
				if ($this->image->get_width() * $this->image->get_height()
						> 640*480)
				{
					$this->image->save_thumbnail(
						$upload_data['full_path'],
						640, 480, IMAGETYPE_AUTO);
				}
				// Create thumbnail.
				$data['picture'] = $upload_data['file_name'];
				$this->image->save_thumbnail($upload_data['file_path']
						. $upload_data['file_name']. '-thumb.jpg', 120, 90);
			}
			
			// Update session user data.
			$this->_update_session_userdata($data);
			
			// Edit account data
			if ($user_id)
			{
				$password = $this->input->post('new-password');
				if ($password)
					$data['password'] = $this->input->post('new-password');
				
				$this->users_model->set_userdata($user_id, $data);
				
				// Redirect to last page before login.
				header('Location: '. site_url(urldecode_segments($redirect)));
			}
			// Registration
			else
			{
				$data['username'] = $this->input->post('username');
				$data['password'] = $this->input->post('password');
				
				$this->users_model->register($data);
				$user_id = $this->users_model->get_userdata($data['username'],
						"id");
				$user_id = $user_id['id'];
				
				// Redirect account activation page.
				header('Location: '. site_url("user/activate/$user_id"));
			}
		}
	}
	
	public function account($redirect = '')
	{
		$this->register($redirect);
	}
	
	public function profile($username, $videos_offset = 0)
	{
		// TODO handle user not found
		
		$this->load->config('localization');
		$this->load->helper('date');
		$this->lang->load('date');
		
		// **
		// ** LOADING MODEL
		// **
		// Logged in user time zone
		$time_zone = $this->session->userdata('time_zone');
		
		// User data
		$userdata = $this->users_model->get_userdata($username);
		$userdata['roles'] = Users_model::roles_to_string($userdata['roles']);
		$country_list = $this->config->item('country_list');
		$userdata['country_name'] = $country_list[ $userdata['country'] ];
		$userdata['last_login'] = human_gmt_to_human_local(
			$userdata['last_login'], $time_zone); 
		$userdata['time_zone'] = $this->lang->line($userdata['time_zone']);
		
		// User's videos
		$this->load->model('videos_model');
		$vs_data['videos'] = $this->videos_model->get_videos_summary(
			NULL, $username, intval($videos_offset),
			$this->config->item('videos_per_page'));
		
		// Pagination
		$this->load->library('pagination');
		$pg_config['base_url'] = site_url("user/profile/$username/");
		$pg_config['uri_segment'] = 4;
		$pg_config['total_rows'] = $this->videos_model->get_videos_count(
			NULL, $username);
		$pg_config['per_page'] = $this->config->item('videos_per_page');
		$this->pagination->initialize($pg_config);
		$vs_data['pagination'] = $this->pagination->create_links();
		$vs_data['title'] = NULL;
		$vs_data['category_name'] = ''; // TODO videos_summary with AJAX
		
		$params = array(
			'title'=> $this->lang->line('user_appelation').' '.$username
				.' &ndash; '
				. $this->config->item('site_name'),
			'css'=> array('catalog.css')
			//'metas' => array('description'=>'')
		);
		$this->load->library('html_head_params', $params);
		
		// Current user profile tab
		$tab = (! $videos_offset ? 0 : 1);
		
		// **
		// ** LOADING VIEWS
		// **
		$this->load->view('html_begin', $this->html_head_params);
		$this->load->view('header', array());
		
		$vs = $this->load->view('catalog/videos_summary_view', $vs_data, TRUE);
		
		$main_params['content'] = $this->load->view('user/profile_view',
			array('userdata'=> $userdata, 'videos_summary'=> $vs, 'tab'=>$tab),
			TRUE);
		$main_params['side'] = $this->load->view('side_default', NULL, TRUE);
		$this->load->view('main', $main_params);
		
		$this->load->view('footer');
		$this->load->view('html_end');
	}
	
	public function activate($user_id, $method='', $activation_code='')
	{
		$user_id = intval($user_id);		
		$res_form_validation = FALSE;
		
		if ($method == 'code')
		{
			if (! $activation_code)
				$res_form_validation = $this->form_validation->run('activate');
			// Activation code is provided in URL.
			else
			{
				if ($this->_valid_activation_code($activation_code)
						&& $this->users_model->activate_account($user_id,
							$activation_code))
				{
					$this->load->helper('message');
					show_info_msg_page($this, sprintf(
						$this->lang->line('user_msg_activated_account'), 
						site_url('user/login')));
					return;
				}
				else
				{
					$this->load->helper('message');
					show_error_msg_page($this, 
							$this->lang->line(
									'user_msg_wrong_activation_code'));
					return;
				}
			}
		}
		else if ($method == 'resend')
		{
			$res_form_validation =
				$this->form_validation->run('resend_activation');
		}
		
		$userdata = $this->users_model->get_userdata($user_id,
				'email, a.activation_code');
		$email = $userdata['email'];
		$activated_account = ($userdata['activation_code'] == NULL);
		
		if ($activated_account)
		{
			$this->load->helper('message');
			show_info_msg_page($this, sprintf(
				$this->lang->line('user_msg_activated_account'), 
				site_url('user/login')));
			return;
		}
		
		$this->load->library('form_validation');
			
		$this->form_validation->set_error_delimiters('<span class="error">',
					'</span>');
		
		if ($res_form_validation === FALSE)
		{
			$params = array(
				'title'=> $this->lang->line('user_title_activation')
					.' &ndash; '
					. $this->config->item('site_name'),
				//'metas' => array('description'=>'')
			);
			$this->load->library('html_head_params', $params);
		
			// **
			// ** LOADING VIEWS
			// **
			$this->load->view('html_begin', $this->html_head_params);
			$this->load->view('header', array());

			// Show form
			$main_params['content'] = 
				$this->load->view('user/activate_view',
				array(	'user_id'=> $user_id,
						'email'=> $userdata['email']),
				TRUE);
			
			$main_params['side'] = $this->load->view('side_default', NULL, TRUE);
			$this->load->view('main', $main_params);
		
			$this->load->view('footer');
			$this->load->view('html_end');
		}
		else
		{
			if ($method == 'code')
			{
				// A message which tells the user that the
				// activation was successful.
				$this->load->helper('message');
				show_info_msg_page($this, sprintf(
					$this->lang->line('user_msg_activated_account'), 
					site_url('user/login')));
				return;
			}
			else if ($method == 'resend')
			{
				// Redirect to resent message
				$this->load->helper('message');
				show_info_msg_page($this, sprintf(
						$this->lang->line('user_msg_activation_resent'),
						$this->input->post('email')));
				return;
			}
		}
	}
	
	public function recover_password()
	{
		$this->load->library('form_validation');
			
		$this->form_validation->set_error_delimiters('<span class="error">',
			'</span>');

		if ($this->form_validation->run('recover_password') === FALSE)
		{
			$params = array(	'title' =>
									$this->lang->line(
										'user_title_password_recovery')
										.' &ndash; '
										. $this->config->item('site_name'),
								//'metas' => array('description'=>'')
			);
			$this->load->library('html_head_params', $params);
				
			// **
			// ** LOADING VIEWS
			// **
			$this->load->view('html_begin', $this->html_head_params);
			$this->load->view('header', array('selected_menu' => 
					'recover_password'));

			$main_params['content'] = $this->load->view(
				'user/recover_password_view', array(),
				TRUE);
			
			$main_params['side'] = $this->load->view('side_default', NULL, TRUE);
			$this->load->view('main', $main_params);
				
			$this->load->view('footer');
			$this->load->view('html_end');
		}
		else
		{
			// Resent message
			$this->load->helper('message');
			show_info_msg_page($this, sprintf(
					$this->lang->line('user_msg_password_recovery_email_sent'),
					$this->input->post('username'),
					$this->input->post('email')));
			return;
		}
	}
	
	public function _format_message($msg, $val = '', $sub = '%s')
	{
		return str_replace($sub, $val, $this->lang->line($msg));
	}
	
	public function _update_session_userdata($data)
	{
		foreach ($data as $key=> $val)
			$this->session->set_userdata($key, $val);
	}
	
	public function _valid_username($username)
	{
		return (preg_match('/^[a-z0-9\._]+$/', $username) === 1);
	}

	public function _valid_username_or_email($username)
	{
		$this->load->helper('email');

		if (valid_email($username))
			return TRUE;
		else
			return $this->_valid_username($username);
	}
	
	public function _valid_date($date)
	{
		if (! $date)
			return TRUE;
		
		return (preg_match('/[\d]{4}-[\d]{2}-[\d]{2}/', $date) === 1);
	}
	
	public function _postprocess_birth_date($date)
	{
		// If the user entered no birth date NULL needs to be inserted into DB.
		if (! $date)
			return NULL;
		
		return $date;
	}
	
	public function _valid_old_password($old_password, $field_username)
	{
		if (! $old_password)
			return TRUE;
		
		$username= $this->input->post($field_username);
		
		if ($this->users_model->login($username, $old_password))
			return TRUE;
		
		return FALSE;
	}
	
	public function _change_password_cond($param)
	{
		$old = $this->input->post('old-password');
		$new = $this->input->post('new-password');
		$newc = $this->input->post('new-password-confirmation');
		
		return (!$old && !$new && !$newc)
			|| ($old && $new && $newc);
	}
	
	public function _required_by_register($param)
	{
		$user_id = $this->input->post('user-id');
		
		if (! $user_id && ! $param)
			return FALSE;
		
		return TRUE;
	}
	
	public function _valid_activation_code($activation_code)
	{
		return (preg_match('/^[a-fA-F0-9]{16}$/', $activation_code) == 1);
	}

	public function _do_login($username, $field_password)
	{
		$password = $this->input->post($field_password);

		$user = $this->users_model->login($username, $password);

		// Authentication failed.
		if ($user === FALSE)
			return FALSE;
		
		// User has not activated the account.
		if ($user['activation_code'] !== NULL)
		{
			$this->activated_account = FALSE;
			$this->user_id = $user['id'];
			return TRUE;
		}
		
		// Authentication successful: set session with user data.
		$this->session->set_userdata(array(
			'user_id'=> $user['id'],
			'username'=> $user['username'],
			'auth_src'=> $user['auth_src'],
			'time_zone'=> $user['time_zone']
		));
		$this->import = (isset($user['import']) ? $user['import'] : FALSE);
		return TRUE;
	}
	
	public function _do_activate($activation_code)
	{
		$user_id = $this->input->post('user-id');
		if ($user_id === FALSE)
			return FALSE;
		$user_id = intval($user_id);
		
		return $this->users_model->activate_account($user_id,
				$activation_code);
	}
	
	public function _do_resend_activation($email)
	{
		$user_id = $this->input->post('user-id');
		if ($user_id === FALSE)
			return FALSE;
		$user_id = intval($user_id);
		
		$this->users_model->set_userdata($user_id,
			array('email'=> $email));
		
		return $this->users_model->send_activation_email($user_id, $email);
	}
	
	public function _username_exists($username)
	{
		$userdata = $this->users_model->get_userdata($username);
		
		if (! $userdata)
			return FALSE;
		
		return TRUE;
	}
	
	public function _internal_account($username)
	{
		$userdata = $this->users_model->get_userdata($username, 'auth_src');
		if (! $userdata)
			return FALSE;

		if ($userdata['auth_src'] != 'internal')
			return FALSE;
		
		return TRUE;
	}
	
	public function _do_recover_password($username)
	{
		$email = $this->input->post('email');
		if (! $email)
			return FALSE;
		
		return $this->users_model->recover_password($username, $email);
	}
}

/* End of file user.php */
/* Location: ./application/controllers/user.php */
