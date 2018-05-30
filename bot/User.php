<?php
namespace RomanBots\Bot;

class User {

	const ROLE_NOBODY = "ROLE_NOBODY";
	const ROLE_STUDENT = "ROLE_STUDENT";
	const ROLE_TEACHER = "ROLE_TEACHER";
	const ROLE_MANAGER = "ROLE_MANAGER";
	const ROLE_ADMIN = "ROLE_ADMIN";

	protected $vk_uid;
	protected $tansultant_uid;
	protected $tansultant_role;
	protected $last_name;
	protected $first_name;
	protected $phone_number;
	protected $confirmation_token;


	public function load($vk_uid){
		if($json = redis_get("$vk_uid:state")){
			$state = json_decode($json, true);
			foreach ($state as $key => $value){
				$this->$key = $value;
			}
		}
		return $this;
	}

	public function save(){
		$json = json_encode($this);
		redis_set("{$this->vk_uid}:state", $json);
		return $this;
	}

}