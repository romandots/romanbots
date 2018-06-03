<?php
namespace RomanBots\Commands;

class HoldCommand extends Command {
	protected $regexp = "заморо\S+\D*(\d+)\D*";
	protected $params = [
		 [
		 	'card_id',
			'\d+',
			'Номер карты студента',
			'Какой у студента номер карты?'
		]
	];

	public function action( ){

		$this->finish("Ура! Получилось! HoldCommand");

	}
}

class HoldCommandCardId extends CommandParameter {
	public $name = "card_id";
	public $pattern = "\d{4,}";
	public $required = true;
	public $label = "Карта студента:";
	public $description = "Укажите номер карты студента";
}