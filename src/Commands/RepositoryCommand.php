<?php
namespace FaceDigital\Crudify\Commands;

use FaceDigital\Crudify\GeneratorTools\FileManger;

class RepositoryCommand extends GeneratorCommand
{
    use FileManger;

    protected $signature = 'crudify:repository
    { name : resource name(singular) }
    {--schema= : Schema options}';

    protected $description = 'Cria um novo Repository e aplica o schema';

    protected string $existsMessage = "Já existe um Repository com o mesmo nome.";

    protected string $existsMessageInterfaces = "Já existe uma Interface com o mesmo nome.";

    public function compileStub(): string
    {
        if ($this->alreadyExistsInterfaces()) {
            $this->warn($this->existsMessageInterfaces);
            return '';
        }

        if (!$this->filesystem->isDirectory(dirname($this->pathInterfaces()))) {
            $this->filesystem->makeDirectory(dirname($this->pathInterfaces()), 0755, true);
        }

        $outputInterface = $this->getTemplate('app/Interfaces/DummyRepositoryInterface');

        $this->filesystem->put($this->pathInterfaces(), $outputInterface);

        $output = $this->getTemplate('app/Repositories/DummyRepository');

        $this->publishRepositoryServiceProvider();

        return $output;
    }

    private function publishRepositoryServiceProvider(): void
    {
        $this->filesystem->replaceInFile(
            '//Register-Use-Bind//',
            "//Register-Use-Bind//\n".$this->getTemplate('bind-use-repository'),
            app_path("Providers/RepositoryServiceProvider.php"));
        $this->filesystem->replaceInFile(
            '//Register-Bind//',
            "//Register-Bind//\n".$this->getTemplate('bind-repository'),
            app_path("Providers/RepositoryServiceProvider.php"));
    }


    protected function path(): string
    {
        $filename = $this->fileName();

        return app_path("Repositories/{$filename}");
    }

    protected function pathInterfaces(): string
    {
        $filename = $this->fileNameInterface();

        return app_path("Interfaces/{$filename}");
    }

    protected function alreadyExists(): bool
    {
        return $this->filesystem->exists(app_path("Repositories/{$this->fileName()}"));
    }

    protected function alreadyExistsInterfaces(): bool
    {
        return $this->filesystem->exists(app_path("Interfaces/{$this->fileNameInterface()}"));
    }

    protected function fileName(): string
    {
        return sprintf('%s.php', $this->argument('name').'Repository');
    }

    protected function fileNameInterface(): string
    {
        return sprintf('%s.php', $this->argument('name').'RepositoryInterface');
    }

}
