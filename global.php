<?php

use RomanBots\Human\Human;

require_once "lib/Redis.php";

/**
 * Send message to tester's chat
 * @param $message string
 */
function _debug_output($message){
	// $bot = new RomanBots\VkBot\VkBot();
	// $bot->human = Human::fromVk( TESTER_UID);
	// $bot->reply($message."\n\n");
}

function _debug_log($message){
	$trace = debug_backtrace();
	$function_name = isset($trace[2]) ? $trace[2]['function'] : '-';
	$mark = date("H:i:s") . ' [' . $function_name . ']';
	if(!is_dir(BOT_BASE_DIRECTORY.'/logs')){
		@mkdir(BOT_BASE_DIRECTORY.'/logs');
		@chmod( BOT_BASE_DIRECTORY.'/logs', 0777);
	}
	$log_name = BOT_BASE_DIRECTORY.'/logs/' . date("Y-M-D") . '.txt';
	@file_put_contents($log_name, $mark . " : " . $message . "\n\n", FILE_APPEND);
}

function _debug($message){
	if(DEBUG && TESTER_UID){
		_debug_output($message);
	}
	if(LOG){
		_debug_log($message);
	}
}

function debug($var, $comment = ''){
	$message = "--\n";
	if($comment){
		$message .= "# $comment\n";
	}
	ob_start();
	var_dump($var);
	$message .= ob_get_clean();
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