<?php  if ( ! defined('BASEPATH')) exit('No direct script access allowed');

function  country_dropdown ( $name="country", $top_countries=array(), 
		$selection=NULL, $show_all=TRUE )  
{
	$ci =& get_instance();
	$ci->config->load('localization');
	$countries = $ci->config->item('country_list');

	$html = "<select name='{$name}'>";
	$selected = NULL;
	if(in_array($selection,$top_countries))  {
		$top_selection = $selection;
		$all_selection = NULL;
	}
	else  {
		$top_selection = NULL;
		$all_selection = $selection;
	}

	if(!empty($top_countries))  {
		foreach($top_countries as $value)  {
			if(array_key_exists($value, $countries))  {
				if($value === $top_selection)  {
					$selected = 'selected="selected"';
				}
				$html .= "<option value='{$value}' {$selected}>{$countries[$value]}</option>";
				$selected = NULL;
			}
		}
		//$html .= "<option>----------</option>";
	}

	if($show_all)  {
		foreach($countries as $key => $country)  {
			if($key === $all_selection)  {
				$selected = 'selected="selected"';
			}
			$html .= "<option value='{$key}' {$selected}>{$country}</option>";
			$selected = NULL;
		}
	}

	$html .= "</select>";
	return $html;
}

function available_languages_dropdown($name, $selection=NULL, $attributes='')
{
	$ci =& get_instance();
	// Use the config file name.
	$ci->config->load('p2p-tube');
	$langs = $ci->config->item('available_languages_list');
	
	$html = "<select name='{$name}' {$attributes}>";
	$selected = NULL;
	
	foreach($langs as $key=> $value)  
	{
		if($key == $selection)  
		{
			$selected = 'selected="selected"';
		}
		$value = ucwords($value);
		$html .= "<option value='{$key}' {$selected}>{$value}</option>";
		$selected = NULL;
	}
	
	$html .= '</select>';
	
	return $html;
}

/* End of file localization_helper.php */
/* Location: ./application/helpers/localization_helper.php */