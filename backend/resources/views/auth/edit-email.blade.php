<x-guest-layout>
    <div class="mb-4 text-sm text-gray-600">
        {{ __('Correct your email please.') }}
    </div>

    <form method="POST" action="{{ route('email.update') }}">
      @csrf
      @method('patch')
      <div class="mt-4">
          <x-input-label for="email" :value="__('Email')" />
          <x-text-input id="email" class="block mt-1 w-full" type="email" name="email" :value="old('email', $user_email)" required autocomplete="email" />
          <x-input-error :messages="$errors->get('email')" class="mt-2" />
      </div>

      <div class="mt-4 flex items-center justify-between">
        <x-primary-button>
          {{ __('Resend Verification Email to this email') }}
        </x-primary-button>
      </div>
    </form>
    
    <form method="POST" action="{{ route('logout') }}">
        @csrf
        <button type="submit" class="underline text-sm text-gray-600 hover:text-gray-900 rounded-md focus:outline-hidden focus:ring-2 focus:ring-offset-2 focus:ring-indigo-500">
            {{ __('Log Out') }}
        </button>
    </form>
</x-guest-layout>
