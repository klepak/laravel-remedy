<?php

namespace Klepak\RemedyApi\Models;


use Klepak\RemedyApi\Exceptions\RemedyApiException;

use GuzzleHttp\Client;
use GuzzleHttp\Exception\BadResponseException;



class RemedyCaseEntity
{
    private $token = null;

    public static function getServer()
    {
        if(env("REMEDYAPI_TEST"))
            return env("TEST_REMEDYAPI_HOST", false);
        
        return env("REMEDYAPI_HOST", false);
    }

    public function request($method, $url, $args = [])
    {
        $client = new Client();
        
        try
        {
            $res = $client->request($method, static::getServer() . $url, $args);

            if($res->getStatusCode() == 200)
            {
                return $res;
            }
            else
            {
                throw new RemedyApiException(null, $res);
            }
        }
        catch(BadResponseException $e)
        {            
            throw new RemedyApiException($e->getRequest(), $e->getResponse());
        }
    }

    public function __construct()
    {
        $res = $this->request("POST", "/api/jwt/login", [
            'form_params' => [
                'username' => env('REMEDYAPI_USERNAME'),
                'password' => env('REMEDYAPI_PASSWORD'),
            ]
        ]);

        $this->token = $res->getBody();
    }
}