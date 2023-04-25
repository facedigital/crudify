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
        return file_get_contents(__DIR__ . '/../../stubs/resources/views/bootstrap/show.blade.php.stub');
    }

    protected function constructSchema(array $schema): array
    {
        $fields = array_map(
            fn ($field) => $this->addColumn($field),
            array_filter($schema, fn ($field) => !array_key_exists('on', $field['options']))
        );

        $template['column'] = implode("\n".str_repeat(' ', 8), $fields);

        return $template;
    }

    private function addColumn(array $field): string
    {
        return sprintf("<x-crudify.info-label label=%s :value=%s->%s />", '"'.Str::studly($field['name']).'"', '"${{singularName}}', $field['name'].'"');
    }
}
