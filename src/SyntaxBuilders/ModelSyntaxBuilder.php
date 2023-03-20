<?php

namespace FaceDigital\FaceGen\SyntaxBuilders;

class ModelSyntaxBuilder extends SyntaxBuilder
{
    protected function getSchemaWrapper(): string
    {
        return file_get_contents(__DIR__ . '/../../stubs/app/Models/model.php.stub');
    }

    protected function constructSchema(array $schema): string
    {
        $fields = array_map(fn ($field) => $this->addColumn($field), $schema);

        return implode("\r\n".str_repeat(' ', 8), $fields);
    }

    private function addColumn(array $field): string
    {
        return sprintf("'%s',", $field['name']);
    }
}
