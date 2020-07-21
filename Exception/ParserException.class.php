<?php

namespace HttPeep\Exception;

/**
 * \HttPeep\Exception\ClientException object.
 */
class ParserException extends ExceptionController {

    /**
     * Build the ClientException object.
     *
     * @return \HttPeep\Exception\ClientException
     */
    function __construct( $message ) {

        try {

            if( ! is_string( $message ) )
                throw new \HttPeep\Exception\Exception( 'Exception messages can only be type of string' );

            $this->message = $message;

        } catch( \HttPeep\Exception\Exception $e ) {

            dd( $e->getMessage() );

        }

    }

}
