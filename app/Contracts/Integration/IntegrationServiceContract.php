<?php

namespace App\Contracts\Integration;

interface IntegrationServiceContract
{
    public static function getClientVersion(): string;

    public function test(): bool;
}
