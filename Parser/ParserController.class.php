<?php

namespace HttPeep\Parser;

/**
 * Parser object.
 */
class ParserController extends \HttPeep\Client {

    /**
     * Build the parser object.
     */
    function __construct( $data ) {

        if( is_string( $data ) ) {

            $this->data = $data;

        }

    }

}
