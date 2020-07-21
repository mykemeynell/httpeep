<?php

namespace HttPeep\Request;

/**
 * RequestController Object.
 */
class RequestController extends \HttPeep\Client {

    /**
     * cURL options.
     *
     * @var array
     */
    protected $curl_opts = [
        CURLOPT_RETURNTRANSFER			=> true,
		CURLOPT_SSL_VERIFYPEER			=> 0,
		CURLOPT_SSL_VERIFYHOST			=> 0,
		CURLOPT_VERBOSE					=> 0
    ];

    /**
     * cURL handle.
     *
     * @var mixed
     */
    protected $curl_handle;

    /**
     * Build RequestController object.
    */
    function __construct() {}

    /**
	 * Sets a curl option.
	 *
	 * @param mixed $option The option that is being set.
	 * @param mixed $value The value that is to be assigned to the curl option.
	 * @return boolean Always returns true.
	 */
	protected function _set_curl_option( $option, $value ) {

		$this->curl_opts[ $option ] = $value;

		return true;

	}

    /**
     * Transmit a request and get the response.
     */
    public function transmit() {

        try {

			$this->curl_handle = curl_init();

            if( strtolower( $this->config->method ) == 'get' )
                $this->_set_curl_option( CURLOPT_URL, $this->config->url . $this->config->query );
            else
                $this->_set_curl_option( CURLOPT_URL, $this->config->url );

            if( strtolower( $this->config->method ) == 'post' ) {

                $this->_set_curl_option( CURLOPT_POST, true );
                $this->_set_curl_option( CURLOPT_POSTFIELDS, $this->config->args->params );

            }

            if( strtolower( $this->config->method ) == 'put' ) {

                $this->_set_curl_option( CURLOPT_CUSTOMREQUEST, 'PUT' );
                $this->_set_curl_option( CURLOPT_POSTFIELDS, $this->config->args->params );

            }

            if( strtolower( $this->config->method ) == 'delete' ) {

                $this->_set_curl_option( CURLOPT_CUSTOMREQUEST, 'DELETE' );
                $this->_set_curl_option( CURLOPT_POSTFIELDS, $this->config->args->params );

            }

            $this->_set_curl_option( CURLOPT_HTTPHEADER, $this->config->headers );

			curl_setopt_array( $this->curl_handle, $this->curl_opts );
			
			if( property_exists( $this->config, 'curlopts' ) && count( ( array )$this->config->curlopts ) > 0 ) {
				
				foreach( $this->config->curlopts as $opt => $value ) {

					if( ! defined( 'CURLOPT_' . strtoupper( $opt ) ) )
						 throw new \HttPeep\Exception\RequestException( 'Error when attempting to transmit. \'CURLOPT_' . strtoupper( $opt ) . '\' is not a valid cURL option.' );

					$this->_set_curl_option( constant( strtoupper( "CURLOPT_{$opt}" ) ), $value );

				}
			}
			
			$this->response = curl_exec( $this->curl_handle );

			if( $this->response === false )
				throw new \HttPeep\Exception\RequestException( curl_error( $this->curl_handle ) );

            $this->set_property( 'http_status_code', curl_getinfo( $this->curl_handle, CURLINFO_HTTP_CODE ) );

		} catch( \HttPeep\Exception\RequestException $e ) {

			echo "<pre>" . $e->get_detail() . "</pre>";

		}

        return $this;

    }


}
