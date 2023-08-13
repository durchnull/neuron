@if (\Illuminate\Support\Facades\File::exists(storage_path('/app/public/' . \Illuminate\Support\Str::after($src, 'storage/'))))
    <img src="{{ $src }}"
        {{ $attributes->merge(['class' => 'w-16 h-16']) }}
    >
@else
    <div class="inline-flex items-center justify-center w-16 h-16 rounded text-white"
         style="background:linear-gradient(135deg, #{{ substr(dechex(crc32($src)), 0, 6) }} 0%, #000{{ substr(dechex(crc32($src)), 0, 3) }} 100%)"
    >
        @include('svg.shopping-bag')
    </div>
@endif
