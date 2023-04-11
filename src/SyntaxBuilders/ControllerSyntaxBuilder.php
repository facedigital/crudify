<?php

namespace FaceDigital\Crudify\SyntaxBuilders;

use Illuminate\Support\Str;

class ControllerSyntaxBuilder extends SyntaxBuilder
{
    protected function into(string $wrapper): string
    {
        return str_replace(['{{column}}'], $this->template, $wrapper);
    }

    protected function getSchemaWrapper(): string
    {
        return file_get_contents(__DIR__ . '/../../stubs/app/Http/Controllers/controller.php.stub');
    }

    protected function constructSchema(array $schema): array
    {
        $fields = array_map(
            fn ($field) => $this->addColumn($field),
            array_filter($schema, fn ($field) => !array_key_exists('on', $field['options']))
        );

        $template['column'] = implode("\n".str_repeat(' ', 12), $fields);
        return $template;
    }

    private function addColumn(array $field): string
    {
        return sprintf("'%s' => '%s',", $field['name'], 'required');
    }
}
