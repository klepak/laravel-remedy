<?php

namespace Klepak\RemedyApi\Models\Eloquent;

use Closure;
use Illuminate\Support\Collection;
use Illuminate\Database\ConnectionInterface;
use Illuminate\Database\Query\Grammars\Grammar;
use Illuminate\Database\Query\Processors\Processor;

class RemedyQueryBuilder extends \Illuminate\Database\Query\Builder
{
    public function where($column, $operator = null, $value = null, $boolean = 'and')
    {
        // translate properties
        \Log::info("Sub-where!");
        return parent::where(...func_get_args());
    }
}