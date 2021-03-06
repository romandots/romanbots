<?php
namespace RomanBots\Bots;

trait VkBotHandleAttachments {


	/**
	 * Save incoming attachments
	 * and return their local filenames
	 * @param $data
	 * @return array
	 */
	protected function saveIncomingAttachments( $data )
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
					$filepath   = self::BOT_VOICE_DIRECTORY;
					$localFile = $filepath . '/' . time() . '.' . $item['doc']['ext'];
					if ( copy( $remoteFile, $localFile ) )
					{
						$attachments[ $item['doc']['title'] ] = $localFile;
					}
				}
			}
			return $attachments;
		} catch ( \Exception $e )
		{
			log_error( $e );
		}
	}

}