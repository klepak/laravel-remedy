<?php

namespace Klepak\RemedyApi;

use GuzzleHttp\Client;
use GuzzleHttp\Exception\ClientException;

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
        catch(ClientException $e)
        {
            echo "ERR";
            if(\isJson($res->getBody()))
            {
                echo "JSON";
                echo $res->getBody();
            }
            else
            {
                echo $res->getBody();
            }
        }
    }
}