<?php

namespace FaceDigital\Crudify\Providers;

use FaceDigital\Crudify\Commands\AllCommand;
use FaceDigital\Crudify\Commands\ControllerCommand;
use FaceDigital\Crudify\Commands\StoreRequestCommand;
use FaceDigital\Crudify\Commands\UpdateRequestCommand;
use FaceDigital\Crudify\Commands\FactoryCommand;
use FaceDigital\Crudify\Commands\MigrationCommand;
use FaceDigital\Crudify\Commands\ModelCommand;
use FaceDigital\Crudify\Commands\Views\ViewIndexCommand;
use FaceDigital\Crudify\Commands\Views\ViewCreateCommand;
use FaceDigital\Crudify\Commands\Views\ViewEditCommand;
use FaceDigital\Crudify\Commands\Views\ViewShowCommand;
use Illuminate\Contracts\Support\DeferrableProvider;
use Illuminate\Support\ServiceProvider;

class CrudifyServiceProvider extends ServiceProvider implements DeferrableProvider
{
    protected const ROOT_PATH = __DIR__. '/../../';

    public function boot()
    {
        $this->publishes([
            // config
            self::ROOT_PATH.'/config/crudify.php' => config_path('crudify.php'),
            // class components
            self::ROOT_PATH.'/src/View/Components/InfoLabel.php' => app_path('View/Components/Crudify/InfoLabel.php'),
            self::ROOT_PATH.'/src/View/Components/InputText.php' => app_path('View/Components/Crudify/InputText.php'),
            self::ROOT_PATH.'/src/View/Components/InputNumber.php' => app_path('View/Components/Crudify/InputNumber.php'),
            self::ROOT_PATH.'/src/View/Components/InputDate.php' => app_path('View/Components/Crudify/InputDate.php'),
            // component views
            self::ROOT_PATH.'/resources/views/components/'.config('crudify.theme').'/info-label.blade.php' => resource_path('views/components/crudify/info-label.blade.php'),
            self::ROOT_PATH.'/resources/views/components/'.config('crudify.theme').'/input-text.blade.php' => resource_path('views/components/crudify/input-text.blade.php'),
            self::ROOT_PATH.'/resources/views/components/'.config('crudify.theme').'/input-number.blade.php' => resource_path('views/components/crudify/input-number.blade.php'),
            self::ROOT_PATH.'/resources/views/components/'.config('crudify.theme').'/input-date.blade.php' => resource_path('views/components/crudify/input-date.blade.php'),
        ], 'crudify');
    }

    public function register()
    {
        $this->mergeConfigFrom(
            self::ROOT_PATH.'/config/crudify.php',
            'crudify'
        );

        if ($this->app->runningInConsole()) {
            $this->commands([
                AllCommand::class,
                MigrationCommand::class,
                FactoryCommand::class,
                ModelCommand::class,
                ControllerCommand::class,
                StoreRequestCommand::class,
                UpdateRequestCommand::class,
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
            AllCommand::class,
            MigrationCommand::class,
            FactoryCommand::class,
            ModelCommand::class,
            ControllerCommand::class,
            StoreRequestCommand::class,
            UpdateRequestCommand::class,
            ViewIndexCommand::class,
            ViewCreateCommand::class,
            ViewEditCommand::class,
            ViewShowCommand::class
        ];
    }
}
