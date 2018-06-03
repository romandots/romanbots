<?php
/**
 * Created by PhpStorm.
 * Human: romandots
 * Date: 30.05.2018
 * Time: 10:39
 */

namespace RomanBots\Bot;

use RomanBots\Commands\Command;

trait BotHandleEvents {


	/**
	 * Handle new incoming chat message
	 * @param $message object
	 */
	public function messageReceived($message)
	{
		debug($message,'messageReceived');
		// 1. Get sender info
		if( ! $this->human = Human::fromMessage($message) ){
			log_error('Cannot get sender data');
			$this->ok();
		}

		// 2. Check if it has body
		if(!property_exists($message, "body")){
			log_error('$message->body does not exists');
			$this->ok();
		}
		$this->userMessage = $message->body;

		// 3. Check if some command is subscribed
		// for this message (waiting for input)
		$key = $this->human->vk_uid.":subscribed";
		if($subscription = redis_get($key)){
			list($command, $param) = explode(":", $subscription);
			Command::load($command, $this);
			$this->continueWithInput($command, $param);
		}

		// 4. Check if message is command
		if( $this->isCommand() ) {
			$this->executeCommand();
		}

		// 5. If it is not — just chat and act like a human
		$this->chat();
	}


	/**
	 * Return secret token
	 * when VK asks
	 * for authorization
	 */
	public function handleConfirmation()
	{
		$this->response( $this->vkCommunityConfirmationCode );
	}

}