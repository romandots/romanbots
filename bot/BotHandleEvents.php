<?php
/**
 * Created by PhpStorm.
 * User: romandots
 * Date: 30.05.2018
 * Time: 10:39
 */

namespace RomanBots\Bot;

trait BotHandleEvents {

	/**
	 * Handle new incoming chat message
	 * @param $data object
	 */
	public function handleMessageNew($data)
	{
		if(!property_exists($data, "object") || !property_exists($data->object, "body")){
			log_error('$data->object->body not exists');
			die('Empty message passed');
		}
		$this->userMessage = $data->object->body;
		$sender = $this->getSenderData($data->object);

		if( $this->isCommand() ) {
			$this->executeCommand();
		} else {
			$this->replyRandomly("Команда не распознана.", $sender);
		}
		$this->ok();
	}


	/**
	 * Return secret token
	 * when VK asks
	 * for authorization
	 */
	public function handleConfirmation()
	{
		$this->response( $this->vkCommunityConfirmationCode );
	}


	/**
	 * Handle incoming callback event
	 * @link  https://vk.com/dev/callback_api
	 * @param $event object
	 */
	public function handleEvent( $event )
	{
		try
		{
			if(! is_object($event) ) {
				$this->ok();
			}
			debug($event->type, "Event dispatched:");
			switch ( $event->type )
			{
				//Подтверждение сервера
				case 'confirmation':
					$this->handleConfirmation();
					break;

				//Получение нового сообщения
				case 'message_new':
					$this->handleMessageNew($event);
					break;

				default:
					$this->ok();
					break;
			}
		} catch ( Exception $e )
		{
			log_error( $e );
		}
	}

}