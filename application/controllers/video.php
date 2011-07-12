<?php

/**
 * Class Video controlls video items handling: watching, commenting, rating,
 * adding etc.
 *
 * @category	Controller
 * @author		Călin-Andrei Burloiu
 */
class Video extends CI_Controller {
	
	public function index()
	{
		
	}
	
	public function watch($id, $name = NULL)
	{
		$this->load->helper('url');
		
		$this->load->view('html_begin');
		$this->load->view('header');
		
		$this->load->model('videos_model');
		$data['video'] = $this->videos_model->getVideo($id)->row();
		if ($name !== NULL && $data['video']->name != $name)
			$data['video']->err = 'INVALID_NAME';
		
		$this->load->view('video/watch_view', $data);
		
		$this->load->view('footer');
		$this->load->view('html_end');
	}
}

/* End of file video.php */
/* Location: ./application/controllers/video.php */
