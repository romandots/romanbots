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

	public $user;
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
	 * @return void
	 */
	public function listen()
	{
		$data = $this->_getInput();
		$this->handleEvent( $data );
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