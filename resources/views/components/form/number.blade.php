<x-form.input
    type="number"
    :model="$model"
    :label="$label ?? null"
    :placeholder="$placeholder ?? null"
    {{-- @todo min max step --}}
/>
