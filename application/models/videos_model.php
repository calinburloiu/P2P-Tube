<?php

/**
 * Class Videos_model models videos information from the DB
 *
 * @category	Model
 * @author		Călin-Andrei Burloiu
 */
class Videos_model extends CI_Model {
	public $db = NULL;
	
	public function __construct()
	{
		if ($this->db === NULL)
		{
			$this->load->library('singleton_db');
			$this->db = $this->singleton_db->connect();
		}
	}
	
	/**
	 * Retrieves information about a set of videos which are going to be
	 * displayed in the catalog.
	 *
	 * TODO: filter, limit, ordering parameters
	 * @param		int $category_id	DB category ID
	 * @param		int $offset
	 * @param		int $count
	 * @return		array	a list of videos, each one being an assoc array with:
	 * <ul>
	 *   <li>id, name, title, duration, thumbs_count, default_thumb, views => from DB</li>
	 *   <li>shorted_title => ellipsized title</li>
	 *   <li>video_url => P2P-Tube video URl</li>
	 *   <li>TODO: user_id, user_name</li>
	 *   <li>thumbs => thumbnail images' URLs</li>
	 * </ul>
	 */
	public function get_videos_summary($category_id, $offset, $count)
	{
		$this->load->helper('text');
		
		$query = $this->db->query(
			'SELECT id, name, title, duration, user_id, views, thumbs_count,
				default_thumb
			FROM `videos`
			WHERE category_id = ?
			ORDER BY name
			LIMIT ?, ?', // TODO summary order 
			array(intval($category_id), $offset, $count)); 
		
		if ($query->num_rows() > 0)
			$videos = $query->result_array();
		else
			return NULL;
		
		foreach ($videos as & $video)
		{
			// P2P-Tube Video URL
			$video['video_url'] = site_url(sprintf("watch/%d/%s",
				$video['id'], $video['name']));
			
			// Thumbnails
			$video['thumbs'] = $this->get_thumbs($video['name'], 
				$video['thumbs_count']);
				
			// Ellipsized title
			//$video['shorted_title'] = ellipsize($video['title'], 45, 0.75);
			$video['shorted_title'] = character_limiter($video['title'], 50);
			
			// TODO: user information
			$video['user_name'] = 'TODO';
		}
		
		return $videos;
	}
	
	public function get_videos_count($category_id)
	{
		$query = $this->db->query(
			'SELECT COUNT(*) count
			FROM `videos`
			WHERE category_id = ?',
			$category_id);
		
		if ($query->num_rows() > 0)
			return $query->row()->count;
		
		// Error
		return NULL;
	}
	
