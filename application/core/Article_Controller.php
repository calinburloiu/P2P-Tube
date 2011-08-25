<?php  if ( ! defined('BASEPATH')) exit('No direct script access allowed');
/**
 * Library Article_Controller can be extended by a controller to be used for 
 * content pages that depend on the language.
 *
 * Several language specific parameters can be coded in language files.
 * Non language specific parameters can be putted in config files.
 *
 * @category	Library
 * @author		Călin-Andrei Burloiu
 */
class Article_Controller extends CI_Controller {
	
	function __construct()
	{
		parent::__construct();
	}
	
	/**
	 * Override this with site specific information (header, menus...) and call
	 * $this->_load which is a generic method that loads the article.
	 * Both parameters must be passed to $this->_load.
	 */
	public function _remap($method, $params = array())
	{
		$this->load->view('echo', 
			array('output' => $this->_load($method, $params),
				'clear' => TRUE)
			);
	}
	
	/**
	 * Returns the article based on the language from
	 * "application/views/article/$language/$method".
	 * 
	 * @param	string $method	defines article name
	 * @param	array $params	odd elements are keys and even elements are
	 * their values (eg.: [0] => key, [1] => value etc.). This are going to
	 * be converted to an associative array that is passed to the view if 
	 * $assoc parameter is FALSE. Otherwise this parameter is already an
	 * associative array.
	 * @param	bool $assoc	states whether or not $params is associative
	 */
	public function _load($method, $params = array(), $assoc = FALSE)
	{
		if (! $assoc)
		{
			$alt = 0;
			$params_assoc = array();
			$prev_val = NULL;
			foreach ($params as $i => $val)
			{
				if ($alt == 0)
					$prev_val = $val;
				else if ($alt == 1)
					$params_assoc[$prev_val] = $val;
				
				$alt = ($alt + 1) % 2;
			}
		}
		else
			$params_assoc = $params;
		
		return $this->load->view('article/'. $this->config->item('language')
			. '/' . $method, $params_assoc, TRUE);
	}
}

/* End of file Article_Controller.php */
/* Location: ./application/core/Article_Controller.php */
