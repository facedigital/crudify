<?php

namespace FaceDigital\Crudify\SyntaxBuilders;
use FaceDigital\Crudify\Commands\Traits\NamingConvention;
use Illuminate\Support\Str;

class ViewEditSyntaxBuilder extends SyntaxBuilder
{
    use NamingConvention;

    protected function into(string $wrapper): string
    {
        return str_replace(['{{column}}'], $this->template, $wrapper);
    }

    protected function getSchemaWrapper(): string
    {
        $stub_path = config('crudify.stubs.views.edit') ? base_path(config('crudify.stubs.views.edit')) : __DIR__.'/../../stubs/resources/views/'.config('crudify.theme').'/edit.blade.php.stub';

        return file_get_contents($stub_path);
    }

    protected function constructSchema(array $schema): array
    {
        $fields = array_map(
            fn ($field) => $this->addColumn($field),
            array_filter($schema, fn ($field) => !array_key_exists('on', $field['options']))
        );

        $template['column'] = implode("\n".str_repeat(' ', 24), $fields);

        return $template;
    }

    private function addColumn(array $field): string
    {
        return sprintf("%s->%s name='%s' label='%s' />", $this->inputType($field), $field['name'].'"', $field['name'], Str::studly($field['name']));
    }

    private function inputType(array $field): string
    {
        $type = '<x-crudify.input-text';

        if (in_array($field['type'], config('crudify.string_types'))) {
            $type = '<x-crudify.input-text';
        }

        if (in_array($field['type'], config('crudify.integer_types'))) {
            $type = '<x-crudify.input-number';
        }

        if (in_array($field['type'], config('crudify.date_types'))) {
            $type = '<x-crudify.input-date';
        }

        return $type.' :value="${{singularName}}';
    }
}
