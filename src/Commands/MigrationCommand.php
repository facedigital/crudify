<?php
namespace FaceDigital\FaceGen\Commands;

use FaceDigital\FaceGen\Parsers\SchemaParser;
use FaceDigital\FaceGen\SyntaxBuilders\MigrationSyntaxBuilder;

class MigrationCommand extends GeneratorCommand
{
    protected $signature = 'facegen:migration
    { name : resource name(singular) }
    {--schema= : Schema options}
    {--timestamp= : Timestamp ex: 2023_14_16_140039 (Optional)}';

    protected $description = 'Cria uma nova migration e aplica o schema';

    public function compileStub(): string
    {
        $content = $this->filesystem->get(__DIR__.'/../../stubs/database/migrations/migration.php.stub');

        if (!$this->option('schema')) {
            $schemaOption = $this->getTableSchema($this->argument('name'));
        } else {
            $schemaOption = $this->option('schema');
        }

        $schema = (new SchemaParser())->parse($schemaOption);
        $schema = (new MigrationSyntaxBuilder())->create($schema);

        $content = str_replace('{{schema_up}}', $schema, $content);

        $content = str_replace(
            ['{{tableName}}', '{{className}}'],
            [$this->tableName(), $this->className()],
            $content
        );

        return $content;
    }

    protected function path(): string
    {
        $filename = $this->fileName();

        return database_path("migrations/{$filename}");
    }

    protected function alreadyExists(): bool
    {
        $files = $this->filesystem->glob(database_path('migrations/*.php'));

        foreach ($files as $file) {
            $this->filesystem->requireOnce($file);
        }

        return class_exists($this->className());
    }

    protected function fileName(): string
    {
        $timestamp = $this->option('timestamp')
            ? $this->option('timestamp')
            : now()->format('Y_H_d_His');

        return sprintf(
            '%s_create_%s_table',
            $timestamp,
            $this->tableName()
        );
    }

    private function className(): string
    {
        return sprintf('Create%sTable', $this->tableName()->studly());
    }
}
