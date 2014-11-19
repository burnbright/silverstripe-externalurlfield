<?php

class ExternalURLFieldTest extends SapphireTest{

	public function testDefaultSaving() {
		$field = new ExternalURLField("URL", "URL");

		$field->setValue(
			"http://username:password@www.hostname.com:81/path?arg=value#anchor"
		);
		$this->assertEquals('http://www.hostname.com:81/path?arg=value#anchor', $field->dataValue());

		$field->setValue("https://hostname.com/path");
		$this->assertEquals('https://hostname.com/path', $field->dataValue());

		$field->setValue("");
		$this->assertEquals("", $field->dataValue());

		$field->setValue("www.hostname.com");
		$this->assertEquals('http://www.hostname.com', $field->dataValue());
	}

	public function testValidation() {
		$field = new ExternalURLField("URL", "URL");
		$validator = new RequiredFields();

		$field->setValue(
			"http://username:password@www.hostname.com:81/path?arg=value#anchor"
		);
		$this->assertTrue($field->validate($validator));

		$field->setValue("");
		$this->assertTrue($field->validate($validator));

		$field->setValue("asefasdfasfasfasfasdfasfasdfas");
		$this->assertFalse($field->validate($validator));

		$field->setValue("http://3628126748");
		$this->assertFalse($field->validate($validator));
	}

}
