<?php

namespace App\Consequence\Presets;

use App\Consequence\ConsequenceCollection;
use App\Consequence\Credit;
use App\Consequence\Discount;
use App\Consequence\Targets\ProductAll;
use Exception;

class CreditOnAllProducts
{
    public static function name(int $amount): string
    {
        return "$amount credit on all products";
    }

    /**
     * @throws Exception
     */
    public static function make(int $amount): ConsequenceCollection
    {
        return ConsequenceCollection::make()
            ->addConsequence(
                Discount::make(
                    $amount,
                    false,
                    [
                        ProductAll::make()
                    ],
                )
            )
            ->addConsequence(
                // @todo [test]
                Credit::make()
            );
    }
}
