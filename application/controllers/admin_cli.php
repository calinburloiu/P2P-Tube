<?php

/**
 * Class Admin_cli controls site administration features
 *
 * @category	Controller
 * @author		Călin-Andrei Burloiu
 */
class Admin_cli extends CI_Controller {
	
	public function __construct()
	{
		parent::__construct();
		
		if (!$this->input->is_cli_request())
		{
			die("This controller is allowed only from CLI!");
		}
	}
	
	public function index()
	{		
	}
	
	/**
	 * Removes users that didn't activated their account within
	 * $days_to_expire days inclusively.
	 * 
	 * @param int $days_to_expire 
	 */
	public function cleanup_unactivated_users($days_to_expire = 2)
	{
		$days_to_expire = intval($days_to_expire);
		
		$this->load->model('users_model');
		
		if ($this->users_model->cleanup_unactivated_users($days_to_expire))
			echo "Users unactivated within $days_to_expire days were successfully deleted from the database.".PHP_EOL;
		else
			echo "No users were deleted.".PHP_EOL;
	}
}

/* End of file admin_cli.php */
/* Location: ./application/controllers/admin_cli.php */
