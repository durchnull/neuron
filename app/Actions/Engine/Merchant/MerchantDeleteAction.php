<?php

namespace App\Actions\Engine\Merchant;

class MerchantDeleteAction extends MerchantAction
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
        // @todo delete all saleschannel

        $this->target->delete();
    }
}
