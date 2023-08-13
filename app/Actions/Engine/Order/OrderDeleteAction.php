<?php

namespace App\Actions\Engine\Order;

class OrderDeleteAction extends OrderAction
{
    public static function rules(): array
    {
        return [];
    }

    public static function afterState(): array
    {
        return [];
    }

    protected function apply(): void
    {
        // @todo

        $this->target->delete();
    }
}
