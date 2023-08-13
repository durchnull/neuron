<?php

namespace App\Actions\Integration\Marketing\Klicktipp;

use App\Actions\Action;
use App\Models\Integration\Marketing\Klicktipp;

abstract class KlicktippAction extends Action
{
    final public static function targetClass(): string
    {
        return Klicktipp::class;
    }
}
