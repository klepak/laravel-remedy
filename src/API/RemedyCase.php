<?php

namespace Klepak\RemedyApi\API;

use Klepak\RemedyApi\Exceptions\RemedyApiException;

use Klepak\RemedyApi\Models\RemedyCase as RemedyModel;

use GuzzleHttp\Client;
use GuzzleHttp\RequestOptions;
use GuzzleHttp\Exception\BadResponseException;

use Klepak\RemedyApi\Traits\ReflectsOnClassName;

/**
 * Remedy API base class
 */
abstract class RemedyCase
{
    use ReflectsOnClassName;

    private $token = null;

    /**
     * Base name of API interface for this case type
     */
    protected static $interface = null;

    /**
     * Maps field names on the API create interface to normalized field names
     *
     * Follows the format Normalized Field Name => Type-Specific Variant Field Name
     */
    protected static $createInterfaceFieldMap = [];

    /**
     * Contains the default fields for the create interface / TODO: explain this
     */
    protected static $createInterfaceDefaultFields = [];

    /**
     * Contains default values of some fields on the create interface
     *
     * Follows the format Normalized Field Name => Value
     */
    protected static $createInterfaceDefaultValues = [];

    /**
     * Maps field names on the API standard interface to normalized field names
     *
     * Follows the format Normalized Field Name => Type-Specific Variant Field Name
     */
    protected static $standardInterfaceFieldMap = [];

    /**
     * Related model passed to constructor
     */
    public $model = null;

    /**
     * Instantiates object, performs authentication with API
     */
    public function __construct(RemedyModel $model)
    {
        $this->model = $model;

        $this->login(env('REMEDYAPI_USERNAME'), env('REMEDYAPI_PASSWORD'));
    }

    /**
     * Initiates a login request with the API
     *
     * @param string $username API username
     * @param string $password API password
     *
     * @return void
     */
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

    /**
     * Returns the API server to use based on the local environment
     *
     * @return string
     */
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

    /**
     * Performs a request against the API
     *
     * @param string $method The HTTP method verb to use for the request
     * @param string $url The API route to request
     * @param array $args Arguments passed on to Guzzle Client
     * @param array $headers HTTP headers to pass to the request
     *
     * @return \GuzzleHttp\Response
     */
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

    /**
     * Gets the variant field name from the normalized field name from the specified map
     *
     * @param string $normalizedField The normalized field name to transform
     * @param array $map The field map to use for the transformation
     *
     * @return string
     */
    public function getVariantFieldName($normalizedField, $map)
    {
        if(isset($map[$normalizedField]))
            return $map[$normalizedField];

        $normalizedField = str_replace("_", " ", $normalizedField);

        \Log::info("Normalized $normalizedField");

        return $normalizedField;
    }

    /**
     * Normalizes the specified field name
     *
     * @param string $field The field name to normalize
     * @param array $map The field map to use to normalize
     *
     * @return string
     */
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

    /**
     * Normalize field using the create interface field map
     *
     * @param string $field The field to normalize
     *
     * @return string
     */
    public function getNormalizedCreateInterfaceFieldName($field)
    {
        return $this->getNormalizedFieldName($field, static::$createInterfaceFieldMap);
    }

    /**
     * Normalize field using the standard interface field map
     *
     * @param string $field The field to normalize
     *
     * @return string
     */
    public function getNormalizedStandardInterfaceFieldName($field)
    {
        return $this->getNormalizedFieldName($field, static::$standardInterfaceFieldMap);
    }

    /**
     * Get variant field from normalized field using the create interface field map
     *
     * @param string $normalizedField The field to transform
     *
     * @return string
     */
    public function getCreateInterfaceVariantFieldName($normalizedField)
    {
        return $this->getVariantFieldName($normalizedField, static::$createInterfaceFieldMap);
    }

    /**
     * Get variant field from normalized field using the standard interface field map
     *
     * @param string $normalizedField The field to transform
     *
     * @return string
     */
    public function getStandardInterfaceVariantFieldName($normalizedField)
    {
        return $this->getVariantFieldName($normalizedField, static::$standardInterfaceFieldMap);
    }

    /**
     * Transform normalized data into variant data using the specified field map
     *
     * @param array $normalizedData The data to transform
     * @param array $map The field map to use for the transformation
     *
     * @return array
     */
    public function transformNormalizedData($normalizedData, $map)
    {
        $variantData = [];
        foreach($normalizedData as $key => $value)
        {
            $variantData[$this->getVariantFieldName($key, $map)] = $value;
        }

        return $variantData;
    }

    /**
     * Transform variant data into normalized data using the specified field map
     *
     * @param array $variantData The data to transform
     * @param array $map The field map to use for the transformation
     *
     * @return array
     */
    public function normalizeVariantData($variantData, $map)
    {
        $normalizedData = [];

        foreach($variantData as $key => $value)
        {
            $normalizedData[$this->getNormalizedFieldName($key, $map)] = $value;
        }

        return $normalizedData;
    }

    /**
     * Transform normalized data into variant data using the create interface field map
     *
     * @param array $normalizedData The data to transform
     *
     * @return array
     */
    public function transformNormalizedCreateInterfaceData($normalizedData)
    {
        return $this->transformNormalizedData($normalizedData, static::$createInterfaceFieldMap);
    }

    /**
     * Transform normalized data into variant data using the standard interface field map
     *
     * @param array $normalizedData The data to transform
     *
     * @return array
     */
    public function transformNormalizedStandardInterfaceData($normalizedData)
    {
        return $this->transformNormalizedData($normalizedData, static::$standardInterfaceFieldMap);
    }

    /**
     * Transform variant data into normalized data using the create interface field map
     *
     * @param array $variantData The data to transform
     *
     * @return array
     */
    public function normalizeVariantCreateInterfaceData($variantData)
    {
        return $this->normalizeVariantData($variantData, static::$createInterfaceFieldMap);
    }

    /**
     * Transform variant data into normalized data using the standard interface field map
     *
     * @param array $variantData The data to transform
     *
     * @return array
     */
    public function normalizeVariantStandardInterfaceData($variantData)
    {
        return $this->normalizeVariantData($variantData, static::$standardInterfaceFieldMap);
    }

    /**
     * Performs an update operation against the API on the current instance
     *
     * @param array $normalizedData The normalized data to use for the update operation
     *
     * @return bool
     */
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

    /**
     * Get full name of API standard interface
     *
     * @return string
     */
    public function getStandardInterface()
    {
        return static::$interface;
    }

    /**
     * Get full name of API create interface
     *
     * @return string
     */
    public function getCreateInterface()
    {
        return static::$interface."_Create";
    }

    /**
     * Performs a create operation against the API
     *
     * Will supplement the provided data with data from static::$createInterfaceDefaultValues for the fields that are not specified
     *
     * @param array $normalizedData The normalized data to use for the create operation
     *
     * @return string InstanceId
     */
    public function create($normalizedData)
    {
        $variantData = $this->transformNormalizedCreateInterfaceData($normalizedData);

        foreach($this->transformNormalizedCreateInterfaceData(static::$createInterfaceDefaultValues) as $key => $value)
        {
            if(!isset($variantData[$key]))
                $variantData[$key] = $value;
        }

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

                    return $this->model->newQuery()->find($caseData->values->InstanceId);
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
