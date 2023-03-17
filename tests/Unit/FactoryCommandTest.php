<?php

namespace FaceDigital\FaceGen\Tests\Unit;

use FaceDigital\FaceGen\Commands\FactoryCommand;
use FaceDigital\FaceGen\Tests\TestCase;
use Symfony\Component\Console\Input\ArrayInput;

class FactoryCommandTest extends TestCase
{
    /**
     * @test
     */
    public function checkFactoryAlreadyExistsFactoryCommand()
    {
        $classname = 'PostFactory';
        $filename = $classname.'.php';
        $path = database_path("factories/{$filename}");

        // cria o arquivo
        fopen($path, 'w');

        $this->artisan('facegen:factory', [
            'name' => 'post',
            '--schema' => 'title:string, subtitle:string:nullable, content:text'
        ])
        ->expectsOutput("Já existe uma factory com o mesmo nome.")
        ->assertExitCode(0);

        // exclui após o teste
        unlink($path);
    }

    /**
     * @test
     */
    public function canRunFactoryCommand()
    {
        $classname = 'PostFactory';
        $filename = $classname.'.php';

        $this->artisan('facegen:factory', [
            'name' => 'post',
            '--schema' => 'title:string, subtitle:string:nullable, content:text'
        ])
        ->expectsOutput("Creating File: {$filename}")
        ->expectsOutput("Created File: {$filename}")
        ->assertExitCode(0);

        unlink(database_path("factories/{$filename}"));
    }

    /**
     * @test
     */
    public function checkCompiledStubFactory()
    {
        /** @var FactoryCommand $factoryCommand */
        $factoryCommand = $this->app->make(FactoryCommand::class);

        $parameters = [
            'facegen:factory',
            'name' => 'post',
            '--schema' => 'title:string(150), subtitle:string:nullable, content:text'
        ];

        $input = new ArrayInput($parameters, $factoryCommand->getDefinition());
        $factoryCommand->setInput($input);

        // dd($factoryCommand->compileStub());

        $this->assertEquals(file_get_contents(__DIR__.'/../../storage/tests/database/factories/PostFactory.compiled'), $factoryCommand->compileStub());
    }
}
