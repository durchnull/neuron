<?php

namespace App\Consequence\Targets;

class ItemReference extends Target
{
    public static function make(array $ids): array
    {
        return [
            self::id(),
            array_filter(
                $ids,
                fn($id) => is_string($id) && !empty($id)
            )
        ];
    }
}
