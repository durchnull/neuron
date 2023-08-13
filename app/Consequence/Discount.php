<?php

namespace App\Consequence;

use Exception;

class Discount extends Consequence
{
    /**
     * @throws Exception
     */
    public function __construct(
        protected int $amount,
        protected bool $percentage,
        protected array $targets
    ) {
        if ($amount < 0) {
            throw new Exception('Discount amount must not be lesser than 0');
        }

        if ($percentage && $amount > 100) {
            throw new Exception('Discount percentage must not be grater than 100');
        }

        if (empty($targets)) {
            throw new Exception('Discount targets can not be empty');
        }
    }

    /**
     * @throws Exception
     */
    public static function make(
        int $amount,
        bool $percentage,
        array $targets,
    ): static {
        return new static(
            $amount,
            $percentage,
            $targets,
        );
    }

    public function getAmount(): int
    {
        return $this->amount;
    }

    public function isPercentage(): bool
    {
        return $this->percentage;
    }

    public function getTargets(): array
    {
        return $this->targets;
    }

    public function toArray(): mixed
    {
        return [
            self::getType(),
            $this->amount,
            $this->percentage,
            $this->targets
        ];
    }
}
