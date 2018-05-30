<?php

namespace Klepak\RemedyApi\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Builder;
#use MichaelAChrisco\ReadOnly\ReadOnlyTrait;

abstract class RemedyCase extends Model
{
    #use ReadOnlyTrait;

    protected $connection = 'remedy';

    protected $guarded = [];
    public $timestamps = false;
    protected $primaryKey = "InstanceId";
    public $incrementing = false;

    // Standardized Field Name => Type-Specific Actual Field Name
    protected static $dbSelectFields = [];
    protected static $dbDefaultFields = [];
    protected static $dbFieldMap = [];

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

    public function normalizeFieldName($field)
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
        $mapped = [];

        foreach($parent as $key => $value)
        {
            $mapped[$this->normalizeFieldName($key)] = $value;
        }

        return $mapped;
    }

    public function __get($key)
    {
        $normalizedField = $this->getVariantFieldName($key);

        \Log::debug("Get $key transformed to $normalizedField");

        return parent::__get($normalizedField);
    }
}
