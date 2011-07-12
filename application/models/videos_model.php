<?php

/**
 * Class Videos_model models videos information from the DB
 *
 * @category	Model
 * @author		Călin-Andrei Burloiu
 */
class Videos_model extends CI_Model {
	private $db = NULL;
	
	function __construct()
	{
		if ($this->db === NULL)
		{
			$this->load->library('singleton_db');
			$this->db = $this->singleton_db->connect();
		}
	}
	
	function getVideosSummary()
	{
		return $this->db->get('videos');
	}
	
	function getVideo($id, $name = NULL)
	{
		return $this->db->query('SELECT * from videos WHERE id = ?', $id);
	}
}

/* End of file videos_model.php */
/* Location: ./application/models/videos_model.php */
