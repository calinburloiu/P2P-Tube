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

		if ($this->form_validation->run('signin') === FALSE)
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
		
		if ($this->form_validation->run('register') === FALSE)
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
				array('userdata'=> $userdata, 'redirect'=> $redirect),
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
		if (! $time_zone)
			$time_zone = 'UTC';
		
		// User data
		$userdata = $this->users_model->get_userdata($username);
		$userdata['roles'] = Users_model::roles_to_string($userdata['roles']);
		$country_list = $this->config->item('country_list');
		$userdata['country_name'] = $country_list[ $userdata['country'] ];
		$userdata['last_login'] = date('Y-m-d H:i:s',  
			gmt_to_local(
				strtotime($userdata['last_login']), 
				$time_zone, 
				TRUE)) . ($time_zone == 'UTC' ? ' (UTC)' : '');
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
		$userdata = $this->users_model->get_userdata($user_id,
				'email, a.activation_code');
		$email = $userdata['email'];
		//print_r($userdata['activation_code']);
		$activated_account = ($userdata['activation_code'] == NULL);
		
		$this->load->library('form_validation');
			
		$this->form_validation->set_error_delimiters('<span class="error">',
					'</span>');
		
		$res_form_validation = FALSE;
		if ($method == 'code')
		{
			$res_form_validation = $this->form_validation->run('activate');
		}
		else if ($method == 'resend')
		{
			$res_form_validation = 
					$this->form_validation->run('resend_activation');
		}
		
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

			if (! $activated_account)
			{
				$main_params['content'] = 
					$this->load->view('user/activate_view',
					array('user_id'=> $user_id, 'email'=> $userdata['email']),
					TRUE);
			}
			else
			{
				$main_params['content'] =
					$this->load->view('user/activated_account_view',
					NULL, TRUE);
			}
			
			$main_params['side'] = $this->load->view('side_default', NULL, TRUE);
			$this->load->view('main', $main_params);
		
			$this->load->view('footer');
			$this->load->view('html_end');
		}
		else
		{
			if ($method == 'code')
			{
				// Redirect to a message which tells the user that the
				// activation was successful.
				header('Location: '. site_url("user/activate/$user_id"));
			}
			else if ($method == 'resend')
			{
				// Redirect to home page
				header('Location: '. site_url());
			}
		}
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
		return FALSE;
	}
}

/* End of file user.php */
/* Location: ./application/controllers/user.php */
