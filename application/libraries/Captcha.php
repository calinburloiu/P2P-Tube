<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed'); 

class Captcha {
	
	private $ci = NULL;
	private $db;
	private $params = NULL;
	
	public function __construct()
	{
		$this->ci =& get_instance();
		$this->ci->config->load('captcha');
		$this->ci->load->library('Singleton_db');
		$this->db = $this->ci->singleton_db->connect();
		
		$this->params = $this->ci->config->item('captcha_params');
		
		if (!$this->params)
			die('Cannot load CAPTCHA config file.');
	}
	
	/**
	 * Generates a CAPTCHA image and returns an HTML image tag for it.
	 * 
	 * @param string $word
	 * @return string
	 */
	public function get_captcha_tag($word = NULL)
	{
		$this->load->helper('captcha');
		
		if ($word)
			$this->params['word'] = $word;

		$cap = create_captcha($this->params);

		$data = array(
			'captcha_time' => $cap['time'],
			'ip_address' => $this->input->ip_address(),
			'word' => $cap['word']
			);

		$str_query = $this->db->insert_string('captcha', $data);
		$this->db->query($str_query);

		return $cap['image'];
	}
	
	/**
	 * Check againt the DB if the word(s) entered by the user ($word) matches
	 * the CAPTCHA and if the CAPTCHA did not expired.
	 */
	public function check_captcha($word)
	{
		// First, delete old captchas
		$expiration_limit = (!$this->params['expiration']
				? 7200 : $this->params['expiration']);
		$expiration = time() - $expiration_limit; // Two hour limit
		$this->db->query("DELETE FROM captcha WHERE captcha_time < ".$expiration);
		// TODO also delete the CAPTCHA image file

		// Then see if a captcha exists:
		$sql = "SELECT COUNT(*) AS count FROM captcha WHERE word = ? AND ip_address = ? AND captcha_time > ?";
		$binds = array($word, $this->input->ip_address(), $expiration);
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
