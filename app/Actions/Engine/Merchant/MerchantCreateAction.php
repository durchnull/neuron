<?php

namespace App\Actions\Engine\Merchant;

use App\Generators\TokenGenerator;
use Exception;

class MerchantCreateAction extends MerchantAction
{
    public static function rules(): array
    {
        return [
            'name' => 'required|string|min:3',
        ];
    }

    /**
     * @throws Exception
     */
    protected function apply(): void
    {
        $this->target->fill(array_merge($this->validated, [
            'token' => TokenGenerator::make()->generate()
        ]))->save();
    }
}
