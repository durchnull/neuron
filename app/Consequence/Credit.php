<?php

namespace App\Consequence;

use Exception;

class Credit extends Consequence
{
    /**
     * @throws Exception
     */
    public function __construct()
    {

    }

    /**
     * @throws Exception
     */
    public static function make(): static
    {
        return new static();
    }


    public function toArray(): mixed
    {
        return [
            self::getType(),
        ];
    }
}
