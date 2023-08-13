<?php

namespace App\Actions\Engine\Stock;

// @todo
class StockDeleteAction extends StockAction
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
        $this->target->delete();
    }
}
