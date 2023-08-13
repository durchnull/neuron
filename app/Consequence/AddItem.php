<?php

namespace App\Consequence;

use Exception;

class AddItem extends Consequence
{
    /**
     * @throws Exception
     */
    public function __construct(
        protected string $reference,
        protected string $productId,
        protected int $quantity,
        protected ?array $configuration = null
    ) {
        if (empty($this->reference)) {
            throw new Exception('Reference can not be empty');
        }

        if (empty($this->productId)) {
            throw new Exception('Product id can not be empty');
        }

        if ($this->quantity < 1) {
            throw new Exception('Quantity must be greater than 0');
        }
    }

    public static function make(
        string $reference,
        string $productId,
        int $quantity,
        array $configuration = null
    ): static
    {
        return new static(
            $reference,
            $productId,
            $quantity,
            $configuration
        );
    }

    public function getReference(): string
    {
        return $this->reference;
    }

    public function getProductId(): string
    {
        return $this->productId;
    }

    public function getQuantity(): int
    {
        return $this->quantity;
    }

    public function getConfiguration(): ?array
    {
        return $this->configuration;
    }

    public function toArray(): mixed
    {
        return [
            self::getType(),
            $this->reference,
            $this->productId,
            $this->quantity,
            $this->configuration,
        ];
    }
}
