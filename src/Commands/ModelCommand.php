<?php
namespace FaceDigital\Crudify\Commands;

use FaceDigital\Crudify\Parsers\SchemaParser;
use FaceDigital\Crudify\SyntaxBuilders\ModelSyntaxBuilder;

class ModelCommand extends GeneratorCommand
{
    protected $signature = 'crudify:model
    { name : resource name(singular) }
    {--schema= : Schema options}
    {--timestamp= : Timestamp ex: 2023_14_16_140039 (Optional)}';

    protected $description = 'Cria uma nova model e aplica o schema';

    protected string $existsMessage = "Já existe uma model com o mesmo nome.";

    public function compileStub(): string
    {
        if (!$this->option('schema')) {
            $schemaOption = $this->getTableSchema($this->argument('name'));
        } else {
            $schemaOption = $this->option('schema');
        }

        $schema = (new SchemaParser())->parse($schemaOption);
        $content = (new ModelSyntaxBuilder())->create($schema);

        $content = str_replace('{{modelName}}', $this->modelName(), $content);

        $protectedTable = $this->option('schema') ? $this->tableName() : $this->argument('name');

        $content = str_replace('{{tableName}}', $protectedTable, $content);


        return $content;
    }

    protected function path(): string
    {
        return app_path("Models/{$this->fileName()}");
    }

    protected function alreadyExists(): bool
    {
        return $this->filesystem->exists($this->path());
    }

    protected function fileName(): string
    {
        return sprintf('%s.php', $this->modelName());
    }
}
