<?php
namespace FaceDigital\Crudify\Commands;

use FaceDigital\Crudify\GeneratorTools\FileManger;
use FaceDigital\Crudify\Parsers\SchemaParser;
use FaceDigital\Crudify\SyntaxBuilders\ServiceSyntaxBuilder;
use Illuminate\Support\Str;

class ServiceCommand extends GeneratorCommand
{
    use FileManger;

    protected $signature = 'crudify:service
    { name : resource name(singular) }
    {--schema= : Schema options}';

    protected $description = 'Cria um novo Service e aplica o schema';

    protected string $existsMessage = "Já existe um service com o mesmo nome.";

    public function compileStub(): string
    {
        
//        if (!$this->option('schema')) {
//            $schemaOption = $this->getTableSchema($this->argument('name'));
//        } else {
//            $schemaOption = $this->option('schema');
//        }


        $output = $this->getTemplate('app/Services/DummyService');
        

        return $output;
    }
    
    private function getStubs(): string
    {
        return file_get_contents(__DIR__ . '/../../stubs/app/Services/DummyService.stub');
    }
    
    protected function path(): string
    {
        $filename = $this->fileName();

        return app_path("Services/{$filename}");
    }

    protected function alreadyExists(): bool
    {
        return $this->filesystem->exists(app_path("Services/{$this->fileName()}"));
    }

    protected function fileName(): string
    {
        return sprintf('%s.php', $this->argument('name').'Service');
    }

}
