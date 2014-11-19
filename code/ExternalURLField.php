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
	    'requirements' => array(
	        'scheme' => true,
	        'user' => false,
	        'pass' => false,
	        'host' => true,
	        'port' => null,
	        'path' => null,
	        'query' => null,
	        'fragment' => null
	    )
	);
	
	/**
	 * @var array
	 */
	protected $config;

	public function __construct($name, $title = null, $value = null) {
		$this->config = $this->config()->default_config;

		parent::__construct($name, $title, $value);
	}
	
	public function getAttributes() {
		return array_merge(
			parent::getAttributes(),
			array(
				'type' => 'url'
			)
		);
	}

	public function setValue($url) {
		$url = $this->stripParts($url);
		parent::setValue($url);
	}

	protected function stripParts($url) {
		$parts = parse_url($url);
		foreach($parts as $part => $value) {
			if($this->config['requirements'][$part] === false){
				unset($parts[$part]);
			}
		}

		return http_build_url($parts);
	}

}