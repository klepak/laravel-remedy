<?php

namespace Klepak\RemedyApi\Tests;

use Orchestra\Testbench\TestCase as BaseTestCase;

/**
 * The base test case class, responsible for bootstrapping the testing environment.
 *
 * @package klepak\laravel-remedy
 * @author  Knut Leborg <knut@lepa.no>
 */
abstract class TestCase extends BaseTestCase
{
    /**
     * Define environment setup.
     *
     * @param  \Illuminate\Foundation\Application  $app
     * @return void
     */
    protected function getEnvironmentSetUp($app)
    {
        $dotenv = new \Dotenv\Dotenv(__DIR__);
        $dotenv->load();

        $app["config"]->set("database.connections.remedy", [
            'driver' => 'sqlsrv',
            'host' => env('TEST_REMEDY_DB_HOST'),
            'port' => env('TEST_REMEDY_DB_PORT'),
            'database' => env('TEST_REMEDY_DB_DATABASE'),
            'username' => env('TEST_REMEDY_DB_USERNAME'),
            'password' => env('TEST_REMEDY_DB_PASSWORD'),
            'charset' => 'utf8',
            'prefix' => '',
        ]);
    }
}