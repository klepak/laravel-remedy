<?php

namespace Klepak\RemedyApi\API;

use Klepak\RemedyApi\Exceptions\RemedyApiException;

use GuzzleHttp\Client;
use GuzzleHttp\Exception\BadResponseException;

abstract class RemedyCase
{
    private $token = null;

    // Standardized Field Name => Type-Specific Actual Field Name
    protected static $createInterfaceFieldMap = [];
    protected static $createInterfaceDefaultFields = [];
    protected static $standardInterfaceFieldMap = [];

    public static function getServer()
    {
        if(env('REMEDY_TEST'))
            $varName = 'TEST_REMEDYAPI_HOST';
        else
            $varName = 'REMEDYAPI_HOST';

        $host = env($varName, false);

        if($host !== false)
            return $host;
        else
            throw new \Exception("No server host defined in .env [$varName]");
    }

    public function request($method, $url, $args = [], $headers = [])
    {
        $client = new Client();

        if($this->token !== null)
            $headers['Authorization'] = "AR-JWT {$this->token}";

        if($method !== 'GET' && !isset($headers['Content-Type']))
            $headers['Content-Type'] = 'application/json';
        
        try
        {
            $res = $client->request($method, static::getServer() . $url, $args, $headers);

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
        $res = $this->request('POST', '/api/jwt/login', [
            'form_params' => [
                'username' => env('REMEDYAPI_USERNAME'),
                'password' => env('REMEDYAPI_PASSWORD'),
            ]
        ], [
            'Content-Type' => 'application/x-www-form-urlencoded'
        ]);

        $this->token = $res->getBody();
    }

    public function create($data)
    {
        
    }
}