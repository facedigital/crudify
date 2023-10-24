<?php
namespace FaceDigital\Crudify\Commands;

use FaceDigital\Crudify\GeneratorTools\FileManger;
use FaceDigital\Crudify\Parsers\SchemaParser;
use FaceDigital\Crudify\SyntaxBuilders\ControllerSyntaxBuilder;

class ControllerCommand extends GeneratorCommand
{
    use FileManger;
    
    protected $signature = 'crudify:controller
    { name : resource name(singular) }
    {--schema= : Schema options}
    {--service= : Optional Yes/No}';

    protected $description = 'Cria um novo controller e aplica o schema';

    protected string $existsMessage = "Já existe um controller com o mesmo nome.";

    public function compileStub(): string
    {
        if (!$this->option('schema')) {
            $schemaOption = $this->getTableSchema($this->argument('name'));
        } else {
            $schemaOption = $this->option('schema');
        }
        
        $schema = (new SchemaParser())->parse($schemaOption);
        $content = (new ControllerSyntaxBuilder())->create($schema);

        $content = str_replace('{{modelName}}', $this->modelName(), $content);
        $content = str_replace('{{pluralName}}', $this->pluralName(), $content);
        $content = str_replace('{{singularName}}', $this->singularName(), $content);
        $content = str_replace('{{parameterName}}', $this->parameterName(), $content);

        
        $content = $this->removeTags($content, ['service']);
        
        
        return $content;
    }

    protected function path(): string
    {
        $filename = $this->fileName();

        return app_path("Http/Controllers/{$filename}");
    }

    protected function alreadyExists(): bool
    {
        return $this->filesystem->exists(app_path("Http/Controllers/{$this->fileName()}"));
    }

    protected function fileName(): string
    {
        return sprintf('%s.php', $this->className());
    }

    private function className(): string
    {
        return sprintf('%sController', $this->modelName());
    }
}
