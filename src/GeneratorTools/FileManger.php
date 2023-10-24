<?php

namespace FaceDigital\Crudify\GeneratorTools;

use Illuminate\Support\Str;

trait FileManger
{
    protected $basePath = 'FaceDigital\Crudify\Traits';
    protected function getTemplate(string $type, bool $checkOptions = false, array $tags = [
                'soft-delete',
                'request',
                'resource',
                'filter',
                'module',
                'service-repository',
                'not-service',
            ]): string
    {
        $patterns = [
            '/Dummy/',
            '/Dummies/',
            '/dummy/',
            '/dummies/',
            '/BaseServiceDM/',
        ];
        $basePathService = $this->basePath;
        if (!is_null(config('crudify.namespace_bases')) && !empty(trim(config('crudify.namespace_bases')))) 
        {
            $basePathService = config('crudify.namespace_bases');
        }
        $replacements = [
            $this->argument('name'),
            Str::plural($this->argument('name')),
            lcfirst($this->argument('name')),
            lcfirst(Str::plural($this->argument('name'))),
            $basePathService,
        ];

        $output = preg_replace(
            $patterns,
            $replacements,
            $this->getStubs($type)
        );
        if($checkOptions){
            return $this->removeTags($output, $tags);   
        }
        return $output;
    }

    private function getStubs(string $type): string
    {
//        return file_get_contents(__DIR__ . '/../Stubs/' . $type . ".stub");
        return file_get_contents(__DIR__ . '/../../stubs/'.$type.'.stub');
    }

    private function removeTags(string $string, array $options): string
    {
        $result = $string;

        foreach ($options as $option) {
            $result = $this->removeTag(
                $result,
                $this->option($option),
                $option
            );
        }

        return $result;
    }

    private function removeTag(string $string, $condition, string $tag): string
    {
        $pattern = $condition
            ? "/@if\(\'$tag\'\)|@endif\(\'$tag\'\)/"
            : "/@if\(\'$tag\'\)((?>[^@]++))*@endif\(\'$tag\'\)/";
        dd($pattern, $string, preg_replace($pattern, '', $string));
        return preg_replace($pattern, '', $string);
    }
}
