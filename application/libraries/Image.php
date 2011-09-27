<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed'); 

/**
 * An image processing library initially intended for creating thumbnails.
 * 
 * @category	Library
 * @author		Simon Jarvis, Călin-Andrei Burloiu
 * @copyright	2006 Simon Jarvis, 2011 Călin-Andrei Burloiu
 */

define('IMAGETYPE_AUTO', 0);

class Image {
	 
	var $image;
	var $image_type;

	function load($filename) {
		$image_info = getimagesize($filename);
		$this->image_type = $image_info[2];
		if( $this->image_type == IMAGETYPE_JPEG ) {
			$this->image = imagecreatefromjpeg($filename);
		} elseif( $this->image_type == IMAGETYPE_GIF ) {
			$this->image = imagecreatefromgif($filename);
		} elseif( $this->image_type == IMAGETYPE_PNG ) {
			$this->image = imagecreatefrompng($filename);
		}
	}
	function save($filename, $image_type=IMAGETYPE_JPEG, $compression=60, $permissions=null) {
		if( $image_type == IMAGETYPE_AUTO) {
			if (preg_match('/\.jpg$/', $filename) 
					|| preg_match('/\.jpeg$/', $filename))
				$image_type = IMAGETYPE_JPEG;
			elseif (preg_match('/\.gif$/', $filename))
				$image_type = IMAGETYPE_GIF;
			elseif (preg_match('/\.png$/', $filename))
				$image_type = IMAGETYPE_PNG;
		}
		
		if( $image_type == IMAGETYPE_JPEG ) {
			imagejpeg($this->image,$filename,$compression);
		} elseif( $image_type == IMAGETYPE_GIF ) {
			imagegif($this->image,$filename);
		} elseif( $image_type == IMAGETYPE_PNG ) {
			imagepng($this->image,$filename);
		}
		
		if( $permissions != null) {
			chmod($filename,$permissions);
		}
	}
	function output($image_type=IMAGETYPE_JPEG) {
		if( $image_type == IMAGETYPE_JPEG ) {
			imagejpeg($this->image);
		} elseif( $image_type == IMAGETYPE_GIF ) {
			imagegif($this->image);
		} elseif( $image_type == IMAGETYPE_PNG ) {
			imagepng($this->image);
		}
	}
	function save_thumbnail($filename, $width, $height,
			$image_type=IMAGETYPE_JPEG)
	{
		$ratio = $this->get_width() / $this->get_height();
		$thumbRatio = $width / $height;

		if($ratio < $thumbRatio)
			$this->resize_to_height($height);
		else
			$this->resize_to_width($width);

		$this->save($filename, $image_type);
	}
	function get_width() {
		return imagesx($this->image);
	}
	function get_height() {
		return imagesy($this->image);
	}
	function resize_to_height($height) {
		$ratio = $height / $this->get_height();
		$width = $this->get_width() * $ratio;
		$this->resize($width,$height);
	}
	function resize_to_width($width) {
		$ratio = $width / $this->get_width();
		$height = $this->get_height() * $ratio;
		$this->resize($width,$height);
	}
	function scale($scale) {
		$width = $this->get_width() * $scale/100;
		$height = $this->get_height() * $scale/100;
		$this->resize($width,$height);
	}
	function resize($width,$height) {
		$new_image = imagecreatetruecolor($width, $height);
		imagecopyresampled($new_image, $this->image, 0, 0, 0, 0, $width, $height, $this->get_width(), $this->get_height());
		$this->image = $new_image;
	}
}


/* End of file Singleton_db.php */
/* Location: ./application/libraries/Singleton_db.php */
