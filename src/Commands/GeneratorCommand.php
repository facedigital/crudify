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

        if (!$this->option('schema')) {
            if (!$this->hasTable()) {
                $this->warn('A tabela '.$this->argument('name').' não existe no banco de dados');
                return;
            }
        }

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

        $this->filesystem->put($this->path(), $this->compileStub());
    }

    protected function makeDirectory()
    {
        if (!$this->filesystem->isDirectory(dirname($this->path()))) {
            $this->filesystem->makeDirectory(dirname($this->path()), 0755, true);
        }
    }
}
