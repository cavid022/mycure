<?php
/*
* @Author: mark
* @Date:   2014-05-07 14:19:04
* @Last Modified by:   mark
* @Last Modified time: 2014-05-07 15:24:42
*/

namespace Bearlikelion\TwigRecaptcha;

class Extension extends \Twig_Extension
{
	public $Captcha;
	private static $instance;

	public function __construct(Array $keys)
	{
		if (!isset($keys['public']) && !(isset($keys['private']))) throw new \Exception('API keys Array incorrect');
		$this->Captcha = new \Captcha\Captcha;
		$this->Captcha->setPublicKey($keys['public']);
		$this->Captcha->setPrivateKey($keys['private']);
	}

	/**
	 * Define Twig functions
	 * @return array
	 */
	public function getFunctions()
	{
		return array(
			'recaptcha' => new \Twig_Function_Method($this, 'getCaptcha', array('is_safe' => array('html')))
		);
	}

	/** Return captcha HTML */
	public function getCaptcha()
	{
		return $this->Captcha->html();
	}

	/** Extension name */
	public function getName()
	{
		return 'captcha_extension';
	}

	public static function singleton()
	{
		if (!isset(self::$instance)) self::$instance = new self;
		return self::$instance->Captcha;
	}
}
