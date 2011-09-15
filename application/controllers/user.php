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

	public function __construct()
	{
		parent::__construct();

		$this->lang->load('user');
	}

	public function index()
	{
	}

	public function login()
	{
		$this->load->library('form_validation');
		$this->load->model('users_model');

		$username = $this->input->post('username');
		$password = $this->input->post('password');
			
		$form_validation_config = array(
		array(
				'field'=>'username',
				'label'=>'lang:user_username_or_email',
				'rules'=>'trim|required|min_length[5]|max_length[32]'
		. '|strtolower|callback__valid_username'
		. '|callback__do_login[password]'
		),
		array(
				'field'=>'password',
				'label'=>'lang:user_password',
				'rules'=>'required|min_length[5]|max_length[32]'
		)
		);
		$this->form_validation->set_rules($form_validation_config);
		$this->form_validation->set_error_delimiters('<span class="error">',
			'</span>');

		if ($this->form_validation->run() === FALSE)
		{
			$params = array(	'title' => $this->config->item('site_name'),
										'css' => array(
											'catalog.css'
			),
			//'js' => array(),
			//'metas' => array('description'=>'')
			);
			$this->load->library('html_head_params', $params);
				
			// **
			// ** LOADING VIEWS
			// **
			$this->load->view('html_begin', $this->html_head_params);
			$this->load->view('header', array('selected_menu' => 'login'));
				
			$this->load->view('user/login_view', array());
				
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
			}
			
			header('Location: '. site_url());
			return;
		}
	}

	public function _valid_username($username)
	{
		$this->load->helper('email');

		if (valid_email($username))
		return TRUE;
		else
		return (preg_match('/^[a-z0-9\._]+$/', $username) == 1);
	}

	public function _do_login($username, $field_password)
	{
		$password = $this->input->post('password');

		$this->load->model('users_model');
		$res_login = $this->users_model->login($username, $password);

		// First authentication of a user with LDAP, i.e. the user does not
		// have an user_id in `users` DB table yet.
		if ($res_login === TRUE)
			return TRUE;
		// Authentication failed
		else if ($res_login === FALSE)
			return FALSE;
		
		// Authentication when the user has an user_id in the DB.
		$this->username = $res_login['username'];
		$this->email = $res_login['email'];
		$this->user_id = $res_login['id'];

		return TRUE;
	}
}

/* End of file user.php */
/* Location: ./application/controllers/user.php */
