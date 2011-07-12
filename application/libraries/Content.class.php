<?php
require_once('includes.inc.php');
require_once('util.inc.php');

/** 
* Handles information from database
*/
class Content
{
	protected $db;
	protected $languages;
	protected $content;
	protected $lang;

	public function __construct($namespace)
	{
		try {
			$this->db = SingletonDB::connect();
			
			// Get languages from DB.
			if(!$this->db->query("SET NAMES 'utf8'")) throw new DBException('query');
			if( !($result = $this->db->query("SELECT * FROM `languages`")) )
				throw new DBException('query');
			while($row = $result->fetch_assoc())
			{
				$this->languages[$row['code']]['name'] = $row['name'];
				$this->languages[$row['code']]['img'] = $row['image'];
			}
			
			// Set current language
			// TODO: language
			$matchedLang = DEFAULT_LANG;//$this->matchLanguage();
			if(isset($_GET['lang']))
			{
				$lang = $this->processLangCode($_GET['lang']);
				$this->lang = $lang;
				if($lang != $matchedLang)
					setcookie("lang", $lang, time() + 30*24*60*60);
				else
					setcookie("lang", $lang, time() - 3600);
			}
			else if(isset($_COOKIE['lang']))
				$this->lang = $this->processLangCode($_COOKIE['lang']);
			else
				$this->lang = $matchedLang;
			
			// Get content from DB, from common and specified namespace.
				if( !($result = $this->db->query("SELECT * FROM `content` WHERE namespace = 'common' OR namespace = '" 
					. $namespace . "'")) )
				throw new DBException('query');
			while($row = $result->fetch_assoc())
			{
				$this->content[$row['name']] = json_decode($row['value'], true);
			}
		
		}
		catch(DBException $e) {
			echo $e->errorMessage();
			die();
		}
	}
	
	// Verifies if the lang code exists in the database and if so it returns it
	// in lower case. It not it returns the default language.
	public function processLangCode($lang)
	{
		$lang = strtolower($lang);
		if(isset($this->languages[$lang]))
			return $lang;
		else
			return DEFAULT_LANG;
	}
	
	public function getLanguages()
	{
		return $this->languages;
	}
	
	public function getLanguage($code)
	{
		if(isset($this->languages[$code]))
			return $this->languages[$code];
		else
			return null;
	}
	
	public function getCrtLanguage()
	{
		return $this->lang;
	}
	
	public function getContent($name, $lang)
	{
		if(isset($this->content[$name]))
			return $this->content[$name][$lang];
		else
			return null;
	}
	
	public function __get($name)
	{
		if(isset($this->content[$name]))
			return $this->content[$name][$this->lang];
		else
			return null;
	}
	
	// Returns user's prefered language if the site supports it or english otherwise.
	public function matchLanguage()
	{
		$pattern = '/^(?P<primarytag>[a-zA-Z]{2,8})'.
    '(?:-(?P<subtag>[a-zA-Z]{2,8}))?(?:(?:;q=)'.
    '(?P<quantifier>\d\.\d))?$/';
		
		foreach (explode(',', $_SERVER['HTTP_ACCEPT_LANGUAGE']) as $lang) 
		{
			$splits = array();

			if (preg_match($pattern, $lang, $splits)) 
			{
				$language = $splits['primarytag'];
				if(isset($this->languages[$language]))
					return $language;
			} 
			else 
				return 'en';
		}
		
		return 'en';
	}

	public function isRowIncomplete($row)
	{
		$incomplete = '';

		if(empty($row['name']))
			$incomplete .= 'n';
		if(empty($row['address']))
			$incomplete .= 'a';
		if(empty($row['developer']))
			$incomplete .= 'd';
		if(empty($row['chief_architect']))
			$incomplete .= '{ca}';
		if($row['whole_area'] == null || $row['whole_area'] == '0000')
			$incomplete .= '{wa}';
		if(empty($row['height']))
			$incomplete .= 'h';
		if(empty($row['phase']))
			$incomplete .= '{ph}';
		if($row['year_begin'] == null)
			$incomplete .= '{yb}';

		$descriptions = json_decode($row['description'], true);
		foreach($this->getLanguages() as $code => $value)
		{
			if(empty($descriptions[$code]))
				$incomplete .= '{de}';
		}

		$images = json_decode($row['images'], true);
		if(count($images) < 1)
			$incomplete .= 'i';
		
		return $incomplete;
	}
	
