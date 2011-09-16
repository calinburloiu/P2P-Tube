<?php

/**
 * Class User controls video hierarchy and searching
 *
 * @category	Controller
 * @author		Călin-Andrei Burloiu
 */
class User extends CI_Controller {

	private $username = NULL;
	private $email = NULL;
	private $user_id = NULL;
	private $ldap_user_info = NULL;

	public function __construct()
	{
		parent::__construct();

		$this->lang->load('user');
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
		$this->load->model('users_model');
			
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
				
			$this->load->view('user/login_view', array(
				'redirect'=> $redirect
			));
				
			$this->load->view('footer');
			$this->load->view('html_end');
		}
		else
		{
			if ($this->user_id !== NULL)
			{
				$this->session->set_userdata(array(
					'user_id'=> $this->user_id,
					'username'=> $this->username
				));
				
				// Redirect to last page before login. 
				header('Location: '. site_url(urldecode_segments($redirect)));
			}
			else
			{
				$this->session->set_userdata(array(
					'username'=> $this->username
				));
				
				// Redirect to register page because an user authenticates here
				// for the first time with LDAP.
				// TODO
				header('Location: '. site_url(urldecode_segments($redirect)));
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
		$this->load->model('users_model');
		$this->load->helper('localization');
		$this->load->helper('date');
			
		$this->form_validation->set_error_delimiters('<span class="error">',
					'</span>');
		
		if ($this->form_validation->run('register') === FALSE)
		{
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
		
			$this->load->view('user/register_view', array(
				'redirect'=> $redirect
			));
		
			$this->load->view('footer');
			$this->load->view('html_end');
		}
		else
		{
			if ($this->user_id !== NULL)
			{
				$this->session->set_userdata(array(
							'user_id'=> $this->user_id,
							'username'=> $this->username
				));
		
				// Redirect to last page before login.
				header('Location: '. site_url(urldecode_segments($redirect)));
			}
			else
			{
				$this->session->set_userdata(array(
							'username'=> $this->username
				));
		
				// Redirect to register page because an user authenticates here
				// for the first time with LDAP.
				// TODO
				header('Location: '. site_url(urldecode_segments($redirect)));
			}
		}
	}
	
	public function _valid_username($username)
	{
		return (preg_match('/^[a-z0-9\._]+$/', $username) == 1);
	}

	public function _valid_username_or_email($username)
	{
		$this->load->helper('email');

		if (valid_email($username))
			return TRUE;
		else
			return $this->_valid_username($username);
	}

	public function _do_login($username, $field_password)
	{
		$password = $this->input->post('password');

		$this->load->model('users_model');
		$user = $this->users_model->login($username, $password);

		// Authentication failed
		if ($user === FALSE)
			return FALSE;
		
		// First authentication of a user with LDAP, i.e. the user does not
		// have an user_id in `users` DB table yet.
		if ($user['auth_src'] == 'ldap_first_time')
		{
			$this->ldap_user_info = $user;
			$this->username = $user['uid'][0];
			$this->email = $user['mail'][0];
			return TRUE;
		}
		
		// Authentication when the user has an user_id in the DB.
		$this->username = $user['username'];
		$this->email = $user['email'];
		$this->user_id = $user['id'];
		
		return TRUE;
	}
}

/* End of file user.php */
/* Location: ./application/controllers/user.php */
