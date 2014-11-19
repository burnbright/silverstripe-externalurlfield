<?php

class ExternalURL extends Varchar{

	/**
	 * 2083 is the lowest common denominator when it comes to url lengths.
	 */
	public function __construct($name = null, $size = 2083, $options = array()) {
		$this->size = $size ? $size : 2083;
		parent::__construct($name, $options);
	}
	
	/**
	 * Remove ugly parts of a url to make it nice
	 */
	public function Nice() {
		$parts = parse_url($url);
        unset($parts['scheme']);
        unset($parts['user']);
        unset($parts['pass']);
        unset($parts['port']);
        unset($parts['query']);
        unset($parts['fragment']);

		return http_build_url($parts);
	}

	public function scaffoldFormField($title = null, $params = null) {
		$field = new ExternalURLField($this->name, $title);
		
		return $field;
	}

}