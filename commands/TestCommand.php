<?php
namespace RomanBots\Commands;

class TestCommand extends Command {
	protected $command = "test";
	protected $regexp = "тест.*";


	public function action( ){

		$this->finish("Ура! Получилось!");

	}
}