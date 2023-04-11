<?php

namespace FaceDigital\Crudify\Tests\Unit;

use FaceDigital\Crudify\Commands\Views\ViewEditCommand;
use FaceDigital\Crudify\Tests\TestCase;
use Symfony\Component\Console\Input\ArrayInput;

class ViewEditCommandTest extends TestCase
{
    /**
     * @test
     */
    public function checkViewEditAlreadyExists()
    {
        $viewName = 'edit';
        $filename = $viewName.'.blade.php';
        $path = resource_path("views/posts/{$filename}");

        // cria o arquivo
        fopen($path, 'w');

        $this->artisan('crudify:edit', [
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
    public function canRunViewEditCommand()
    {
        $viewName = 'edit';
        $filename = $viewName.'.blade.php';
        $path = resource_path("views/posts/{$filename}");


        $this->artisan('crudify:edit', [
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
    public function checkCompiledStubViewEdit()
    {
        /** @var ViewEditCommand $viewEditCommand */
        $viewEditCommand = $this->app->make(ViewEditCommand::class);

        $parameters = [
            'crudify:edit',
            'name' => 'post',
            '--schema' => 'title:string(150), subtitle:string:nullable, content:text, user_id:foreign:constrained'
        ];

        $input = new ArrayInput($parameters, $viewEditCommand->getDefinition());
        $viewEditCommand->setInput($input);

        $this->assertEquals(file_get_contents(__DIR__.'/../../storage/tests/resources/views/edit.blade.php.compiled'), $viewEditCommand->compileStub());
    }
}