	// Returns a list of buildings with a specified category as an 
	// associative array with ID keys. Every element has associated:
	// name, year_begin, image (thumbnail).
	public function getPortofolioSummary($category, $page, $checkIncomplete=false)
	{
		$portofolio = null;

		if(!$checkIncomplete)
			$columns = "id, name, year_begin, images, category";
		else
			$columns = "*";
		
		$query = "SELECT ". $columns. " FROM `portofolio`";
		if($category != null)
			$query .= " WHERE category = '". $category . "'";
		$query .= " ORDER BY year_begin DESC, name ASC";
		if($page != null)
			$query .= " LIMIT ". ($page * PRJ_PER_PAGE) . ", " . PRJ_PER_PAGE;
		$result = $this->db->query($query);
			
		while($row = $result->fetch_assoc())
		{
			$portofolio[$row['id']]['name'] = $row['name'];
			$portofolio[$row['id']]['year_begin'] = $row['year_begin'];
			$images = json_decode($row['images'], true);
			if(!$images)
				$portofolio[$row['id']]['image'] = null;
			else
				$portofolio[$row['id']]['image'] = getThumbFileName($images[0]);
			$portofolio[$row['id']]['category'] = $row['category'];

			if($checkIncomplete)
				$portofolio[$row['id']]['incomplete'] = $this->isRowIncomplete($row);
		}
		
		return $portofolio;
	}
	
	// Returns 3 dictionaries representing projects from portofolio.
	// Each dictionary has id as key and another dictionary as value with:
	// name, year_begin, description, thumnail.
	public function getPortofolioAds()
	{
		$portofolio = null;
		
		$query = "SELECT id, name, year_begin, description, images FROM `portofolio` ORDER BY RAND() LIMIT "
			. P_ADS_COUNT;
		$result = $this->db->query($query);
			
		while($row = $result->fetch_assoc())
		{
			$portofolio[$row['id']]['name'] = $row['name'];
			$portofolio[$row['id']]['year_begin'] = $row['year_begin'];
			$images = json_decode($row['images'], true);
			if(!$images)
				$portofolio[$row['id']]['image'] = null;
			else
				$portofolio[$row['id']]['image'] = getThumbFileName($images[0]);
			$descriptions = json_decode($row['description'], true);
			$portofolio[$row['id']]['description'] = $descriptions[$this->lang];
		}
		
		return $portofolio;
	}
	
	public function getPortofolioCardinality($category)
	{
		$query = "SELECT count(id) c FROM `portofolio` WHERE category = '". $category. "'";
		$result = $this->db->query($query);
		
		$row = $result->fetch_row();
		if($row)
			return $row[0];
	}
	
	public function getRowNumber($id, $category)
	{
		$query = "
SELECT id id_t, name name_t, year_begin year_begin_t, (

	SELECT COUNT( * ) 
	FROM  `portofolio` 
	WHERE category =  '". $category. "'
	AND id <> id_t
	AND (year_begin = year_begin_t AND name < name_t OR year_begin > year_begin_t)
	ORDER BY year_begin DESC , name ASC
) pos
FROM  `portofolio` 
WHERE id = ". $id;
		$result = $this->db->query($query);
		
		$row = $result->fetch_assoc();
		if($row)
			return $row['pos'];
	}

