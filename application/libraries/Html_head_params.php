<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed'); 

/**
 * Library HTML_head_params contains HTML information that is going to be 
 * included inside the head tag like: title, stylesheets, scripts and meta
 * information.
 *
 * The constructor automatically adds the autoload-configured CSSs and JSs 
 * if any from "application/config/${site_config}.php" so they don't have to be
 * added manually. The configuration parameters are:
 * 'autoload_css', 'autoload_js'.
 *
 * The variables are passed as data in 'application/views/html_begin.php' which
 * is going to generate the tags based on their information.
 *
 * All .css files must be located in 'css' and all .js file in
 * 'js'.
 *
 * @category	Library
 * @author		Călin-Andrei Burloiu
 */
class Html_head_params {
	public $title;
	// List of .css files
	public $css;
	// List of .js files
	public $js;
	// Dictionary for meta tags: name => content
	public $metas;
	
	protected $site_config = 'p2p-tube';
	
	/**
	 * Initializes member variables with the parameters provided and adds the
	 * default stylesheet to member $css and the default script to
	 * member $js. The URL prefixes are also added to filenames.
	 *
	 * Do not add in the parameters list the default stylesheet and script!
	 *
	 * @access		public
	 * @param		array $params	asscociative list with the following parameters:
	 *   * 'title' => HTML title tag content (page title)
	 *   * 'css' => list of .css files without any path
	 *   * 'js' => list of .js files without any path
	 *   * 'metas' => associative list of "name => content" meta
	 */
	public function __construct($params)
	{
		$CI =& get_instance();
		
		if (isset($this->site_config))
			$CI->load->config($this->site_config);
		else
		{ /* TODO: no site config*/ }
		
		if (isset($params['title']))
			$this->title = $params['title'];
		else
			$this->title = '';
			
		if (isset($params['css']))
			$this->css = $params['css'];
		else
			$this->css = array();
			
		if (isset($params['js']))
			$this->js = $params['js'];
		else
			$this->js = array();
		
		if (isset($params['metas']))
			$this->metas = $params['metas'];
		else
			$this->metas = array();
			
		// Default parameters from configuration file
		$this->css = array_merge(
			$CI->config->item('autoload_css'), $this->css);
		$this->js = array_merge(
			$CI->config->item('autoload_js'), $this->js);
		
		// URL correct prefixes
		foreach ($this->css as $i => $val)
			$this->css[$i] = site_url("css/$val");
		foreach ($this->js as $i => $val)
			$this->js[$i] = site_url("js/$val");
	}	
}

/* End of file HTML_head_params.php */
/* Location: ./application/libraries/HTML_head_params.php */
