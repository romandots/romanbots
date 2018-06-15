<?php
namespace RomanBots\Bots;

use GuzzleHttp\Client;

class ProtoBot {

	protected $api_url;
	protected $api_version;
	protected $api_secret;
	protected $access_token;
	protected $client;
	public $sessionId;

	public $response;


	/**
	 * ProtoBot constructor.
	 * @param        $api_url
	 * @param null   $api_secret
	 * @param        $access_token
	 */
	public function __construct( $api_url, $api_secret = null, $access_token = null )
	{
		$this->api_url      = $api_url;
		$this->access_token = $access_token;
		$this->sessionId    = substr(md5( time() ), 0, 32);
		$this->_guzzle();
	}


	public function dump()
	{
		dd( $this );
	}


	/**
	 * Get full URL to API method
	 * @param string $path
	 * @return string
	 */
	protected function _fullpath( $path = '' )
	{
		return sprintf( '%s/%s', $this->api_url, $path );
	}


	/**
	 * Init Guzzle client
	 */
	protected function _guzzle()
	{
		$this->client = new Client( [
			                            'base_uri' => $this->_fullpath(),
			                            'timeout' => 2.0,
			                            // 'debug' => true,
			                            'headers' => [
				                            'Accept' => "*/*",
				                            'Authorization' => 'Bearer ' . $this->access_token,
				                            'Cache-Control' => "no-cache"
			                            ]
		                            ] );
	}

	/**
	 * Output data to browser
	 * @param $data mixed
	 */
	protected function output( $data )
	{
		header("HTTP/1.1 200 OK");
		echo $data;
	}

	/**
	 * Get response body
	 * @return object|false Response
	 */
	public function response( ){
		if(is_object($this->response)){
			return json_decode( (string) $this->response->getBody() );
		} else {
			return false;
		}
	}

	/**
	 * GET request
	 * @param string $path
	 * @param mixed  $data (optional)
	 * @return object self
	 */
	public function get( $path, $data = null )
	{
		$this->response = $this->client->get($path,
		                                  [
			                                  'query' => $data
		                                  ] );
		return $this;
	}


	/**
	 * POST request
	 * @param string $path
	 * @param mixed  $data (optional)
	 * @return object self
	 */
	public function post( $path, $data = null )
	{
		return $this->client->post( $path, $data );
	}

}