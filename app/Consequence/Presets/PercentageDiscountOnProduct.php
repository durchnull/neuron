<?php

namespace App\Consequence\Presets;

use App\Consequence\ConsequenceCollection;
use App\Consequence\Discount;
use App\Consequence\Targets\ProductIds;
use Exception;

class PercentageDiscountOnProduct
{
    public static function name(string $percentage = null, string $productName = null): string
    {
        $percentage = $percentage ?? 'Percentage';
        $productName = $productName ?? 'product';

        return "$percentage discount on $productName";
    }

    /**
     * @throws Exception
     */
    public static function make(int $percentage, string $productId): ConsequenceCollection
    {
        return ConsequenceCollection::make()
            ->addConsequence(
                Discount::make(
                    $percentage,
                    true,
                    [
                        ProductIds::make([
                            $productId
                        ])
                    ],
                )
            );
    }
}
