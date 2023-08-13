@section('head')
    <script src="{{ asset('neuron.js') }}"></script>
@endsection
<x-layouts.shop>
    <div id="order"></div>
    <script>
        Neuron('{{ $apiUrl }}', '{{ $apiToken }}', '{{ $shopUrl }}')
    </script>
</x-layouts.shop>
