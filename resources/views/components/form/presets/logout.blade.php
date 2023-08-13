<span {{ $attributes->merge(['class' => 'inline-block']) }}>
    <a href="{{ route('logout') }}"
       class="inline-block w-full h-full"
       onclick="event.preventDefault(); document.getElementById('logout-form').submit();"
    >{{ $slot }}</a>
    <form id="logout-form"
          action="{{ route('logout') }}"
          method="POST"
    >
        @csrf
    </form>
</span>
