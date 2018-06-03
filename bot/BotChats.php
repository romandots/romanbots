<?php
/**
 * Created by PhpStorm.
 * Human: romandots
 * Date: 30.05.2018
 * Time: 20:52
 */

namespace RomanBots\Bot;


trait BotChats {

	/**
	 * Keep conversation alive
	 */
	public function chat(){

		$this->ok();

	}

	/**
	 * Send reply back to the human
	 * @param $message string Message or format string for printf
	 * @param mixed Any number of parameters that should be passed
	 *              to the command
	 * @return void
	 */
	public function reply(){
		if(!$this->human || !$this->human->vk_uid){
			return;
		}
		$args = func_get_args();
		if(!count($args)) {
			$message = "";
		} else {
			$message = array_shift($args);
			if(count($args) > 1){
				$message = sprintf($message, ...$args);
			}
		}
		// undefined $this->human->vk_uid,
		$request_params = array(
			'message' => $message.$this->human->vk_uid,
			'user_id' => $this->human->vk_uid,
			'access_token' => $this->vkApiToken,
			'v' => self::VK_API_VERSION
		);
		debug($message, "Reply sent to {$this->human->vk_uid}:{$this->human->last_name}");

		$get_params = http_build_query($request_params);

		file_get_contents('https://api.vk.com/method/messages.send?'. $get_params);
	}


	/**
	 * Reply with personal appeal
	 * @param $message
	 * @return void
	 */
	public function replyPersonally($message)
	{
		if(!empty($this->human) && !empty($this->human->first_name)) {
			$appeal = ", ".$this->human->first_name;
			$message = preg_replace("/(.*\b)(\W+)$/u", "$1{$appeal}$2", $message);
		}
		$this->reply($message);
	}


	/**
	 * Randomly add personal appeal to reply
	 * @param $message
	 * @return void
	 */
	public function replyRandomly($message){
		if( rand(0,1) )
		{
			$this->reply( $message );
		} else {
			$this->replyPersonally($message);
		}
	}

}