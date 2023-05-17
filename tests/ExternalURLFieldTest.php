<?php

namespace BurnBright\ExternalURLField\Tests;

use BurnBright\ExternalURLField\ExternalURLField;
use SilverStripe\Dev\SapphireTest;
use SilverStripe\Forms\RequiredFields;

class ExternalURLFieldTest extends SapphireTest
{
    public function testSetConfig()
    {
        $field = new ExternalURLField("URL", "URL");

        //test example from README
        $field->setConfig(array(
            //these are always required / set
            'defaultparts' => array(
                'scheme' => 'http'
            ),
            //these parts are removed from saved urls
            'removeparts' => array(
                'scheme' => false,
                'user' => true,
                'pass' => true,
                'host' => false,
                'port' => false,
                'path' => false,
                'query' => false,
                'fragment' => false
            ),
            'html5validation' => true
        ));

        $field->setConfig("defaultparts", array(
            'scheme' => 'https',
            'host' => 'example.com'
        ));
        $this->assertEquals($field->getConfig('defaultparts'), array(
            'scheme' => 'https',
            'host' => 'example.com'
        ));

        $field->setConfig("removeparts", array(
            'query' => true,
            'fragment' => true
        ));
        $this->assertEquals($field->getConfig('removeparts'), array(
            'scheme' => false,
            'user' => true,
            'pass' => true,
            'host' => false,
            'port' => false,
            'path' => false,
            'query' => true,
            'fragment' => true
        ));

        $field->setConfig("html5validation", false);
        $this->assertEquals(false, $field->getConfig("html5validation"));
    }

    public function testDefaultSaving()
    {
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
        $this->stringStartsWith('http://www.hostname.com', $field->dataValue());

        $field->setValue("http://");
        $this->assertEquals('', $field->dataValue());
    }

    public function testValidation()
    {
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
