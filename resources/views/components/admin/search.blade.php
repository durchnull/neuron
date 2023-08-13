<div {{ $attributes->merge(['class' => 'max-w-xs']) }}>
    <form wire:submit="search">
        <x-form.text
            model="query"
            placeholder="Search"
        />
    </form>
</div>
