<?php

namespace FaceDigital\Crudify\Commands\Traits;

use Illuminate\Support\Str;

trait NamingConvention
{
    protected function singularName()
    {
        return Str::of($this->argument('name'))->singular();
    }

    protected function pluralName()
    {
        return Str::of($this->argument('name'))->plural();
    }

    protected function tableName()
    {
        return $this->pluralName()->snake();
    }

    protected function modelName()
    {
        return $this->singularName()->studly();
    }

    protected function objectName()
    {
        return $this->singularName()->camel();
    }

    protected function routeName()
    {
        return $this->pluralName()->kebab();
    }

    protected function parameterName()
    {
        return $this->singularName()->snake();
    }
}
