<?php
namespace RomanBots\API;

use GuzzleHttp\Client;
use RomanBots\Bots\Human;

class VkApi {

	const VK_API_VERSION = '5.0';

	protected $vkApiToken;
	protected $client;

	/**
	 * VkApi constructor.
	 */
	public function __construct()
	{
		$this->vkApiToken  =  config( 'vk.api_access_token' );
		$this->_guzzle();
		// debug($this,"VkApi instance created:");
	}

	/**
	 * Init Guzzle client
	 */
	protected function _guzzle()
	{
		$this->client = new Client( [
			                            'base_uri' => config('vk.api_url'),
			                            'timeout' => 2.0,
			                            // 'debug' => true,
			                            'headers' => [
				                            'Accept' => "*/*",
				                            'Authorization' => 'Bearer ' . config('vk.access_token'),
				                            'Cache-Control' => "no-cache"
			                            ]
		                            ] );
	}


	/**
	 * Send reply back to the human
	 * @param Human  $human
	 * @param string $message     Message
	 * @param array  $attachments (optional) Attachments
	 * @return object VkApi
	 * @throws \GuzzleHttp\Exception\GuzzleException
	 */
	public function message(Human $human, $message, $attachments = null){
		if(!$human || !$human->vk_uid){
			fatal("VK UID not set");
		}
		$request_params = array(
			'message' => $message,
			'user_id' => $human->vk_uid,
			'access_token' => $this->vkApiToken,
			'v' => self::VK_API_VERSION
		);
		$get_params = http_build_query($request_params);
		if( !$attachments && is_array($attachments) ) {
			$request_params['attachments'] = $attachments;
		}
		debug($request_params, "Sending message to {$human->vk_uid}:{$human->last_name} via VkApi:");

		$client = new Client();
		$client->request('GET', 'https://api.vk.com/method/messages.send?'. $get_params);
		return $this;
	}

}