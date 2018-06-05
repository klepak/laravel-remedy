<?php

namespace Klepak\RemedyApi\Traits;

/**
 * Provides reflection on class name
 */
trait ReflectsOnClassName
{
    /**
     * Get class name without namespace
     */
    public static function getClassName() {
        $Class = explode('\\',static::class);
        return end($Class);
    }
}