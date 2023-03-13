<?php

namespace FaceDigital\FaceGen\Tests\Unit;

use FaceDigital\FaceGen\Commands\MigrationCommand;
use FaceDigital\FaceGen\Tests\TestCase;
use Illuminate\Support\Str;
use Symfony\Component\Console\Input\ArrayInput;

class MigrationCommandTest extends TestCase
{
    /**
     * @test
     */
    public function canRunMigrationCommand ()
    {
        $name = 'post';

        $filename = sprintf(
            '%s_create_%s_table',
            now()->format('Y_H_d_His'),
            Str::snake($name = 'posts')
        );

        // dump($filename);

        $this->artisan('facegen:migration', [
            'name' => Str::singular($name),
            '--schema' => 'title:string(150), subtitle:string:nullable, content:text'
        ])
        ->expectsOutput("Migration criada: {$filename}")
        ->assertExitCode(0);

        // unlink(database_path("migrations/{$filename}"));
    }

    /**
     * @test
     */
    public function checkCompiledStubMigration () {
        /** @var MigrationCommand $migrationCommand */
        $migrationCommand = $this->app->make(MigrationCommand::class);

        $parameters = [
            'facegen:migration',
            'name' => 'post',
            '--schema' => 'title:string(150), subtitle:string:nullable, content:text'
        ];

        $input = new ArrayInput($parameters, $migrationCommand->getDefinition());
        $migrationCommand->setInput($input);

        // dd($migrationCommand->compileStub());

        $this->assertEquals(file_get_contents(__DIR__.'/../../storage/tests/database/migrations/create_posts_table.compiled'), $migrationCommand->compileStub());
    }
}
