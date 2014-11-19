<?php

class ExternalURLFieldTest extends SapphireTest{

	public function testDefaultSaving() {
		$field = new ExternalURLField("URL", "URL");
		$field->setValue(
			"http://username:password@www.hostname.com:81/path?arg=value#anchor"
		);
		$this->assertEquals('http://www.hostname.com:81/path?arg=value#anchor', $field->dataValue());
		$field->setValue(
			"http://hostname.com/path"
		);
		$this->assertEquals('http://hostname.com/path', $field->dataValue());

	}

}
