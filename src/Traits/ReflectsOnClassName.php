<?php

namespace Klepak\RemedyApi\Traits;

trait ReflectsOnClassName
{
    public static function getClassName() {
        $Class = explode('\\',static::class);
        return end($Class);
    }
}