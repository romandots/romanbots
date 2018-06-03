<?php
require __DIR__.'/../vendor/colinmollenhour/credis/Client.php';

if(!function_exists("redis")){
	function redis(){

		return new \Credis_Client( REDIS_HOST, REDIS_PORT );
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
if(!function_exists("redis_del")){
	function redis_del($key){
		return redis()->del($key);
	}
}