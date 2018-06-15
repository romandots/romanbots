<?php
namespace RomanBots\Bots;

use Google\ApiCore\ApiException;
use Google\ApiCore\ValidationException;
use Google\Cloud\Dialogflow\V2\QueryInput;
use Google\Cloud\Dialogflow\V2\SessionsClient;
use Google\Cloud\Dialogflow\V2\TextInput;
use function GuzzleHttp\Psr7\parse_query;

putenv('GOOGLE_APPLICATION_CREDENTIALS='.BASE_DIR.'/keys/'.DIALOGFLOW_KEY_FILE);

/**
 * Class DialogFlowBot
 * API for Dialogflow.com
 * @package RomanBots\Bots
 */
class DialogFlowBot extends ProtoBot {

	const API_VERSION = 'v1';
	const PROTOCOL_VERSION = '20170712';


	/**
	 * DialogFlowBot constructor.
	 */
	public function __construct()
	{
		parent::__construct(config("dialogflow.api_url"), self::API_VERSION, config("dialogflow.access_token"));
	}


	/**
	 * @param string $message
	 * @param array  $contexts
	 * @return object Response body
	 */
	public function query( string $message, array $contexts = null)
	{
		$data = [
			'query' => $message,
			'lang'  => config('dialogflow.language_code'),
			'timezone'  => config('dialogflow.time_zone'),
			'sessionId'  => $this->sessionId,
			'v' => self::PROTOCOL_VERSION
		];
		$this->get('query', $data);
		return $this->response();
	}
}