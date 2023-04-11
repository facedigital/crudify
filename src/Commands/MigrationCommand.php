<?php
namespace FaceDigital\Crudify\Commands;

use FaceDigital\Crudify\Parsers\SchemaParser;
use FaceDigital\Crudify\SyntaxBuilders\MigrationSyntaxBuilder;

class MigrationCommand extends GeneratorCommand
{
    protected $signature = 'crudify:migration
    { name : resource name(singular) }
    {--schema= : Schema options}
    {--timestamp= : Timestamp ex: 2023_14_16_140039 (Optional)}';

    protected $description = 'Cria uma nova migration e aplica o schema';

    public function compileStub(): string
    {
        if (!$this->option('schema')) {
            $schemaOption = $this->getTableSchema($this->argument('name'));
        } else {
            $schemaOption = $this->option('schema');
        }

        $schema = (new SchemaParser())->parse($schemaOption);
        $content = (new MigrationSyntaxBuilder())->create($schema);

        $content = str_replace(
            ['{{tableName}}', '{{className}}'],
            [$this->tableName(), $this->className()],
            $content
        );

        return $content;
    }

    protected function path(): string
    {
        return database_path("migrations/{$this->fileName()}");
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
            '%s_create_%s_table.php',
            $timestamp,
            $this->tableName()
        );
    }

    private function className(): string
    {
        return sprintf('Create%sTable', $this->tableName()->studly());
    }
}
