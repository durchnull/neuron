<?php

namespace App\Actions;

use App\Enums\Order\PolicyReasonEnum;
use App\Exceptions\Order\PolicyException;
use Illuminate\Validation\ValidationException;

interface Actionable
{

    /**
     * @return void
     * @throws PolicyException
     * @throws ValidationException
     */
    public function trigger(): void;

    /**
     * @return mixed
     */
    public function target(): mixed;

    /**
     * @return bool
     */
    public function denied(): bool;

    /**
     * @return PolicyReasonEnum[]
     */
    public function policies(): array;
}
