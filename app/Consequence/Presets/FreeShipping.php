<?php

namespace App\Consequence\Presets;

use App\Consequence\ConsequenceCollection;
use App\Consequence\Discount;
use App\Consequence\Targets\Shipping;
use Exception;

class FreeShipping
{
    public static function name(): string
    {
        return "Free shipping";
    }

    /**
     * @throws Exception
     */
    public static function make(): ConsequenceCollection
    {
        return ConsequenceCollection::make()
            ->addConsequence(
                Discount::make(
                    100,
                    true,
                    [
                        Shipping::make()
                    ],
                )
            );
    }
}
