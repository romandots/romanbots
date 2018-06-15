<?php
namespace RomanBots\Bots;

use RomanBots\Bots\Human;

trait VkBotEvents {
	 use DialogFlowConnect;

	/**
	 * Handle incoming callback event
	 * @link  https://vk.com/dev/callback_api
	 * @param $event object
	 */
	public function handleIncomingEvent( $event )
	{
		if(!is_object($event)){
			fatal("Event must be object");
		}
		if(!property_exists($event, "type")){
			fatal("No event type set");
		}
		try
		{
			debug($event->type, "Event type:");
			debug($event, "Event object:");
			switch ( $event->type )
			{
				//Подтверждение сервера
				case 'confirmation':
					$this->handleVkConfirmation();
					break;

				//Получение нового сообщения
				case 'message_new':
					$message = $event->object;
					$this->human = Human::fromVkMessage( $message);
					$this->handleNewChatMessage($message);
					break;

				default:
					// Return OK message so VK
					// won't bother us with the same
					// messages anymore
					$this->ok();
			}
		} catch ( \Exception $e )
		{
			log_error( $e );
		}
	}


	/**
	 * Handle new incoming chat message
	 * @param $message object message
	 */
	public function handleNewChatMessage($message)
	{
		// debug($message,'handleNewChatMessage');

		// 1. First of all respond with ok
		$this->ok();


		// 2. Check if it has body
		if(!property_exists($message, "body")){
			fatal('$message->body does not exists');
		}
		$this->humanMessage = $message->body;

		// 3. Pass the message to Dialogflow
		$this->toDialogflow($this->humanMessage);

		// 4. Reply to user
		if($this->botReply){
			// @todo Placeholders in messages like %FIRST_NAME%, etc.
			$this->send($this->botReply);
			exit();
		}

		// 3. Check if some command is subscribed
		// for this message (waiting for input)
		// $key = $this->human->vk_uid.":subscribed";
		// if($subscription = redis_get($key)){
		// 	list($command, $param) = explode(":", $subscription);
		// 	Command::load($command, $this);
		// 	$this->continueWithInput($command, $param);
		// }

		// 4. Check if message is command
		// if( $this->isCommand() ) {
		// 	$this->executeCommand();
		// }

		// 5. If it is not — just chat and act like a human
		$this->send( 'DF не ответил: %s', $this->human->first_name, $this->humanMessage);
	}

	/**
	 * @todo
	 *      handleWelcomeEvent - when user joins group
	 *      handeFarewellEvent - the opposite
	 *      etc
	 */

}