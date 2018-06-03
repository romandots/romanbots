<?php
namespace RomanBots\Bots;

class Human {

	const ROLE_NOBODY = "ROLE_NOBODY";
	const ROLE_STUDENT = "ROLE_STUDENT";
	const ROLE_TEACHER = "ROLE_TEACHER";
	const ROLE_MANAGER = "ROLE_MANAGER";
	const ROLE_ADMIN = "ROLE_ADMIN";

	public $vk_uid;
	public $card_number;
	public $tansultant_uid;
	public $tansultant_role;
	public $last_name;
	public $first_name;
	public $location;
	public $age;
	public $genres = [];
	public $phone_number;
	public $confirmation_token;

	// @todo
	public function loadTansultantProfile(){}

	static public function fromVkMessage( $message){
		debug($message, "Exctract user data from \$message");
		return Human::fromVk( $message->user_id);
	}

	static public function fromVk( $vk_uid){
		$user = new Human();
		if($json = redis_get("$vk_uid:state")){
			$state = json_decode($json, true);
			foreach ($state as $key => $value){
				$user->$key = $value;
			}
			debug($user, "Human from Redis:");

		}
		if($user && $user->first_name){
			return $user;
		} else {
			return Human::getVkUserData($vk_uid);
		}
	}

	static public function getVkUserData($userId){
		$user = new Human();
		$user->vk_uid = $userId;
		$method = "https://api.vk.com/method/users.get?user_ids={$userId}&access_token=".VK_API_ACCESS_TOKEN."&v=5.0";
		if($callback = json_decode( file_get_contents( $method ), true)) {
			$userData = $callback['response'][0];
			debug($userData, "User data loaded from VK");
			foreach ($userData as $property=>$value){
				$user->$property = $value;
			}
			$user->save();
		}
		debug($user, "Human from VK API:");
		return $user;
	}



	public function sendVk($message){

	}


	/**
	 * Props setter
	 * @param $key string Key
	 * @param $value mixed Value
	 * @return $this object Human
	 */
	public function set( $key, $value ){
		if(property_exists($this, $key))
		$this->$key = $value;
		$this->save();
		return $this;
	}


	/**
	 * Save this object
	 * @return Human
	 */
	public function save(){
		return $this;//->persist($this, "state");
	}

	/**
	 * Persist data in Redis
	 * @param $data mixed
	 * @param $key string
	 * @return $this object Human
	 */
	protected function persist($data, $key){
		redis_set("{$this->vk_uid}:{$key}", ( is_string($data) || is_int($data) )? $data : json_encode($data));
		return $this;
	}


	/**
	 * Load persisted data
	 * @param $key string Key (`state` by default)
	 * @return mixed Data
	 */
	protected function load($key = 'state'){
		return redis_get($key);
	}
}