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
	 * @return mixed can return FALSE if authentication failed, a `users`DB row
	 * as an associative array if authentication was successful
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
		
		$query = $this->db->query("SELECT u.*, a.activation_code
			FROM `users` u LEFT JOIN `users_unactivated` a ON (u.id = a.user_id)
			WHERE $cond_user
				AND (auth_src = 'ldap' OR password = '$enc_password')");
		
		// It is possible that the user has a LDAP account but he's
		// authenticating here for the first time so it does not have an entry
		// in `users` table.
		if ($query->num_rows() === 0)
		{
			$ldap_userdata = $this->ldap_login($username, $password);
			if ($ldap_userdata === FALSE)
				return FALSE;
			$userdata = $this->convert_ldap_userdata($ldap_userdata);
			$this->register($userdata);
			
			$user = $this->login($username, $password);
			$user['import'] = TRUE;
			return $user;
		}
		
		$user = $query->row_array();
		
		// Authenticate with LDAP.
		if ($user['auth_src'] == 'ldap'
				&& ! $this->ldap_login($username, $password))
			return FALSE; 
		
		if (empty($user['username']) || empty($user['email'])
				|| empty($user['first_name']) || empty($user['last_name'])
				|| empty($user['country']))
			$user['import'] = TRUE;
		
		// Update last login time.
		$this->db->query("UPDATE `users`
			SET last_login = UTC_TIMESTAMP()
			WHERE username = '$username'");
		
		// If we are here internal authentication has successful.
		return $user;
	}
	
	/**
	 * Begin the OpenID login by redirecting user to the OP to authenticate.
	 * 
	 * @param string $openid 
	 */
	public function openid_begin_login($openid)
	{
		$this->lang->load('openid');
		$this->load->library('openid');

		$request_to = site_url('user/check_openid_login');

		$req = array('nickname');
		$opt = array('fullname', 'email', 'dob', 'country');
		$policy = site_url('user/openid_policy');

		$ax_attributes[] = Auth_OpenID_AX_AttrInfo::make(
				'http://axschema.org/contact/email', 1, TRUE);
		$ax_attributes[] = Auth_OpenID_AX_AttrInfo::make(
				'http://axschema.org/namePerson/first', 1, TRUE);
		$ax_attributes[] = Auth_OpenID_AX_AttrInfo::make(
				'http://axschema.org/namePerson/last', 1, TRUE);
		$ax_attributes[] = Auth_OpenID_AX_AttrInfo::make(
				'http://axschema.org/contact/country', 1, TRUE);

		$this->openid->set_request_to($request_to);
		$this->openid->set_trust_root(base_url());
		$this->openid->set_sreg(TRUE, $req, $opt, $policy);
		$this->openid->set_ax(TRUE, $ax_attributes);

		// Redirection to OP site will follow.
		$this->openid->authenticate($openid);
	}
	
	/**
	 * Finalize the OpenID login. Register user if is here for the first time.
	 * 
	 * @return mixed returns a `users` DB row as an associative array if
	 * authentication was successful or Auth_OpenID_CANCEL/_FAILURE if it was
	 * unsuccessful.
	 */
	public function openid_complete_login()
	{
		$this->lang->load('openid');
		$this->load->library('openid');
		
		$request_to = site_url('user/check_openid_login');
		$this->openid->set_request_to($request_to);

		$response = $this->openid->get_response();
		
		if ($response->status === Auth_OpenID_CANCEL
				|| $response->status === Auth_OpenID_FAILURE)
			return $response->status;

		// Auth_OpenID_SUCCESS
		$openid = $response->getDisplayIdentifier();
		//$esc_openid = htmlspecialchars($openid, ENT_QUOTES);

		// Get user_id to see if it's the first time the user logs in with
		// OpenID.
		$query = $this->db->query("SELECT * from `users_openid`
			WHERE openid_url = '$openid'");
		$import = FALSE;
		
		// First time with OpenID => register user
		if ($query->num_rows() === 0)
		{
			$user_id = $this->openid_register($response);
			$import = TRUE;
		}
		// Not first time with OpenID.
		else
			$user_id = $query->row()->user_id;
		
		// Login
		$query = $this->db->query("SELECT * FROM `users`
			WHERE id = $user_id");
		$userdata = $query->row_array();
		$userdata['import'] = $import;
		
		if (empty($userdata['username']) || empty($userdata['email'])
				|| empty($userdata['first_name'])
				|| empty($userdata['last_name'])
				|| empty($userdata['country']))
			$userdata['import'] = TRUE;
		
		// Update last login time.
		$this->db->query("UPDATE `users`
			SET last_login = UTC_TIMESTAMP()
			WHERE id = $user_id");

		return $userdata;		
	}
	
	/**
	 * Register an user that logged in with OpenID for the first time.
	 * 
	 * @param object $op_response object returned by Janrain 
	 * Consumer::complete method.
	 * @return mixed the user_id inserted or FALSE on error
	 */
	public function openid_register($op_response)
	{
		$sreg_resp = Auth_OpenID_SRegResponse::fromSuccessResponse($op_response);
		$ax_resp = Auth_OpenID_AX_FetchResponse::fromSuccessResponse($op_response);
		
		if ($ax_resp)
		{
			$ax_email = $ax_resp->get('http://axschema.org/contact/email');
			$ax_first_name = $ax_resp->get(
					'http://axschema.org/namePerson/first');
			$ax_last_name = $ax_resp->get('http://axschema.org/namePerson/last');
			$ax_country = $ax_resp->get('http://axschema.org/contact/country');
		}
		else
		{
			$ax_email = '';
			$ax_first_name = '';
			$ax_last_name = '';
			$ax_country = '';
		}
		
		if ($sreg_resp)
		{
			$sreg_email = $sreg_resp->get('email', '');
			$sreg_fullname = $sreg_resp->get('fullname', '');
			$sreg_nickname = $sreg_resp->get('nickname', '');
			$sreg_country = $sreg_resp->get('country', '');
			$sreg_dob = $sreg_resp->get('dob', NULL);
		}
		else
		{
			$sreg_email = $sreg_fullname = $sreg_nickname = $sreg_country = '';
			$sreg_dob = NULL;
		}

		// E-mail
		if (empty($ax_email) || is_a($ax_email, 'Auth_OpenID_AX_Error'))
			$data['email'] = $sreg_email;
		else
			$data['email'] = $ax_email[0];
		$data['email'] = strtolower($data['email']);
		
		// First Name
		if (empty($ax_first_name)
				|| is_a($ax_first_name, 'Auth_OpenID_AX_Error'))
			$data['first_name'] = '';
		else
			$data['first_name'] = $ax_first_name[0];
		
		// Sur Name
		if (empty($ax_last_name) || is_a($ax_last_name, 'Auth_OpenID_AX_Error'))
			$data['last_name'] = '';
		else
			$data['last_name'] = $ax_last_name[0];
		
		// First Name and Last Name
		if (empty($data['first_name']) || empty($data['last_name']))
		{
			if ($sreg_fullname)
			{
				if (empty($data['first_name']))
					$data['first_name'] = substr(
							$sreg_fullname, 0, strrpos($sreg_fullname, ' '));
				if (empty($data['last_name']))
					$data['last_name'] = substr(
							$sreg_fullname, strrpos($sreg_fullname, ' ') + 1);
			}
		}
		
		// Username
		$data['username'] = $sreg_nickname;
		if (!$data['username'])
		{
			// Generate username from email
			if (!empty($data['email']))
			{
				$data['username'] = substr($data['email'],
						0, strpos($data['email'], '@'));
				$data['username'] = preg_replace(array('/[^a-z0-9\._]*/'),
						array(''), $data['username']);
			}
			// Generate username from first name and sur name
			else if(!empty($data['first_name']) || !empty($data['last_name']))
			{
				$data['username'] = $data['first_name'] . '_'
						. $data['last_name'];
			}
			// Generate a random username
			else
				$data['username'] = $this->gen_username();
		}
		// Limit username to 24 characters because a prefix of 8 characters
		// will be added: 'autogen_'.
		$data['username'] = substr($data['username'], 0, 24);
		// Append a random character to the username each time it still exists.
		if ($this->get_userdata($data['username']))
		{
			$chars = 'abcdefghijklmnopqrstuvwxyz0123456789';
			$len_chars = strlen($chars);
			$data['username'] .= '_';
			do
			{
				$data['username'] .= $chars[ mt_rand(0, $len_chars - 1) ];
			} while($this->get_userdata($data['username']));
		}
		// Usernames autogenerated have 'autogen_' prefix and can be changed
		// by the user from the account section. After this process it cannot
		// be changed anymore.
		$data['username'] = 'autogen_' . $data['username'];
		
		// Country
		if (empty($ax_country) || is_a($ax_country, 'Auth_OpenID_AX_Error'))
			$data['country'] = $sreg_country;
		else
			$data['country'] = $ax_country[0];
		
		// Birth Date
		$data['birth_date'] = $sreg_dob;
		
		// OpenID
		$data['auth_src'] = 'openid';
		
		if (!$this->register($data, $op_response->getDisplayIdentifier()))
			return FALSE;
		
		$query = $this->db->query("SELECT id from `users`
			WHERE username = '{$data['username']}'");
		return $query->row()->id;
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
	 * Do not add join_date and last_login column, they will be automatically
	 * added.
	 * Provide an $openid with the OpenID as value in order to register users
	 * logging in this way.
	 * 
	 * @param array $data	corresponds to DB columns
	 */
	public function register($data, $openid = NULL)
	{
		$this->load->helper('array');
		
		// TODO verify mandatory data existance
		
		// Process data.
		if (isset($data['password']))
			$data['password'] = sha1($data['password']);
		
		if (empty($data['birth_date']))
			$data['birth_date'] = NULL;
		
		$cols = '';
		$vals = '';
		foreach ($data as $col=> $val)
		{
			if ($val === NULL)
			{
				$cols .= "$col, ";
				$vals .= "NULL, ";
				continue;
			}
				
			$cols .= "$col, ";
			if (is_int($val))
				$vals .= "$val, ";
			else if (is_string($val))
				$vals .= "'$val', ";
		}
		$cols = substr($cols, 0, -2);
		$vals = substr($vals, 0, -2);
		
		$query = $this->db->query("INSERT INTO `users`
			($cols, registration_date, last_login)
			VALUES ($vals, utc_timestamp(), utc_timestamp())");
		if ($query === FALSE)
			return FALSE;
		
		// If registered with OpenID insert a row in `users_openid`.
		if ($openid)
		{
			// Find user_id.
			$query = $this->db->query("SELECT id from `users`
				WHERE username = '{$data['username']}'");
			if ($query->num_rows() === 0)
				return FALSE;
			$user_id = $query->row()->id;
			
			// Insert row in `users_openid`.
			$query = $this->db->query("INSERT INTO `users_openid`
				(openid_url, user_id)
				VALUES ('$openid', $user_id)");
			if (!$query)
				return FALSE;
		}
		
		// If registered with internal authentication it needs to activate
		// the account.
		if ($data['auth_src'] == 'internal')
		{
			$activation_code = Users_model::gen_activation_code($data['username']);
			$user_id = $this->get_user_id($data['username']);
			$query = $this->db->query("INSERT INTO `users_unactivated`
				(user_id, activation_code)
				VALUES ($user_id, '$activation_code')");
			$this->send_activation_email($user_id, $data['email'],
				$activation_code, $data['username']);
		}
		
		// TODO exception on failure
		return $query;
	}
	
	public function get_user_id($username)
	{
		$query = $this->db->query("SELECT id FROM `users`
			WHERE username = '$username'");
		
		if ($query->num_rows() === 0)
			return FALSE;
		
		return $query->row()->id;
	}
	
	/**
	 * Removes users that didn't activated their account within $days_to_expire
	 * days inclusively.
	 * 
	 * @param int $days_to_expire 
	 */
	public function cleanup_unactivated_users($days_to_expire)
	{
		// Get user_id-s with expired activation period.
		$query = $this->db->query("SELECT u.id
			FROM `users` u, `users_unactivated` a
			WHERE u.id = a.user_id
				AND DATEDIFF(CURRENT_DATE(), u.registration_date) > $days_to_expire");
		
		if ($query->num_rows() > 0)
		{
			$str_user_ids = '';
			$results = $query->result();
			foreach ($results as $result)
				$str_user_ids .= "{$result->id}, ";
			$str_user_ids = substr($str_user_ids, 0, -2);
		}
		else
			return FALSE;
		
		// Delete from `users` table.
		$ret = $this->db->query("DELETE FROM `users`
			WHERE id IN ($str_user_ids)");
		if (!$ret)
			return FALSE;
		
		// Delete from `users_unactivated table.
		$ret = $this->db->query("DELETE FROM `users_unactivated`
			WHERE user_id IN ($str_user_ids)");
		if (!$ret)
			return FALSE;
		
		// Success
		return TRUE;
	}
	
	/**
	 * Activated an account for an user having $user_id with $activation_code.
	 * 
	 * @param int $user_id
	 * @param string $activation_code	hexa 16 characters string
	 * @return returns TRUE if activation was successful and FALSE otherwise
	 */
	public function activate_account($user_id, $activation_code)
	{
		$query = $this->db->query("SELECT * FROM `users_unactivated`
			WHERE user_id = $user_id
				AND activation_code = '$activation_code'");
		
		if ($query->num_rows() === 0)
			return FALSE;
		
		$this->db->query("DELETE FROM `users_unactivated`
			WHERE user_id = $user_id");
		
		return TRUE;
	}
	
	public function send_activation_email($user_id, $email = NULL,
			$activation_code = NULL, $username = NULL)
	{
		if (!$activation_code || !$email || !$username)
		{
			if (!$email)
				$cols = 'email, ';
			else
				$cols = '';
			
			$userdata = $this->get_userdata($user_id,
					$cols. "a.activation_code, username");
			$activation_code =& $userdata['activation_code'];
			
			if (!$email)
				$email =& $userdata['email'];
			$username =& $userdata['username'];
		}
		
		if ($activation_code === NULL)
			return TRUE;
		
		$subject = '['. $this->config->item('site_name')
				. '] Account Activation';
		$activation_url =
				site_url("user/activate/$user_id/code/$activation_code"); 
		$msg = sprintf($this->lang->line('user_activation_email_content'),
			$username, $this->config->item('site_name'), site_url(),
			$activation_url, $activation_code);
		$headers = "From: ". $this->config->item('noreply_email');
		
		return mail($email, $subject, $msg, $headers);
	}
	
	public function recover_password($username, $email)
	{
		$userdata = $this->get_userdata($username, 'email, username, id');
		
		if (strcmp($userdata['email'], $email) !== 0)
			return FALSE;
		
		$recovered_password = Users_model::gen_password();
		
		$this->set_userdata(intval($userdata['id']), array('password'=> 
				$recovered_password));
		
		$subject = '['. $this->config->item('site_name')
		. '] Password Recovery';
		$msg = sprintf($this->lang->line('user_password_recovery_email_content'),
			$username, $this->config->item('site_name'), site_url(),
			$recovered_password);
		$headers = "From: ". $this->config->item('noreply_email');
		
		mail($email, $subject, $msg, $headers);
		
		return TRUE;
	}
	
	/**
	 * Returns data from `users` table. If $user is int it is used as an
	 * id, if it is string it is used as an username.
	 * 
	 * @param mixed $user
	 * @param string $table_cols	(optional) string with comma separated
	 * `users` table column names. Use a.activation_code to check user's
	 * account activation_code. If this value is NULL than the account is
	 * active.
	 * @return array	associative array with userdata from DB
	 */
	public function get_userdata($user, $table_cols = '*')
	{
		if (is_int($user))
			$cond = "id = $user";
		else
			$cond = "username = '$user'";
		
		$query = $this->db->query("SELECT $table_cols
			FROM `users` u LEFT JOIN `users_unactivated` a
				ON (u.id = a.user_id)
			WHERE $cond");
		
		if ($query->num_rows() === 0)
			return FALSE;
		
		$userdata = $query->row_array();
		
		// Post process userdata.
		if (isset($userdata['picture']))
		{
			$userdata['picture_thumb'] = site_url(
				"data/user_pictures/{$userdata['picture']}-thumb.jpg");
			$userdata['picture'] = site_url(
				"data/user_pictures/{$userdata['picture']}");
		} 
		
		return $userdata;
	}
	
	/**
	 * Modifies data from `users` table for user with $user_id.
	 * 
	 * @param int $user_id
	 * @param array $data	key-value pairs with columns and new values to be
	 * modified
	 * @return boolean	returns TRUE on success and FALSE otherwise
	 */
	public function set_userdata($user_id, $data)
	{
		// TODO verify mandatory data existance
		
		// Process data.
		if (isset($data['password']))
			$data['password'] = sha1($data['password']);
		// TODO picture data: save, convert, make it thumbnail
		
		if (empty($data['birth_date']))
			$data['birth_date'] = NULL;
		
		$set = '';
		foreach ($data as $col => $val)
		{
			if ($val === NULL)
			{
				$set .= "$col = NULL, ";
				continue;
			}
			
			if (is_int($val))
				$set .= "$col = $val, ";
			else if (is_string($val))
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
	
	public static function gen_activation_code($str = '')
	{
		$ci =& get_instance();
		
		$activation_code = substr(
			sha1(''. $str. $ci->config->item('encryption_key')
				. mt_rand()),
			0,
			16);
		
		return $activation_code;
	}
	
	public static function gen_password()
	{
		$ci =& get_instance();
		$length = 16;
		$chars = '0123456789abcdefghijklmnopqrstuvwxyzABCDEFGHIJKLMNOPQRSTUVWXYZ,.?!_-';
		$len_chars = strlen($chars);
		$enc_key = $ci->config->item('encryption_key');
		$len_enc_key = strlen($enc_key);
		$password = '';
		
		for ($p = 0; $p < $length; $p++) 
		{
			$i = (mt_rand(1, 100) * ord($enc_key[ mt_rand(0, $len_enc_key-1) ]))
				% $len_chars;
			$password .= $chars[$i];
		}
		
		return $password;
	}
	
	public static function gen_username()
	{
		$chars = 'abcdefghijklmnopqrstuvwxyz0123456789._';
		$len_chars = strlen($chars);
		$len = 8;
		$username = '';
		
		for ($i = 0; $i < $len; $i++)
			$username .= $chars[ mt_rand(0, $len_chars - 1) ];
		
		return $username;
	}
	
	public static function roles_to_string($roles)
	{
		$ci =& get_instance();
		$ci->lang->load('user');
		
		if ($roles == USER_ROLE_STANDARD)
			return $ci->lang->line('user_role_standard');
		else
		{
			$str_roles = '';
			
			if ($roles & USER_ROLE_ADMIN)
				$str_roles .= $ci->lang->line('user_role_admin') . '; ';
		}
		
		return $str_roles;
	}
}

/* End of file users_model.php */
/* Location: ./application/models/users_model.php */
