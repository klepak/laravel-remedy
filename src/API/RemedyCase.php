<?php

namespace Klepak\RemedyApi\API;

use Klepak\RemedyApi\Exceptions\RemedyApiException;

use Klepak\RemedyApi\Models\RemedyCase as RemedyModel;

use GuzzleHttp\Client;
use GuzzleHttp\Exception\BadResponseException;

abstract class RemedyCase
{
    private $token = null;

    protected static $interface = null;

    // Standardized Field Name => Type-Specific Variant Field Name
    protected static $createInterfaceFieldMap = [];
    protected static $createInterfaceDefaultFields = [];

    protected static $standardInterfaceFieldMap = [];

    private $model = null;

    public function __construct(RemedyModel $model)
    {
        $this->model = $model;

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

    public function getVariantFieldName($normalizedField, $map)
    {
        if(isset($map[$normalizedField]))
            return $map[$normalizedField];
        
        return $normalizedField;
    }

    public function getNormalizedFieldName($field, $map)
    {
        $reversedFieldMap = [];
        foreach($map as $normalized => $variant)
        {
            $reversedFieldMap[$variant] = $normalized;
        }

        if(isset($reversedFieldMap[$field]))
            return $reversedFieldMap[$field];

        return $field;
    }

    public function getNormalizedCreateInterfaceFieldName($field)
    {
        return $this->getNormalizedFieldName($field, static::$createInterfaceFieldMap);
    }

    public function getNormalizedStandardInterfaceFieldName($field)
    {
        return $this->getNormalizedFieldName($field, static::$standardInterfaceFieldMap);
    }

    public function getCreateInterfaceVariantFieldName($normalizedField)
    {
        return $this->getVariantFieldName($normalizedField, static::$createInterfaceFieldMap);
    }

    public function getStandardInterfaceVariantFieldName($normalizedField)
    {
        return $this->getVariantFieldName($normalizedField, static::$standardInterfaceFieldMap);
    }

    // Transforms normalized data into variant data
    public function transformNormalizedData($normalizedData)
    {
        $variantData = [];
        foreach($normalizedData as $key => $value)
        {
            $variantData[$this->getStandardInterfaceVariantFieldName($key)] = $value;
        }

        return $variantData;
    }

    public function update($normalizedData)
    {
        $variantData = $this->transformNormalizedData($normalizedData);

        $args = json_encode([
            'values' => $variantData
        ]);

        $res = $this->request('PUT', "/api/arsys/v1/entry/{$this->interface}Interface/{$this->model->Entry_ID}|{$this->model->Entry_ID}", $args);

        if($res->getStatusCode() == 204)
        {
            return true;
        }
        else
        {
            throw new RemedyApiException(null, $res);
        }
    }

    public function create($normalizedData)
    {
        $variantData = $this->transformNormalizedData($normalizedData);

    }
}