<x-guest-layout>
    <form method="POST" action="{{ route('password.store') }}">
        @csrf

        <!-- Password Reset Token -->
        <input type="hidden" name="token" value="{{ $request->route('token') }}">

        <fieldset class="fieldset bg-base-200 border-base-300 rounded-box p-6 w-xs">
            <legend class="fieldset-legend text-lg font-semibold">{{ __('Reset Password') }}</legend>

            <!-- Email Address -->
            <div class="form-control w-full">
                <label class="label" for="email">
                    <span class="label-text">{{ __('Email') }}</span>
                </label>
                <x-text-input id="email" name="email" type="email" :value="old('email', $request->email)"
                              required autofocus autocomplete="username" />
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

            <div class="flex items-center justify-end mt-6">
                <button type="submit" class="btn btn-neutral">
                    {{ __('Reset Password') }}
                </button>
            </div>
        </fieldset>
    </form>
</x-guest-layout>
