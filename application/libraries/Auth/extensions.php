<?php

/**
 * CodeIgniter style sessions.
 * 
 * @package OpenID
 */
class Auth_Yadis_CISession extends Auth_Yadis_PHPSession {
	
	protected $ci = NULL;
	
	public function __construct()
	{
		$this->ci =& get_instance();
		$this->ci->load->library('session');
	}
	
    /**
     * Set a session key/value pair.
     *
     * @param string $name The name of the session key to add.
     * @param string $value The value to add to the session.
     */
    function set($name, $value)
    {
		$this->ci->session->set_userdata($name, $value);
    }

    /**
     * Get a key's value from the session.
     *
     * @param string $name The name of the key to retrieve.
     * @param string $default The optional value to return if the key
     * is not found in the session.
     * @return string $result The key's value in the session or
     * $default if it isn't found.
     */
    function get($name, $default=NULL)
    {
		$value = $this->ci->session->userdata($name);
        if ($value !== FALSE) 
		{
            return $value;
        }
		else
		{
            return $default;
        }
    }

    /**
     * Remove a key/value pair from the session.
     *
     * @param string $name The name of the key to remove.
     */
    function del($name)
    {
        $this->ci->session->unset_userdata($name);
    }

    /**
     * Return the contents of the session in array form.
     */
    function contents()
    {
        return $this->ci->session->all_userdata();
    }
}
?>
