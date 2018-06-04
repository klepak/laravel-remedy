<?php

namespace Klepak\RemedyApi\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Builder;

use Klepak\RemedyApi\Traits\ReflectsOnClassName;

#use MichaelAChrisco\ReadOnly\ReadOnlyTrait;

abstract class RemedyCase extends Model
{
    use ReflectsOnClassName;

    protected $connection = 'remedy';

    protected $worklogTable = null;

    protected $guarded = [];
    public $timestamps = false;
    protected $primaryKey = "InstanceId";
    public $incrementing = false;

    // Standardized Field Name => Type-Specific Variant Field Name
    protected static $dbSelectFields = [];
    protected static $dbDefaultFields = [];
    protected static $dbFieldMap = [];

    protected static $statusTextMap = [];

    public function getDefaultFields()
    {
        return static::$dbDefaultFields;
    }

    public function getSelectFields()
    {
        return static::$dbSelectFields;
    }

    /**
     * The "booting" method of the model.
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

    public function getVariantFieldName($normalizedField)
    {
        if(isset(static::$dbFieldMap[$normalizedField]))
            return static::$dbFieldMap[$normalizedField];
        
        return $normalizedField;
    }

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

    public function getStatusTextAttribute()
    {
        return isset(static::$statusTextMap[$this->Status]) ? static::$statusTextMap[$this->Status] : "Unknown";
    }

    public function __get($key)
    {
        $normalizedField = $this->getVariantFieldName($key);

        \Log::debug("Get $key transformed to $normalizedField");

        return parent::__get($normalizedField);
    }

    public function __set($key, $value)
    {
        $normalizedField = $this->getVariantFieldName($key);

        \Log::debug("Set $key transformed to $normalizedField");

        return parent::__set($normalizedField, $value);
    }
    
    public function api()
    {
        $apiClassName = "\\Klepak\\RemedyApi\\API\\{$this->getClassName()}";
        return new $apiClassName($this);
    }
}
