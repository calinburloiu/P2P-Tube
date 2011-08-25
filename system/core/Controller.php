<?php  if ( ! defined('BASEPATH')) exit('No direct script access allowed');
/**
 * CodeIgniter
 *
 * An open source application development framework for PHP 5.1.6 or newer
 *
 * @package		CodeIgniter
 * @author		ExpressionEngine Dev Team
 * @copyright	Copyright (c) 2008 - 2011, EllisLab, Inc.
 * @license		http://codeigniter.com/user_guide/license.html
 * @link		http://codeigniter.com
 * @since		Version 1.0
 * @filesource
 */

// ------------------------------------------------------------------------

/**
 * CodeIgniter Application Controller Class
 *
 * This class object is the super class that every library in
 * CodeIgniter will be assigned to.
 *
 * @package		CodeIgniter
 * @subpackage	Libraries
 * @category	Libraries
 * @author		ExpressionEngine Dev Team
 * @link		http://codeigniter.com/user_guide/general/controllers.html
 */
class CI_Controller {
	
	// TODO remove development declarations (used for Eclipse)
// 	/**
// 	* @var CI_Config
// 	*/
// 	var $config;
// 	/**
// 	 * @var CI_DB_active_record
// 	 */
// 	var $db;
// 	/**
// 	 * @var CI_Email
// 	 */
// 	var $email;
// 	/**
// 	 * @var CI_Form_validation
// 	 */
// 	var $form_validation;
// 	/**
// 	 * @var CI_Input
// 	 */
// 	var $input;
// 	/**
// 	 * @var CI_Loader
// 	 */
// 	var $load;
// 	/**
// 	 * @var CI_Router
// 	 */
// 	var $router;
// 	/**
// 	 * @var CI_Session
// 	 */
// 	var $session;
// 	/**
// 	 * @var CI_Table
// 	 */
// 	var $table;
// 	/**
// 	 * @var CI_Unit_test
// 	 */
// 	var $unit;
// 	/**
// 	 * @var CI_URI
// 	 */
// 	var $uri;
// 	/**
// 	 * @var CI_Pagination
// 	 */
// 	var $pagination;
// 	 My declarations
// 	/**
// 	 * @var Videos_model
// 	 */
// 	var $videos_model;

	private static $instance;

	/**
	 * Constructor
	 */
	public function __construct()
	{
		self::$instance =& $this;
		
		// Assign all the class objects that were instantiated by the
		// bootstrap file (CodeIgniter.php) to local class variables
		// so that CI can run as one big super object.
		foreach (is_loaded() as $var => $class)
		{
			$this->$var =& load_class($class);
		}

		$this->load =& load_class('Loader', 'core');

		$this->load->_base_classes =& is_loaded();

		$this->load->_ci_autoloader();

		log_message('debug', "Controller Class Initialized");

	}

	public static function &get_instance()
	{
		return self::$instance;
	}
}
// END Controller class

/* End of file Controller.php */
/* Location: ./system/core/Controller.php */