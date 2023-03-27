<?php
namespace FaceDigital\FaceGen\Commands\Views;

use FaceDigital\FaceGen\Parsers\SchemaParser;
use FaceDigital\FaceGen\SyntaxBuilders\ViewIndexSyntaxBuilder;
use FaceDigital\FaceGen\Commands\GeneratorCommand;

class ViewIndexCommand extends GeneratorCommand
{
    protected $signature = 'facegen:index
    { name : resource name(singular) }
    {--schema= : Schema options}
    {--timestamp= : Timestamp ex: 2023_14_16_140039 (Optional)}';

    protected $description = 'Cria uma nova view e aplica o schema';

    protected string $existsMessage = "Já existe uma view com o mesmo nome.";

    public function compileStub(): string
    {
        if (!$this->option('schema')) {
            $schemaOption = $this->getTableSchema($this->argument('name'));
        } else {
            $schemaOption = $this->option('schema');
        }

        $schema = (new SchemaParser())->parse($schemaOption);
        $content = (new ViewIndexSyntaxBuilder())->create($schema);

        $content = str_replace('{{modelName}}', $this->modelName(), $content);
        $content = str_replace('{{pluralName}}', $this->pluralName(), $content);
        $content = str_replace('{{singularName}}', $this->singularName(), $content);

        return $content;
    }

    protected function path(): string
    {
        $filename = $this->fileName();

        return resource_path("views/{$this->pluralName()}/{$filename}");
    }

    protected function alreadyExists(): bool
    {
        return $this->filesystem->exists(resource_path("views/{$this->pluralName()}/{$this->fileName()}"));
    }

    protected function fileName(): string
    {
        return sprintf('%s.blade.php', 'index');
    }
}
