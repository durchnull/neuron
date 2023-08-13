<?php

namespace App\Consequence\Presets;

use App\Consequence\ConsequenceCollection;
use App\Consequence\Discount;
use App\Consequence\Targets\ProductAll;
use Exception;

class PercentageDiscountOnAllProducts
{
    public static function name(string $percentage = null): string
    {
        $percentage = $percentage ?? 'Percentage';

        return "$percentage discount on all products";
    }

    /**
     * @throws Exception
     */
    public static function make(int $percentage): ConsequenceCollection
    {
        return ConsequenceCollection::make()
                ->addConsequence(
                    Discount::make(
                        $percentage,
                        true,
                        [
                            ProductAll::make()
                        ],
                    )
                );
    }
}
