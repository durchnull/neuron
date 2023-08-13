<?php

namespace App\Condition\Presets;

use App\Condition\ConditionCollection;
use Exception;

class Unconditional
{
    public static function name(): string
    {
        return 'No condition';
    }

    /**
     * @throws Exception
     */
    public static function make(): ConditionCollection
    {
        return ConditionCollection::make();
    }
}
