<?php

namespace App\Casts;

use Exception;
use Illuminate\Contracts\Database\Eloquent\CastsAttributes;
use Illuminate\Database\Eloquent\Model;

class ConsequenceCollection implements CastsAttributes
{
    /**
     * Cast the given value.
     *
     * @param  array<string, mixed>  $attributes
     * @throws Exception
     */
    public function get(Model $model, string $key, mixed $value, array $attributes): mixed
    {
        return \App\Consequence\ConsequenceCollection::fromArray(json_decode($value, true));
    }

    /**
     * Prepare the given value for storage.
     *
     * @param  array<string, mixed>  $attributes
     */
    public function set(Model $model, string $key, mixed $value, array $attributes): mixed
    {
        /** @var \App\Consequence\ConsequenceCollection $value */
        return json_encode($value->toArray());
    }
}
