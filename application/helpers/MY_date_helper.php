<?php  if ( ! defined('BASEPATH')) exit('No direct script access allowed');

function human_gmt_to_human_local($human_gmt, $time_zone = NULL)
{
	if (! $time_zone)
		$time_zone = 'UTC';
	
	return date('Y-m-d H:i:s',  
			gmt_to_local(
				strtotime($human_gmt), 
				$time_zone, 
				TRUE)) . ($time_zone == 'UTC' ? ' (UTC)' : '');
}

/* End of file MY_date_helper.php */
/* Location: ./application/helpers/MY_date_helper.php */