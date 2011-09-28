<?php  if ( ! defined('BASEPATH')) exit('No direct script access allowed');

$CI =& get_instance();

$config['per_page'] = 16; 
$config['num_links'] = 2;

$config['full_tag_open'] = '<div class="pagination">';
$config['full_tag_close'] = '</div>';

$config['first_tag_open'] = ' <span class="pg-first">';
$config['first_tag_close'] = '</span> ';
$config['first_link'] = $CI->lang->line('ui_page_first');

$config['prev_tag_open'] = ' <span class="pg-prev">';
$config['prev_tag_close'] = '</span> ';
$config['prev_link'] = $CI->lang->line('ui_page_previous');

$config['next_tag_open'] = ' <span class="pg-next">';
$config['next_tag_close'] = '</span> ';
$config['next_link'] = $CI->lang->line('ui_page_next');

$config['last_tag_open'] = ' <span class="pg-last">';
$config['last_tag_close'] = '</span> ';
$config['last_link'] = $CI->lang->line('ui_page_last');

$config['num_tag_open'] = ' <span class="pg-num">';
$config['num_tag_close'] = '</span> ';