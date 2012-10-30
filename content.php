<?php

class Content {
	protected $content;
	protected $image;

	function __construct($content='') {
		if ($content) {
			$this->set_content($content);
		}
	}
	function set_content($content='') {
		$this->content = $content;
		$this->set_first_image();
	}
	protected function set_first_image() {
		if ( preg_match('@<img[^>]+src=[\'"]([^\'"]+?)(jpg|png)[\'"][^>]*?>@', $this->content, $matches) ) {
			// image[0] = <img src="xxxx">
			// image[1] = filename.
			// image[2] = jpg|png
			$this->image = $matches;
		} else {
			$this->image = '';
		}
	}
	function get_excerpt($length=110,$moretext="...") {
		return $this->_get_excerpt_mbstrimwidth($length,$moretext);
	}
	function _get_excerpt_mbstrimwidth($length=110,$moretext="...") {
		$length = intval($length);
		$moretext = strip_tags($moretext);
		$content = strip_shortcodes(strip_tags($this->content));
		$outdata = mb_strimwidth($content,0,$length,$moretext,"UTF-8");
		$outdata = htmlspecialchars($outdata,ENT_QUOTES,"UTF-8");
		return $outdata;
	}

	function get_first_image($default='') {
		$first_img = $default;
		if ( is_array($this->image) ) {
			$first_img = $this->image[1] . $this->image[2] ;
		}
		return $first_img;
	}
	function get_first_image_link($default='') {
		$first_img = $default;
		if ( is_array($this->image) ) {
			$first_img = $this->image[0] ;
		}
		return $first_img;
	}
	function get_no_first_image_content() {
		$content = $this->content;
		if ( is_array($this->image) ) {
			$content = str_replace($this->image[0],'',$content);
		}
		return nl2br(do_shortcode($content));
	}
	function get_no_image_content() {
		$content = strip_tags($this->content,'<p><a>');
		return nl2br(do_shortcode($content));
	}
}
