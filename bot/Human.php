<?php
namespace RomanBots\Bot;

class Human {

	const ROLE_NOBODY = "ROLE_NOBODY";
	const ROLE_STUDENT = "ROLE_STUDENT";
	const ROLE_TEACHER = "ROLE_TEACHER";
	const ROLE_MANAGER = "ROLE_MANAGER";
	const ROLE_ADMIN = "ROLE_ADMIN";

	public $vk_uid;
	public $tansultant_uid;
	public $tansultant_role;
	public $last_name;
	public $first_name;
	public $phone_number;
	public $confirmation_token;

	// @todo
	public function loadTansultantProfile(){}

	static public function fromMessage($message){
		return Human::load($message->user_id);
	}

	static public function load($vk_uid){
		$user = new Human();
		if($json = redis_get("$vk_uid:state")){
			$state = json_decode($json, true);
			foreach ($state as $key => $value){
				$user->$key = $value;
			}
			return $user;
		} else {
			return Human::get($vk_uid);
		}
	}

	static public function get($userId){
		$user = new Human();
		$user->vk_uid = $userId;
		$method = "https://api.vk.com/method/users.get?user_ids={$userId}&access_token={VK_API_ACCESS_TOKEN}&v=5.0";
		if($callback = json_decode( file_get_contents( $method ))) {
			$userData = $callback->response[0];
			foreach (get_class_vars($userData) as $property){
				$user->$property = $userData->$property;
			}
			$user->save();
		}
		return $user;
	}

	public function save(){
		$json = json_encode($this);
		redis_set("{$this->vk_uid}:state", $json);
		return $this;
	}

}