<?php
namespace RomanBots\Commands;

class HoldCommand extends Command {
	protected $regexp = "заморо\S+\D*(\d+)\D*";

	public function action( ){

		$this->finish("Ура! Получилось! HoldCommand");

	}
}