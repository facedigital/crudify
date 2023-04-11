<?php

namespace FaceDigital\FaceGen\SyntaxBuilders;

use Illuminate\Support\Str;

class FactorySyntaxBuilder extends SyntaxBuilder
{
    protected function into(string $wrapper): string
    {
        return str_replace(['{{column}}'], $this->template, $wrapper);
    }

    protected function getSchemaWrapper(): string
    {
        return file_get_contents(__DIR__ . '/../../stubs/database/factories/factory.php.stub');
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
        if ('_id' === substr($field['name'], -3)) {
            return sprintf(
                "'%s' => %s::factory(),",
                $field['name'],
                Str::of(str_replace('_id', '', $field['name']))->studly()->singular()
            );
        }

        return sprintf("'%s' => \$this->faker->%s,", $field['name'], $this->fakerType($field));
    }

    private function fakerType(array $field): string
    {
        $type = 'text(';

        if (in_array($field['type'], config('crudify.integer_types'))) {
            $type = 'randomNumber(';
        }

        if (in_array($field['type'], config('crudify.float_types'))) {
            $type = 'randomNumber(';
        }

        if (in_array($field['type'], config('crudify.date_types'))) {
            $type = 'date(';
        }

        if (in_array($field['type'], config('crudify.foreign_types'))) {
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
