<?php
namespace RomanBots\Commands;

abstract class Command {
	protected $command;
	protected $params = [];
	protected $regexp;
	protected $help = "Неверно указаны параметры команды";

	protected $human;
	protected $originalMessage;
	protected $bot; //caller bot instance


	/**
	 * Command constructor.
	 * @param $human            object Human data
	 * @param $originalMessage string Full message from user
	 * @param $bot \RomanBots\VkBot\VkBot VkBot Class Instance (caller)
	 */
	public function __construct( $human, $originalMessage, &$bot )
	{
		log_msg("Command {$this->command} Class called...");
		$this->originalMessage  = $originalMessage;
		$this->human    = $human;
		$this->bot  = $bot;
		$this->loadSession();
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


	public function setParam($param, $value){

		$this->params[$param] = $value;
		$this->saveSession();
		debug($this->params, "Params updated:");
	}

	protected function _sessionName($param = ''){
		return sprintf('%s:%s:%s',
		               $this->user->id,
		               $this->command,
		               $param);
	}

	public function resetSession(){
		redis_set($this->_sessionName(), json_encode([]));
	}

	public function saveSession(){
		redis_set($this->_sessionName(), json_encode($this->params));
	}

	public function loadSession(){
		if($json = redis_get($this->_sessionName())) {
			$this->params = json_decode($json);
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
		$this->action();
	}


	/**
	 * Finalize the command
	 * @param $message
	 */
	public function finish($message){
		$this->output($message);
		exit();
	}


	/**
	 * Output an error
	 * @param $error
	 */
	public function error( $error ){
		$this->output("Ошибка: ".$error);
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
	 * Request the input
	 * and assign it to param
	 * @param $param
	 * @param $message
	 */
	public function prompt( $param, $message ){
		// save current state
		$this->saveSession();
		// subscribe for answer
		// when next message from
		// user is received it will
		// be recorded to $param
		$this->bot->waitForInput($this->command, $param);
		// ask the question
		$this->output($message);
	}


	/**
	 * Basic output method
	 * returns message to user
	 * @param $message
	 * @return Command
	 */
	public function output($message){
		$this->bot->send( $message);
		return $this;
	}


	/**
	 * Get list of active commands classes
	 * @return array
	 */
	static function all(){
		return include BOT_BASE_DIRECTORY."/commands.php";
	}


	/**
	 * Load Command Class by it's name
	 * @param $command
	 * @param $bot
	 * @return mixed
	 */
	static function load($command, &$bot){
		$commandClass = mb_convert_case($command, MB_CASE_TITLE)."Command";
		try
		{
			return new $commandClass($bot->user, $bot->userMessage, $bot);
		} catch (\Exception $e){
			log_error($e);
		}
	}


}