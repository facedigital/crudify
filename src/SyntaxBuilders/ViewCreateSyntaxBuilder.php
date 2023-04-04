<?php

namespace FaceDigital\FaceGen\SyntaxBuilders;

use FaceDigital\FaceGen\Commands\Traits\NamingConvention;
use Illuminate\Support\Str;

class ViewCreateSyntaxBuilder extends SyntaxBuilder
{
    use NamingConvention;

    protected function into(string $wrapper): string
    {
        return str_replace(['{{column}}'], $this->template, $wrapper);
    }

    protected function getSchemaWrapper(): string
    {
        return file_get_contents(__DIR__ . '/../../stubs/resources/views/create.blade.php.stub');
    }

    protected function constructSchema(array $schema): array
    {
        $fields = array_map(
            fn ($field) => $this->addColumn($field),
            array_filter($schema, fn ($field) => !array_key_exists('on', $field['options']))
        );

        $template['column'] = implode("\n".str_repeat(' ', 28), $fields);

        return $template;
    }

    private function addColumn(array $field): string
    {
        return sprintf("%s name='%s' label='%s' />", $this->inputType($field), $field['name'], Str::studly($field['name']));
    }

    private function inputType(array $field): string
    {
        $type = '<x-facegen.input-text';

        if (in_array($field['type'], config('facegen.string_types'))) {
            $type = '<x-facegen.input-text';
        }

        if (in_array($field['type'], config('facegen.integer_types'))) {
            $type = '<x-facegen.input-number';
        }

        if (in_array($field['type'], config('facegen.date_types'))) {
            $type = '<x-facegen.input-date';
        }

        return $type;
    }
}
