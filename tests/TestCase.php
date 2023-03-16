<?php

namespace FaceDigital\FaceGen\Tests;

use FaceDigital\FaceGen\Providers\FaceGenServiceProvider;
use Orchestra\Testbench\TestCase as Orchestra;
use Illuminate\Foundation\Testing\RefreshDatabase;

abstract class TestCase extends Orchestra
{
    use RefreshDatabase;

    protected function getPackageProviders($app): array
    {
        return [
            FaceGenServiceProvider::class
        ];
    }

    protected function getEnvironmentSetUp($app)
    {
        $configs = $this->configs();
        $app['config']->set('facegen.blueprint_types', $configs['blueprint_types']);
    }

    protected function configs(): array
    {
        return require __DIR__ . '/../config/facegen.php';
    }
}
