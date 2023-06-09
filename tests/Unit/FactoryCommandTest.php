<?php

namespace FaceDigital\Crudify\Tests\Unit;

use FaceDigital\Crudify\Commands\FactoryCommand;
use FaceDigital\Crudify\Tests\TestCase;
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

        $this->artisan('crudify:factory', [
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

        $this->artisan('crudify:factory', [
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
            'crudify:factory',
            'name' => 'post',
            '--schema' => 'title:string(150), subtitle:string:nullable, content:text, user_id:foreign:constrained'
        ];

        $input = new ArrayInput($parameters, $factoryCommand->getDefinition());
        $factoryCommand->setInput($input);

        $this->assertEquals(file_get_contents(__DIR__.'/../../storage/tests/database/factories/PostFactory.compiled'), $factoryCommand->compileStub());
    }

     /**
     * @test
     */
    public function checkCompiledStubOldForeignSyntaxFactory()
    {
        /** @var FactoryCommand $factoryCommand */
        $factoryCommand = $this->app->make(FactoryCommand::class);

        $parameters = [
            'crudify:factory',
            'name' => 'post',
            '--schema' => 'title:string(150), subtitle:string:nullable, content:text, user_id:unsignedBigInteger:foreign'
        ];

        $input = new ArrayInput($parameters, $factoryCommand->getDefinition());
        $factoryCommand->setInput($input);

        $this->assertEquals(file_get_contents(__DIR__.'/../../storage/tests/database/factories/PostFactory.compiled'), $factoryCommand->compileStub());
    }
}
