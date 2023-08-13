<?php

namespace App\Actions\Engine\Address;

class AddressDeleteAction extends AddressAction
{
    public static function rules(): array
    {
        return [];
    }

    protected function gate(array $attributes): void
    {
        // @todo doesnt belong to any order
    }

    protected function apply(): void
    {
        $this->target->delete();
    }
}
