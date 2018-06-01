<?php

namespace Klepak\RemedyApi\Exceptions;

use GuzzleHttp\Psr7;
use Log;

class RemedyApiException extends \Exception
{
    private $request = null;
    private $response = null;

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

    public function log()
    {
        $requestString = "";

        if($this->request !== null)
            $requestString .= "\nRequest:\n".Psr7\str($this->request);
        
        if($this->response !== null)
            $requestString .= "\nResponse:\n".Psr7\str($this->response);
        
        Log::debug("Remedy API request failed.$requestString");
    }

    public function getRequest()
    {
        return $this->request;
    }

    public function getResponse()
    {
        return $this->response;
    }
}