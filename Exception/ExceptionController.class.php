<?php
	
namespace HttPeep\Exception;

/**
 * Exception controller
 */
class ExceptionController extends \Exception {

    function __construct() {

    }

    /**
     * Get detail relating to message.
     *
     * @return string
     */
    public function get_detail() {

    	return $this->get_trace_and_message();

    }

    /**
     * Get a trace with message
     *
     * @return string
     */
    public function get_trace_and_message() {

        $message = [];

        $message[] = $this->getMessage();

        if( ! empty( $this->getTrace() ) ) :

            foreach( $this->getTrace() as $k => $v ) :

                $passed = '';
                $file = $v[ 'file' ];
                $line = $v[ 'line' ];
                $args = $v[ 'args' ];

                $call = ( isset( $v[ 'class' ], $v[ 'type' ] ) && $v[ 'class' ] != '' && $v[ 'type' ] != '' ) ? $v['class'] . $v['type'] . $v['function'] : $v['function'];

                if( ! empty( $args ) ) {



                    foreach( $args as $i => $p ) {

                        $p = $p;

                        if( is_string( $p ) ) {

                            $passed .= ( is_string( $p ) ) ? "'{$p}', " : "{$p}, ";

                        } elseif( is_array( $p ) ) {

                            if( ! empty( $p ) ) {
                                $passed .= '[ ';
                                foreach( $p as $ak => $av ) {

                                    $passed .= ( is_string( $ak ) ) ? "'{$ak}'" : "{$ak}";
                                    $passed .= " => ";
                                    $passed .= ( is_string( $av ) ) ? "'{$av}', " : "{$av}, ";


                                }

                                $passed = rtrim( $passed, ', ' );
                                $passed .= ' ], ';
                            }

                        }

                    }

                    $passed = rtrim( $passed, ', ' );

                }

                $call = "{$call}( {$passed} )";

                $message[] = "#{$k} " . $v['file'] . "({$line}): {$call}";

            endforeach;

        endif;

        return implode( "\r\n", $message );

    }

}
