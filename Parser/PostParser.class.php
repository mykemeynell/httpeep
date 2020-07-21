<?php

namespace HttPeep\Parser;

/**
 * Parser object.
 */
class PostParser extends \HttPeep\Parser\ParserController {

    /**
     * Build the parser object.
     */
    function __construct( $data, $config = [] ) {

        if( ! is_array( $config ) )
            throw new \HttPeep\Exception\ParserExcetion( 'Config can only be type of array' );

        if( ! isset( $config[ 'param_seperator' ] ) )
            $this->set_property( 'param_seperator', '&' );

        if( ! isset( $config[ 'prepend' ] ) )
            $this->set_property( 'prepend', '?' );

        if( ! is_array( $data ) )
            throw new \HttPeep\Exception\ParserException( 'Data can only be type of array' );

        $this->set_property( 'params', http_build_query( $data, '', $this->param_seperator ) );

        return $this;

    }
}
