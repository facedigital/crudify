<?php
namespace FaceDigital\Crudify\Commands;

use FaceDigital\Crudify\Parsers\SchemaParser;
use FaceDigital\Crudify\SyntaxBuilders\FactorySyntaxBuilder;

class FactoryCommand extends GeneratorCommand
{
    protected $signature = 'crudify:factory
    { name : resource name(singular) }
    {--schema= : Schema options}
    {--timestamp= : Timestamp ex: 2023_14_16_140039 (Optional)}';

    protected $description = 'Cria uma nova factory e aplica o schema';

    protected string $existsMessage = "Já existe uma factory com o mesmo nome.";

    public function compileStub(): string
    {
        if (!$this->option('schema')) {
            $schemaOption = $this->getTableSchema($this->argument('name'));
        } else {
            $schemaOption = $this->option('schema');
        }

        $schema = (new SchemaParser())->parse($schemaOption);
        $content = (new FactorySyntaxBuilder())->create($schema);

        $content = str_replace('{{modelName}}', $this->modelName(), $content);

        return $content;
    }

    protected function path(): string
    {
        $filename = $this->fileName();

        return database_path("factories/{$filename}");
    }

    protected function alreadyExists(): bool
    {
        return $this->filesystem->exists(database_path("factories/{$this->fileName()}"));
    }

    protected function fileName(): string
    {
        return sprintf('%s.php', $this->className());
    }

    private function className(): string
    {
        return sprintf('%sFactory', $this->modelName());
    }
}
