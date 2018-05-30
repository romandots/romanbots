<?php
/**
 * Created by PhpStorm.
 * User: romandots
 * Date: 30.05.2018
 * Time: 10:39
 */

namespace RomanBots\Bot;

trait BotCommands {

	/**
	 * Load command in execute it
	 * @return void
	 */
	public function executeCommand(){
		log_msg("Execute command...");
		try{
			$commands = include BOT_BASE_DIRECTORY."/commands.php";
			debug($commands, "Loaded commands:");

			// Get every command from config file
			// and check if it matches
			// execute when found
			foreach ($commands as $commandClass ){
				/** @var Command $command */
				$command = new $commandClass( $this->userData, $this->userMessage, $this);
				debug($command, $commandClass);
				if($command->match()){
					$command->execute();
					break;
				}
			}

		} catch (\Exception $e) {
			log_error('Cannot execute the command '.$command.': '.$e);
			die('Cannot execute the command');
		}
	}

	/**
	 * Find command in user message
	 * @return bool|string Command name
	 */
	public function isCommand(){
		log_msg("Check if the message `{$this->userMessage}` is command...");
		if($this->_extractCommandName($this->userMessage)){
			log_msg("...true");
			return true;
		} else	{
			log_msg("...false");
			return false;
		}
	}

	/**
	 * Sets current working command
	 * @param $commandName string
	 */
	public function startCommandFlow($commandName){
		$this->commandInProgress = $commandName;
	}

	/**
	 * Sets current working command to null
	 */
	public function endCommandFlow(){
		$this->commandInProgress = null;
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