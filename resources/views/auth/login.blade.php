<style>
    body {
        background-image: url('https://images.unsplash.com/photo-1669255265913-3c8482c76655?q=80&w=1931&auto=format&fit=crop&ixlib=rb-4.1.0&ixid=M3wxMjA3fDB8MHxwaG90by1wYWdlfHx8fGVufDB8fHx8fA%3D%3D');
        background-size: cover;
        background-position: center;
        background-repeat: no-repeat;
        font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
        min-height: 100vh; /* Asegura que el body ocupe toda la pantalla */
        margin: 0; /* Quita márgenes por defecto */
    }

    .login-card {
        background: rgba(0, 0, 0, 0.7); /* Fondo semi-transparente */
        backdrop-filter: blur(8px);
        border-radius: 16px;
        padding: 2rem;
        box-shadow: 0 0 25px rgba(0, 0, 0, 0.6);
        color: #fff;
        max-width: 400px;
        width: 100%;
        margin: auto;
        margin-top: 200px;
    }

    .login-card label {
        color: #f0f0f0;
        font-weight: 500;
    }

    .login-card input {
        background-color: #1e1e1e;
        color: white;
        border: 1px solid #444;
    }

    .login-card input:focus {
        border-color: #ffffff;
        outline: none;
        box-shadow: 0 0 0 3px rgba(255, 255, 255, 0.3);
    }

    .login-btn {
        background-color: #ffffff;
        border: none;
        color: rgb(0, 0, 0);
        padding: 0.5rem 1.5rem;
        border-radius: 8px;
        font-weight: bold;
        transition: background 0.3s;
    }

    .login-btn:hover {
        background-color: #d80e62;
    }

    .login-link {
        color: #ccc;
    }

    .login-link:hover {
        color: #fff;
        text-decoration: underline;
    }
</style>

<x-guest-layout>
    <div class="login-card">
        <x-slot name="logo">
            <div class="flex justify-center mb-6">
                <x-authentication-card-logo />
            </div>
        </x-slot>

        <x-validation-errors class="mb-4" />

        @session('status')
            <div class="mb-4 font-medium text-sm text-green-400">
                {{ $value }}
            </div>
        @endsession

        <form method="POST" action="{{ route('login') }}">
            @csrf

            <div class="mb-4">
                <x-label for="email" value="{{ __('Email') }}" />
                <x-input id="email" class="block mt-1 w-full" type="email" name="email" :value="old('email')" required autofocus autocomplete="username" />
            </div>

            <div class="mb-4">
                <x-label for="password" value="{{ __('Password') }}" />
                <x-input id="password" class="block mt-1 w-full" type="password" name="password" required autocomplete="current-password" />
            </div>

            <div class="block mb-4">
                <label for="remember_me" class="flex items-center">
                    <x-checkbox id="remember_me" name="remember" />
                    <span class="ml-2 text-sm text-gray-300">{{ __('Remember me') }}</span>
                </label>
            </div>

            <div class="flex items-center justify-between">
                @if (Route::has('password.request'))
                    <a class="text-sm login-link" href="{{ route('password.request') }}">
                        {{ __('Forgot your password?') }}
                    </a>
                @endif

                <button class="login-btn" type="submit">
                    {{ __('Log in') }}
                </button>
            </div>
        </form>
    </div>
</x-guest-layout>
