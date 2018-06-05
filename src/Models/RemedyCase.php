<?php

namespace Klepak\RemedyApi\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Builder;

use Klepak\RemedyApi\Traits\ReflectsOnClassName;

#use MichaelAChrisco\ReadOnly\ReadOnlyTrait;

abstract class RemedyCase extends Model
{
    use ReflectsOnClassName;

    /**
     * Database connection to use for Remedy models
     */
    protected $connection = 'remedy';

    /**
     * Associated worklog DB table
     */
    protected $worklogTable = null;

    /**
     * Controls whether model should use automatic timestamp (Disabled for Remedy models)
     */
    public $timestamps = false;
    
    /**
     * Set primary key of model (InstanceId for Remedy models)
     */
    protected $primaryKey = "InstanceId";

    /**
     * Set whether model should auto-increment its primary key (Disabled for Remedy models)
     */
    public $incrementing = false;

    /**
     * Variant field names to select in default model scope
     */
    protected static $dbSelectFields = [];
    
    /**
     * Maps type-specific field names in the database to normalized field names
     * 
     * Follows the format Normalized Field Name => Type-Specific Variant Field Name
     */
    protected static $dbFieldMap = [];

    /**
     * Maps status int value from database to correct string representation
     * 
     * Follows the format status_int => status_text
     */
    protected static $statusTextMap = [];


    /**
     * Get select fields for standard model scope
     * 
     * @return array
     */
    public function getSelectFields()
    {
        return static::$dbSelectFields;
    }

    /**
     * Boots the model. Adds standard global scope.
     *
     * @return void
     */
    protected static function boot()
    {
        parent::boot();

        $dbSelectFields = static::$dbSelectFields;
        static::addGlobalScope('standard', function(Builder $builder) use ($dbSelectFields) {
            $builder->select($dbSelectFields);
        });
    }

    /**
     * Gets the variant field name from the normalized field name
     * 
     * @param string $normalizedField The normalized field name to transform
     * 
     * @return string
     */
    public function getVariantFieldName($normalizedField)
    {
        if(isset(static::$dbFieldMap[$normalizedField]))
            return static::$dbFieldMap[$normalizedField];
        
        return $normalizedField;
    }

    /**
     * Gets the normalized field name from the variant field name
     * 
     * @param string $field The variant field name to normalize
     * 
     * @return string
     */
    public function getNormalizedFieldName($field)
    {
        $reversedFieldMap = [];
        foreach(static::$dbFieldMap as $normalized => $variant)
        {
            $reversedFieldMap[$variant] = $normalized;
        }

        if(isset($reversedFieldMap[$field]))
            return $reversedFieldMap[$field];

        return $field;
    }

    /**
     * Dumps standard properties of model to array.
     * 
     * Normalizes field names, sorts by key.
     * 
     * @return array
     */
    public function toArray()
    {
        $parent = parent::toArray();
        $mapped = [
            "Status_Text" => $this->status_text
        ];

        foreach($parent as $key => $value)
        {
            $mapped[$this->getNormalizedFieldName($key)] = $value;
        }

        ksort($mapped);

        return $mapped;
    }

    /**
     * Accessor for status_text, gets status string based on int value, or "Unknown"
     * 
     * @return string
     */
    public function getStatusTextAttribute()
    {
        return isset(static::$statusTextMap[$this->Status]) ? static::$statusTextMap[$this->Status] : "Unknown";
    }

    /**
     * Magic __get method to transform normalized field names to variant field names when trying to access properties.
     * 
     * @return string
     */
    public function __get($key)
    {
        $variantField = $this->getVariantFieldName($key);

        \Log::debug("Get $key transformed to $variantField");

        return parent::__get($variantField);
    }

    /**
     * Magic __set method to transform normalized field names to variant field names when trying to set properties.
     * 
     * @return string
     */
    public function __set($key, $value)
    {
        $variantField = $this->getVariantFieldName($key);

        \Log::debug("Set $key transformed to $variantField");

        return parent::__set($variantField, $value);
    }
    
    /**
     * Instantiates API object of correct type for current model.
     * 
     * @return \Klepak\RemedyApi\Api\RemedyCase
     */
    public function api()
    {
        $apiClassName = "\\Klepak\\RemedyApi\\API\\{$this->getClassName()}";
        return new $apiClassName($this);
    }
}
