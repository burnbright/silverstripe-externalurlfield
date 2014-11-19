<?php

class ExternalURLTest extends SapphireTest{
	
	public function testDefault() {
		$f = new ExternalURL("MyField");
		$f->setValue("http://username:password@www.hostname.com:81/path?arg=value#anchor");
		$this->assertEquals(
			"http://username:password@www.hostname.com:81/path?arg=value#anchor",
			(string)$f)
		;
	}

	public function testNiceFormatting() {
		$f = new ExternalURL("MyField");
		$f->setValue("http://username:password@www.hostname.com:81/path?arg=value#anchor");
		$this->assertEquals("www.hostname.com/path", $f->Nice());
	}

	public function testScaffolding() {
		$f = new ExternalURL("MyField");
		$field = $f->scaffoldFormField();
		$this->assertInstanceOf("ExternaURLField", $field);
	}

}
