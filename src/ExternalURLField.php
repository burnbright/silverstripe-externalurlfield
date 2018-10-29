<?php

namespace BurnBright\ExternalURLField;

use SilverStripe\Forms\TextField;

/**
 * ExternalURLField
 *
 * Form field for entering, saving, validating external urls.
 */
class ExternalURLField extends TextField
{
    /**
     * Default configuration
     *
     * URL validation regular expression was sourced from
     * @see https://gist.github.com/dperini/729294
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
        ),
        'html5validation' => true,
        'validregex' => '%^(?:(?:https?|ftp)://)(?:\S+(?::\S*)'
            . '?@|\d{1,3}(?:\.\d{1,3}){3}|(?:(?:[a-z\d\x{00a1}-\x{ffff}]+-?)*[a-z\d\x{00a1}-\x{ffff}]+)'
            . '(?:\.(?:[a-z\d\x{00a1}-\x{ffff}]+-?)*[a-z\d\x{00a1}-\x{ffff}]+)*(?:\.[a-z\x{00a1}-\x{ffff}]{2,6}))'
            . '(?::\d+)?(?:[^\s]*)?$%iu'
    );

    /**
     * @var array
     */
    protected $config;

    public function __construct($name, $title = null, $value = null)
    {
        $this->config = $this->config()->default_config;

        parent::__construct($name, $title, $value);
    }

    public function Type()
    {
        return 'url text';
    }

    /**
     * @param string $name
     * @param mixed $val
     */
    public function setConfig($name, $val = null)
    {
        if (is_array($name) && $val == null) {
            foreach ($name as $n => $value) {
                $this->setConfig($n, $value);
            }

            return $this;
        }
        if (is_array($this->config[$name])) {
            if (!is_array($val)) {
                user_error("The value for $name must be an array");
            }
            $this->config[$name] = array_merge($this->config[$name], $val);
        } elseif (isset($this->config[$name])) {
            $this->config[$name] = $val;
        }

        return $this;
    }

    /**
     * @param String $name Optional, returns the whole configuration array if empty
     * @return mixed|array
     */
    public function getConfig($name = null)
    {
        if ($name) {
            return isset($this->config[$name]) ? $this->config[$name] : null;
        } else {
            return $this->config;
        }
    }

    /**
     * Set additional attributes
     * @return array Attributes
     */
    public function getAttributes()
    {
        $parentAttributes = parent::getAttributes();
        $attributes = array();

        if (!isset($parentAttributes['placeholder'])) {
            $attributes['placeholder'] = $this->config['defaultparts']['scheme'] . "://example.com"; //example url
        }

        if ($this->config['html5validation']) {
            $attributes += array(
                'type' => 'url', //html5 field type
                'pattern' => 'https?://.+', //valid urls only
            );
        }

        return array_merge(
            $parentAttributes,
            $attributes
        );
    }

    /**
     * Rebuild url on save
     * @param string $url
     * @param array|DataObject $data {@see Form::loadDataFrom}
     * @return $this
     */
    public function setValue($url, $data = null)
    {
        if ($url) {
            $url = $this->rebuildURL($url);
        }
        parent::setValue($url, $data);
    }

    /**
     * Add config scheme, if missing.
     * Remove the parts of the url we don't want.
     * Set any defaults, if missing.
     * Remove any trailing slash, and rebuild.
     * @return string
     */
    protected function rebuildURL($url)
    {
        $defaults = $this->config['defaultparts'];
        if (!preg_match('#^[a-zA-Z]+://#', $url)) {
            $url = $defaults['scheme'] . "://" . $url;
        }
        $parts = parse_url($url);
        if (!$parts) {
            //can't parse url, abort
            return "";
        }
        foreach ($parts as $part => $value) {
            if ($this->config['removeparts'][$part] === true) {
                unset($parts[$part]);
            }
        }
        //set defaults, if missing
        foreach ($defaults as $part => $default) {
            if (!isset($parts[$part])) {
                $parts[$part] = $default;
            }
        }

        return rtrim(http_build_url($defaults, $parts), "/");
    }

    /**
     * Server side validation, using a regular expression.
     */
    public function validate($validator)
    {
        $this->value = trim($this->value);
        $regex = $this->config['validregex'];
        if ($this->value && $regex && !preg_match($regex, $this->value)) {
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
