<?php
require "vendor/autoload.php";
define( 'CALLBACK_API_EVENT_CONFIRMATION', 'confirmation' );
define( 'CALLBACK_API_EVENT_MESSAGE_NEW', 'message_new' );

require_once 'config.php';
require_once 'global.php';

require_once 'api/vk_api.php';
require_once 'api/yandex_api.php';

require_once 'bot/bot.example.php';

if ( !isset( $_REQUEST ) )
{
	exit;
}

callback_handleEvent();

function callback_handleEvent()
{
	$event = _callback_getEvent();

	try
	{
		switch ( $event['type'] )
		{
			//Подтверждение сервера
			case CALLBACK_API_EVENT_CONFIRMATION:
				log_msg( 'launch _callback_handleConfirmation()' );
				_callback_handleConfirmation();
				break;

			//Получение нового сообщения
			case CALLBACK_API_EVENT_MESSAGE_NEW:
				log_msg( 'launch _callback_handleMessageNew()' );
				_callback_handleMessageNew( $event['object'] );
				break;

			default:
				log_msg( 'Unsupported event' );
				_callback_response( 'Unsupported event' );
				break;
		}
	} catch ( Exception $e )
	{
		log_error( $e );
	}

	_callback_okResponse();
}

function _callback_getEvent()
{
	return json_decode( file_get_contents( 'php://input' ), true );
}

function _callback_handleConfirmation()
{
	_callback_response( CALLBACK_API_CONFIRMATION_TOKEN );
}

function _callback_handleMessageNew( $data )
{
	if ( key_exists( "attachments", $data ) )
	{
		$attachments = saveIncomingAttachments( $data['attachments'] );
		foreach ( $attachments as $type => $attach )
		{
			if ( $type == "audio.webm" )
			{
				$speechKit = new Speech(YANDEX_API_KEY);
				$text = $speechKit->recognize($attach);
				log_msg( "Yandex Parsed this: $text" );
			}
		}
	}

	$user_id = $data['user_id'];
	bot_sendMessage( $user_id, $text );
	_callback_okResponse();
}

function saveIncomingAttachments( $data )
{
	try
	{
		$attachments = [];
		foreach ( $data as $item )
		{
			// Voice message
			if ( $item['type'] == "doc" && $item['doc']['title'] == "audio.webm" )
			{
				$remoteFile = $item['doc']['url'];
				$filepath   = BOT_AUDIO_DIRECTORY . '/incoming';
				if ( !is_dir( $filepath ) )
				{
					mkdir( $filepath );
					chmod( $filepath, 0777 );
				}
				$localFile = $filepath . '/' . time() . '.' . $item['doc']['ext'];
				if ( copy( $remoteFile, $localFile ) )
				{
					$attachments[ $item['doc']['title'] ] = $localFile;
				}
			}
		}
		return $attachments;
	} catch ( Exception $e )
	{
		log_error( $e );
	}
}

function _callback_okResponse()
{
	_callback_response( 'ok' );
}

function _callback_response( $data )
{
	echo $data;
	exit();
}


