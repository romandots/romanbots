<?php
namespace RomanBots\Commands;

abstract class Command {
	protected $command;
	protected $params = [];
	protected $regexp;
	protected $help = "Неверно указаны параметры команды";

	protected $user;
	protected $originalMessage;
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
	 * This is where all the action is going on
	 * Must call error()/finish() in the end
	 */
	abstract function action( );


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


	/**
	 * Check if all parameters are set
	 * @return bool
	 */
	public function allParametersSet( ){
		foreach ($this->params as $param => $value){
			if (empty($value)) {
				return false;
			}
		}
		return true;
	}

	/**
	 * Assign params from command to model
	 * @param $params
	 */
	public function assignParameters($params){
		debug($params, "Parameters received:");
		$assignedParams = [];
		foreach ($this->params as $parameter){
			$assignedParams[$parameter] = current($params);
		}
		debug($assignedParams, "Parameters assigned:");
		$this->params = $assignedParams;
	}


	/**
	 * Execute the command
	 */
	public function execute( ){
		log_msg("Executing command `$this->command` for message `$this->originalMessage`");
		if(!$this->allParametersSet()){
			log_error("Parameters not set correctly");
			$this->finish($this->help);
		}
		$this->bot->startCommandFlow($this->command);
		$this->action();
	}


	/**
	 * Finalize the command
	 * @param $message
	 */
	public function finish($message){
		$this->output($message);
		$this->bot->endCommandFlow();
		exit();
	}


	/**
	 * Output an error
	 * @param $error
	 */
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


	/**
	 * Basic output method
	 * returns message to user
	 * @param $message
	 */
	public function output($message){
		$this->bot->reply($message);
	}


	/**
	 * Get list of active commands classes
	 * @return array
	 */
	static function list(){
		return include BOT_BASE_DIRECTORY."/commands.php";
	}



}