@if ($value->isNotEmpty())
    <div>
        @foreach($value as $transaction)
            <div class="my-4">
                <x-admin.attributes.status :value="$transaction->status"/>
            </div>
        @endforeach
    </div>
@endif
