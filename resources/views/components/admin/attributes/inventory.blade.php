@if (is_a($value, \App\Models\Integration\Inventory\Inventory::class))
    <span class="whitespace-nowrap">
        <span class="flex items-center mb-1">{{ $value->name }}</span>
        <span class="inline-flex items-center">
            <x-admin.attributes.status :value="$value->enabled"/>
            <x-admin.attributes.status :value="$value->distribute_order"/>
            <x-admin.attributes.status :value="$value->receive_inventory"/>
        </span>
    </span>
@elseif(is_string($value) && class_exists($value))
    <span>{{ class_basename($value) }}</span>
@endif

