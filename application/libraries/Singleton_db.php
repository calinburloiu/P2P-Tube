<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed'); 

/**
 * Library Singleton_db implements a factory that retrieves a single instance
 * of a Database object for the whole CodeIgniter application.
 * 
 * This avoids opening multiple connections to the same database and ensures
 * that you obtain a Database object only when you need it.
 *
 * @category	Controller
 * @author		Călin-Andrei Burloiu
 */
class Singleton_db {
	
	private static $db;
	
	function __construct()
	{
	}
	
	public static function connect()
	{
		if(!isset(self::$db))
		{
			$CI = & get_instance();
			
			self::$db = $CI->load->database('default', TRUE);
		}
		
		return self::$db;
	}
}

/* End of file Singleton_db.php */
/* Location: ./application/libraries/Singleton_db.php */
