<x-guest-layout>
    <form method="POST" action="{{ route('register') }}">
        @csrf

        <fieldset class="fieldset bg-base-200 border-base-300 rounded-box p-6 w-xs">
            <legend class="fieldset-legend text-lg font-semibold">{{ __('Register') }}</legend>

            <!-- Name -->
            <div class="form-control w-full">
                <label class="label" for="name">
                    <span class="label-text">{{ __('Name') }}</span>
                </label>
                <x-text-input id="name" name="name" type="text" :value="old('name')"
                              required autofocus autocomplete="name" />
                <x-input-error :messages="$errors->get('name')" class="mt-1 text-sm text-error" />
            </div>

            <!-- Email Address -->
            <div class="form-control w-full mt-4">
                <label class="label" for="email">
                    <span class="label-text">{{ __('Email') }}</span>
                </label>
                <x-text-input id="email" name="email" type="email" :value="old('email')"
                              required autocomplete="username" />
                <x-input-error :messages="$errors->get('email')" class="mt-1 text-sm text-error" />
            </div>

            <!-- Password -->
            <div class="form-control w-full mt-4">
                <label class="label" for="password">
                    <span class="label-text">{{ __('Password') }}</span>
                </label>
                <x-text-input id="password" name="password" type="password"
                              required autocomplete="new-password" />
                <x-input-error :messages="$errors->get('password')" class="mt-1 text-sm text-error" />
            </div>

            <!-- Confirm Password -->
            <div class="form-control w-full mt-4">
                <label class="label" for="password_confirmation">
                    <span class="label-text">{{ __('Confirm Password') }}</span>
                </label>
                <x-text-input id="password_confirmation" name="password_confirmation" type="password"
                              required autocomplete="new-password" />
                <x-input-error :messages="$errors->get('password_confirmation')" class="mt-1 text-sm text-error" />
            </div>

            <div class="flex items-center justify-end gap-4 mt-6">
                <a href="{{ route('login') }}"
                   class="link link-hover text-sm">
                    {{ __('Already registered?') }}
                </a>

                <button type="submit" class="btn btn-neutral">
                    {{ __('Register') }}
                </button>
            </div>
        </fieldset>
    </form>
</x-guest-layout>
