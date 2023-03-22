<?php

namespace FaceDigital\FaceGen\Tests\Unit;

use FaceDigital\FaceGen\Commands\ControllerCommand;
use FaceDigital\FaceGen\Tests\TestCase;
use Symfony\Component\Console\Input\ArrayInput;

class ControllerCommandTest extends TestCase
{
    /**
     * @test
     */
    public function checkControllerAlreadyExistsControllerCommand()
    {
        $classname = 'PostController';
        $filename = $classname.'.php';
        $path = app_path("Http/Controllers/{$filename}");

        // cria o arquivo
        fopen($path, 'w');

        $this->artisan('facegen:controller', [
            'name' => 'post',
            '--schema' => 'title:string, subtitle:string:nullable, content:text'
        ])
        ->expectsOutput("Já existe um controller com o mesmo nome.")
        ->assertExitCode(0);

        // exclui após o teste
        unlink($path);
    }

    /**
     * @test
     */
    public function canRunControllerCommand()
    {
        $classname = 'PostController';
        $filename = $classname.'.php';

        $this->artisan('facegen:controller', [
            'name' => 'post',
            '--schema' => 'title:string, subtitle:string:nullable, content:text'
        ])
        ->expectsOutput("Creating File: {$filename}")
        ->expectsOutput("Created File: {$filename}")
        ->assertExitCode(0);

        unlink(app_path("Http/Controllers/{$filename}"));
    }

    /**
     * @test
     */
    public function checkCompiledStubController()
    {
        /** @var ControllerCommand $controllerCommand */
        $controllerCommand = $this->app->make(ControllerCommand::class);

        $parameters = [
            'facegen:controller',
            'name' => 'post',
            '--schema' => 'title:string(150), subtitle:string:nullable, content:text, user_id:foreign:constrained'
        ];

        $input = new ArrayInput($parameters, $controllerCommand->getDefinition());
        $controllerCommand->setInput($input);

        $this->assertEquals(file_get_contents(__DIR__.'/../../storage/tests/app/Http/Controllers/PostController.php.compiled'), $controllerCommand->compileStub());
    }
}
