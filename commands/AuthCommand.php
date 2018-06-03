<?php
namespace RomanBots\Commands;

class AuthCommand extends Command {

	protected $command = "auth";
	protected $help = "Отправьте сообщение с текстом:\n /вход";
	protected $regexp = "вход";
	protected $params = [
		'phone_number',
		'confirmation_token'
	];

	public function action(){

		if(!$this->phone_number){
			$this->ask("Введите свой номер телефона", "phone_number");
		}

	}


	public function ask($question, $variable){
		// subscribe for answer
		redis_set($this->_sessionName("subscribed"), $variable);
		// ask the question
		$this->output($question);
	}
}