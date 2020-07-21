<?php

namespace HttPeep;

/**
 * API class.
 */
class API {

    /**
     * Endpoint
     *
     * @var string
     */
    protected $endpoint;

    /**
     * Instance
     *
     * @var API
     */
    protected static $instance;

    /**
     * Build the Auth object.
     */
    function __construct() {

        self::$instance = $this;

    }

    /**
     * Get current instnace.
     *
     * @return API
     */
    public static function instance() {

        return self::$instance;

    }

    /**
     * Set property object.
     *
     * @param string $property
     * @param mixed $value
     */
    public function set_property( $property, $value ) {

        $this->$property = $value;

        return $this;

    }


}
