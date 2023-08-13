<?php

namespace App\Console\Commands\Traits;

use function Laravel\Prompts\select;
use function Laravel\Prompts\text;

trait PromptScalar
{
    public function promptBool(string $question, ?bool $default = null): bool
    {
        return select($question, [
                'Yes',
                'No'
            ], is_bool($default) ? ($default === true ? 'Yes' : 'No') : null) === 'Yes';
    }

    public function promptName(string $question): string
    {
        $name = '';

        while (empty($name)) {
            $name = text($question);
        }

        return $name;
    }

    public function promptQuantity(string $question = 'Quantity', int $default = 1): int
    {
        $quantity = null;

        while ($quantity === null) {
            $quantity = text($question, $default);

            if (!is_int($quantity) && $quantity <= 0) {
                $quantity = null;
            }
        }

        return $quantity;
    }

    public function promptConfiguration(): array
    {
        return [];
    }


    public function promptPosition(string $question = 'Position', int $default = 1): int
    {
        $position = null;

        while ($position === null) {
            $position = text($question, $default);

            if (!is_int($position) && $position < 0) {
                $position = null;
            }
        }

        return $position;
    }

    public function promptAmount(string $question = 'Amount', int $default = 100): int
    {
        $amount = null;

        while ($amount === null) {
            $amount = text($question, $default);

            if (!is_int($amount) && $amount < 0) {
                $amount = null;
            }
        }

        return $amount;
    }
}
