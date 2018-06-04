<?php

namespace Klepak\RemedyApi\API;

use Klepak\RemedyApi\Exceptions\RemedyApiException;

use Klepak\RemedyApi\Models\RemedyCase as RemedyModel;

use GuzzleHttp\Client;
use GuzzleHttp\RequestOptions;
use GuzzleHttp\Exception\BadResponseException;

use Klepak\RemedyApi\Traits\ReflectsOnClassName;

abstract class RemedyCase
{
    use ReflectsOnClassName;

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

        $this->login(env('REMEDYAPI_USERNAME'), env('REMEDYAPI_PASSWORD'));
    }

    public function login($username, $password)
    {
        $res = $this->request('POST', '/api/jwt/login', [
            'form_params' => [
                'username' => $username,
                'password' => $password,
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

        $args["headers"] = $headers;

        $fullUrl = static::getServer() . $url;

        \Log::debug("$method: $fullUrl");
        \Log::debug("\n".print_r($args,true));
        if(isset($args["json"]))
            \Log::debug("\n".json_encode($args["json"]));

        try
        {
            $res = $client->request($method, $fullUrl, $args);

            return $res;
        }
        catch(BadResponseException $e)
        {
            \Log::info("Bad response");
            throw new RemedyApiException($e->getRequest(), $e->getResponse());
        }
    }

    public function getVariantFieldName($normalizedField, $map)
    {
        if(isset($map[$normalizedField]))
            return $map[$normalizedField];

        $normalizedField = str_replace("_", " ", $normalizedField);

        \Log::info("Normalized $normalizedField");
        
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
    public function transformNormalizedData($normalizedData, $map)
    {
        $variantData = [];
        foreach($normalizedData as $key => $value)
        {
            $variantData[$this->getVariantFieldName($key, $map)] = $value;
        }

        return $variantData;
    }

    public function normalizeVariantData($variantData, $map)
    {
        $normalizedData = [];
        
        foreach($variantData as $key => $value)
        {
            $normalizedData[$this->getNormalizedFieldName($key, $map)] = $value;
        }

        return $normalizedData;
    }

    public function transformNormalizedCreateInterfaceData($normalizedData)
    {
        return $this->transformNormalizedData($normalizedData, static::$createInterfaceFieldMap);
    }

    public function transformNormalizedStandardInterfaceData($normalizedData)
    {
        return $this->transformNormalizedData($normalizedData, static::$standardInterfaceFieldMap);
    }

    public function normalizeVariantCreateInterfaceData($variantData)
    {
        return $this->normalizeVariantData($variantData, static::$createInterfaceFieldMap);
    }

    public function normalizeVariantStandardInterfaceData($variantData)
    {
        return $this->normalizeVariantData($variantData, static::$standardInterfaceFieldMap);
    }

    public function update($normalizedData)
    {
        $variantData = $this->transformNormalizedStandardInterfaceData($normalizedData);

        $args = [
            RequestOptions::JSON => [
                'values' => $variantData
            ]
        ];

        $res = $this->request('PUT', "/api/arsys/v1/entry/{$this->getStandardInterface()}/{$this->model->Entry_ID}|{$this->model->Entry_ID}", $args);

        if($res->getStatusCode() == 204)
        {
            return true;
        }
        else
        {
            throw new RemedyApiException(null, $res);
        }
    }

    public function getStandardInterface()
    {
        return static::$interface."Interface";
    }

    public function getCreateInterface()
    {
        return static::$interface."Interface_Create";
    }

    public function create($normalizedData)
    {
        $variantData = $this->transformNormalizedCreateInterfaceData($normalizedData);

        $args = [
            RequestOptions::JSON => [
                'values' => $variantData
            ]
        ];

        $interface = $this->getCreateInterface();

        // perform initial request to create case
        $initialCreateRequest = $this->request('POST', "/api/arsys/v1/entry/$interface", $args);
        if($initialCreateRequest->getStatusCode() == 201)
        {
            // extract request id from response to get case data

            $headers = $initialCreateRequest->getHeaders();

            if(!isset($headers["Location"]) && !isset($headers["Location"][0]))
                throw new RemedyApiException(null, $initialCreateRequest, "Missing Location header in response");

            $locationSegments = explode("/", $headers["Location"][0]);
            $requestId = end($locationSegments);

            // perform request to retrieve data about created case
            $retrieveCaseDataRequest = $this->request('GET', "/api/arsys/v1/entry/$interface/$requestId?fields=values(InstanceId)");

            if($retrieveCaseDataRequest->getStatusCode() == 200)
            {
                $body = $retrieveCaseDataRequest->getBody();
                if(\isJson($body))
                {
                    $caseData = json_decode($body);

                    if(!isset($caseData->values) && !isset($caseData->values->InstanceId))
                        throw new RemedyApiException(null, $retrieveCaseDataRequest, "Missing InstanceId in case data");

                    return $caseData->values->InstanceId;
                }
                else
                {
                    throw new RemedyApiException(null, $retrieveCaseDataRequest, "Response body is not valid JSON");
                }
            }
            else
            {
                throw new RemedyApiException(null, $retrieveCaseDataRequest, "Unexpected HTTP response code from subsequent request");
            }
        }
        elseif($initialCreateRequest->getStatusCode() == 204)
        {
            // success (no content)
            echo "No content received";
            throw new RemedyApiException(null, $initialCreateRequest, "Unexpected HTTP response code");
        }
        else
        {
            throw new RemedyApiException(null, $initialCreateRequest, "Unexpected HTTP response code");
        }
    }
}