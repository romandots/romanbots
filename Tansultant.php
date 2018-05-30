<?php

namespace RomanBots;

class Tansultant {

	protected $db;


	public function __construct( ){
		$this->connect();
	}

	protected function connect(){
		$this->db = new mysqli(DB_HOST, DB_USERNAME, DB_PASSWORD, DB_DATABASE);
		if ($this->db->connect_error) {
			log_error("Connection failed: " . $this->db->connect_error);
		}
	}


	/**
	 * Get clients by Card ID
	 * @param $cardId
	 * @return array
	 */
	protected function getClientId($cardId){
		$sql = "SELECT FROM clients WHERE card_id = $cardId";
		$result = $this->db->query($sql);
		$clients = [];
		if ($result->num_rows > 0) {
			// output data of each row
			while($clients[] = $result->fetch_assoc()) {}
		}
		$this->db->close();
		return $clients;
	}


	public function commandTest( ){
		return "Test command executed";
	}

	public function commandPause($userData,  $cardId ){
		$cardId = trim($cardId);
		$clients = $this->getClientId($cardId);
		if( count($clients) > 1 ) {
			return "Найдено несколько клиентов с номером карты $cardId. Сначала необходимо это исправить!";
		} elseif( count($clients) < 1 ) {
			return "Клиент с номером карты $cardId не найден...";
		} else {
			return "Заморозить абонементы {$clients[0]['lastname']} {$clients[0]['name']}";
		}
	}

}