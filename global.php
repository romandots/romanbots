<?php

use RomanBots\Bots\Human;
use RomanBots\Bots\VkBot;

require_once "lib/Redis.php";

/**
 * Send message to tester's chat
 * @param $message string
 */
function _debug_output($message){
	// $bot = new VkBot();
	// $bot->human = Human::fromVk( TESTER_UID);
	// $bot->reply($message."\n\n");
}

function _debug_log($message){
	$trace = debug_backtrace();
	$function_name = isset($trace[2]) ? $trace[2]['function'] : '-';
	$mark = date("H:i:s") . ' [' . $function_name . ']';
	if(!is_dir( BASE_DIR.'/logs')){
		@mkdir( BASE_DIR.'/logs');
		@chmod( BASE_DIR.'/logs', 0777);
	}
	$log_name = BASE_DIR.'/logs/' . date( "Y-m-d") . '.txt';
	@file_put_contents($log_name, $mark . " : " . $message . "\n\n", FILE_APPEND);
}

function _debug($message){
	if(DEBUG ){
		if(TESTER_UID){
			_debug_output($message);
		}
		// dump($message);
	}
	if(LOG){
		_debug_log($message);
	}
}

function dump($var, $comment = ''){
	if($comment) {
		echo  "<h4>$comment</h4>";
	}
	echo  "<pre>";
	print_r($var);
	echo  "</pre>";
}

function dd($var, $comment = ''){
	dump($var, $comment);
	exit();
}

function debug($var, $comment = ''){
	$message = "--\n";
	if($comment){
		$message .= "# $comment\n";
	}
	ob_start();
	var_dump($var);
	$message .= ob_get_flush();
	_debug($message);
}

function log_msg($message) {
	if (is_array($message)) {
		$message = json_encode($message);
	}
	_debug('[INFO] ' . $message);
}

function log_error($message) {
  if (is_array($message)) {
    $message = json_encode($message);
  }

	_debug('[ERROR] ' . $message);
}

function fatal($message){
	log_error($message);
	die('ok');
}

function _config_get_data($array, $path, $default = null)
{
	if(is_array($array))
	{
		$item = array_shift($path);
		if(key_exists($item, $array))
		{
			$var = $array[ $item ];
			if(count($path))
			{
				return _config_get_data($var, $path, $default);
			} else {
				return $var;
			}
		}
	} else {
		return $array;
	}
}

function config($var, $default = null) {
	if( $path = explode('.', $var) ){
		$filename = BASE_DIR.'/config/'.array_shift($path).'.php';
		if(file_exists($filename))
		{
			$config = include($filename);
			$data = _config_get_data($config, $path, $default);
			// debug($data, "Loading config var: $var");
			return $data;
		}
	}
	// debug($default, "Loading config var: $var - failed, returning default value:");
	return $default;
}