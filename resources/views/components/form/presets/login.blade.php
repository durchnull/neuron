<form method="POST"
      action="{{ route('login') }}"
>
    @csrf
    <x-form.text model="email"
                 label="Email"

    />
    <x-form.password model="password"
                     label="Password"
    />
    <div class="flex items-center justify-between">
        <x-form.checkbox model="remember"
                         label="Remember"
        />
        <a href="{{ route('password.request') }}" class="ml-8">Forgot your password?</a>
    </div>

    <x-buttons.button-submit class="w-full">Login</x-buttons.button-submit>
</form>
