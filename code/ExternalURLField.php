<?php

/**
 * Form field for entering external urls
 */
class ExternalURLField extends TextField{

	/**
	 * @config
	 * @var array
	 */
	private static $default_config = array(
		'defaultparts' => array(
			'scheme' => 'http'
		),
		'removeparts' => array(
			'scheme' => false,
			'user' => true,
			'pass' => true,
			'host' => false,
			'port' => false,
			'path' => false,
			'query' => false,
			'fragment' => false
		)
	);
	
	/**
	 * URL validation regular expression
	 * @see https://gist.github.com/dperini/729294
	 */
	private static $valid_url_regex = '%^(?:(?:https?|ftp)://)(?:\S+(?::\S*)?@|\d{1,3}(?:\.\d{1,3}){3}|(?:(?:[a-z\d\x{00a1}-\x{ffff}]+-?)*[a-z\d\x{00a1}-\x{ffff}]+)(?:\.(?:[a-z\d\x{00a1}-\x{ffff}]+-?)*[a-z\d\x{00a1}-\x{ffff}]+)*(?:\.[a-z\x{00a1}-\x{ffff}]{2,6}))(?::\d+)?(?:[^\s]*)?$%iu';
	
	/**
	 * @var array
	 */
	protected $config;

	public function __construct($name, $title = null, $value = null) {
		$this->config = $this->config()->default_config;

		parent::__construct($name, $title, $value);
	}
	
	public function Type() {
		return 'url text';
	}

	public function getAttributes() {
		return array_merge(
			parent::getAttributes(),
			array(
				'type' => 'url', //html5 field type
				'pattern' => 'https?://.+', //valid urls only
				"placeholder" => "http://example.com" //example url
			)
		);
	}

	/**
	 * Rebuild url on save
	 * @param string $url
	 */
	public function setValue($url) {
		if($url){
			$url = $this->rebuildURL($url);
		}
		parent::setValue($url);
	}

	/**
	 * Add config scheme, if missing.
	 * Remove the parts of the url we don't want.
	 * Enforce any defaults
	 * Remove any trailing slash.
	 * @return string
	 */
	protected function rebuildURL($url) {
		if(!preg_match('#^[a-zA-Z]+://#', $url)){
			$url = $this->config['defaultparts']['scheme']."://".$url;
		}
		$parts = parse_url($url);
		foreach($parts as $part => $value) {
			if($this->config['removeparts'][$part] === true){
				unset($parts[$part]);
			}
		}
		$defaults = $this->config['defaultparts'];

		return rtrim(http_build_url($defaults, $parts), "/");
	}

	/**
	 * Server side validation, using a regular expression.
	 */
	public function validate($validator) {
		$this->value = trim($this->value);
		$regex = self::config()->valid_url_regex;
		if($this->value && $regex && !preg_match($regex, $this->value)){
			$validator->validationError(
				$this->name,
				_t('ExternalURLField.VALIDATION', "Please enter a valid URL"),
				"validation"
			);
			return false;
		}
		return true;
	}

}
