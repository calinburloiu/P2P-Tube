<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed'); 

/**
 * A library which simplifies the insertion and verification of CATCHAs.
 * 
 * @author Călin-Andrei Burloiu
 * @category Library
 */
class Captcha {
	
	private $ci = NULL;
	private $db;
	private $params = NULL;
	
	public function __construct($params = NULL)
	{
		$this->ci =& get_instance();
		$this->ci->config->load('captcha');
		$this->ci->load->library('Singleton_db');
		$this->db = $this->ci->singleton_db->connect();
		
		// Configuration parameters.
		if (!$params)
		{
			$this->params = $this->ci->config->item('captcha_params');
		}
		else
			$this->params = $params;
		
		if (!$this->params)
			die('Cannot load CAPTCHA config file.');
	}
	
	public function get_params()
	{
		return $this->params;
	}

	public function set_params($params)
	{
		$this->params = $params;
	}

	/**
	 * Generates a CAPTCHA image and returns an array of associative data
	 * about the image.
	 * 
	 * @param string $word
	 * @return array
	 */
	public function get_captcha($word = NULL)
	{
		$this->ci->load->helper('captcha');
		
		if ($word)
			$this->params['captcha_params']['word'] = $word;

		// Creating the CAPTCHA.
		$cap = create_captcha($this->params['captcha_params']);

		$data = array(
			'captcha_time' => $cap['time'],
			'ip_address' => $this->ci->input->ip_address(),
			'word' => $cap['word']
			);

		// Remember in DB the CAPTCHA - user mapping.
		$str_query = $this->db->insert_string('captcha', $data);
		$this->db->query($str_query);

		return $cap;
	}
	
	/**
	 * Check againt the DB if the word(s) entered by the user ($word) matches
	 * the CAPTCHA and if the CAPTCHA did not expired.
	 * 
	 * @param string $word
	 * @return boolean
	 */
	public function check_captcha($word)
	{
		// First, delete old captchas
		$expiration_limit = (!$this->params['captcha_params']['expiration']
				? 7200 : $this->params['captcha_params']['expiration']);
		$expiration = time() - $expiration_limit; // Two hour limit
		$this->db->query("DELETE FROM captcha WHERE captcha_time < ".$expiration);
		// TODO also delete the CAPTCHA image file

		// Then see if a captcha exists:
		$sql = "SELECT COUNT(*) AS count FROM captcha WHERE word = ? AND ip_address = ? AND captcha_time > ?";
		$binds = array($word, $this->ci->input->ip_address(), $expiration);
		$query = $this->db->query($sql, $binds);
		$row = $query->row();

		if ($row->count == 0)
		{
			return FALSE;
		}
		
		return TRUE;
	}
}

/* End of file Captcha.php */
/* Location: ./application/libraries/Captcha.php */
