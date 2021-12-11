<?php

namespace BurnBright\ExternalURLField;

use BurnBright\ExternalURLField\ExternalURLField;
use SilverStripe\ORM\FieldType\DBVarchar;

class ExternalURL extends DBVarchar
{
    private static $casting = array(
        "Domain" => ExternalURL::class,
        "URL" => ExternalURL::class,
        "NiceMaxLength" => => ExternalURL::class,
    );

    /**
     * 2083 is the lowest common denominator when it comes to url lengths.
     */
    public function __construct($name = null, $size = 2083, $options = array())
    {
        parent::__construct($name, $size, $options);
    }

    /**
     * Remove ugly parts of a url to make it nice
     */
    public function Nice()
    {
        if ($this->value && $parts = parse_url($this->URL())) {
            $remove = array('scheme', 'user', 'pass', 'port', 'query', 'fragment');
            foreach ($remove as $part) {
                unset($parts[$part]);
            }

            return rtrim(http_build_url($parts), "/");
        }
    }
    
    /**
     * break up url to avoid having urls that are too long ...
     */
    public function NiceMaxLength(?int $maxLength = 45, ?string $concatenater = ' ... ') 
    {
        $url = $this->Nice();
        if ( strlen($url) > $maxLength) {
            $firstBitLength = round($maxLength/3*2);
            $secondBitLength = round($maxLength/3) * -1;
            return substr($url, 0, $firstBitLength) . $concatenater . substr($url, $secondBitLength);
        } else {
            return $url;
        }
    }        

    /**
     * Get just the domain of the url.
     */
    public function Domain()
    {
        if ($this->value) {
            return parse_url($this->URL(), PHP_URL_HOST);
        }
    }

    /**
     * Remove the www subdomain, if present.
     */
    public function NoWWW()
    {
        return ltrim($this->value, "www.");
    }

    /**
     * Scaffold the ExternalURLField for this ExternalURL
     */
    public function scaffoldFormField($title = null, $params = null)
    {
        $field = new ExternalURLField($this->name, $title);
        $field->setMaxLength($this->getSize());

        return $field;
    }

    public function forTemplate()
    {
        if ($this->value) {
            return $this->URL();
        }
    }
}
