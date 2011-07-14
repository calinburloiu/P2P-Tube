<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed'); 

/**
 * Library HTML_head_params contains HTML information that is going to be 
 * included inside the head tag like: title, stylesheets, scripts and meta
 * information.
 *
 * The constructor automatically adds the default stylesheet and default script
 * if any from 'application/config/p2p-tube.php' so they don't have to be added
 * manually.
 *
 * The variables are passed as data in 'application/views/html_begin.php' which
 * is going to generate the tags based on their information.
 *
 * All .css files must be located in 'stylesheets' and all .js file in
 * 'javascripts'.
 *
 * @category	Library
 * @author		Călin-Andrei Burloiu
 */
class Html_head_params {
	public $title;
	// List of .css files
	public $stylesheets;
	// List of .js files
	public $javascripts;
	// Dictionary for meta tags: name => content
	public $metas;
	
	/**
	 * Initializes member variables with the parameters provided and adds the
	 * default stylesheet to member $stylesheets and the default script to
	 * member $javascripts. The URL prefixes are also added to filenames.
	 *
	 * Do not add in the parameters list the default stylesheet and script!
	 *
	 * @access		public
	 * @param		array $params	asscociative list with the following parameters:
	 *   * 'title' => HTML title tag content (page title)
	 *   * 'stylesheets' => list of .css files without any path
	 *   * 'javascripts' => list of .js files without any path
	 *   * 'metas' => associative list of "name => content" meta
	 */
	public function __construct($params)
	{
		$CI =& get_instance();
		$CI->load->helper('url');
		$CI->load->config('p2p-tube');
		
		if (isset($params['title']))
			$this->title = $params['title'];
		else
			$this->title = '';
			
		if (isset($params['stylesheets']))
			$this->stylesheets = $params['stylesheets'];
		else
			$this->stylesheets = array();
			
		if (isset($params['javascripts']))
			$this->javascripts = $params['javascripts'];
		else
			$this->javascripts = array();
		
		if (isset($params['metas']))
			$this->metas = $params['metas'];
		else
			$this->metas = array();
			
		// Default parameters from configuration file
		if ($CI->config->item('default_stylesheet') != '')
			$this->stylesheets[] = $CI->config->item('default_stylesheet');
		if ($CI->config->item('default_javascript') != '')
			$this->javascripts[] = $CI->config->item('default_javascript');
		
		// URL correct prefixes
		foreach ($this->stylesheets as $i => $val)
			$this->stylesheets[$i] = site_url("stylesheets/$val");
		foreach ($this->javascripts as $i => $val)
			$this->javascripts[$i] = site_url("javascript/$val");
	}	
}

/* End of file HTML_head_params.php */
/* Location: ./application/libraries/HTML_head_params.php */
