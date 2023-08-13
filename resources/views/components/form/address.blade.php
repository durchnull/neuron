<div {{ $attributes->merge(['class' => '']) }}>
    <x-typography.title class="block my-8">{{ $title }}</x-typography.title>
    <x-form.select
        model="{{ $type }}Salutation"
        label="Salutation"
        :options="array_map(fn(\App\Enums\Address\SalutationEnum $salutation) => [
            'value' => $salutation->value,
            'label' => $salutation->value,
        ],\App\Enums\Address\SalutationEnum::cases())"
    />
    <div class="grid grid-cols-2 gap-1">
        <x-form.text
            model="{{ $type }}FirstName"
            label="First name"
        />
        <x-form.text
            model="{{ $type }}LastName"
            label="Last name"
        />
        <x-form.text
            model="{{ $type }}Street"
            label="Street"
        />
        <x-form.text
            model="{{ $type }}Number"
            label="House number"
        />
        <x-form.text
            model="{{ $type }}Additional"
            label="Additional"
        />
        <x-form.text
            model="{{ $type }}PostalCode"
            label="Postal code"
        />
        <x-form.text
            model="{{ $type }}City"
            label="City"
        />
        <x-form.text
            model="{{ $type }}CountryCode"
            label="Country ({{ \App\Models\Engine\Shipping::pluck('country_code')->unique()->implode(',') }})"
        />
    </div>
</div>
