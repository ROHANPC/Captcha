<?php

namespace SugiPHP\Captcha;

class Captcha
{
	/**
	 * Storage for configuration settings
	 * @var array
	 */
	protected $config = array();

	protected $image;

	public function __construct(array $config = array())
	{
		// Default configuration options
		$this->config = array(
			// width of the image in pixels
			"width"                    => 120,
			// height of the image in pixels
			"height"                   => 40,
			// number of chars
			"length"                   => 5,
			// chars that will be used on code generation
			"charset"                  => "abcdefhjkmnprstuvwxyz23456789",
			// absolute path to the font
			"font"                     => __DIR__."/captcha.ttf",
			// you can pass your own code or set to false for random
			"code"                     => false,
			// quality
			"quality"                  => 15,
		);

		// Override default options
		foreach ($this->config as $name => $def) {
			if (isset($config[$name])) {
				$this->config[$name] = $config[$name];
			}
		}

		if (empty($this->config["code"])) {
			$this->config["code"] = $this->genCode();
		}
	}

	public function getCode()
	{
		return $this->config["code"];
	}

	public function build()
	{
		$image = imagecreatetruecolor($this->config["width"], $this->config["height"]);
		$bg = imagecolorallocate($image, mt_rand(225, 255), mt_rand(225, 255), mt_rand(225, 255));
		imagefill($image, 0, 0, $bg);

		$this->writeCode($image);

		$this->image = $image;

		return $this;
	}

	public function get()
	{
		ob_start();
		imagejpeg($this->image, null, $this->config["quality"]);

		return ob_get_clean();
	}

	public function output()
	{
		header("Content-type: image/jpg");
		imagejpeg($this->image, null, $this->config["quality"]);
	}

	/**
	 * Returns base64 encoded CAPTCHA image
	 */
	public function base64()
	{
		return "data:image/jpeg;base64," . base64_encode($this->get());
	}

	/**
	 * Draws a captcha code on the image canvas.
	 *
	 * @param  resource $image
	 * @return void
	 */
	protected function writeCode($image)
	{
		$code = $this->config["code"];
		$width = $this->config["width"];
		$height = $this->config["height"];
		$font = $this->config["font"];
		$len = mb_strlen($code, "UTF-8");

		$size = $width / $len - mt_rand(1, 3);
		$box = imagettfbbox($size, 0, $font, $code);
		$textWidth = $box[2] - $box[0];
		$textHeight = $box[1] - $box[7];
		$x = ($width - $textWidth) / 2;
		$y = ($height - $textHeight) / 2 + $size;

		for ($i = 0; $i < $len; $i++) {
			$char = mb_substr($code, $i, 1, "UTF-8");
			$box = imagettfbbox($size, 0, $font, $char);
			$w = $box[2] - $box[0];
			$angle = mt_rand(-10, 10);
			$offset = mt_rand(-2, 2);
			$color = imagecolorallocate($image, mt_rand(0, 125), mt_rand(0, 125), mt_rand(0, 125));
			imagettftext($image, $size, $angle, $x, $y + $offset, $color, $font, $char);
			$x += $w;
		}
	}

	/**
	 * Generates a code.
	 *
	 * @return string
	 */
	protected function genCode()
	{
		$code = "";

		$charset = $this->config["charset"];
		$cnt = mb_strlen($charset, "UTF-8");

		for ($i = 0; $i < $this->config["length"]; $i++) {
			$code .= mb_substr($charset, mt_rand(0, $cnt - 1), 1, "UTF-8");
		}

		return $code;
	}
}
