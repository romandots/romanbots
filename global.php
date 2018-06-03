<?php

use RomanBots\Bot\Human;

require_once "lib/Redis.php";

/**
 * Send message to tester's chat
 * @param $message string
 */
function _debug_output($message){
	$bot = new RomanBots\Bot\Bot(VK_API_ACCESS_TOKEN, CALLBACK_API_CONFIRMATION_TOKEN, VK_API_SECRET);
	$bot->user = Human::load(TESTER_UID);
	$bot->reply($message."\n\n");
}

function _debug_log($message){
	$trace = debug_backtrace();
	$function_name = isset($trace[2]) ? $trace[2]['function'] : '-';
	$mark = date("H:i:s") . ' [' . $function_name . ']';
	$log_name = BOT_LOGS_DIRECTORY.'/log_' . date("j.n.Y") . '.txt';
	file_put_contents($log_name, $mark . " : " . $message . "\n\n", FILE_APPEND);
}

function _debug($message){
	if(DEBUG){
		_debug_output($message);
	} else {
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