	// If fullInfo is true, information for all languages will be returned.
	public function getProject($id, $fullInfo = false)
	{
		$result = $this->db->query(
			"SELECT * from `portofolio` WHERE id = " . $id);
		$row = $result->fetch_assoc();
		
		if(!$row)
			return null;
		
		$row['category_code'] = $row['category'];
		switch($row['category_code'])
		{
		case 'office':
			$row['category'] = $this->project_category_office;
			break;
		case 'residential':
			$row['category'] = $this->project_category_residential;
			break;
		case 'hotel':
			$row['category'] = $this->project_category_hotel;
			break;
		case 'industrial':
			$row['category'] = $this->project_category_industrial;
			break;
		case 'commercial':
			$row['category'] = $this->project_category_commercial;
			break;
		case 'restorations':
			$row['category'] = $this->project_category_restorations;
			break;
		case 'hospitals':
			$row['category'] = $this->project_category_category;
			break;
		default: // other
			$row['category'] = $this->project_category_other;
			break;
		}

		// Translate NULL values.
		if(!isset($project['whole_area']) || $project['whole_area'] == null)
			$project['whole_area'] = '';
		if(!isset($project['year_begin']) || $project['year_begin'] == null)
			$project['year_begin'] = '';
		if(!isset($project['year_end']) || $project['year_end'] == null)
			$project['year_end'] = '';
		
		$descriptions = json_decode($row['description'], true);
		$row['description'] = $descriptions[$this->lang];
		if($fullInfo)
			$row['descriptions'] = $descriptions;
		
		$images = json_decode($row['images'], true);
		$row['images'] = $images;

		return $row;
	}
	
	public static function insertProject($project)
	{
		try {
			$db = SingletonDB::connect();
			
			$db->query("SET NAMES 'utf8'");
			
			$project['name'] = str_replace("'", "`", $project['name']);
			$project['description'] = json_encode(str_replace("'", "`", $project['descriptions']));
			$project['images'] = json_encode($project['images']);
			
			$result = $db->query(
				"INSERT INTO `portofolio` (name, category, address, developer, chief_architect, whole_area, height, phase, year_begin, year_end, description, images)
				 VALUES ('". $project['name']. "', '". $project['category_code']. "', '". $project['address']. "', '". $project['developer']. "', '". $project['chief_architect']. "', ". $project['whole_area']. ", '". $project['height']. "', '". $project['phase']. "', ". $project['year_begin']. ", ". $project['year_end']. ", '". $project['description']. "', '". $project['images']. "')");
			
			if(!$result)
				throw new DBException('query');
		}
		catch(DBException $e) {
			echo $e->errorMessage();
			die();
		}
	}
	
	public static function updateProject($id, $project)
	{
		try {
			$db = SingletonDB::connect();
			
			$db->query("SET NAMES 'utf8'");
			
			$project['name'] = str_replace("'", "`", $project['name']);
			$project['description'] = json_encode(str_replace("'", "`", $project['descriptions']));
			$project['images'] = json_encode($project['images']);
			
			$query = "UPDATE `portofolio` SET name='". $project['name']. "', category='". $project['category_code']. "', address='". $project['address']. "', developer='". $project['developer']. "', chief_architect='". $project['chief_architect']. "', whole_area=". $project['whole_area']. ", height='". $project['height']. "', phase='". $project['phase']. "', year_begin=". $project['year_begin']. ", year_end=". $project['year_end']. ", description='". $project['description']. "', images='". $project['images']. "' WHERE id=". $id;
			$result = $db->query($query);
			
			if(!$result)
				throw new DBException('query');
		}
		catch(DBException $e) {
			echo $e->errorMessage();
			die();
		}
	}

	public static function deleteProject($id, $project)
	{
		foreach($project['images'] as $image)
		{
			deleteImage($image);
		}

		try {
			$db = SingletonDB::connect();
			
			$query = "DELETE FROM `portofolio` WHERE id=". $id;
			$result = $db->query($query);
			
			if(!$result)
				throw new DBException('query');
		}
		catch(DBException $e) {
			echo $e->errorMessage();
			die();
		}
	}
}

//$x = new Content('common');
//echo $x->getRowNumber(56, 'residential');
?>