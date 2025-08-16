<x-guest-layout>
    <div class="mb-4 text-sm text-base-content opacity-75">
        {{ __('Thanks for signing up! Before getting started, could you verify your email address by clicking on the link we just emailed to you? If you didn\'t receive the email, we will gladly send you another.') }}
    </div>

    @if (session('status') == 'verification-link-sent')
        <div class="mb-4 font-medium text-sm text-success">
            {{ __('A new verification link has been sent to the email address you provided during registration.') }}
        </div>
    @endif

    @if (session('status') == 'email-updated')
        <div class="mb-4 font-medium text-sm text-green-600">
            {{ __('Your email is updated and a new verification link has been sent to the email address.') }}
        </div>
    @endif

    <div class="mt-4 flex items-center justify-between space-x-4">
        <form method="POST" action="{{ route('verification.send') }}">
            @csrf
            <button type="submit" class="btn btn-neutral">
                {{ __('Resend Verification Email') }}
            </button>
        </form>

        <a href="{{ route('email.edit')}}">
            <button type="submit" class="underline text-sm text-gray-600 hover:text-gray-900 rounded-md focus:outline-hidden focus:ring-2 focus:ring-offset-2 focus:ring-indigo-500 mr-3">
                {{ __('Edit mail') }}
            </button>
        </a>


        <form method="POST" action="{{ route('logout') }}">
            @csrf
            <button type="submit" class="btn btn-ghost text-sm">
                {{ __('Log Out') }}
            </button>
        </form>
    </div>
</x-guest-layout>