	/**
	 * Retrieves information about a video.
	 *
	 * If $name does not match with the video's `name` from the DB an error is
	 * marked in the key 'err'. If it's NULL it is ignored.
	 *
	 * @access		public
	 * @param		string $id	video's `id` column from `videos` DB table
	 * @param		string $name	video's `name` column from `videos` DB
	 * table. NULL means there is no name provided.
	 * @return		array	an associative list with information about a video
	 * with the following keys:
	 * <ul>
	 *   <li>all columns form DB with some exceptions that are overwritten or new</li>
	 *   <li>content is moved in assets</li>
	 *   <li>assets => list of associative lists where each one represents a</li>
	 * video asset having keys: "src", "res", "par" and "ext". Value of key
	 * "src" is the video torrent formated as
	 * {name}_{format}.{video_ext}.{default_torrent_ext}</li>
	 *   <li>user_name => TODO: user name from `users` table</li>
	 *   <li>category_title => a human-friendly category name</li>
	 *   <li>tags => associative list of "tag => score"</li>
	 *   <li>date => date and time when the video was created</li>
	 *   <li>thumbs => thumbnail images' URLs</li>
	 * </ul>
	 */
	public function get_video($id, $name = NULL)
	{
		$this->load->helper('video');
		
		$query = $this->db->query('SELECT * 
								FROM `videos` 
								WHERE id = ?', $id);
		$video = array();
		
		if ($query->num_rows() > 0)
		{
			$video = $query->row_array();
			if ($name !== NULL && $video['name'] != $name)
				$video['err'] = 'INVALID_NAME';
		}
		else
		{
			$video['err'] = 'INVALID_ID';
			return $video;
		}
		
		// Convert JSON encoded string to arrays.
		$video['assets'] = json_decode($video['formats'], TRUE);
		unset($video['formats']);
		$video['tags'] = json_decode($video['tags'], TRUE);
		asort($video['tags']);
		$video['tags'] = array_reverse($video['tags'], TRUE);
		
		// Sort assets by their megapixels number.
		function access_function($a) { return $a['res']; }
		function assets_cmp($a, $b) 
			{ return megapixels_cmp($a, $b, "access_function"); }
		usort($video['assets'], "assets_cmp");
		
		// Torrents
		$video['url'] = array();
		foreach ($video['assets'] as & $asset)
		{
			$def = substr($asset['res'], strpos($asset['res'], 'x') + 1) . 'p';
 			$asset['src'] = site_url('data/torrents/'. $video['name'] . '_'
 				. $def . '.'. $asset['ext']
 				. '.'. $this->config->item('default_torrent_ext'));
		}
		
		// Category title
		$categories = $this->config->item('categories');
		$category_name = $categories[ intval($video['category_id']) ];
		$video['category_title'] = $category_name ?
			$this->lang->line("ui_categ_$category_name") : $category_name;
		
		// Thumbnails
		$video['thumbs'] = $this->get_thumbs($video['name'], $video['thumbs_count']);
		
		// TODO: user information
		$video['user_name'] = 'TODO';
		
		return $video;
	}
	
	/**
	 * Increment a video parameter from DB: `views`, `likes` or `dislikes`.
	 * 
	 * @param int $id	DB video id
	 * @param string $param	DB parameter column name.
	 * @return void
	 */
	public function inc_video_var($id, $var)
	{
		// TODO error report if query returns FALSE
		$this->db->query('UPDATE `videos` '
						. 'SET `'. $var. '`=`'. $var. '`+1 '
						. 'WHERE id='. $id); 
	}
	
	public function get_thumbs($name, $count)
	{
		$thumbs = array();
		
		for ($i=0; $i < $count; $i++)
			$thumbs[] = site_url(sprintf("data/thumbs/%s_t%02d.jpg", $name, $i));
		
		return $thumbs;
	}

	/**
	 * Searches videos in DB based on a search query string and returns an
	 * associative array of results.
	 * If count is zero the function only return the number of results.
	 * @param string $search_query
	 * @param int $offset
	 * @param int $count
	 * @param int $category_id	if NULL, all categories are searched
	 * @return array	an associative array with the same keys as that from
	 * get_videos_summary's result, but with two additional keys: 
	 * description and date.
	 */
	public function search_videos($search_query, $offset = 0, $count = 0, 
									$category_id = NULL)
	{
		if ($count === 0)
		{
			$selected_columns = "COUNT(*) count";
			$limit = "";
		}
		else
		{ 
			$selected_columns = "id, name, title, duration, user_id, views, thumbs_count, default_thumb, date, description";
			$limit = "LIMIT $offset, $count";
		}
		
		if ($category_id !== NULL)
			$category_cond = "category_id = '$category_id' AND ";
		else
			$category_cond = "";

		$search_query = trim($search_query);
		
		$query = $this->db->query(
			"SELECT $selected_columns
			FROM `videos`
			WHERE  $category_cond MATCH (title, description, tags) AGAINST (?)
			$limit",
			array($search_query)); 
		
		if ($query->num_rows() > 0)
		{
			if ($count === 0)
				return $query->row()->count;
			else
				$videos = $query->result_array();
		}
		else
			return NULL;
		
		$this->load->helper('text');
		
		foreach ($videos as & $video)
		{
			// P2P-Tube Video URL
			$video['video_url'] = site_url(sprintf("watch/%d/%s",
				$video['id'], $video['name']));
			
			// Thumbnails
			$video['thumbs'] = $this->get_thumbs($video['name'], 
				$video['thumbs_count']);
				
			// Ellipsized title
			//$video['shorted_title'] = ellipsize($video['title'], 45, 0.75);
			$video['shorted_title'] = character_limiter($video['title'], 50);
			
			// TODO: user information
			$video['user_name'] = 'TODO';
		}
		
		return $videos;
	}
}

/* End of file videos_model.php */
/* Location: ./application/models/videos_model.php */
