<?php

namespace RomanBots\Bot;

use RomanBots\Commands\Command;

class Bot {

	use BotHandleEvents, BotCommands;

	protected $vkApiToken;
	protected $vkCommunityConfirmationCode;
	protected $vkSecret;

	const VK_API_VERSION = '5.0';

	const BOT_BASE_DIRECTORY = BOT_BASE_DIRECTORY;
	const BOT_LOGS_DIRECTORY = BOT_LOGS_DIRECTORY;
	const BOT_IMAGES_DIRECTORY = BOT_IMAGES_DIRECTORY;
	const BOT_AUDIO_DIRECTORY = BOT_AUDIO_DIRECTORY;
	const BOT_VOICE_DIRECTORY = BOT_VOICE_DIRECTORY;

	public $userId;
	public $userData;
	public $userMessage;
	protected $commandInProgress;

	/**
	 * Bot constructor.
	 * @param $vkApiToken
	 * @param $vkCommunityConfirmationCode
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
	 * Save incoming attachments
	 * and return their local filenames
	 * @param $data
	 * @return array
	 */
	protected function saveIncomingAttachments( $data )
	{
		try
		{
			$attachments = [];
			foreach ( $data as $item )
			{
				// Voice message
				if ( $item['type'] == "doc" && $item['doc']['title'] == "audio.webm" )
				{
					$remoteFile = $item['doc']['url'];
					$filepath   = self::BOT_VOICE_DIRECTORY;
					$localFile = $filepath . '/' . time() . '.' . $item['doc']['ext'];
					if ( copy( $remoteFile, $localFile ) )
					{
						$attachments[ $item['doc']['title'] ] = $localFile;
					}
				}
			}
			return $attachments;
		} catch ( Exception $e )
		{
			log_error( $e );
		}
	}


	/**
	 * @param $message object
	 * @return mixed object
	 */
	protected function getSenderData($message){
		//затем с помощью users.get получаем данные об авторе
		$this->userId = $message->user_id;
		$method = "https://api.vk.com/method/users.get?user_ids={$message->user_id}&access_token={$this->vkApiToken}&v=5.0";
		if($callback = json_decode( file_get_contents( $method ))) {
			$this->userData = $callback->response[0];
			$this->userData->id = $this->userId;
			return $this->userData;
		}
	}


	/**
	 * Send reply back to the user
	 * @param $message string Message or format string for printf
	 * @param mixed Any number of parameters that should be passed
	 *              to the command
	 * @return void
	 */
	public function reply(){
		$args = func_get_args();
		if(!count($args)) {
			$message = "";
		} else {
			$message = array_shift($args);
			if(count($args) > 1){
				$message = sprintf($message, ...$args);
			}
		}
		$request_params = array(
			'message' => $message,
			'user_id' => $this->userId,
			'access_token' => $this->vkApiToken,
			'v' => self::VK_API_VERSION
		);
		debug($message, "Reply sent to {$this->userId}");

		$get_params = http_build_query($request_params);

		file_get_contents('https://api.vk.com/method/messages.send?'. $get_params);
	}


	/**
	 * Reply with personal appeal
	 * @param $message
	 * @param $userData
	 * @return void
	 */
	public function replyPersonally($message, $userData)
	{
		if(!empty($userData) && !empty($userData->first_name)) {
			$appeal = ", ".$userData->first_name;
			$message = preg_replace("/(.*\b)(\W+)$/u", "$1{$appeal}$2", $message);
		}
		$this->reply($message);
	}


	/**
	 * Randomly add personal appeal to reply
	 * @param $message
	 * @param $userData
	 * @return void
	 */
	public function replyRandomly($message, $userData){
		if( rand(0,1) )
		{
			$this->reply( $message );
		} else {
			$this->replyPersonally($message, $userData);
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


	/**
	 * Make all required dirs
	 * @return void
	 */
	private function _mkdir() {

		if(!is_dir(BOT_LOGS_DIRECTORY))
		{
			mkdir( self::BOT_LOGS_DIRECTORY );
			chmod( self::BOT_LOGS_DIRECTORY, 0777 );
		}
		if(!is_dir(BOT_IMAGES_DIRECTORY))
		{
			mkdir( self::BOT_IMAGES_DIRECTORY );
			chmod( self::BOT_IMAGES_DIRECTORY, 0777 );
		}
		if(!is_dir(BOT_AUDIO_DIRECTORY))
		{
			mkdir( self::BOT_AUDIO_DIRECTORY );
			chmod( self::BOT_AUDIO_DIRECTORY, 0777 );
		}
		if(!is_dir(BOT_VOICE_DIRECTORY))
		{
			mkdir( self::BOT_VOICE_DIRECTORY );
			chmod( self::BOT_VOICE_DIRECTORY, 0777 );
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