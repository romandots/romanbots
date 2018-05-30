<?php

function debug($var, $comment = ''){
	if($comment){
		_log_write("=== $comment: ===\n");
	} else {
		_log_write("=================================================\n");
	}
	_log_write(print_r($var, 1));
	_log_write("=================================================\n");
}

function log_msg($message) {
  if (is_array($message)) {
    $message = json_encode($message);
  }

  _log_write('[INFO] ' . $message);
}

function log_error($message) {
  if (is_array($message)) {
    $message = json_encode($message);
  }

  _log_write('[ERROR] ' . $message);
}

function _log_write($message) {
  $trace = debug_backtrace();
  $function_name = isset($trace[2]) ? $trace[2]['function'] : '-';
  $mark = date("H:i:s") . ' [' . $function_name . ']';
  $log_name = BOT_LOGS_DIRECTORY.'/log_' . date("j.n.Y") . '.txt';
  file_put_contents($log_name, $mark . " : " . $message . "\n", FILE_APPEND);
}
