<?php
namespace FaceDigital\FaceGen\Commands;

use FaceDigital\FaceGen\Parsers\SchemaParser;
use FaceDigital\FaceGen\SyntaxBuilders\FactorySyntaxBuilder;
use Illuminate\Console\Command;
use Illuminate\Filesystem\Filesystem;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;

class FactoryCommand extends Command
{
    protected $signature = 'facegen:factory
    { name : resource name(singular) }
    {--schema= : Schema options}
    {--timestamp= : Timestamp ex: 2023_14_16_140039 (Optional)}';
    protected $description = 'Cria uma nova factory e aplica o schema';

    protected $filesystem;

    public function __construct(Filesystem $filesystem)
    {
        parent::__construct();
        $this->filesystem = $filesystem;
    }

    public function handle(): void
    {
        // Se não passar o Schema
        if (!$this->option('schema')) {
            //verifica se a tabela existe
            if (!$this->hasTable()) {
                $this->warn('A tabela '.$this->argument('name').' não existe no banco de dados');
                return;
            }
        }

        // verifica se a factory/arquivo/class já existe
        if ($this->alreadyExists()) {
            $this->warn("Já existe uma factory com o mesmo nome.");
            return;
        }

        // gera o arquivo
        $this->generate();

        // exibir saida
        $this->line("<info>Factory {$this->className()} criada: {$this->fileName()}</info>");
    }

    public function generate()
    {
        // Criar diretorio se nao existir
        $this->makeDirectory();
        // fazer os replaces no stub
        // $stubReplaced = '';


        if (!$this->option('schema')) {
            // ler estrutura da tabela
            $schema = $this->getTableSchema($this->argument('name'));
            // TODO: gerar model, controller e Views com base no Schema
            return $schema;
        }

        // salvar o arquivo
        $this->filesystem->put($this->path(), $this->compileStub());
    }

    public function compileStub()
    {
        // $content = $this->filesystem->get(__DIR__.'/../../stubs/database/factories/factory.php.stub');

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

    private function makeDirectory()
    {
        if (!$this->filesystem->isDirectory(dirname($this->path()))) {
            $this->filesystem->makeDirectory(dirname($this->path()), 0755, true);
        }
    }

    private function path(): string
    {
        $filename = $this->fileName();

        return database_path("factories/{$filename}");
    }

    private function alreadyExists(): bool
    {
        return $this->filesystem->exists(database_path("factories/{$this->fileName()}"));
    }

    private function hasTable()
    {
        return Schema::hasTable($this->argument('name'));
    }

    private function fileName(): string
    {
        return sprintf('%s.php', $this->className());
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
        return sprintf('%sFactory', $this->modelName());
    }

    public function getTableSchema(string $table): string
    {
        $schema = DB::getDoctrineSchemaManager();
        $columns = $schema->listTableColumns($table);

        $colsWithFields = [];

        foreach ($columns as $column) {
            array_push($colsWithFields, $this->getColumnAttributes($table, $column));
        }

        $colsWithFields = implode(', ', $colsWithFields);

        return $colsWithFields;
    }

    private function getColumnAttributes($table, $column): string
    {
        $doctrineColumn = DB::getDoctrineColumn($table, $column->getName());

        $colType = Schema::getColumnType($table, $column->getName());
        $colDefault = $doctrineColumn->getDefault();

        $columnAtributes = (object) [
            "name"          => $column->getName(),
            "type"          => $colType ? ':'.$colType : '',
            "length"        => $doctrineColumn->getLength() ? $doctrineColumn->getLength() : '',
            "unsigned"      => $doctrineColumn->getUnsigned() ? ':unsigned' : '',
            "notNull"       => $doctrineColumn->getNotnull() ? '' : ':nullable',
            "default"       => $colDefault ? 'default('.$colDefault.')' : '',
            "autoincrement" => $doctrineColumn->getAutoincrement() ? ':autoIncrement' : '',
        ];

        return $this->mountColumnString($columnAtributes);
    }

    protected function mountColumnString($attrs): string
    {
        $colString = $attrs?->name.$attrs?->type.$attrs?->length.$attrs?->unsigned.$attrs?->notNull.$attrs?->default.$attrs?->autoincrement;

        return $colString;
    }
}
