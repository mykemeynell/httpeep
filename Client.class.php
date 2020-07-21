<?php

namespace HttPeep;

/**
 * HttPeep\Client object.
 */
class Client extends API {

    /**
     * Endpoint address.
     *
     * @var string
     */
    protected $endpoint;

    /**
     * Resource.
     *
     * @var string
     */
    protected $resource = '';

    /**
     * Resolved url
     *
     * @param array
     */
    private $resolve;

    /**
     * Arguments
     *
     * @var mixed
     */
    protected $args;

    /**
     * Method
     *
     * @var string
     */
    protected $method;

    /**
     * Headers
     *
     * @var array
     */
    protected $headers = [];
    
    /**
     * Curl options
     *
     * @var array
     */
    protected $curl_options = [];
    
    /**
     * Protocol
     *
     * @var string
     */
    protected $protocol = 'tcp';
    
    /**
     * Build the \HttPeep\Client object.
     *
     * @param string $url URL being queried.
     */
    function __construct( $url = null, $options = null ) {

		if( ! is_null( $url ) ) {
	     
	        if( ! is_string( $url ) )
	            throw new \HttPeep\Exception\ClientException( '$url can only be a string, you supplied ' . gettype( $url ) );
	
	        $this->set_property( 'endpoint', $url );

		}
        
        if( ! is_null( $options ) ) {
	        
	        if( isset( $options[ 'curl' ] ) )
	        	$this->curl_options = $options[ 'curl' ];
	        	
	        if( isset( $options[ 'headers' ] ) )
	        	$this->headers = $options[ 'headers' ];
	        
        }

    }
    
	/**
	 * Set the URL
	 *
	 * @param string $url
	 * @return HttPeep\Client
	 */
	public function set_url( $url ) {
		
		if( ! is_string( $url ) )
			throw new \HttPeep\Exception\ClientException( '$url can only be a string, you supplied ' . gettype( $url ) );
			
		$this->set_property( 'endpoint', $url );
	
		return $this;
		
	}

    /**
     * Get the URL
     *
     * @return string
     */
    public function get_url() {

        return sprintf( '%s://%s:%d%s', $this->resolve[ 'scheme' ], $this->resolve[ 'host' ], $this->resolve[ 'port' ], $this->resolve[ 'path' ] );

    }

    /**
     * Append query to end of URL.
     *
     * @return string
     */
    public function get_query() {

        if( is_object( $this->args ) && property_exists( $this->args, 'prepend' ) && property_exists( $this->args, 'params' ) )
            return sprintf( '%s%s', $this->args->prepend, $this->args->params );
        else {
            return '';
        }

    }

    /**
     * Build a config array to pass to Request objects
     */
    public function config() {

        return (object)[
            'endpoint'      => $this->endpoint,
            'resolve'       => $this->resolve,
            'args'          => ( ! empty( ( array )$this->args ) ) ? $this->args : ( object )[ 'params' => [] ],
            'resource'      => $this->resource,
            'url'           => $this->get_url(),
            'query'         => $this->get_query(),
            'method'        => $this->method,
            'headers'       => $this->headers,
            'curlopts'		=> $this->curl_options
        ];

    }

    /**
     * Return the JSON of body.
     *
     * @return object
     */
    public function json() {

		return ( property_exists( $this->request, 'response' ) ) ? json_decode( $this->request->response ) : null;

    }
    
    /**
     * Get the body
     *
     * @return string
     */
    public function body() {
    	
		return ( property_exists( $this->request, 'response' ) ) ? $this->request->response : null;
    	
    }
    
    /**
     * Return the body as a PHP array
     *
     * @return array 
     */
    public function to_array() {
    	
    	return ( property_exists( $this->request, 'response' ) ) ? json_decode( $this->request->response, true ) : null;

    }
    
    /**
	 * Function returns XML string for input associative array. 
	 * @param Array $array Input associative array
	 * @param String $wrap Wrapping tag
	 * @param Boolean $upper To set tags in uppercase
	 */
	public function xml($array = null, $wrap = 'RESPONSE', $upper = true, $recur = false) {
		
		if( ! is_object( $this->json() ) )
			return null;
		
	    $xml = [];
	    
	    if( is_null( $array ) )
	    	$array = $this->json();
	    
	    if ( $wrap != null && $recur == false )
	        $xml[] = "<$wrap>\n";
	
	    foreach ($array as $key => $value) :
		    
		    if ($upper == true)
	            $key = strtoupper($key);
		    
		    if( is_array( $value ) || is_object( $value ) ) {
		    	
		    	$child = $this->xml( $value, $wrap, $upper, true );
		    	
		    	$child_key = ( $recur === false && ! is_int( $key ) ) ? $key : "ROW{$key}";
		    	
		    	$xml[] = "\t<$child_key>";
		    	$xml[] = "\t\t{$child}";
		    	$xml[] = "\t</$child_key>";
		    	
		    	continue;
		    	
		    }
	
	        $xml[] = "\t<$key>" . htmlspecialchars(trim($value)) . "</$key>";
	        
	    endforeach;
	    
	    if ( $wrap != null && $recur == false )
	        $xml[] = "</$wrap>";
	
	    return implode( "\r\n", $xml );
	
	}

