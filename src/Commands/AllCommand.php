<?php
namespace FaceDigital\Crudify\Commands;

use Illuminate\Console\Command;
use FaceDigital\Crudify\Commands\Traits\TableSchema;

class AllCommand extends Command
{
    use TableSchema;

    protected $signature = 'crudify:all
    { name : resource name(singular) }
    {--schema= : Schema options}
    {--except= : O que não quer que não seja gerado (Optional)}
    {--timestamp= : Timestamp ex: 2023_14_16_140039 (Optional)}';

    protected $description = 'Executa todos os comandos crudify';

    public function handle(): void
    {
        $this->line("Start Generating...");

        if (!$this->option('schema')) {
            if (!$this->hasTable()) {
                $this->warn('A tabela '.$this->argument('name').' não existe no banco de dados');
                return;
            }
        }

        $this->generate();

        $this->line("All done!!");
    }

    protected function generate()
    {

        if (!$this->option('schema')) {
            $schemaOption = $this->getTableSchema($this->argument('name'));
        } else {
            $schemaOption = $this->option('schema');
        }
        dd($schemaOption);
        $tableName = $this->argument('name');
        $timestamp = $this->option('timestamp') ? $this->option('timestamp') : null;
        $except = $this->option('except');

       if ((!$except || !in_array('migration', $except)) && $this->option('schema')) {
           $this->callMigration($tableName, $schemaOption, $timestamp);
       }

       if (!$except || !in_array('factory', $except)) {
           $this->callFactory($tableName, $schemaOption);
       }

       if (!$except || !in_array('model', $except)) {
           $this->callModel($tableName, $schemaOption);
       }

       if (!$except || !in_array('controller', $except)) {
            $service = (!$except || !in_array('service', $except)) ? 'y' : null ;
           $this->callController($tableName, $schemaOption, $service);
       }

       if (!$except || !in_array('store-request', $except)) {
           $this->callStoreRequest($tableName, $schemaOption);
       }

       if (!$except || !in_array('update-request', $except)) {
           $this->callUpdateRequest($tableName, $schemaOption);
       }

       if (!$except || !in_array('views', $except)) {
           $this->callViews($tableName, $schemaOption);
       }

        if (!$except || !in_array('service', $except)) {
            $this->callService($tableName, $schemaOption);
        }

        if (!$except || !in_array('repository', $except)) {
            $this->callRepository($tableName, $schemaOption);
        }
    }

    private function callService(string $tableName, string $schemaOption): void
    {
        $this->call("crudify:service", ['name' => $tableName, '--schema' => $schemaOption]);
        $this->line("Service Created!");
    }

    private function callRepository(string $tableName, string $schemaOption): void
    {
        $this->call("crudify:repository", ['name' => $tableName, '--schema' => $schemaOption]);
        $this->line("Repository Created!");
    }

    private function callMigration(string $tableName, string $schemaOption, string|null $timestamp): void
    {
        $this->call("crudify:migration", ['name' => $tableName, '--schema' => $schemaOption, '--timestamp' => $timestamp]);
        $this->line("Migration Created!");
    }

    private function callFactory(string $tableName, string $schemaOption): void
    {
        $this->call("crudify:factory", ['name' => $tableName, '--schema' => $schemaOption]);
        $this->line("Factory Created!");
    }

    private function callModel(string $tableName, string $schemaOption): void
    {
        $this->call("crudify:model", ['name' => $tableName, '--schema' => $schemaOption]);
        $this->line("Model Created!");
    }

    private function callController(string $tableName, string $schemaOption, $service): void
    {
        $opt = ['name' => $tableName, '--schema' => $schemaOption];
        if($service == 'y')
            $opt = ['name' => $tableName, '--schema' => $schemaOption, '--service' => $service];
        $this->call("crudify:controller", $opt);
        $this->line("Controller Created!");
    }

    private function callStoreRequest(string $tableName, string $schemaOption): void
    {
        $this->call("crudify:storereq", ['name' => $tableName, '--schema' => $schemaOption]);
        $this->line("Store Request Created!");
    }

    private function callUpdateRequest(string $tableName, string $schemaOption): void
    {
        $this->call("crudify:updatereq", ['name' => $tableName, '--schema' => $schemaOption]);
        $this->line("Update Request Created!");
    }

    private function callViews(string $tableName, string $schemaOption): void
    {
        $this->call("crudify:index", ['name' => $tableName, '--schema' => $schemaOption]);
        $this->line("View index Created!");

        $this->call("crudify:create", ['name' => $tableName, '--schema' => $schemaOption]);
        $this->line("View create Created!");

        $this->call("crudify:edit", ['name' => $tableName, '--schema' => $schemaOption]);
        $this->line("View edit Created!");

        $this->call("crudify:show", ['name' => $tableName, '--schema' => $schemaOption]);
        $this->line("View show Created!");
    }
}
