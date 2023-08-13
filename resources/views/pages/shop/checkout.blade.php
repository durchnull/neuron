@section('head')
    <script src="{{ asset('neuron.js') }}"></script>
@endsection
<x-layouts.shop>
    <div id="checkout"></div>
    <script>
        Neuron('{{ $apiUrl }}', '{{ $apiToken }}', '{{ $shopUrl }}')
    </script>
</x-layouts.shop>
