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
	 * @return mixed can return FALSE if authentication failed, a DB row as an
	 * associative array if authentication was succesful or an associative
	 * array with LDAP user information if authentication with LDAP was
	 * successful but the user logged in for the first time and it does not
	 * have an entry in `users` table yet. The key 'auth_src' distinguishes
	 * which associative array was returned:
	 * <ul>
	 *   <li>'internal' or 'ldap': a DB row</li>
	 *   <li>'ldap_first_time': LDAP user information</li>
	 * </ul>
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
			WHERE $cond_user
				AND (auth_src = 'ldap' OR password = '$enc_password')");
		
		// It is possible that the user has a LDAP account but he's
		// authenticating here for the first time so it does not have an entry
		// in `users` table.
		if ($query->num_rows() !== 1)
		{
			$ldap_userdata = $this->ldap_login($username, $password);
			$userdata = $this->convert_ldap_userdata($ldap_userdata);
			$this->register($userdata);
			
			$user = $this->login($username, $password);
			$user['import'] = TRUE;
			return $user;
			
			/* foreach ($ldap_userdata as $k => $v)
			{
				echo "<h1>$k</h1>";
				print_r($v);
			}
			die(); */
		}
		
		$user = $query->row_array();
		
		// Authenticate with LDAP.
		if ($user['auth_src'] == 'ldap')
			return ($this->ldap_login($username, $password) !== FALSE 
				? $user : FALSE);
		
		// If we are here internal authentication has successful.
		return $user;
	}
	
	/**
	 * Converts an array returned by LDAP login to an array which contains
	 * user data ready to be used in `users` DB.
	 * 
	 * @param array $ldap_userdata
	 * @return array
	 */
	public function convert_ldap_userdata($ldap_userdata)
	{
		$userdata['username'] = $ldap_userdata['uid'][0];
		$userdata['email'] = $ldap_userdata['mail'][0];
		$userdata['first_name'] = $ldap_userdata['givenname'][0];
		$userdata['last_name'] = $ldap_userdata['sn'][0];
		
		$userdata['auth_src'] = 'ldap';
		
		return $userdata;
	}
	
	/**
	* Login with LDAP.
	*
	* @param string	$username
	* @param string $password
	* @return boolean
	* @author  Alex Herișanu, Răzvan Deaconescu, Călin-Andrei Burloiu
	*/
	public function ldap_login($username, $password)
	{
		$this->config->load('ldap');
		
		// First connection: binding.
		// TODO exception
		$ds = ldap_connect($this->config->item('ldap_server')) or die("Can't connect to ldap server.\n");
		if (!@ldap_bind($ds, $this->config->item('ldap_bind_user'),
			$this->config->item('ldap_bind_password'))) 
		{
			ldap_close($ds);
			die("Can't connect to ".$this->config->item('ldap_server')."\n");
			return FALSE;
		}
		$sr = ldap_search($ds, "dc=cs,dc=curs,dc=pub,dc=ro", "(uid=" . $username . ")");
		if (ldap_count_entries($ds, $sr) > 1)
		die("Multiple entries with the same uid in LDAP database??");
		if (ldap_count_entries($ds, $sr) < 1) {
			ldap_close($ds);
			return FALSE;
		}
		
		$info = ldap_get_entries($ds, $sr);
		$dn = $info[0]["dn"];
		ldap_close($ds);
		
		// Second connection: connect with user's credentials.
		$ds = ldap_connect($this->config->item('ldap_server')) or die("Can't connect to ldap server\n");
		if (!@ldap_bind($ds, $dn, $password) or $password == '') {
			ldap_close($ds);
			return FALSE;
		}
		
		// Verifify if DN belongs to the requested OU.
		$info[0]['ou_ok'] = $this->ldap_dn_belongs_ou( $dn, $this->config->item('ldap_req_ou') );
		
		// Set authentication source.
		$info[0]['auth_src'] = 'ldap_first_time';
		
		return $info[0];
	}
	
	/**
	* Verify if a user belongs to a group.
	* 
	* @param string $dn = "ou=Student,ou=People..."
	* @param array $ou = array ("Student", etc
	* @return TRUE or FALSE
	* @author  Răzvan Herișanu, Răzvan Deaconescu, Călin-Andrei Burloiu
	*/
	public function ldap_dn_belongs_ou($dn, $ou)
	{
		if (!is_array($ou))
			$ou = array ($ou);
		
		$founded = FALSE;
		$words = explode(',', $dn);
		foreach ($words as $c) {
			$parts = explode("=", $c);
			$key = $parts[0];
			$value = $parts[1];
		
			if (strtolower($key) == "ou" && in_array($value, $ou) )
				$founded = TRUE;
		}
		
		return $founded;
	}
	
	/**
	 * Adds a new user to DB.
	 * 
	 * @param array $data	corresponds to DB columns
	 */
	public function register($data)
	{
		$this->load->helper('array');
		
		// TODO verify mandatory data existance
		
		// Process data.
		if (isset($data['password']))
			$data['password'] = sha1($data['password']);
		// TODO picture data: save, convert, make it thumbnail
		
		$cols = '';
		$vals = '';
		foreach ($data as $col=> $val)
		{
			$cols .= "$col, ";
			if (is_int($val))
				$vals .= "$val, ";
			else
				$vals .= "'$val', ";
		}
		$cols = substr($cols, 0, -2);
		$vals = substr($vals, 0, -2);
		
		$query = $this->db->query("INSERT INTO `users`
			($cols)
			VALUES ($vals)");
		
		// TODO exception on failure
		return $query;
	}
	
	/**
	 * Returns data from `users` table for user with $user_id.
	 * 
	 * @param int $user_id
	 */
	public function get_userdata($user_id)
	{
		$query = $this->db->query("SELECT * from `users`
			WHERE id = $user_id");
		
		if ($query->num_rows() === 0)
			return FALSE;
		
		return $query->row_array();
	}
	
	/**
	 * Modifies data from `users` table for user with $user_id.
	 * 
	 * @param int $user_id
	 * @param array $data	key-value pairs with columns and new values to be
	 * modified
	 */
	public function set_userdata($user_id, $data)
	{
		// TODO verify mandatory data existance
		
		// Process data.
		if (isset($data['password']))
			$data['password'] = sha1($data['password']);
		// TODO picture data: save, convert, make it thumbnail
		
		$set = '';
		foreach ($data as $col => $val)
		{
			if (is_int($val))
				$set .= "$col = $val, ";
			else
				$set .= "$col = '$val', ";
		}
		$set = substr($set, 0, -2);
		
		$query_str = "UPDATE `users`
			SET $set WHERE id = $user_id";
		//echo "<p>$query_str</p>";
		$query = $this->db->query($query_str);
		
		// TODO exception
		return $query;
	}
}

/* End of file users_model.php */
/* Location: ./application/models/users_model.php */
