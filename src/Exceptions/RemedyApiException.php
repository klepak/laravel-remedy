<?php

namespace Klepak\RemedyApi\Exceptions;

use GuzzleHttp\Psr7;
use Log;

class RemedyApiException extends \Exception
{
    private $request = null;
    private $response = null;

    public function __construct($request, $response)
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
                $messages[] = "[$type] $text";
            }

            $messageText = implode(" / ", $messages);

            parent::__construct($messageText);
            Log::error($messageText);
        }
        else
        {
            parent::__construct("Non-json message response");
        }
    }

    public function log()
    {
        Log::debug("Remedy API request failed.\n".Psr7\str($this->request));
    }

    public function getRequest()
    {
        return $this->request;
    }
}