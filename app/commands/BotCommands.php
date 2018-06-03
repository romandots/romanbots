<?php
/**
 * Created by PhpStorm.
 * Human: romandots
 * Date: 30.05.2018
 * Time: 10:39
 */

namespace RomanBots\VkBot;

use RomanBots\Commands\Command;

trait BotCommands {


	public function waitForInput($command, $param){
		log_msg("Subscribe and wait for input of `$param`");
		$key = $this->human->vk_uid.":subscribed";
		$value = $command.":".$this->$param;
		redis_set($key, $value);
		return $this;
	}


	public function continueWithInput($command, $param){
		$key = $this->human->vk_uid.":subscribed";
		if($subscription = redis_get($key)){
			list($command, $param) = explode(":", $subscription);
			$command = Command::load( $command, $this->chatMessage, $this);
			$command->setParam($param, $this->chatMessage);
			$command->execute();
		}

	}

	/**
	 * Load command in execute it
	 * @return void
	 */
	public function executeCommand(){
		log_msg("Execute command...");
		try{
			$commands = include BOT_BASE_DIRECTORY."/commands.php";
			log_msg("Loading commands...");

			// Get every command from config file
			// and check if it matches
			// execute when found
			foreach (Command::all() as $command ){
				if($command->match()){
					$command->execute();
					break;
				}
			}

		} catch (\Exception $e) {
			log_error('Cannot execute the command: '.$e);
			die('Cannot execute the command');
		}
	}

	/**
	 * Find command in user message
	 * @return bool|string Command name
	 */
	public function isCommand(){
		log_msg("Check if the message `{$this->chatMessage}` is command...");
		if( empty(COMMAND_PREFIX) ){
			return true;
		} elseif($this->_extractCommandName($this->chatMessage)){
				log_msg("...true");
				return true;
		} else	{
			log_msg("...false");
			return false;
		}
	}

	/**
	 * Find command name is string
	 * @param $string string
	 * @return null
	 */
	protected function _extractCommandName($string){
		if(preg_match("/^".COMMAND_PREFIX."(\S+).*/i", $string, $matches)){
			debug($matches[1], "Exrtacted command:");
			return $matches[1];
		} else {
			return null;
		}
	}
}