<?php  if ( ! defined('BASEPATH')) exit('No direct script access allowed');

$CI =& get_instance();

$config['per_page'] = 16; 
$config['num_links'] = 2;

$config['full_tag_open'] = '<div class="pagination">';
$config['full_tag_close'] = '</div>';

$config['first_link'] = $CI->lang->line('ui_page_first');
$config['prev_link'] = $CI->lang->line('ui_page_previous');
$config['next_link'] = $CI->lang->line('ui_page_next');
$config['last_link'] = $CI->lang->line('ui_page_last');