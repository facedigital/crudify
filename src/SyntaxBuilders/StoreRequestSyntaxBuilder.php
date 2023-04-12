<?php

namespace FaceDigital\Crudify\SyntaxBuilders;

class StoreRequestSyntaxBuilder extends SyntaxBuilder
{
    protected function into(string $wrapper): string
    {
        return str_replace(['{{column}}'], $this->template, $wrapper);
    }

    protected function getSchemaWrapper(): string
    {
        return file_get_contents(__DIR__ . '/../../stubs/app/Http/Requests/store-request.php.stub');
    }

    protected function constructSchema(array $schema): array
    {
        $fields = array_map(fn ($field) => $this->addColumn($field), $schema);
        $template['column'] = implode("\n".str_repeat(' ', 8), $fields);

        return $template;
    }

    private function addColumn(array $field): string
    {
        return sprintf("'%s' => '%s',", $field['name'], 'required');
    }
}
