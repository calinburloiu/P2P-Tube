<?php

/**
 * Class User controls video hierarchy and searching
 *
 * @category	Controller
 * @author		Călin-Andrei Burloiu
 */
class User extends CI_Controller {

	private $import = FALSE;

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
			if (! $this->import)
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
				$userdata = $this->users_model->get_userdata($user_id);
			}
			else
			{
				$userdata = FALSE;
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
			$this->load->view('header', array('selected_menu' => 'register'));
			
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
			
			// Edit account data
			if ($user_id)
			{
				$password = $this->input->post('new-password');
				if ($password)
					$data['password'] = $this->input->post('new-password');
				
				$this->users_model->set_userdata($user_id, $data);
			}
			// Registration
			else
			{
				$data['username'] = $this->input->post('username');
				$data['password'] = $this->input->post('password');
				
				$this->users_model->register($data);
			}
			
			// Redirect to last page before login.
			header('Location: '. site_url(urldecode_segments($redirect)));
		}
	}
	
	public function account($redirect = '')
	{
		$this->register($redirect);
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

	public function _do_login($username, $field_password)
	{
		$password = $this->input->post($field_password);

		$user = $this->users_model->login($username, $password);

		// Authentication failed.
		if ($user === FALSE)
			return FALSE;
		
		// Authentication successful: set session with user data.
		$this->session->set_userdata(array(
			'user_id'=> $user['id'],
			'username'=> $user['username'],
			'auth_src'=> $user['auth_src']
		));
		$this->import = $user['import'];
		return TRUE;
	}
}

/* End of file user.php */
/* Location: ./application/controllers/user.php */
