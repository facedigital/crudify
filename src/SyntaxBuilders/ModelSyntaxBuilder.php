<?php

namespace FaceDigital\FaceGen\SyntaxBuilders;

use Illuminate\Support\Str;

class ModelSyntaxBuilder extends SyntaxBuilder
{
    protected function into(string $wrapper): string
    {
        return str_replace(['{{column}}', '{{foreign}}'], $this->template, $wrapper);
    }

    protected function getSchemaWrapper(): string
    {
        return file_get_contents(__DIR__ . '/../../stubs/app/Models/model.php.stub');
    }

    protected function getForeignSchemaWrapper(): string
    {
        return file_get_contents(__DIR__ . '/../../stubs/app/Models/foreign.stub');
    }

    protected function constructSchema(array $schema): array
    {
        $fields = array_map(fn ($field) => $this->addColumn($field), $schema);
        $template['column'] = implode("\n".str_repeat(' ', 8), $fields);

        $fields = array_map(
            fn ($field) => $this->addForeign($field),
            array_filter($schema, fn ($field) => $field['foreign'])
        );
        $template['foreign'] = implode("\n", $fields);

        return $template;
    }

    private function addColumn(array $field): string
    {
        return sprintf("'%s',", $field['name']);
    }

    private function addForeign(array $field): string
    {
        return str_replace(
            ['{{foreignClass}}', '{{foreignModel}}'],
            [Str::camel($field['foreign']), $field['foreign']],
            $this->getForeignSchemaWrapper()
        );
    }
}
