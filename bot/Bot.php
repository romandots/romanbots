<?php

namespace RomanBots\Bot;

use RomanBots\Commands\Command;

class Bot {

	use BotHandleEvents, BotCommands, BotHandlesAttachments, BotChats;

	protected $vkApiToken;
	protected $vkCommunityConfirmationCode;
	protected $vkSecret;

	const VK_API_VERSION = '5.0';

	const BOT_BASE_DIRECTORY = BOT_BASE_DIRECTORY;
	const BOT_LOGS_DIRECTORY = BOT_LOGS_DIRECTORY;
	const BOT_IMAGES_DIRECTORY = BOT_IMAGES_DIRECTORY;
	const BOT_AUDIO_DIRECTORY = BOT_AUDIO_DIRECTORY;
	const BOT_VOICE_DIRECTORY = BOT_VOICE_DIRECTORY;

	public $human;
	public $userMessage;


	/**
	 * Bot constructor.
	 * @param      $vkApiToken
	 * @param      $vkCommunityConfirmationCode
	 * @param null $vkSecret
	 */
	public function __construct( $vkApiToken, $vkCommunityConfirmationCode, $vkSecret = null )
	{
		$this->vkApiToken                  = $vkApiToken;
		$this->vkCommunityConfirmationCode = $vkCommunityConfirmationCode;
		$this->vkSecret                    = $vkSecret;
		$this->_mkdir();
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
		$data = $this->_getInput();
		// what ever it says
		// handle this
		$this->handleEvent( $data );
	}

	/**
	 * Handle incoming callback event
	 * @link  https://vk.com/dev/callback_api
	 * @param $event object
	 */
	public function handleEvent( $event )
	{
		if(!is_object($event)){
			die("Event must be object");
		}
		if(!property_exists($event, "type")){
			die("No event type set");
		}
		try
		{
//			debug($event->type, "Event dispatched:");
			switch ( $event->type )
			{
				//Подтверждение сервера
				case 'confirmation':
					$this->handleConfirmation();
					break;

				//Получение нового сообщения
				case 'message_new':
					$this->messageReceived($event->object);
					break;

				default:
			}
			// Return OK message so VK
			// won't bother us with the same
			// messages anymore
			$this->ok();
			exit();
		} catch ( Exception $e )
		{
			log_error( $e );
		}
	}

	/**
	 * Send default OK
	 * to VK server
	 * @return void
	 */
	protected function ok()
	{
		$this->response( 'ok' );
	}


	/**
	 * Return response back
	 * to VK server
	 * @param $data mixed
	 */
	protected function response( $data )
	{
		header("HTTP/1.1 200 OK");
		echo $data;
		exit();
	}


	public function setUserById(){


	}

	/**
	 * Make all required dirs
	 * @return void
	 */
	private function _mkdir()
	{

		try
		{
			if (!is_dir(BOT_LOGS_DIRECTORY))
			{
				@mkdir(self::BOT_LOGS_DIRECTORY);
				@chmod(self::BOT_LOGS_DIRECTORY, 0777);
			}
			if (!is_dir(BOT_IMAGES_DIRECTORY))
			{
				@mkdir(self::BOT_IMAGES_DIRECTORY);
				@chmod(self::BOT_IMAGES_DIRECTORY, 0777);
			}
			if (!is_dir(BOT_AUDIO_DIRECTORY))
			{
				@mkdir(self::BOT_AUDIO_DIRECTORY);
				@chmod(self::BOT_AUDIO_DIRECTORY, 0777);
			}
			if (!is_dir(BOT_VOICE_DIRECTORY))
			{
				@mkdir(self::BOT_VOICE_DIRECTORY);
				@chmod(self::BOT_VOICE_DIRECTORY, 0777);
			}
		} catch (\Exception $e)
		{
			log_error("mkdir failed: " . $e);
		}
	}

	private function _getInput()
	{
		$data = json_decode( file_get_contents( 'php://input' ) );
		if( $data && $this->_checkIdentity($data)) {
			return $data;
		}
	}



	/**
	 * Check the secret signature
	 * @param $response
	 * @return bool
	 */
	private function _checkIdentity($response){
		if( $this->vkSecret && !isset($response->secret)) {
			die('Secret token is not passed!');
		}
		if( $this->vkSecret && $this->vkSecret != $response->secret ) {
			die('Secret token is incorrect!');
		}
		return true;
	}


}