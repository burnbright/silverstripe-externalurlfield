<?php

namespace BurnBright\ExternalURLField\Tests;

use BurnBright\ExternalURLField\ExternalURL;
use BurnBright\ExternalURLField\ExternalURLField;
use SilverStripe\Dev\SapphireTest;

class ExternalURLTest extends SapphireTest
{
    public function testDefault()
    {
        $f = new ExternalURL("MyField");
        $f->setValue("http://username:password@www.hostname.com:81/path?arg=value#anchor");
        $this->assertEquals(
            "http://username:password@www.hostname.com:81/path?arg=value#anchor",
            (string)$f
        );
        $this->assertEquals(2083, $f->getSize());
    }

    public function testNice()
    {
        $f = new ExternalURL("MyField");
        $f->setValue("http://username:password@www.hostname.com:81/path?arg=value#anchor");
        $this->assertEquals("www.hostname.com/path", $f->Nice());
    }

    public function testDomain()
    {
        $f = new ExternalURL("MyField");
        $f->setValue("http://username:password@www.hostname.com:81/path?arg=value#anchor");
        $this->assertEquals("www.hostname.com", $f->Domain());
    }

    public function testScaffolding()
    {
        $f = new ExternalURL("MyField");
        $field = $f->scaffoldFormField();
        $this->assertInstanceOf(ExternalURLField::class, $field);
        $this->assertEquals(2083, $field->getMaxLength());
    }
}
