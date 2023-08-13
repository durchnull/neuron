<?php

namespace App\Actions\Engine\SalesChannel;

class SalesChannelDeleteAction extends SalesChannelAction
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
