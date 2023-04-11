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
        $app['config']->set('crudify.blueprint_types', $configs['blueprint_types']);
        $app['config']->set('crudify.string_types', $configs['string_types']);
        $app['config']->set('crudify.integer_types', $configs['integer_types']);
        $app['config']->set('crudify.float_types', $configs['float_types']);
        $app['config']->set('crudify.date_types', $configs['date_types']);
        $app['config']->set('crudify.foreign_types', $configs['foreign_types']);
    }

    protected function configs(): array
    {
        return require __DIR__ . '/../config/crudify.php';
    }
}
