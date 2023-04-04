<?php

namespace FaceDigital\FaceGen\Commands\Traits;

use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;

trait TableSchema
{
    public function getTableSchema(string $table): string
    {
        $schema = DB::getDoctrineSchemaManager();
        $columns = $schema->listTableColumns($table);

        $colsWithFields = [];

        foreach ($columns as $column) {
            array_push($colsWithFields, $this->getColumnAttributes($table, $column));
        }

        $colsWithFields = implode(', ', $colsWithFields);

        return $colsWithFields;
    }

    protected function hasTable()
    {
        return Schema::hasTable($this->argument('name'));
    }

    private function getColumnAttributes($table, $column): string
    {
        $doctrineColumn = DB::getDoctrineColumn($table, $column->getName());

        $colType = Schema::getColumnType($table, $column->getName());
        $colDefault = $doctrineColumn->getDefault();

        $columnAtributes = (object) [
            "name"          => Str::lower($column->getName()),
            "type"          => $colType ? ':'.$colType : '',
            "length"        => $doctrineColumn->getLength() ? '('.$doctrineColumn->getLength().')' : '',
            "unsigned"      => $doctrineColumn->getUnsigned() ? ':unsigned' : '',
            "notNull"       => $doctrineColumn->getNotnull() ? '' : ':nullable',
            "default"       => $colDefault ? 'default('.$colDefault.')' : '',
            "autoincrement" => $doctrineColumn->getAutoincrement() ? ':autoIncrement' : '',
        ];

        return $this->mountColumnString($columnAtributes);
    }

    private function mountColumnString($attrs): string
    {
        $colString = $attrs?->name.$attrs?->type.$attrs?->length.$attrs?->unsigned.$attrs?->notNull.$attrs?->default.$attrs?->autoincrement;

        return $colString;
    }
}
