<?php

namespace Klepak\RemedyApi;

use GuzzleHttp\Client;
use GuzzleHttp\Exception\BadResponseException;

use GuzzleHttp\Psr7;

class RemedyCaseEntity
{
    public $token = null;

    public static function getServer()
    {
        if(env("REMEDYAPI_TEST"))
            return env("TEST_REMEDYAPI_HOST", false);
        
        return env("REMEDYAPI_HOST", false);
    }

    public function __construct()
    {
        $client = new Client();
        
        try
        {
            $res = $client->request('POST', static::getServer() . "/api/jwt/login", [
                'form_params' => [
                    'username' => env('REMEDYAPI_USERNAME')."2",
                    'password' => env('REMEDYAPI_PASSWORD'),
                ],
            ]);

            if($res->getStatusCode() == 200)
            {
                $this->token = $res->getBody();
            }
        }
        catch(BadResponseException $e)
        {
            \Log::info("Remedy login request failed.\n".Psr7\str($e->getRequest()));
            
            if(\isJson($e->getResponse()->getBody()))
            {   
                echo $e->getResponse()->getBody();
            }
            else
            {
                echo $e->getResponse()->getBody();
            }
        }
    }
}