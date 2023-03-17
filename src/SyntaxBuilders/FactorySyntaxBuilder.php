<?php

namespace FaceDigital\FaceGen\SyntaxBuilders;

class FactorySyntaxBuilder
{
    protected string $template;

    public function create(array $schema): string
    {
        return $this->createSchema($schema);
    }

    private function createSchema(array $schema): string
    {
        $fields = $this->constructSchema($schema);

        return $this->insert($fields)->into($this->getSchemaWrapper());
    }

    private function insert(string $template): FactorySyntaxBuilder
    {
        $this->template = $template;

        return $this;
    }

    private function into(string $wrapper): string
    {
        return str_replace('{{column}}', $this->template, $wrapper);
    }

    private function getSchemaWrapper()
    {
        return file_get_contents(__DIR__ . '/../../stubs/database/factories/factory.php.stub');
    }

    private function constructSchema(array $schema): string
    {
        $fields = array_map(fn ($field) => $this->addColumn($field), $schema);

        return implode("\r\n".str_repeat(' ', 12), $fields);
    }

    private function addColumn(array $field): string
    {
        return sprintf("'%s' => \$this->faker->%s,", $field['name'], $this->fakerType($field));
    }

    private function fakerType(array $field): string
    {
        $type = 'text(';

        if (in_array($field['type'], config('facegen.integer_types'))) {
            $type = 'randomNumber(';
        }

        if (in_array($field['type'], config('facegen.float_types'))) {
            $type = 'randomNumber(';
        }

        if (in_array($field['type'], config('facegen.date_types'))) {
            $type = 'date(';
        }

        if (in_array($field['type'], config('facegen.foreign_types'))) {
            $type = 'randomNumber(';
        }

        if ($field['type'] === 'boolean') {
            $type = 'boolean(';
        }

        if ($field['arguments']) {
            $type .= $field['arguments'][0];
        }

        return $type.')';
    }
}
