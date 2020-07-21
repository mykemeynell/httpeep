<?php

namespace HttPeep\Request;

class Put extends RequestController {

    function __construct( $config ) {

        if( ! is_object( $config ) ) {

            throw new \HttPeep\Exception\RequestException( '$data can only be type of object, ' . gettype( $config ) . ' passed.' );

        }

        $this->config = $config;

        $this->transmission = $this->transmit();

        return $this;

    }

}
