<?php

namespace FaceDigital\FaceGen\Commands\Traits;

use Illuminate\Support\Str;

trait NamingConvention
{
    protected function resourceName()
    {
        return Str::of($this->argument('name'))
            ->lower()
            ->singular();
    }

    protected function tableName()
    {
        return Str::of($this->argument('name'))
            ->snake()
            ->plural();
    }

    protected function modelName()
    {
        return $this
            ->resourceName()
            ->studly();
    }

    protected function controllerName()
    {
        return sprintf('%sController', $this->modelName());
    }

    protected function routeName()
    {
        return $this
            ->tableName()
            ->kebab();
    }

    protected function paramName()
    {
        return $this
            ->modelName()
            ->kebab();
    }
}
