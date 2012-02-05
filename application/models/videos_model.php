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
		parent::__construct();
		
		if ($this->db === NULL)
		{
			$this->load->library('singleton_db');
			$this->db = $this->singleton_db->connect();
		}
	}
	
	/**
	 * Retrieves a set of videos information which can be used for displaying
	 * that videos as a list with few details.
	 *
	 * @param		int $category_id	DB category ID; pass NULL for all
	 * categories
	 * @param		mixed $user			an user_id (as int) or an username 
	 * (as string); pass NULL for all users
	 * @param		int $offset
	 * @param		int $count
	 * @param		string $ordering	control videos ording by these
	 * possibilities:
	 * <ul>
	 *   <li><strong>'hottest':</strong> newest most appreciated first. An
	 *   appreciated video is one which has a bigger
	 *   score = views + likes - dislikes.</li>
	 *   <li><strong>'newest':</strong> newest first.</li>
	 *   <li><strong>'alphabetically':</strong> sort alphabetically.</li>
	 * </ul>
	 * @return		array	a list of videos, each one being an assoc array with:
	 * <ul>
	 *   <li>id, name, title, duration, thumbs_count, default_thumb, views => from DB</li>
	 *   <li>shorted_title => ellipsized title</li>
	 *   <li>video_url => P2P-Tube video URl</li>
	 *   <li>user_id, user_name</li>
	 *   <li>thumbs => thumbnail images' URLs</li>
	 * </ul>
	 */
	public function get_videos_summary($category_id, $user, $offset, $count,
		$ordering = 'hottest')
	{
		$this->load->helper('text');
		
		// Ordering
		switch ($ordering)
		{
		case 'hottest':
			$order_statement = "ORDER BY date DESC, score DESC, RAND()";
			break;
		case 'newest':
			$order_statement = "ORDER BY date DESC";
			break;
		case 'alphabetically':
			$order_statement = "ORDER BY title";
			break;
			
		default:
			$order_statement = "";
		}
		
		// Category filtering
		if ($category_id === NULL)
			$cond_category = "1";
		else
		{
			$category_id = intval($category_id);
			$cond_category = "category_id = $category_id";
		}
		
		// User filtering
		if ($user === NULL)
			$cond_user = "1";
		else
		{
			if (is_int($user))
				$cond_user = "v.user_id = $user";
			else if (is_string($user))
				$cond_user = "u.username = '$user'";
		}
		
		$query = $this->db->query(
			"SELECT v.id, name, title, duration, user_id, u.username, views,
				thumbs_count, default_thumb,
				(views + likes - dislikes) AS score
			FROM `videos` v, `users` u
			WHERE v.user_id = u.id AND $cond_category AND $cond_user
			$order_statement
			LIMIT $offset, $count"); 
		
		if ($query->num_rows() > 0)
			$videos = $query->result_array();
		else
			return array();
		
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
		}
		
		return $videos;
	}
	
	/**
	 * Returns the number of videos from database from a specific category or
	 * user.
	 * NULL parameters count videos from all categories and / or all users.
	 * 
	 * @param int $category_id
	 * @param mixed $user	an user_id (as int) or an username (as string)
	 * @return int	number of videos or FALSE if an error occured
	 */
	public function get_videos_count($category_id = NULL, $user = NULL)
	{
		if ($category_id === NULL)
			$cond_category = "1";
		else
			$cond_category = "category_id = $category_id";
		
		if ($user === NULL)
			$cond_user = "1";
		else
		{
			if (is_int($user))
				$cond_user = "v.user_id = $user";
			else if(is_string($user))
				$cond_user = "u.username = '$user'";
		}
		
		$query = $this->db->query(
			"SELECT COUNT(*) count
			FROM `videos` v, `users` u
			WHERE v.user_id = u.id AND $cond_category AND $cond_user");
		
		if ($query->num_rows() > 0)
			return $query->row()->count;
		
		// Error
		return FALSE;
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
	 *   <li>username => user name from `users` table</li>
	 *   <li>category_title => a human-friendly category name</li>
	 *   <li>tags => associative list of "tag => score"</li>
	 *   <li>date => date and time when the video was created</li>
	 *   <li>thumbs => thumbnail images' URLs</li>
	 * </ul>
	 */
	public function get_video($id, $name = NULL)
	{
		$this->load->helper('video');
		$this->load->helper('text');
		
		$query = $this->db->query("SELECT v.*, u.username 
								FROM `videos` v, `users` u
								WHERE v.user_id = u.id AND v.id = $id");
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
			$asset['def'] = $def;
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
		
		// Shorted description
		$video['shorted_description'] = character_limiter(
				$video['description'], 128);
		
		return $video;
	}
	
	/**
	 * Retrieves comments for a video.
	 * 
	 * @param int $video_id
	 * @param int $offset
	 * @param int $count
	 * @param string $ordering	control comments ording by these possibilities:
	 * <ul>
	 *   <li><strong>'hottest':</strong> newest most appreciated first. An
	 *   appreciated comment is one which has a bigger
	 *   score = likes - dislikes.</li>
	 *   <li><strong>'newest':</strong> newest first.</li>
	 * </ul>
	 * @return array	an array with comments
	 */
	public function get_video_comments($video_id, $offset, $count,
			$ordering = 'newest')
	{
		$this->load->helper('date');
		$cond_hottest = '';
		
		// Ordering
		switch ($ordering)
		{
		case 'newest':
			$order_statement = "ORDER BY time DESC";
			break;
		case 'hottest':
			$order_statement = "ORDER BY score DESC, time DESC";
			$cond_hottest = "AND c.likes + c.dislikes > 0";
			break;
				
		default:
			$order_statement = "";
		}
		
		$query = $this->db->query(
			"SELECT c.*, u.username, u.time_zone, (c.likes + c.dislikes) AS score
				FROM `videos_comments` c, `users` u
				WHERE c.user_id = u.id AND video_id = $video_id $cond_hottest
				$order_statement
				LIMIT $offset, $count");
		
		if ($query->num_rows() == 0)
			return array();
		
		$comments = $query->result_array();
		
		foreach ($comments as & $comment)
		{
			$comment['local_time'] = human_gmt_to_human_local($comment['time'],
				$comment['time_zone']);
		}
		
		return $comments;
	}
	
	public function get_video_comments_count($video_id)
	{
		$query = $this->db->query(
					"SELECT COUNT(*) count
						FROM `videos_comments`
						WHERE video_id = $video_id");
				
		if ($query->num_rows() == 0)
			return FALSE;
		
		return $query->row()->count;
	}
	
	/**
	 * Insert in DB a comment for a video.
	 * 
	 * @param int $video_id
	 * @param int $user_id
	 * @param string $content
	 */
	public function comment_video($video_id, $user_id, $content)
	{
		// Prepping content.
		$content = substr($content, 0, 512);
		$content = htmlspecialchars($content);
		$content = nl2br($content);
		
		return $query = $this->db->query(
			"INSERT INTO `videos_comments` (video_id, user_id, content, time)
			VALUES ($video_id, $user_id, '$content', UTC_TIMESTAMP())");
	}
	
	/**
	 * Increments views count for a video.
	 * 
	 * @param int $id	DB video id
	 * @return void
	 */
	public function inc_views($id)
	{
		return $this->db->query('UPDATE `videos` '
						. 'SET `views`=`views`+1 '
						. 'WHERE id='. $id); 
	}
	
	public function vote($video_id, $user_id, $like = TRUE)
	{
		if ($like)
		{
			$col = 'likes';
			$action = 'like';
		}
		else
		{
			$col = 'dislikes';
			$action = 'dislike';
		}
		
		$query = $this->db->query("SELECT * FROM `users_actions`
			WHERE user_id = $user_id
				AND target_id = $video_id
				AND target_type = 'video'
				AND action = '$action'
				AND date = CURDATE()");
		// User already voted today
		if ($query->num_rows() > 0)
			return -1;
		
		$this->db->query("UPDATE `videos`
			SET $col = $col + 1
			WHERE id = $video_id");
		
		// Mark this action so that the user cannot repeat it today.
		$this->db->query("INSERT INTO `users_actions`
				(user_id, action, target_type, target_id, date)
			VALUES ( $user_id, '$action', 'video', $video_id, CURDATE() )");
		
		$query = $this->db->query("SELECT $col FROM `videos`
			WHERE id = $video_id");
		
		if ($query->num_rows() === 1)
		{
			$row = $query->row_array();
			return $row[ $col ];
		}
		
		// Error
		return FALSE;
	}
	
	public function vote_comment($comment_id, $user_id, $like = TRUE)
	{
		if ($like)
		{
			$col = 'likes';
			$action = 'like';
		}
		else
		{
			$col = 'dislikes';
			$action = 'dislike';
		}
	
		$query = $this->db->query("SELECT * FROM `users_actions`
				WHERE user_id = $user_id
					AND target_id = $comment_id
					AND target_type = 'vcomment'
					AND action = '$action'
					AND date = CURDATE()");
		// User already voted today
		if ($query->num_rows() > 0)
			return -1;
	
		$this->db->query("UPDATE `videos_comments`
				SET $col = $col + 1
				WHERE id = $comment_id");
	
		// Mark this action so that the user cannot repeat it today.
		$this->db->query("INSERT INTO `users_actions`
					(user_id, action, target_type, target_id, date)
				VALUES ( $user_id, '$action', 'vcomment', $comment_id, CURDATE() )");
	
		$query = $this->db->query("SELECT $col FROM `videos_comments`
				WHERE id = $comment_id");
	
		if ($query->num_rows() === 1)
		{
			$row = $query->row_array();
			return $row[ $col ];
		}
	
		// Error
		return FALSE;
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
		$search_query = trim($search_query);
		$search_query = str_replace("'", " ", $search_query);
		
		// Search word fragments.
		// sfc = search fragment condition
		$sfc = "( ";
		// sfr = search fragment relevance
		$sfr = "( ";
		$sep = ' +-*<>()~"';
		$fragm = strtok($search_query, $sep);
		while ($fragm !== FALSE)
		{
			$sfc .= "(title LIKE '%$fragm%'
					OR description LIKE '%$fragm%'
					OR tags LIKE '%$fragm%') OR ";
			
			// Frament relevances are half of boolean relevances such
			// that they will appear at the end of the results.
			$sfr .= "0.25 * (title LIKE '%$fragm%')
					+ 0.1 * (description LIKE '%$fragm%')
					+ 0.15 * (tags LIKE '%$fragm%') + ";
			
			$fragm = strtok($sep);
		}
		$sfc = substr($sfc, 0, -4) . " )";
		$sfr = substr($sfr, 0, -3) . " )";
		
		if (! $this->is_advanced_search_query($search_query))
		{
			$search_cond = "MATCH (title, description, tags)
					AGAINST ('$search_query') OR $sfc";
			$relevance = "( MATCH (title, description, tags)
					AGAINST ('$search_query') + $sfr ) AS relevance";
		}
		// boolean mode
		else
		{
			$against = "AGAINST ('$search_query' IN BOOLEAN MODE)";
			$search_cond = "( MATCH (title, description, tags)
					$against) OR $sfc";
			$relevance = "( 0.5 * (MATCH(title) $against)
					+ 0.3 * (MATCH(tags) $against)
					+ 0.2 * (MATCH(description) $against)
					+ $sfr) AS relevance";
		}
		
		if ($count === 0)
		{
			$selected_columns = "COUNT(*) count";
			$order = "";
			$limit = "";
		}
		else
		{
			// TODO select data, description if details are needed
			$selected_columns = "v.id, name, title, duration, user_id, views,
					thumbs_count, default_thumb, u.username,
					(views + likes - dislikes) AS score, 
					$relevance";
			$order = "ORDER BY relevance DESC, score DESC";
			$limit = "LIMIT $offset, $count";
		}
		
		if ($category_id !== NULL)
			$category_cond = "category_id = '$category_id' AND ";
		else
			$category_cond = "";

		$str_query = "SELECT $selected_columns
			FROM `videos` v, `users` u
			WHERE  v.user_id = u.id AND $category_cond ( $search_cond )
			$order
			$limit";
// 		echo "<p>$str_query</p>";
		$query = $this->db->query($str_query);
		
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
	
	public function decode_search_query($search_query)
	{
		$search_query = urldecode($search_query);
		
		$search_query = str_replace('_AST_', '*', $search_query);
		$search_query = str_replace('_AND_', '+', $search_query);
		$search_query = str_replace('_GT_', '>', $search_query);
		$search_query = str_replace('_LT_', '<', $search_query);
		$search_query = str_replace('_PO_', '(', $search_query);
		$search_query = str_replace('_PC_', ')', $search_query);
		$search_query = str_replace('_LOW_', '~', $search_query);
		$search_query = str_replace('_QUO_', '"', $search_query);
		
		return $search_query;
	}
	
	public function encode_search_query($search_query)
	{
		$search_query = str_replace('*', '_AST_', $search_query);
		$search_query = str_replace('+', '_AND_', $search_query);
		$search_query = str_replace('>', '_GT_', $search_query);
		$search_query = str_replace('<', '_LT_', $search_query);
		$search_query = str_replace('(', '_PO_', $search_query);
		$search_query = str_replace(')', '_PC_', $search_query);
		$search_query = str_replace('~', '_LOW_', $search_query);
		$search_query = str_replace('"', '_QUO_', $search_query);
		
		$search_query = urlencode($search_query);
	
		return $search_query;
	}
	
	/**
	 * Return TRUE if it contains any special caracter from an advanced search
	 * query.
	 * @param string $search_query
	 * @return boolean
	 */
	public function is_advanced_search_query($search_query)
	{
		return (preg_match('/\*|\+|\-|>|\<|\(|\)|~|"/', $search_query) == 0
			? FALSE : TRUE);
	}
}

/* End of file videos_model.php */
/* Location: ./application/models/videos_model.php */
