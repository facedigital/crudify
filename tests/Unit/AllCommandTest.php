<?php

namespace FaceDigital\FaceGen\Tests\Unit;

use FaceDigital\FaceGen\Tests\TestCase;
use Illuminate\Support\Str;
use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;

class AllCommandTest extends TestCase
{
    /**
     * @test
     */
    public function canRunAllCommand()
    {
        $name = 'post';

        $timestamp = now()->format('Y_H_d_His');
        $migrationFilename = sprintf(
            '%s_create_%s_table.php',
            $timestamp,
            Str::snake($name = 'posts')
        );

        $controllerFilename = sprintf(
            '%sController.php',
            Str::singular(Str::studly($name))
        );

        $factoryFilename = sprintf(
            '%sFactory.php',
            Str::singular(Str::studly($name))
        );

        $modelFilename = sprintf(
            '%s.php',
            Str::singular(Str::studly($name))
        );

        $viewPath = Str::plural($name);

        $this->artisan('crudify:all', [
            'name' => Str::singular($name),
            '--schema' => 'title:string, subtitle:string:nullable, content:text',
            '--timestamp' => $timestamp
        ])
        ->expectsOutput("Migration Created!")
        ->expectsOutput("Factory Created!")
        ->expectsOutput("Model Created!")
        ->expectsOutput("Controller Created!")
        ->expectsOutput("View index Created!")
        ->expectsOutput("View create Created!")
        ->expectsOutput("View edit Created!")
        ->expectsOutput("View show Created!")
        ->expectsOutput("All done!!")
        ->assertExitCode(0);

        unlink(database_path("migrations/$migrationFilename"));
        unlink(app_path("Http/Controllers/$controllerFilename"));
        unlink(app_path("Models/$modelFilename"));
        unlink(database_path("factories/$factoryFilename"));
        unlink(resource_path("views/$viewPath/index.blade.php"));
        unlink(resource_path("views/$viewPath/create.blade.php"));
        unlink(resource_path("views/$viewPath/edit.blade.php"));
        unlink(resource_path("views/$viewPath/show.blade.php"));
    }

    /**
     * @test
     */
    public function checkCanRunAllCommandFromTable()
    {
        $name = 'post';

        $timestamp = now()->format('Y_H_d_His');
        $migrationFilename = sprintf(
            '%s_create_%s_table.php',
            $timestamp,
            Str::snake($name = 'posts')
        );

        $controllerFilename = sprintf(
            '%sController.php',
            Str::singular(Str::studly($name))
        );

        $factoryFilename = sprintf(
            '%sFactory.php',
            Str::singular(Str::studly($name))
        );

        $modelFilename = sprintf(
            '%s.php',
            Str::singular(Str::studly($name))
        );

        $viewPath = Str::plural($name);

        Schema::dropIfExists('posts');

        // Cria Tabela Posts para teste
        Schema::create('posts', function (Blueprint $table) {
            $table->string('title');
            $table->string('subtitle')->nullable();
            $table->text('content');
        });

        $this->artisan('crudify:all', [
            'name' => Str::plural($name),
            '--timestamp' => $timestamp
        ])
        ->expectsOutput("Migration Created!")
        ->expectsOutput("Factory Created!")
        ->expectsOutput("Model Created!")
        ->expectsOutput("Controller Created!")
        ->expectsOutput("View index Created!")
        ->expectsOutput("View create Created!")
        ->expectsOutput("View edit Created!")
        ->expectsOutput("View show Created!")
        ->expectsOutput("All done!!")
        ->assertExitCode(0);

        unlink(database_path("migrations/$migrationFilename"));
        unlink(app_path("Http/Controllers/$controllerFilename"));
        unlink(app_path("Models/$modelFilename"));
        unlink(database_path("factories/$factoryFilename"));
        unlink(resource_path("views/$viewPath/index.blade.php"));
        unlink(resource_path("views/$viewPath/create.blade.php"));
        unlink(resource_path("views/$viewPath/edit.blade.php"));
        unlink(resource_path("views/$viewPath/show.blade.php"));

        // remove tabela após o teste
        Schema::dropIfExists('posts');
    }

    /**
     * @test
     */
    public function checkCanRunAllCommandFromTableWithExcept()
    {
        $name = 'post';

        $controllerFilename = sprintf(
            '%sController.php',
            Str::singular(Str::studly($name))
        );

        $modelFilename = sprintf(
            '%s.php',
            Str::singular(Str::studly($name))
        );

        $viewPath = Str::plural($name);

        $this->artisan('crudify:all', [
            'name' => Str::plural($name),
            '--schema' => 'title:string, subtitle:string:nullable, content:text',
            '--except' => ['migration', 'factory']
        ])
        ->expectsOutput("Model Created!")
        ->expectsOutput("Controller Created!")
        ->expectsOutput("View index Created!")
        ->expectsOutput("View create Created!")
        ->expectsOutput("View edit Created!")
        ->expectsOutput("View show Created!")
        ->expectsOutput("All done!!")
        ->assertExitCode(0);

        unlink(app_path("Http/Controllers/$controllerFilename"));
        unlink(app_path("Models/$modelFilename"));
        unlink(resource_path("views/$viewPath/index.blade.php"));
        unlink(resource_path("views/$viewPath/create.blade.php"));
        unlink(resource_path("views/$viewPath/edit.blade.php"));
        unlink(resource_path("views/$viewPath/show.blade.php"));

        // remove tabela após o teste
        Schema::dropIfExists('posts');
    }
}
