<?php

namespace Klepak\RemedyApi\Tests\Unit\API;

use Klepak\RemedyApi\Tests\TestCase;
use Klepak\RemedyApi\Traits\ReflectsOnClassName;

abstract class RemedyCaseTest extends TestCase
{
    use ReflectsOnClassName;

    protected $api = null;

    /**
     * Setup the test environment.
     */
    protected function setUp()
    {
        parent::setUp();

        $this->api = $this->getModel()->api();
    }

    public function getModel()
    {
        $remedyName = str_replace_last("Test", "", $this->getClassName());
        $fullyQualifiedClassName = 'Klepak\RemedyApi\Models\\' . $remedyName;

        return new $fullyQualifiedClassName;
    }

    public function testApiLogin()
    {
        $this->setExpectedException('\Klepak\RemedyApi\Exceptions\RemedyApiException');

        // test incorrect credentials login should produce exception
        $this->api->login("incorrect", "credentials");
    }

    public function testCreate()
    {
        #$this->api->create([
        #    "Description" => "TEST"
        #]);
    }
}