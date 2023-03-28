<?php

namespace FaceDigital\FaceGen\Providers;

use FaceDigital\FaceGen\Commands\ControllerCommand;
use FaceDigital\FaceGen\Commands\FactoryCommand;
use FaceDigital\FaceGen\Commands\MigrationCommand;
use FaceDigital\FaceGen\Commands\ModelCommand;
use FaceDigital\FaceGen\Commands\Views\ViewIndexCommand;
use FaceDigital\FaceGen\Commands\Views\ViewCreateCommand;
use FaceDigital\FaceGen\Commands\Views\ViewEditCommand;
use FaceDigital\FaceGen\Commands\Views\ViewShowCommand;
use Illuminate\Contracts\Support\DeferrableProvider;
use Illuminate\Support\ServiceProvider;

class FaceGenServiceProvider extends ServiceProvider implements DeferrableProvider
{
    protected const ROOT_PATH = __DIR__. '/../../';

    public function boot()
    {
        $this->publishes([
            self::ROOT_PATH.'/config/facegen.php' => config_path('facegen.php'),
        ], 'facegen');
    }

    public function register()
    {
        $this->mergeConfigFrom(
            self::ROOT_PATH.'/config/facegen.php',
            'facegen'
        );

        if ($this->app->runningInConsole()) {
            $this->commands([
                MigrationCommand::class,
                FactoryCommand::class,
                ModelCommand::class,
                ControllerCommand::class,
                ViewIndexCommand::class,
                ViewCreateCommand::class,
                ViewEditCommand::class,
                ViewShowCommand::class
            ]);
        }
    }

    public function provides()
    {
        return [
            MigrationCommand::class,
            FactoryCommand::class,
            ModelCommand::class,
            ControllerCommand::class,
            ViewIndexCommand::class,
            ViewCreateCommand::class,
            ViewEditCommand::class,
            ViewShowCommand::class
        ];
    }
}
