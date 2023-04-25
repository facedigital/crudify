<?php

namespace FaceDigital\Crudify\SyntaxBuilders;

use FaceDigital\Crudify\Commands\Traits\NamingConvention;
use Illuminate\Support\Str;

class ViewIndexSyntaxBuilder extends SyntaxBuilder
{
    use NamingConvention;

    protected function into(string $wrapper): string
    {
        return str_replace(['{{column}}', '{{row}}', '{{colsSize}}'], $this->template, $wrapper);
    }

    protected function getSchemaWrapper(): string
    {
        return file_get_contents(__DIR__ . '/../../stubs/resources/views/bootstrap/index.blade.php.stub');
    }

    protected function constructSchema(array $schema): array
    {
        $fields = array_map(
            fn ($field) => $this->addColumn($field),
            array_filter($schema, fn ($field) => !array_key_exists('on', $field['options']))
        );

        $template['column'] = implode("\n".str_repeat(' ', 16), $fields);

        $fields = array_map(
            fn ($field) => $this->addRow($field),
            array_filter($schema, fn ($field) => !array_key_exists('on', $field['options']))
        );

        $template['row'] = implode("\n".str_repeat(' ', 16), $fields);

        $template['colsSize'] = count($fields) + 1;

        return $template;
    }

    private function addColumn(array $field): string
    {
        return sprintf("<div class='px-4 py-2'>%s</div>", Str::studly($field['name']));
    }

    private function addRow(array $field): string
    {
        return sprintf("<div class='px-4 py-2'>{{ $%s->%s }}</div>", '{{singularName}}', $field['name']);
    }
}
