<?php
namespace RomanBots\Commands;

abstract class Command {

	protected $user;
	protected $originalMessage;
	protected $command;
	protected $params = [];
	protected $regexp;

	protected $bot; //caller bot instance


	/**
	 * Command constructor.
	 * @param $user            object User data
	 * @param $originalMessage string Full message from user
	 * @param $bot \RomanBots\Bot\Bot Bot Class Instance (caller)
	 */
	public function __construct( $user, $originalMessage, &$bot )
	{
		log_msg("Command {$this->command} Class called...");
		$this->user    = $user;
		$this->originalMessage  = $originalMessage;
		$this->bot  = $bot;
		debug($this, $this->command);
	}


	/**
	 * Magic getter pulls properties
	 * from params array
	 * @param $name
	 * @return mixed
	 */
	public function __get($name){
		if (!property_exists( $this, $name) && array_key_exists($name, $this->params)) {
			return $this->params[$name];
		} else {
			return $this->$name;
		}
	}


	/**
	 * Check if user message matches the command syntax
	 * @return bool
	 */
	public function match(){
		log_msg("Testing message `$this->originalMessage` on regular exp `/^".COMMAND_PREFIX.$this->regexp."$/ui` for command `$this->command`");
		if(preg_match("/^".COMMAND_PREFIX.$this->regexp."$/ui", mb_strtolower($this->originalMessage),$matches)){
			array_shift($matches);
			$this->assignParameters($matches);
			return true;
		}
		return false;
	}


	public function assignParameters($params){
		debug($params, "Parameters received:");
		$assignedParams = [];
		foreach ($this->params as $parameter){
			$assignedParams[$parameter] = current($params);
		}
		debug($assignedParams, "Parameters assigned:");
		$this->params = $assignedParams;
	}


	abstract function action( );


	public function execute( ){
		log_msg("Executing command `$this->command` for message `$this->originalMessage`");
		$this->bot->startCommandFlow($this->command);
		$this->action();
	}


	public function finish($message){
		$this->output($message);
		$this->bot->endCommandFlow();
	}


	public function error( $error ){
		$this->output("Ошибка: ".$error);
		$this->bot->endCommandFlow();
	}


	/**
	 * @todo
	 * @param $message
	 */
	public function confirm($message){
		$message .= "\n\n";
		$message .= "Отправьте +, если вы согласны.\n";
		$message .= "Или любой другой символ, если передумали.";
		$this->output($message);
	}


	/**
	 * @todo
	 * @param $param
	 * @param $message
	 */
	public function prompt( $param, $message ){	}


	public function output($message){
		$this->bot->reply($message);
	}

}