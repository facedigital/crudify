<?php

namespace FaceDigital\Crudify\Tests\Unit;

use FaceDigital\Crudify\Commands\Views\ViewCreateCommand;
use FaceDigital\Crudify\Tests\TestCase;
use Symfony\Component\Console\Input\ArrayInput;

class ViewCreateCommandTest extends TestCase
{
    /**
     * @test
     */
    public function checkViewCreateAlreadyExists()
    {
        $viewName = 'create';
        $filename = $viewName.'.blade.php';
        $path = resource_path("views/posts/{$filename}");

        // cria o arquivo
        fopen($path, 'w');

        $this->artisan('crudify:create', [
            'name' => 'post',
            '--schema' => 'title:string, subtitle:string:nullable, content:text'
        ])
        ->expectsOutput("Já existe uma view com o mesmo nome.")
        ->assertExitCode(0);

        // exclui após o teste
        unlink($path);
    }

    /**
     * @test
     */
    public function canRunViewCreateCommand()
    {
        $viewName = 'create';
        $filename = $viewName.'.blade.php';
        $path = resource_path("views/posts/{$filename}");


        $this->artisan('crudify:create', [
            'name' => 'post',
            '--schema' => 'title:string, subtitle:string:nullable, content:text'
        ])
        ->expectsOutput("Creating File: {$filename}")
        ->expectsOutput("Created File: {$filename}")
        ->assertExitCode(0);

        unlink($path);
    }

    /**
     * @test
     */
    public function checkCompiledStubViewCreate()
    {
        /** @var ViewCreateCommand $viewCreateCommand */
        $viewCreateCommand = $this->app->make(ViewCreateCommand::class);

        $parameters = [
            'crudify:create',
            'name' => 'post',
            '--schema' => 'title:string(150), subtitle:string:nullable, content:text, user_id:foreign:constrained'
        ];

        $input = new ArrayInput($parameters, $viewCreateCommand->getDefinition());
        $viewCreateCommand->setInput($input);

        $this->assertEquals(file_get_contents(__DIR__.'/../../storage/tests/resources/views/create.blade.php.compiled'), $viewCreateCommand->compileStub());
    }
}
