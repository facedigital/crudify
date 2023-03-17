<?php

namespace FaceDigital\FaceGen\Commands\Contracts;

interface Generator
{
    public function handle(): void;
    public function compileStub(): string;
}
