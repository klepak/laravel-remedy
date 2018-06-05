<?php

namespace Klepak\RemedyApi\Exceptions;

use GuzzleHttp\Psr7;
use Log;

/**
 * Exception thrown by Remedy API errors
 */
class RemedyApiException extends \Exception
{
    /**
     * The associated request object, if any
     */
    private $request = null;

    /**
     * The associated response object, if any
     */
    private $response = null;

    /**
     * Parse request and response, return proper exception and log
     */
    public function __construct($request, $response, $customMessage = null)
    {
        $this->request = $request;
        $this->response = $response;

        if(\isJson($response->getBody()))
        {
            $messages = [];
            
            $responseJson = json_decode($this->response->getBody());
            foreach($responseJson as $message)
            {
                $text = isset($message->messageText) ? $message->messageText : "No message";
                $type = isset($message->messageType) ? $message->messageType : "UNKNOWN";

                if(isset($message->messageAppendedText) && $message->messageAppendedText !== null)
                    $text .= " ($message->messageAppendedText)";

                $messages[] = "[$type] $text";
            }

            $messageText = implode(" / ", $messages);

            if($customMessage !== null)
                $messageText = $customMessage.": ".$messageText;

            parent::__construct($messageText);
            Log::error($messageText);
        }
        else
        {
            if($customMessage !== null)
                $messageText = $customMessage;
            else
                $messageText = "Non-json message response";
            
            $this->log();
                
            parent::__construct($messageText);
        }
    }

    /**
     * Dumps info about the error to log
     */
    public function log()
    {
        $requestString = "";

        if($this->request !== null)
            $requestString .= "\nRequest:\n".Psr7\str($this->request);
        
        if($this->response !== null)
            $requestString .= "\nResponse:\n".Psr7\str($this->response);
        
        Log::debug("Remedy API request failed.$requestString");
    }

    /**
     * Retrieve the associated request object
     */
    public function getRequest()
    {
        return $this->request;
    }

    /**
     * Retrieve the associated response object
     */
    public function getResponse()
    {
        return $this->response;
    }
}