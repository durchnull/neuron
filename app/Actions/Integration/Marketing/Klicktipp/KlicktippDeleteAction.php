<?php

namespace App\Actions\Integration\Marketing\Klicktipp;

class KlicktippDeleteAction extends KlicktippAction
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
