<?php

/**
 * Class Users_model models user information from DB
 * 
 * @category 	Model
 * @author 		calinburloiu
 *
 */
class Users_model extends CI_Model {
	public $db = NULL;

	public function __construct()
	{
		parent::__construct();

		if ($this->db === NULL)
		{
			$this->load->library('singleton_db');
			$this->db = $this->singleton_db->connect();
		}
	}

	/**
	 * Check authentication credentials. $username can be username or e-mail.
	 * 
	 * @param string $username
	 * @param string $password
	 */
	public function login($username, $password)
	{
		$this->load->helper('email');
		
		// User logs with e-mail address.
		if (! valid_email($username))
			$cond_user = "username = '$username'";
		else
			$cond_user = "email = '$username'";
		
		$enc_password = sha1($password);
		
		// TODO select only required fields.
		$query = $this->db->query("SELECT * FROM `users` 
			WHERE $cond_user AND password = '$enc_password'");
		
		if ($query->num_rows() !== 1)
			return FALSE;

		return $query->row_array();
	}
}

/* End of file users_model.php */
/* Location: ./application/models/users_model.php */
