<div class="flex text-center">
    <div class="flex-1 flex flex-col items-center justify-center">
        <x-typography.title class="flex items-center my-4">{{ $cartRule->name }}</x-typography.title>
        <span class="text-green-600">@include('svg.check-badge')</span>
        <x-typography.title class="block text-gray-400 my-4">Promotion</x-typography.title>
    </div>
    <div class="flex-1 flex flex-col items-center justify-center p-8 h-40 text-slate-600 bg-slate-50">
        <x-typography.small class="uppercase mb-4">if</x-typography.small>
        <span>{{ $cartRule->rule->condition->name }}</span>
    </div>
    <div class="flex items-center justify-center p-8 h-40 ">@include('svg.arrow-small-right')</div>
    <div class="flex-1 flex flex-col items-center justify-center p-8 h-40 text-slate-600 bg-slate-50">
        <x-typography.small class="uppercase mb-4">then apply</x-typography.small>
        <span>{{ $cartRule->rule->name }}</span>
    </div>
</div>
