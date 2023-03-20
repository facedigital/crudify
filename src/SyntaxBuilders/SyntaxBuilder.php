<?php

namespace FaceDigital\FaceGen\SyntaxBuilders;

abstract class SyntaxBuilder
{
    protected array $template;

    abstract protected function constructSchema(array $schema): array;

    abstract protected function into(string $wrapper): string;

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

    protected function insert(array $template): SyntaxBuilder
    {
        $this->template = $template;

        return $this;
    }
}
