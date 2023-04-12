<?php

namespace FaceDigital\Crudify\Commands;

use FaceDigital\Crudify\Commands\Contracts\Generator;
use FaceDigital\Crudify\Commands\Traits\NamingConvention;
use FaceDigital\Crudify\Commands\Traits\TableSchema;

use Illuminate\Console\Command;
use Illuminate\Filesystem\Filesystem;

abstract class GeneratorCommand extends Command implements Generator
{
    use NamingConvention, TableSchema;

    protected Filesystem $filesystem;

    protected string $existsMessage = 'Already Exists!';

    abstract protected function path(): string;

    abstract protected function alreadyExists(): bool;

    abstract protected function fileName(): string;


    public function __construct(Filesystem $filesystem)
    {
        parent::__construct();
        $this->filesystem = $filesystem;
    }

    public function handle(): void
    {
        $this->line("Creating File: {$this->fileName()}");

        // TODO: Mover para fora
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
            $this->warn($this->existsMessage);
            return;
        }

        $this->generate();

        $this->line("<info>Created File:</info> {$this->fileName()}");
    }

    protected function generate()
    {
        $this->makeDirectory();

        // TODO: Mover para fora
        // if (!$this->option('schema')) {
        //     // ler estrutura da tabela
        //     $schema = $this->getTableSchema($this->argument('name'));
        //     // TODO: gerar model, controller e Views com base no Schema
        //     return $schema;
        // }

        $this->filesystem->put($this->path(), $this->compileStub());
    }

    protected function makeDirectory()
    {
        if (!$this->filesystem->isDirectory(dirname($this->path()))) {
            $this->filesystem->makeDirectory(dirname($this->path()), 0755, true);
        }
    }
}
