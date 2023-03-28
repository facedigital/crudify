<?php

namespace FaceDigital\FaceGen\Tests\Unit;

use FaceDigital\FaceGen\Commands\Views\ViewShowCommand;
use FaceDigital\FaceGen\Tests\TestCase;
use Symfony\Component\Console\Input\ArrayInput;

class ViewShowCommandTest extends TestCase
{
    /**
     * @test
     */
    public function checkViewShowAlreadyExists()
    {
        $viewName = 'show';
        $filename = $viewName.'.blade.php';
        $path = resource_path("views/posts/{$filename}");

        // cria o arquivo
        fopen($path, 'w');

        $this->artisan('facegen:show', [
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
    public function canRunViewShowCommand()
    {
        $viewName = 'show';
        $filename = $viewName.'.blade.php';
        $path = resource_path("views/posts/{$filename}");


        $this->artisan('facegen:show', [
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
    public function checkCompiledStubViewShow()
    {
        /** @var ViewShowCommand $viewCreateCommand */
        $viewCreateCommand = $this->app->make(ViewShowCommand::class);

        $parameters = [
            'facegen:show',
            'name' => 'post',
            '--schema' => 'title:string(150), subtitle:string:nullable, content:text, user_id:foreign:constrained'
        ];

        $input = new ArrayInput($parameters, $viewCreateCommand->getDefinition());
        $viewCreateCommand->setInput($input);

        $this->assertEquals(file_get_contents(__DIR__.'/../../storage/tests/resources/views/show.blade.php.compiled'), $viewCreateCommand->compileStub());
    }
}
