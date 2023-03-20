<?php

namespace FaceDigital\FaceGen\Tests\Unit;

use FaceDigital\FaceGen\Commands\ModelCommand;
use FaceDigital\FaceGen\Tests\TestCase;
use Symfony\Component\Console\Input\ArrayInput;

class ModelCommandTest extends TestCase
{
    /**
     * @test
     */
    public function checkModelAlreadyExistsModelCommand()
    {
        $classname = 'Post';
        $filename = $classname.'.php';
        $path = app_path("Models/{$filename}");

        // cria o arquivo
        fopen($path, 'w');

        $this->artisan('facegen:model', [
            'name' => 'post',
            '--schema' => 'title:string, subtitle:string:nullable, content:text'
        ])
        ->expectsOutput("Já existe uma model com o mesmo nome.")
        ->assertExitCode(0);

        // exclui após o teste
        unlink($path);
    }

    /**
     * @test
     */
    public function canRunModelCommand()
    {
        $classname = 'Post';
        $filename = $classname.'.php';

        $this->artisan('facegen:model', [
            'name' => 'post',
            '--schema' => 'title:string, subtitle:string:nullable, content:text'
        ])
        ->expectsOutput("Creating File: {$filename}")
        ->expectsOutput("Created File: {$filename}")
        ->assertExitCode(0);

        unlink(app_path("Models/{$filename}"));
    }

    /**
     * @test
     */
    public function checkCompiledStubModel()
    {
        /** @var ModelCommand $modelCommand */
        $modelCommand = $this->app->make(ModelCommand::class);

        $parameters = [
            'facegen:model',
            'name' => 'post',
            '--schema' => 'title:string(150), subtitle:string:nullable, content:text'
        ];

        $input = new ArrayInput($parameters, $modelCommand->getDefinition());
        $modelCommand->setInput($input);

        // dd($modelCommand->compileStub());

        $this->assertEquals(file_get_contents(__DIR__.'/../../storage/tests/app/Models/Post.compiled'), $modelCommand->compileStub());
    }
}
