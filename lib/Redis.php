<?php
require 'credis/Client.php';

if(!function_exists("redis")){
	function redis(){
		return new \Credis_Client('127.0.0.1');
	}
}
if(!function_exists("redis_set")){
	function redis_set($key, $value){
		return redis()->set($key, $value);
	}
}
if(!function_exists("redis_get")){
	function redis_get($key){
		return redis()->get($key);
	}
}