<?php
namespace FaceDigital\FaceGen\Commands;

use FaceDigital\FaceGen\Parsers\SchemaParser;
use FaceDigital\FaceGen\SyntaxBuilders\MigrationSyntaxBuilder;
use Illuminate\Console\Command;
use Illuminate\Filesystem\Filesystem;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;

class MigrationCommand extends Command
{
    protected $signature = 'facegen:migration
    { name : resource name(singular) }
    {--schema= : Schema options}
    {--timestamp= : Timestamp ex: 2023_14_16_140039 (Optional)}';
    protected $description = 'Cria uma nova migration e aplica o schema';

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

        // verifica se a migration/arquivo/class já existe
        if ($this->alreadyExists()) {
            $this->warn("Já existe uma migration com o mesmo nome de classe.");
            return;
        }

        // gera o arquivo
        $this->generate();

        // exibir saida
        $this->line("<info>Migration criada:</info> {$this->fileName()}");
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

        foreach ($files as $file) {
            $this->filesystem->requireOnce($file);
        }

        return class_exists($this->className());
    }

    private function hasTable()
    {
        return Schema::hasTable($this->argument('name'));
    }

    private function fileName(): string
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
