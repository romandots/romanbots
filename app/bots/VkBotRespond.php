<?php
/**
 * Created by PhpStorm.
 * Human: romandots
 * Date: 30.05.2018
 * Time: 20:52
 */

namespace RomanBots\Bots;


trait VkBotRespond {

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
	 * @return object VkBot
	 */
	public function send(){
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

		// Send chat message via VkApi
		$this->vkApi->message($this->human, $message);
		return $this;
	}


	/**
	 * Reply with personal appeal
	 * @param $message
	 * @return void
	 */
	public function reply($message)
	{
		if(!empty($this->human) && !empty($this->human->first_name)) {
			$appeal = ", ".$this->human->first_name;
			$message = preg_replace("/(.*\b)(\W+)$/u", "$1{$appeal}$2", $message);
		}
		$this->send( $message);
	}

	/**
	 * Return secret token
	 * when VK asks
	 * for authorization
	 */
	public function handleVkConfirmation()
	{
		log_msg("CONFIRMATION CODE: {$this->vkCommunityConfirmationCode}");
		$this->_response( $this->vkCommunityConfirmationCode );
	}

	/**
	 * Send default OK
	 * to VK server
	 * @return void
	 */
	protected function ok()
	{
		log_msg("Returning OK. So all the further output will be logged.");
		$this->_response( 'ok' );
		ob_start();
	}


	/**
	 * Return response back
	 * to VK server
	 * @param $data mixed
	 */
	protected function _response( $data )
	{
		header("HTTP/1.1 200 OK");
		echo $data;
	}

}