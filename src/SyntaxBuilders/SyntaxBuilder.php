<?php

namespace FaceDigital\FaceGen\SyntaxBuilders;

abstract class SyntaxBuilder
{
    protected string $template;

    abstract protected function constructSchema(array $schema): string;

    abstract protected function getSchemaWrapper(): string;

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

    protected function into(string $wrapper): string
    {
        return str_replace('{{column}}', $this->template, $wrapper);
    }
}
