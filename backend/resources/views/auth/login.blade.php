<x-guest-layout>
    <!-- Session Status -->
    <x-auth-session-status class="mb-4" :status="session('status')" />

    <form method="POST" action="{{ route('login') }}">
        @csrf

        <fieldset class="fieldset bg-base-200 border-base-300 rounded-box w-full border p-6">
            <legend class="fieldset-legend text-lg font-semibold">{{ __('Login') }}</legend>

            <!-- Email Address -->
            <div class="form-control w-full">
                <label class="label" for="email">
                    <span class="label-text">{{ __('Email') }}</span>
                </label>
                <x-text-input id="email" type="email" name="email"
                    class="w-full"
                    :value="old('email')"
                    required autofocus autocomplete="username" />
                <x-input-error :messages="$errors->get('email')" class="mt-1 text-sm text-error" />
            </div>

            <!-- Password -->
            <div class="form-control w-full mt-4">
                <label class="label" for="password">
                    <span class="label-text">{{ __('Password') }}</span>
                </label>
                <x-text-input id="password" type="password" name="password"
                    class="w-full"
                    required autocomplete="current-password" />
                <x-input-error :messages="$errors->get('password')" class="mt-1 text-sm text-error" />
            </div>

            <!-- Remember Me -->
            <div class="form-control mt-4">
                <label class="cursor-pointer label" for="remember_me">
                    <input id="remember_me" type="checkbox" class="checkbox" name="remember">
                    <span class="label-text ml-2">{{ __('Remember me') }}</span>
                </label>
            </div>

            <!-- Forgot Password & Submit -->
            <div class="mt-6 flex justify-between items-center">
                @if (Route::has('password.request'))
                    <a class="link text-sm" href="{{ route('password.request') }}">
                        {{ __('Forgot your password?') }}
                    </a>
                @endif

                <button class="btn btn-neutral">
                    {{ __('Log in') }}
                </button>
            </div>
        </fieldset>
    </form>
</x-guest-layout>
