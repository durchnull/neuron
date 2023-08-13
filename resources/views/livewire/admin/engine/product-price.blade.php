<x-form.form>
    <x-form.toggle model="enabled" :value="$enabled"/>
    <x-form.price model="netPrice"
                  label="Price (net) in EUR"
    />
    <x-grid-2>
        <x-form.date-time model="beginAt"
                          label="Begin"
        />
        <x-form.date-time model="endAt"
                          label="End"
        />
    </x-grid-2>
</x-form.form>
