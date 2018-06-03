<?php
declare(strict_types=1);

namespace RomanBots\tests;
use PHPUnit\Framework\TestCase;
use RomanBots\Bot\Bot;
use RomanBots\Commands\Command;

final class CommandTest extends TestCase {

	private function bot(){
		return new Bot( VK_API_ACCESS_TOKEN, CALLBACK_API_CONFIRMATION_TOKEN, VK_API_SECRET);
	}

	private function userData(){
		$userData =  new \stdClass();
		$userData->last_name = "Иванов";
		$userData->first_name = "Иван";
		$userData->id = 9876543;
		return $userData;
	}

	public function testCommandAllReturnsArray()
	{
		$this->assertInternalType("array", Command::all());
	}

	public function testEveryCommandIsInstanceOfCommand()
	{
		$bot = $this->bot();
		foreach (Command::all() as $commandClass)
		{
			$command = new $commandClass($this->userData(), "", $bot);
			$this->assertInstanceOf(
				Command::class,
				$command
			);
		}
	}

}



