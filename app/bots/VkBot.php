<?php
namespace RomanBots\Bots;

use RomanBots\API\VkApi;

class VkBot extends ProtoBot {

	use VkBotReceive, VkBotRespond, VkBotHandleAttachments;

	const API_VERSION = '5.0';

	public $vkApi;
	public $human;
	public $chatMessage;


	/**
	 * VkBot constructor.
	 */
	public function __construct()
	{
		parent::__construct( config( "vk.api_url" ), config( 'vk.api_secret' ), config( "vk.api_access_token" ) );
		$this->vkApi = new VkApi();
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
		if ( $data && $this->checkSecretCode( $data ) )
		{
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
	private function checkSecretCode( $response )
	{
		if ( $this->api_secret && !isset( $response->secret ) )
		{
			fatal( 'Secret token is not passed!' );
		}
		if ( $this->api_secret && $this->api_secret != $response->secret )
		{
			fatal( 'Secret token is incorrect!' );
		}

		return true;
	}


	/**
	 * Return secret token
	 * when VK asks
	 * for authorization
	 */
	public function handleVkConfirmation()
	{
		log_msg( "CONFIRMATION CODE: {config('vk.confirmation_code)}" );
		$this->output( config( 'vk.confirmation_code' ) );
	}
}