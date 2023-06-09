<?php

namespace FaceDigital\Crudify\Tests\Unit;

use FaceDigital\Crudify\Commands\MigrationCommand;
use FaceDigital\Crudify\Tests\TestCase;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Str;
use Symfony\Component\Console\Input\ArrayInput;
use Illuminate\Database\Schema\Blueprint;

class MigrationCommandTest extends TestCase
{
    /**
     * @test
     */
    public function canRunMigrationCommand()
    {
        $name = 'post';

        $timestamp = now()->format('Y_H_d_His');
        $filename = sprintf(
            '%s_create_%s_table.php',
            $timestamp,
            Str::snake($name = 'posts')
        );

        $this->artisan('crudify:migration', [
            'name' => Str::singular($name),
            '--schema' => 'title:string, subtitle:string:nullable, content:text',
            '--timestamp' => $timestamp
        ])
        ->expectsOutput("Creating File: {$filename}")
        ->expectsOutput("Created File: {$filename}")
        ->assertExitCode(0);

        unlink(database_path("migrations/{$filename}"));
    }

    /**
     * @test
     */
    public function checkCompiledStubMigration()
    {
        /** @var MigrationCommand $migrationCommand */
        $migrationCommand = $this->app->make(MigrationCommand::class);

        $parameters = [
            'crudify:migration',
            'name' => 'post',
            '--schema' => 'title:string, subtitle:string:nullable, content:text'
        ];

        $input = new ArrayInput($parameters, $migrationCommand->getDefinition());
        $migrationCommand->setInput($input);

        $this->assertEquals(file_get_contents(__DIR__.'/../../storage/tests/database/migrations/create_posts_table.compiled'), $migrationCommand->compileStub());
    }

    /**
     * @test
     */
    public function checkCompiledStubMigrationFromTable()
    {
        /** @var MigrationCommand $migrationCommand */
        $migrationCommand = $this->app->make(MigrationCommand::class);

        Schema::dropIfExists('posts');

        // Cria Tabela Posts para teste
        Schema::create('posts', function (Blueprint $table) {
            $table->string('title');
            $table->string('subtitle')->nullable();
            $table->text('content');
        });

        $parameters = [
            'crudify:migration',
            'name' => 'posts'
        ];

        $input = new ArrayInput($parameters, $migrationCommand->getDefinition());
        $migrationCommand->setInput($input);

        $this->assertEquals(file_get_contents(__DIR__.'/../../storage/tests/database/migrations/create_posts_table.compiled'), $migrationCommand->compileStub());

        // remove tabela após o teste
        Schema::dropIfExists('posts');
    }

    /**
     * @test
     */
    public function checkGenerateSchemaFromTable()
    {
        /** @var MigrationCommand $migrationCommand */
        $migrationCommand = $this->app->make(MigrationCommand::class);

        Schema::dropIfExists('posts');

        // Cria Tabela Posts para teste
        Schema::create('posts', function (Blueprint $table) {
            $table->id();
            $table->string('title');
            $table->string('subtitle')->nullable();
            $table->text('content');
            $table->timestamps();
        });

        $this->assertEquals("id:integer:autoIncrement, title:string, subtitle:string:nullable, content:text, created_at:datetime:nullable, updated_at:datetime:nullable", $migrationCommand->getTableSchema('posts'));

        // remove tabela após o teste
        Schema::dropIfExists('posts');
    }
}
