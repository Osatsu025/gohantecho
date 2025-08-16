<x-guest-layout>
    <div class="mb-4 text-sm text-base-content opacity-75">
        {{ __('This is a secure area of the application. Please confirm your password before continuing.') }}
    </div>

    <form method="POST" action="{{ route('password.confirm') }}">
        @csrf

        <fieldset class="fieldset bg-base-200 border-base-300 rounded-box p-6 w-full">
            <legend class="fieldset-legend text-lg font-semibold">{{ __('Confirm Password') }}</legend>

            <!-- Password -->
            <div class="form-control w-full">
                <label class="label" for="password">
                    <span class="label-text">{{ __('Password') }}</span>
                </label>
                <x-text-input id="password" type="password" name="password"
                       class="w-full"
                       required autocomplete="current-password" />
                <x-input-error :messages="$errors->get('password')" class="mt-1 text-sm text-error" />
            </div>

            <div class="flex justify-end mt-6">
                <button type="submit" class="btn btn-neutral">
                    {{ __('Confirm') }}
                </button>
            </div>
        </fieldset>
    </form>
</x-guest-layout>
