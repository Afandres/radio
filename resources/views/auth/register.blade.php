<style>
    body {
        background-image: url('https://images.unsplash.com/photo-1636294155433-29863ca33aa6?q=80&w=2070&auto=format&fit=crop&ixlib=rb-4.1.0&ixid=M3wxMjA3fDB8MHxwaG90by1wYWdlfHx8fGVufDB8fHx8fA%3D%3D');
        background-size: cover;
        background-position: center;
        background-repeat: no-repeat;
        font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
        min-height: 100vh;
        margin: 0;
    }

    .auth-card {
        background: rgba(0, 0, 0, 0.7);
        backdrop-filter: blur(8px);
        border-radius: 16px;
        padding: 2rem;
        box-shadow: 0 0 25px rgba(0, 0, 0, 0.6);
        color: #fff;
        max-width: 400px;
        width: 100%;
        margin: auto;
        margin-top: 150px;
    }

    .auth-card label,
    .auth-card input {
        color: #f0f0f0;
    }

    .auth-card input {
        background-color: #1e1e1e;
        border: 1px solid #444;
    }

    .auth-card input:focus {
        border-color: #ffffff;
        outline: none;
        box-shadow: 0 0 0 3px rgba(255, 255, 255, 0.3);
    }

    .auth-btn {
        background-color: #ffffff;
        border: none;
        color: rgb(0, 0, 0);
        padding: 0.5rem 1.5rem;
        border-radius: 8px;
        font-weight: bold;
        transition: background 0.3s;
    }

    .auth-btn:hover {
        background-color: #d80e62;
    }

    .auth-link {
        color: #ccc;
    }

    .auth-link:hover {
        color: #fff;
        text-decoration: underline;
    }
</style>

<x-guest-layout>
    <div class="auth-card">
        <x-slot name="logo">
            <x-authentication-card-logo />
        </x-slot>

        <x-validation-errors class="mb-4" />

        <form method="POST" action="{{ route('register') }}">
            @csrf

            <div class="mb-4">
                <x-label for="name" value="{{ __('Name') }}" />
                <x-input id="name" class="block mt-1 w-full" type="text" name="name" :value="old('name')" required autofocus autocomplete="name" />
            </div>

            <div class="mb-4">
                <x-label for="email" value="{{ __('Email') }}" />
                <x-input id="email" class="block mt-1 w-full" type="email" name="email" :value="old('email')" required autocomplete="username" />
            </div>

            <div class="mb-4">
                <x-label for="password" value="{{ __('Password') }}" />
                <x-input id="password" class="block mt-1 w-full" type="password" name="password" required autocomplete="new-password" />
            </div>

            <div class="mb-4">
                <x-label for="password_confirmation" value="{{ __('Confirm Password') }}" />
                <x-input id="password_confirmation" class="block mt-1 w-full" type="password" name="password_confirmation" required autocomplete="new-password" />
            </div>

            @if (Laravel\Jetstream\Jetstream::hasTermsAndPrivacyPolicyFeature())
                <div class="mb-4">
                    <x-label for="terms">
                        <div class="flex items-center">
                            <x-checkbox name="terms" id="terms" required />

                            <div class="ms-2">
                                {!! __('I agree to the :terms_of_service and :privacy_policy', [
                                        'terms_of_service' => '<a target="_blank" href="'.route('terms.show').'" class="underline text-sm text-gray-600 hover:text-gray-900 rounded-md focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-indigo-500">'.__('Terms of Service').'</a>',
                                        'privacy_policy' => '<a target="_blank" href="'.route('policy.show').'" class="underline text-sm text-gray-600 hover:text-gray-900 rounded-md focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-indigo-500">'.__('Privacy Policy').'</a>',
                                ]) !!}
                            </div>
                        </div>
                    </x-label>
                </div>
            @endif

            <div class="flex items-center justify-end mt-4">
                <a class="underline text-sm text- hover:text-gray-900 rounded-md focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-indigo-500" href="{{ route('login') }}">
                    {{ __('Already registered?') }}
                </a>

                <button class="auth-btn ms-4">
                    {{ __('Register') }}
                </button>
            </div>
        </form>
    </div>
</x-guest-layout>
