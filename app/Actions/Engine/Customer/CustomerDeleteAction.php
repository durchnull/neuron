<?php

namespace App\Actions\Engine\Customer;

class CustomerDeleteAction extends CustomerAction
{
    public static function rules(): array
    {
        return [];
    }

    protected function gate(array $attributes): void
    {
        // @todo doesnt belong to any orders

        // addresses
    }

    protected function apply(): void
    {
        $this->target->delete();
    }
}
