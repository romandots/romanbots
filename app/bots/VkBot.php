<?php
namespace RomanBots\Bots;

use RomanBots\API\VkApi;

class VkBot {

	use VkBotReceive, VkBotRespond, VkBotHandleAttachments;

	const VK_API_VERSION = '5.0';

	protected $vkApiToken;
	protected $vkCommunityConfirmationCode;
	protected $vkSecret;
	protected $vkApi;

	public $human;
	public $chatMessage;


	/**
	 * VkBot constructor.
	 * @param      $vkApiToken
	 * @param      $vkCommunityConfirmationCode
	 * @param null $vkSecret
	 */
	public function __construct( $vkApiToken = null, $vkCommunityConfirmationCode = null, $vkSecret = null )
	{
		$this->vkApiToken                  = $vkApiToken ?: VK_API_ACCESS_TOKEN;
		$this->vkCommunityConfirmationCode = $vkCommunityConfirmationCode ?: CALLBACK_API_CONFIRMATION_TOKEN;
		$this->vkSecret                    = $vkSecret ?: VK_API_SECRET;
		$this->vkApi = new VkApi($this->vkApiToken);
		debug($this,"VkBot instance created:");
	}


	/**
	 * Listen for incoming events
	 * This is where it begins...
	 * @return void
	 */
	public function listen()
	{
		// hook up with vk server and
		// listen what it says
		$data = json_decode( file_get_contents( 'php://input' ) );
		if( $data && $this->checkSecretCode( $data)) {
			// what ever it says
			// handle this
			$this->handleIncomingEvent( $data );
		}
	}



	/**
	 * Check the secret signature
	 * @param $response
	 * @return bool
	 */
	private function checkSecretCode( $response){
		if( $this->vkSecret && !isset($response->secret)) {
			fatal('Secret token is not passed!');
		}
		if( $this->vkSecret && $this->vkSecret != $response->secret ) {
			fatal('Secret token is incorrect!');
		}
		return true;
	}


}