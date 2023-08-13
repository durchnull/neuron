<?php

namespace App\Actions\Engine\Vat;

class VatUpdateAction extends VatAction
{

    public static function rules(): array
    {
        return [
            'vatable_id' => 'nullable|uuid',
            'vatable_type' => 'nullable|string',
            'country_code' => 'nullable|string|size:2', // @todo ISO 3166-1 alpha-2
            'rate' => 'nullable|integer|min:0|max:10000',
        ];
    }

    protected function apply(): void
    {
        $this->target->update($this->validated);
    }
}
