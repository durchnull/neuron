<?php

namespace App\Actions;

use App\Enums\Order\PolicyReasonEnum;
use App\Enums\TriggerEnum;
use App\Exceptions\Order\PolicyException;
use App\Models\Engine\Order;
use Exception;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Validator;
use Illuminate\Validation\ValidationException;

abstract class Action implements Actionable
{
    protected mixed $target;

    protected array $attributes;

    protected TriggerEnum $trigger;

    protected array $validated;

    public bool $applied;

    public bool $silent;

    protected array $policies;

    /**
     * @param  mixed  $target
     * @param  array  $attributes
     * @param  TriggerEnum  $trigger
     * @param  bool  $silent
     * @throws Exception
     */
    public function __construct(mixed $target, array $attributes, TriggerEnum $trigger, bool $silent = false)
    {
        if (get_class($target) !== static::targetClass()) {
            throw new Exception(
                "Target class mismatch " . get_class($target) . " is not" . static::targetClass()
            );
        }

        $this->target = $target;
        $this->attributes = $attributes;
        $this->trigger = $trigger;
        $this->policies = [];
        $this->validated = [];
        $this->applied = false;
        $this->silent = $silent;
    }

    /**
     * @return string
     */
    abstract public static function targetClass(): string;

    /**
     * @return void
     * @throws ValidationException
     * @throws PolicyException
     */
    final public function trigger(): void
    {
        $this->validated = $this->validate($this->attributes);

        Log::channel('engine')->info(
            class_basename(get_called_class()) . ' ' . $this->trigger->value . ' ' . json_encode($this->validated)
        );

        $this->gate($this->attributes);

        if (empty($this->policies)) {
            $this->apply();
            $this->applied = true;

            if (static::targetClass() === Order::class) {
                DB::table('order_events')->insert([
                    'order_id' => $this->target->id,
                    'action' => class_basename(get_called_class()),
                    'data' => json_encode($this->validated),
                    'created_at' => now()
                ]);
            }

            Log::channel('engine')->info('> ' .
                class_basename(get_called_class()) . ' ' .
                $this->trigger->value . ' ' .
                ' [' . optional($this->target())->name . '] ' .
                optional($this->target())->id . ' ' . optional($this->target())->version
            );
        } else {
            $this->applied = false;
            throw new PolicyException($this);
        }
    }

    /**
     * @return mixed
     */
    final public function target(): mixed
    {
        return $this->target;
    }

    final public function validated(): array
    {
        return $this->validated;
    }

    /**
     * @return array
     */
    abstract public static function rules(): array;

    /**
     * @param  array  $attributes
     * @return void
     */
    protected function gate(array $attributes): void
    {
    }

    /**
     * @return void
     */
    abstract protected function apply(): void;

    /**
     * @param  array  $attributes
     * @return array
     * @throws ValidationException
     */
    final protected function validate(array $attributes): array
    {
        $validator = Validator::make($attributes, static::rules());

        if ($validator->fails()) {
            Log::info(class_basename(get_called_class()) . ' ' . json_encode($validator->errors()->toArray()));
            throw new ValidationException($validator);
        }

        return $validator->validated();
    }


    /**
     * @return bool
     */
    final public function denied(): bool
    {
        return $this->applied === false;
    }

    /**
     * @return array|string[]
     */
    final public function policies(): array
    {
        return $this->policies;
    }

    /**
     * @param  PolicyReasonEnum  $policyReason
     * @return void
     */
    final public function addPolicy(PolicyReasonEnum $policyReason): void
    {
        $this->policies[] = $policyReason;
    }

    protected function getChangedAttributes(array $attributes): array
    {
        $changed = [];

        foreach ($attributes as $attribute => $type) {
            $method = "get" . ucfirst($type) . "DifferenceOrNull";
            $changed[$attribute] = $this->{$method}($this->target->{$attribute}, $this->validated[$attribute] ?? null);
        }

        return array_filter($changed);
    }

    protected function getStringDifferenceOrNull(string $value1 = null, string $value2 = null): ?string
    {
        if ($value2 === null) {
            return null;
        }

        return $value1 !== $value2 ? $value2 : null;
    }

    protected function getIntDifferenceOrNull(int $value1 = null, int $value2 = null): ?int
    {
        if ($value2 === null) {
            return null;
        }

        return $value1 !== $value2 ? $value2 : null;
    }

    protected function getBoolDifferenceOrNull(bool $value1 = null, bool $value2 = null): ?bool
    {
        if ($value2 === null) {
            return null;
        }

        return $value1 !== $value2 ? $value2 : null;
    }

    protected function getArrayDifferenceOrNull(array $value1 = null, array $value2 = null): ?array
    {
        if ($value2 === null) {
            return null;
        }

        return json_encode($value1) !== json_encode($value2) ? $value2 : null;
    }
}
