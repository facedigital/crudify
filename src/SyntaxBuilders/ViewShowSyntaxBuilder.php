<?php

namespace FaceDigital\Crudify\SyntaxBuilders;
use FaceDigital\Crudify\Commands\Traits\NamingConvention;
use Illuminate\Support\Str;

class ViewShowSyntaxBuilder extends SyntaxBuilder
{
    use NamingConvention;

    protected function into(string $wrapper): string
    {
        return str_replace(['{{column}}'], $this->template, $wrapper);
    }

    protected function getSchemaWrapper(): string
    {
        $stub_path = config('crudify.stubs.views.show') ? base_path(config('crudify.stubs.views.show')) : __DIR__.'/../../stubs/resources/views/'.config('crudify.theme').'/show.blade.php.stub';

        return file_get_contents($stub_path);
    }

    protected function constructSchema(array $schema): array
    {
        $fields = array_map(
            fn ($field) => $this->addColumn($field),
            array_filter($schema, fn ($field) => !array_key_exists('on', $field['options']))
        );

        $template['column'] = implode("\n".str_repeat(' ', 20), $fields);

        return $template;
    }

    private function addColumn(array $field): string
    {
        return sprintf("<x-crudify.info-label label=%s :value=%s->%s />", '"'.Str::studly($field['name']).'"', '"${{singularName}}', $field['name'].'"');
    }
}
