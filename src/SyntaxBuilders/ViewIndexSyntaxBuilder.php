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
        $stub_path = config('crudify.stubs.views.index') ? base_path(config('crudify.stubs.views.index')) : __DIR__.'/../../stubs/resources/views/'.config('crudify.theme').'/index.blade.php.stub';

        return file_get_contents($stub_path);
    }

    protected function constructSchema(array $schema): array
    {
        $fields = array_map(
            fn ($field) => $this->addColumn($field),
            array_filter($schema, fn ($field) => !array_key_exists('on', $field['options']))
        );

        $template['column'] = implode("\n".str_repeat(' ', 32), $fields);

        $fields = array_map(
            fn ($field) => $this->addRow($field),
            array_filter($schema, fn ($field) => !array_key_exists('on', $field['options']))
        );

        $template['row'] = implode("\n".str_repeat(' ', 32), $fields);

        $template['colsSize'] = count($fields) + 1;

        return $template;
    }

    private function addColumn(array $field): string
    {
        if(config('crudify.theme') == 'bootstrap') {
            return sprintf("<th scope='col'>%s</th>", Str::studly($field['name']));
        }

        if(config('crudify.theme') == 'tailwind') {
            return sprintf("<div class='px-4 py-2'>%s</div>", Str::studly($field['name']));
        }
    }

    private function addRow(array $field): string
    {
        if(config('crudify.theme') == 'bootstrap') {
            return sprintf("<td>{{ $%s->%s }}</td>", '{{singularName}}', $field['name']);
        }

        if(config('crudify.theme') == 'tailwind') {
            return sprintf("<div class='px-4 py-2'>{{ $%s->%s }}</div>", '{{singularName}}', $field['name']);
        }
    }
}
