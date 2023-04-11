<?php

namespace FaceDigital\Crudify\Tests\Unit;

use FaceDigital\Crudify\Commands\Views\ViewIndexCommand;
use FaceDigital\Crudify\Tests\TestCase;
use Symfony\Component\Console\Input\ArrayInput;

class ViewIndexCommandTest extends TestCase
{
    /**
     * @test
     */
    public function checkViewIndexAlreadyExists()
    {
        $viewName = 'index';
        $filename = $viewName.'.blade.php';
        $path = resource_path("views/posts/{$filename}");

        // cria o arquivo
        fopen($path, 'w');

        $this->artisan('crudify:index', [
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
    public function canRunViewIndexCommand()
    {
        $viewName = 'index';
        $filename = $viewName.'.blade.php';
        $path = resource_path("views/posts/{$filename}");


        $this->artisan('crudify:index', [
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
    public function checkCompiledStubViewIndex()
    {
        /** @var ViewIndexCommand $viewIndexCommand */
        $viewIndexCommand = $this->app->make(ViewIndexCommand::class);

        $parameters = [
            'crudify:index',
            'name' => 'post',
            '--schema' => 'title:string(150), subtitle:string:nullable, content:text, user_id:foreign:constrained'
        ];

        $input = new ArrayInput($parameters, $viewIndexCommand->getDefinition());
        $viewIndexCommand->setInput($input);

        $this->assertEquals(file_get_contents(__DIR__.'/../../storage/tests/resources/views/index.blade.php.compiled'), $viewIndexCommand->compileStub());
    }
}
