@switch($value)
    @case(\App\Enums\Product\ProductTypeEnum::Product)
        @include('svg.tag')
        @break
    @case(\App\Enums\Product\ProductTypeEnum::Bundle)
        @include('svg.swatch')
        @break
@endswitch
