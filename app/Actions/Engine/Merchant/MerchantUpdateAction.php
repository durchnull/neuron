<?php

namespace App\Actions\Engine\Merchant;

class MerchantUpdateAction extends MerchantAction
{
    public static function rules(): array
    {
        return [
            'name' => 'nullable|string|min:3',
        ];
    }

    protected function apply(): void
    {
        $this->target->fill($this->validated)->save();
    }
}
