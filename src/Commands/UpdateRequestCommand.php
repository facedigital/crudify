<?php
namespace FaceDigital\Crudify\Commands;

use FaceDigital\Crudify\Parsers\SchemaParser;
use FaceDigital\Crudify\SyntaxBuilders\UpdateRequestSyntaxBuilder;

class UpdateRequestCommand extends GeneratorCommand
{
    protected $signature = 'crudify:updatereq
    { name : resource name(singular) }
    {--schema= : Schema options}';

    protected $description = 'Cria um novo Update Request e aplica o schema';

    protected string $existsMessage = "Já existe uma Update Request com o mesmo nome.";

    public function compileStub(): string
    {
        if (!$this->option('schema')) {
            $schemaOption = $this->getTableSchema($this->argument('name'));
        } else {
            $schemaOption = $this->option('schema');
        }

        $schema = (new SchemaParser())->parse($schemaOption);
        $content = (new UpdateRequestSyntaxBuilder())->create($schema);

        $content = str_replace('{{modelName}}', $this->modelName(), $content);

        return $content;
    }

    protected function path(): string
    {
        return app_path("Http/Requests/{$this->fileName()}");
    }

    protected function alreadyExists(): bool
    {
        return $this->filesystem->exists($this->path());
    }

    protected function fileName(): string
    {
        return sprintf('%sUpdateRequest.php', $this->modelName());
    }
}
