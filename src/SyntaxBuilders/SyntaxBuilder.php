<?php

namespace FaceDigital\FaceGen\SyntaxBuilders;

abstract class SyntaxBuilder
{
    protected string $template;

    abstract protected function constructSchema(array $schema): string;

    abstract protected function into(string $wrapper): string;

    abstract protected function getSchemaWrapper(): string;
    // ----

    public function create(array $schema): string
    {
        return $this->createSchema($schema);
    }

    protected function createSchema(array $schema): string
    {
        $fields = $this->constructSchema($schema);

        return $this->insert($fields)->into($this->getSchemaWrapper());
    }

    protected function insert(string $template): SyntaxBuilder
    {
        $this->template = $template;

        return $this;
    }

    // private function into(string $wrapper): string
    // {
    //     return str_replace('{{schema_up}}', $this->template, $wrapper);
    // }

    // private function getSchemaWrapper()
    // {
    //     return file_get_contents(__DIR__ . '/../../stubs/database/migrations/schema-create.stub');
    // }

    // private function constructSchema(array $schema): string
    // {
    //     $fields = array_map(fn ($field) => $this->addColumn($field), $schema);

    //     return implode("\r\n".str_repeat(' ', 12), $fields);
    // }

    // private function addColumn(array $field): string
    // {
    //     $syntax = sprintf("\$table->%s('%s')", $field['type'], $field['name']);

    //     // If there are arguments for the schema type, like decimal('amount', 5, 2)
    //     // then we have to remember to work those in.
    //     if ($field['arguments']) {
    //         $syntax = substr($syntax, 0, -1) . ', ';

    //         $syntax .= implode(', ', $field['arguments']) . ')';
    //     }

    //     foreach ($field['options'] as $method => $value) {
    //         $syntax .= sprintf("->%s(%s)", $method, $value === true ? '' : $value);
    //     }

    //     return $syntax .= ';';
    // }
}
