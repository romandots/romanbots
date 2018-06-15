<?php
/**
 * Created by PhpStorm.
 * User: romandots
 * Date: 15.06.2018
 * Time: 4:32
 */

namespace RomanBots\Bots;


trait DialogFlowConnect {

	protected $dfBot;

	public function toDialogflow(string $message ){
		if($message ){
			$response = $this->processVkMessage($message);
			if(property_exists($response,'result')){
				$this->processDfResult($response->result);
			}
		}
	}


	public function toVk( $message ){

	}


	/**
	 * @return string
	 */
	protected function generateSessionIdFromVkUid(){
		$this->sessionId = md5($this->human->vk_uid);
		return $this->sessionId;
	}


	/**
	 * Pass message to Dialogflow API
	 * and send response back to VK
	 * @param string $message
	 * @return string
	 */
	protected function processVkMessage(string $message){
		if(!$this->dfBot) {
			$this->dfBot = new DialogFlowBot();
		}
		$response = $this->dfBot->query($message, $this->generateSessionIdFromVkUid());
		return $response;
	}


	protected function processDfResult($result){

		// 1. Check if there's a speech from DF
		if(property_exists($result, 'fulfillment') && property_exists($result->fulfillment, 'speech')) {
			$this->botReply = $result->fulfillment->speech;
		}

		// 2. Some other action
		elseif(property_exists($result, 'fulfillment') ) {
			$this->botReply =  print_r($result->fulfillment, true);
		}

		// 3. Some other action
		else {
			$this->botReply = "response: ".print_r($result, true);
		}

	}

	protected function processDfMessage(){}

}