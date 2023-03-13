<?php
namespace FaceDigital\FaceGen\Commands;

use FaceDigital\FaceGen\Parsers\SchemaParser;
use FaceDigital\FaceGen\SyntaxBuilders\MigrationSyntaxBuilder;
use Illuminate\Console\Command;
use Illuminate\Filesystem\Filesystem;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Str;

class MigrationCommand extends Command
{
    protected $signature = 'facegen:migration
    { name : resource name(singular) }
    {--schema= : Schema options}';
    protected $description = 'Cria uma nova migration e aplica o schema';

    protected $filesystem;

    public function __construct(Filesystem $filesystem)
    {
        parent::__construct();
        $this->filesystem = $filesystem;
    }

    public function handle(): void
    {
        // onde será salvo o arquivo gerado [ok]
        // $path = $this->path();

        // aplicar convenção de nomenclatura [ok]
        // dump($this->resourceName());
        // dump($this->tableName());
        // dump($this->modelName());
        // dump($this->controllerName());
        // dump($this->routeName());
        // dump($this->paramName());
        // dump($this->className());

        // verificar se a tabela existe *

        // ler estrutura da tabela *

        // verificar se arquivo existe
        if ($this->alreadyExists()) {
            $this->warn("Já existe uma migration com o mesmo nome de classe.");
            return;
        }

        // gerar o arquivo
        $this->generate();

        // exibir saida
        // dd("<info>Migration Criada:</info> {$this->fileName()}");
        // dd($this->className());
        // dump(Schema::getColumnListing('posts'));
        // dd("<info>Migration criada:</info> {$this->fileName()}");
        $this->line("<info>Migration criada:</info> {$this->fileName()}");

    }

    public function generate ()
    {
        // Criar diretorio se nao existir
        $this->makeDirectory();
        // fazer os replaces no stub
        // $stubReplaced = '';

        // salvar o arquivo
        $this->filesystem->put($this->path(), $this->compileStub());

    }

    public function compileStub()
    {
        $content = $this->filesystem->get(__DIR__.'/../../stubs/database/migrations/migration.php.stub');

        $schema = (new SchemaParser())->parse($this->option('schema'));
        $schema = (new MigrationSyntaxBuilder())->create($schema);

        $content = str_replace('{{schema_up}}', $schema, $content);

        $content = str_replace(
            ['{{tableName}}', '{{className}}'],
            [$this->tableName(), $this->className()],
            $content
        );

        return $content;
    }

    private function makeDirectory()
    {
        if (!$this->filesystem->isDirectory(dirname($this->path()))) {
            $this->filesystem->makeDirectory(dirname($this->path()), 0755, true);
        }
    }

    private function path(): string
    {
        $filename = $this->fileName();

        return database_path("migrations/{$filename}");
    }

    private function alreadyExists(): bool
    {
        $files = $this->filesystem->glob(database_path('migrations/*.php'));

        foreach($files as $file) {
            $this->filesystem->requireOnce($file);
        }

        return class_exists($this->className());
    }

    private function fileName(): string
    {
        return $filename = sprintf(
            '%s_create_%s_table',
            now()->format('Y_H_d_His'),
            $this->tableName()
        );
    }

    private function resourceName()
    {
        return Str::of($this->argument('name'))
            ->lower()
            ->singular();
    }

    private function tableName()
    {
        return Str::of($this->argument('name'))
            ->snake()
            ->plural();
    }

    private function modelName()
    {
        return $this
            ->resourceName()
            ->studly();
    }

    private function controllerName()
    {
        return sprintf('%sController', $this->modelName());
    }

    private function routeName()
    {
        return $this
            ->tableName()
            ->kebab();
    }

    private function paramName()
    {
        return $this
            ->modelName()
            ->kebab();
    }

    private function className()
    {
        return sprintf('Create%sTable', $this->tableName()->studly());
    }

}
