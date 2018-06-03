<?php
namespace RomanBots\API;

use RomanBots\Bots\Human;

class VkApi {

	const VK_API_VERSION = '5.0';

	protected $vkApiToken;

	/**
	 * VkApi constructor.
	 * @param      $vkApiToken
	 */
	public function __construct( $vkApiToken = null )
	{
		$this->vkApiToken  = $vkApiToken ?: VK_API_ACCESS_TOKEN;
		debug($this,"VkApi instance created:");
	}


	/**
	 * Send reply back to the human
	 * @param Human $human
	 * @param string $message     Message
	 * @param array $attachments (optional) Attachments
	 * @return object VkApi
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
		if( !$attachments && is_array($attachments) ) {
			$request_params['attachments'] = $attachments;
		}
		debug($request_params, "Sending message to {$human->vk_uid}:{$human->last_name} via VkApi:");

		$get_params = http_build_query($request_params);

		file_get_contents('https://api.vk.com/method/messages.send?'. $get_params);
		return $this;
	}

}