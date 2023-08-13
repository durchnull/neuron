<div {{ $attributes->merge(['class' => 'w-full overflow-scroll']) }}>
    <table class="border-collapse table-auto w-full text-sm">{{ $slot }}</table>
</div>
