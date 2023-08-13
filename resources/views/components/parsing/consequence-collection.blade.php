@if ($consequenceCollection instanceof \App\Consequence\ConsequenceCollection)
    <span>{{ json_encode($consequenceCollection->toArray()) }}</span>
@endif