    /**
     * Add a header to the request
     *
     * @param string $header
     * @param string $value
     * @return Client
     */
    public function header( $header, $value ) {

        $this->headers[] = "{$header}: {$value}";

        return $this;

    }
    
	/**
	 * Get the default port number for a request scheme
	 *
	 * @param string $scheme
	 * @return int The port number
	 */
	public function _get_default_request_port( $scheme ) {
		
		return ( array_key_exists( strtolower( $scheme ), $this->request_ports ) ) ? $this->request_ports[ strtolower( $scheme ) ] : null;
		
	}

    /**
     * GET method request
     *
     * @param string $resource Path to resource.
     * @param array $params Data that can be appended to the URL and passed to endpoint.
     *
     * @return array
     */
    public function get( $resource, $params = [] ) {

        if( ! is_string( $resource ) )
            throw new \HttPeep\Exception\ClientException( 'Resource must be type of string' );

        if( ! is_array( $params ) )
            throw new \HttPeep\Exception\ClientException( 'Params can only be type of array' );

        if( ! empty( $params ) )
            $this->set_property( 'args', new \HttPeep\Parser\GetParser( $params ) );

        $this->set_property( 'resource', $resource );

        $this->set_property( 'method', 'get' );

        $this->resolve = parse_url( rtrim( $this->endpoint, '/' ) . $this->resource );

        if( ! isset( $this->resolve['port'] ) )
	        $this->resolve['port'] = getservbyname( $this->resolve[ 'scheme' ], $this->protocol );

        $this->set_property( 'request', new \HttPeep\Request\Get( $this->config() ) );

        return $this;

    }

    /**
     * POST method request
     *
     * @param string $resource Path to resource.
     * @param array $params Data that can be appended to the URL and passed to endpoint.
     *
     * @return array
     */
    public function post( $resource, $params = [] ) {

        if( ! is_string( $resource ) )
            throw new \HttPeep\Exception\ClientException( 'Resource must be type of string' );

        if( ! is_array( $params ) )
            throw new \HttPeep\Exception\ClientException( 'Params can only be type of array' );

        if( ! empty( $params ) )
            $this->set_property( 'args', new \HttPeep\Parser\PostParser( $params ) );

        $this->set_property( 'resource', $resource );

        $this->set_property( 'method', 'post' );

        $this->resolve = parse_url( rtrim( $this->endpoint, '/' ) . $this->resource );

        if( ! isset( $this->resolve['port'] ) )
	        $this->resolve['port'] = getservbyname( $this->resolve[ 'scheme' ], $this->protocol );

        $this->set_property( 'request', new \HttPeep\Request\Post( $this->config() ) );

        return $this;

    }

    /**
     * PUT method request
     *
     * @param string $resource Path to resource.
     * @param array $params Data that can be appended to the URL and passed to endpoint.
     *
     * @return array
     */
    public function put( $resource, $params = [] ) {

        if( ! is_string( $resource ) )
            throw new \HttPeep\Exception\ClientException( 'Resource must be type of string' );

        if( ! is_array( $params ) )
            throw new \HttPeep\Exception\ClientException( 'Params can only be type of array' );

        if( ! empty( $params ) )
            $this->set_property( 'args', new \HttPeep\Parser\PutParser( $params ) );

        $this->set_property( 'resource', $resource );

        $this->set_property( 'method', 'put' );

        $this->resolve = parse_url( rtrim( $this->endpoint, '/' ) . $this->resource );

        if( ! isset( $this->resolve['port'] ) )
	        $this->resolve['port'] = getservbyname( $this->resolve[ 'scheme' ], $this->protocol );

        $this->set_property( 'request', new \HttPeep\Request\Put( $this->config() ) );

        return $this;

    }

    /**
     * PUT method request
     *
     * @param string $resource Path to resource.
     * @param array $params Data that can be appended to the URL and passed to endpoint.
     *
     * @return array
     */
    public function delete( $resource, $params = [] ) {

        if( ! is_string( $resource ) )
            throw new \HttPeep\Exception\ClientException( 'Resource must be type of string' );

        if( ! is_array( $params ) )
            throw new \HttPeep\Exception\ClientException( 'Params can only be type of array' );

        if( ! empty( $params ) )
            $this->set_property( 'args', new \HttPeep\Parser\DeleteParser( $params ) );

        $this->set_property( 'resource', $resource );

        $this->set_property( 'method', 'delete' );

        $this->resolve = parse_url( rtrim( $this->endpoint, '/' ) . $this->resource );

        if( ! isset( $this->resolve['port'] ) )
	        $this->resolve['port'] = getservbyname( $this->resolve[ 'scheme' ], $this->protocol );

        $this->set_property( 'request', new \HttPeep\Request\Delete( $this->config() ) );

        return $this;

    }


}
