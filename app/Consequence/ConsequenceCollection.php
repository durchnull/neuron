<?php

namespace App\Consequence;

use Illuminate\Contracts\Support\Arrayable;
use Illuminate\Support\Str;

class ConsequenceCollection implements Arrayable
{
    protected array $consequences;

    public function __construct()
    {
        $this->consequences = [];
    }

    public static function make(): ConsequenceCollection
    {
        return new static();
    }

    public function addConsequence(Consequence $consequence): ConsequenceCollection
    {
        $this->consequences[] = $consequence;

        return $this;
    }

    public function getConsequences(): array
    {
        return $this->consequences;
    }

    public static function fromArray(array $data): ConsequenceCollection
    {
        $consequenceCollection = ConsequenceCollection::make();

        foreach ($data as $_data) {
            /** @var Discount|AddItem $class */
            $class = '\\App\\Consequence\\' . Str::studly($_data[0]);
            unset($_data[0]);

            $consequenceCollection->addConsequence(
                $class::make(...array_values($_data))
            );
        }

        return $consequenceCollection;
    }

    public function toArray(): mixed
    {
        return array_map(
            fn($item) => $item->toArray(),
            $this->consequences
        );
    }
}
