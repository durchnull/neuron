<?php

namespace App\Actions\Engine\Vat;

class VatCreateAction extends VatAction
{

    public static function rules(): array
    {
        return [
            'sales_channel_id' => 'required|uuid|exists:sales_channels,id',
            'vatable_id' => 'required|uuid',
            'vatable_type' => 'required|string',
            'country_code' => 'required|string|size:2', // @todo ISO 3166-1 alpha-2
            'rate' => 'required|integer|min:0|max:10000',
        ];
    }

    protected function apply(): void
    {
        $this->target->fill($this->validated)->save();
    }
}
