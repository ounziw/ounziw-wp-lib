<?php
/*
 * GPL ver.2
 * GCopyright 2012 by Fumito MIZUNO http://php-web.net/
 * Ghttp://www.gnu.org/licenses/gpl-2.0.html
 */

function ucfirst_sc_func( $atts, $content = null ) {
	extract( shortcode_atts( array(
					), $atts ));
	return ucfirst($content);
}

add_shortcode( 'ucfirst_sc', 'ucfirst_sc_func' );

class ContentTest extends PHPUnit_Framework_TestCase {
	private $content;
	public function setUp() {
		$this->content = new Content;
	}
	public function test_image() {
		$input = 'aaa<img src="abc.png">bbb';
		$this->content->set_content($input);
		$output = $this->content->get_first_image();
		$expected = 'abc.png';
		$this->assertEquals($expected, $output);
	}
	public function test_image_link() {
		$input = 'aaa<img src="abc.png">bbb';
		$this->content->set_content($input);
		$output = $this->content->get_first_image_link();
		$expected = '<img src="abc.png">';
		$this->assertEquals($expected, $output);
	}
	public function test_image2() {
		$input = 'aaa<img src="abc.png">bbb<img src="xx.jpg">b<img src="adef.jpg">ccc';
		$this->content->set_content($input);
		$output = $this->content->get_first_image();
		$expected = 'abc.png';
		$this->assertEquals($expected, $output);
	}
	public function test_noimage() {
		$input = 'aaabbbccc';
		$this->content->set_content($input);
		$output = $this->content->get_first_image();
		$expected = '';
		$this->assertEquals($expected, $output);
	}
	public function test_get_no_first_image() {
		$input = 'aaa<img src="abc.png" alt="abc">bbb<img src="def.jpg">ccc';
		$this->content->set_content($input);
		$output = $this->content->get_no_first_image_content();
		$expected = 'aaabbb<img src="def.jpg">ccc';
		$this->assertEquals($expected, $output);
	}
	public function test_get_no_image_content() {
		$input = 'aaa<img src="abc.png">bbb<img src="def.jpg">ccc';
		$this->content->set_content($input);
		$output = $this->content->get_no_image_content();
		$expected = 'aaabbbccc';
		$this->assertEquals($expected, $output);
	}
	public function test_two_br() {
		$input = "aa\n\nbb";
		$this->content->set_content($input);
		$output = $this->content->get_no_image_content();
		$expected = 'aa<br />
<br />
bb';
		$this->assertEquals($expected, $output);
	}
	public function width() {
		return array(
			// input, width, expected 
			array('あいうえおかきくけ十あいうえおかきくけ十あいうえおかきくけ十あいうえおかきくけ十あいうえおかきくけ十あいうえお',110,'あいうえおかきくけ十あいうえおかきくけ十あいうえおかきくけ十あいうえおかきくけ十あいうえおかきくけ十あいうえお'),
			array('あいうえおかきくけ十あいうえおかきくけ十あいうえおかきくけ十あいうえおかきくけ十あいうえおかきくけ十あいうえおか',110,'あいうえおかきくけ十あいうえおかきくけ十あいうえおかきくけ十あいうえおかきくけ十あいうえおかきくけ十あいう...'),
			array('あいうえお',10,'あいうえお'),
			array('あいうえおか',10,'あいう...'),
		);
	}

	/**
	 * @dataProvider width
	 */
	public function test_excerpt_width($input,$width,$expected) {
		$this->content->set_content($input);
		$output = $this->content->_get_excerpt_mbstrimwidth($width,"...");
		$this->assertEquals($expected, $output);
	}

	public function test_reject_script() {
		$input = 'あい<script>alert</script>';
		$this->content->set_content($input);
		$output = $this->content->_get_excerpt_mbstrimwidth(110,"...");
		$expected = 'あいalert';
		$this->assertEquals($expected, $output);
	}
	public function test_more() {
		$input = 'あいうえおかきくけ十あいうえおかきくけ十あいうえおかきくけ十あいうえおかきくけ十あいうえおかきくけ十あいうえおか';
		$this->content->set_content($input);
		$output = $this->content->_get_excerpt_mbstrimwidth(110,"--続く--");
		$expected = 'あいうえおかきくけ十あいうえおかきくけ十あいうえおかきくけ十あいうえおかきくけ十あいうえおかきくけ十あ--続く--';
		$this->assertEquals($expected, $output);
	}
	public function test_more2() {
		$input = 'あいうえおかきくけ十あいうえおかきくけ十あいうえおかきくけ十あいうえおかきくけ十あいうえおかきくけ十あいうえおか';
		$this->content->set_content($input);
		$output = $this->content->_get_excerpt_mbstrimwidth(110,"--------");
		$expected = 'あいうえおかきくけ十あいうえおかきくけ十あいうえおかきくけ十あいうえおかきくけ十あいうえおかきくけ十あ--------';
		$this->assertEquals($expected, $output);
	}
	public function test_more_reject_tag() {
		$input = 'あいうえおかきくけ十あいうえおかきくけ十あいうえおかきくけ十あいうえおかきくけ十あいうえおかきくけ十あいうえおか';
		$this->content->set_content($input);
		$output = $this->content->_get_excerpt_mbstrimwidth(110,"<b>abc</b>");
		$expected = 'あいうえおかきくけ十あいうえおかきくけ十あいうえおかきくけ十あいうえおかきくけ十あいうえおかきくけ十あいうabc';
		$this->assertEquals($expected, $output);
	}
	public function test_shortcode() {
		$input = 'aaa[ucfirst_sc]xxx[/ucfirst_sc]bbb';
		$this->content->set_content($input);
		$output = $this->content->get_no_image_content();
		$expected = 'aaaXxxbbb';
		$this->assertEquals($expected, $output);
	}
	public function test_excerpt_remove_shortcode() {
		$input = 'aaa[ucfirst_sc]xxx[/ucfirst_sc]bbb';
		$this->content->set_content($input);
		$output = $this->content->_get_excerpt_mbstrimwidth(110);
		$expected = 'aaabbb';
		$this->assertEquals($expected, $output);
	}
}

