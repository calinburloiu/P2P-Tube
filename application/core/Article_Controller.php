<?php  if ( ! defined('BASEPATH')) exit('No direct script access allowed');
/**
 * Library Article_Controller can be extended by a controller to be used for 
 * content pages that depend on the language.
 *
 * The page views are usually located in
 * "application/views/article/$language/$method".
 * Parameters:
 * <ul>
 * 	<li><strong>Article Title:</strong> in language file 'article_lang.php':
 * an entry named "article_$method".
 * If not present "$method" is used as a name.</li>
 * 	<li><strong>Article Meta Description:</strong> in language file..:
 * an entry "article_${method}_description"</li>
 * 	<li><strong>Helpers, Libraries:</strong> in config file 'article.php':
 * an entry named "article_${method}_helpers" or "article_${method}_libraries"
 * respectively with an array of helpers or libraries to be loaded for the
 * article.</li>
 * <li><strong>CSSs, JSs:</strong> in config file 'article.php':
 * an entry named "article_${method}_css" or "article_${method}_js"
 * respectively with an array of .css or .js to be loaded into members $css
 * and $js. It's up to the programmer to define how this members are going
 * to be used.</li>
 * </ul> 
 *
 * @category	Base Controller Library
 * @author		Călin-Andrei Burloiu
 */
class Article_Controller extends CI_Controller {
	
	protected $title = NULL;
	protected $metaDescription = NULL;
	protected $helpers = array();
	protected $libraries = array();
	protected $css = array();
	protected $js = array();
	
	function __construct()
	{
		parent::__construct();
		
		// Language, title and description
		$this->lang->load('article');
		
		// Helpers and libraries.
		$this->config->load('article');
	}
	
	/**
	 * Extend this with site specific information (header, menus...) and call
	 * $this->_load which is a generic method that loads the article.
	 * Both parameters must be passed to $this->_load.
	 */
	public function _remap($method, $params = array())
	{
		// Title
		$this->title = $this->lang->line("article_$method");
		if ($this->title === FALSE)
			$this->title = $method;

		// Meta Description
		$this->metaDescription = $this->lang->line("article_${method}_description");
		if ($this->metaDescription === FALSE)
			$this->metaDescription = '';
		
		// Helpers
		$this->helpers = $this->config->item("article_${method}_helpers");
		if ($this->helpers !== FALSE)
			$this->load->helper($this->helpers);
		
		// Libraries
		$this->libraries = $this->config->item("article_${method}_library");
		if ($this->libraries !== FALSE)
			$this->load->library($libraries);
		
		// CSSs
		$css =& $this->config->item("article_${method}_css");
		if ($css !== FALSE)
			$this->css = $css;
		
		// JavaScripts
		$js =& $this->config->item("article_${method}_js");
		if ($js !== FALSE)
			$this->js = $js;
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
