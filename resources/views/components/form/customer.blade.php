<div {{ $attributes->merge(['class' => 'grid grid-cols-2 gap-4 my-4']) }}>
    <x-form.text
        model="email"
        label="Email"
    />
    <x-form.text
        model="phone"
        label="Phone"
    />
</div>
