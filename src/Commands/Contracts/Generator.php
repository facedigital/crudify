<?php

namespace FaceDigital\Crudify\Commands\Contracts;

interface Generator
{
    public function handle(): void;
    public function compileStub(): string;
}
