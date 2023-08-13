<?php

namespace App\Actions\Integration\Inventory\Weclapp;

class WeclappDeleteAction extends WeclappAction
{
    public static function rules(): array
    {
        return [];
    }

    protected function gate(array $attributes): void
    {
    }

    protected function apply(): void
    {
        // @todo move products to other inventory?
        $this->target->delete();
    }
}
