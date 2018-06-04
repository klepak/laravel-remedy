<?php

namespace Klepak\RemedyApi\Tests\Unit\Models;

use Klepak\RemedyApi\Tests\TestCase;
use Klepak\RemedyApi\Traits\ReflectsOnClassName;

abstract class RemedyCaseTest extends TestCase
{
    use ReflectsOnClassName;

    public function getModel()
    {
        $remedyName = str_replace_last("Test", "", $this->getClassName());
        $fullyQualifiedClassName = 'Klepak\RemedyApi\Models\\' . $remedyName;

        return new $fullyQualifiedClassName;
    }

    public function testCanGetData()
    {
        $model = $this->getModel();

        $data = $model->take(1)->get()->first();

        $this->assertTrue($data != null);

        foreach($model->getSelectFields() as $key)
        {
            $this->assertArrayHasKey($model->getNormalizedFieldName($key), $data->toArray());
        }
    }
}