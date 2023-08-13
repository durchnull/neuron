<?php

namespace App\Consequence\Presets;

use App\Consequence\AddItem;
use App\Consequence\ConsequenceCollection;
use App\Consequence\Discount;
use App\Consequence\Targets\ItemReference;
use Exception;
use Illuminate\Support\Str;

class AddFreeItem
{
    public static function name(
        string $productName = null,
        int $quantity = null,
        ?array $configuration = null
    ): string {
        $productName = $productName ?? 'product';
        $quantity = $quantity ?? '1';
        $configuration = $configuration ? ' ' . json_encode($configuration) : '';

        return "$quantity free $productName" . $configuration;
    }

    /**
     * @throws Exception
     */
    public static function make(
        string $productId,
        int $quantity,
        array $configuration = null
    ): ConsequenceCollection {
        $reference = Str::uuid()->toString();

        return ConsequenceCollection::make()
            // @todo [test] AddItem, then Discount; Discount, then AddItem
            ->addConsequence(
                AddItem::make(
                    $reference,
                    $productId,
                    $quantity,
                    $configuration
                )
            )
            ->addConsequence(
                Discount::make(
                    100,
                    true,
                    [
                        ItemReference::make([
                            $reference
                        ])
                    ],
                )
            );
    }
}
