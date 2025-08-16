<nav x-data="{ open: false }" class="navbar bg-base-100 border-b border-base-200">
    <div class="container mx-auto flex justify-between items-center">
        <!-- Logo -->
        <div class="flex items-center gap-2">
            <a href="{{ route('dashboard') }}" class="btn btn-ghost text-xl">
                <x-application-logo class="h-8 w-auto fill-current text-primary" />
            </a>
        </div>

        <!-- Desktop Navigation Links -->
        <div class="hidden sm:flex gap-4">
            <x-nav-link :href="route('dashboard')" :active="request()->routeIs('dashboard')">
                {{ __('Dashboard') }}
            </x-nav-link>
        </div>

        <!-- User Dropdown -->
        <div class="hidden sm:flex items-center gap-4">
            <div class="dropdown dropdown-end">
                <div tabindex="0" role="button" class="btn btn-ghost btn-sm rounded-btn">
                    {{ Auth::user()->name }}
                    <svg class="ml-2 w-4 h-4 fill-current" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 20 20">
                        <path d="M5.25 7.75L10 12.5l4.75-4.75" stroke="currentColor" stroke-width="1.5" fill="none" />
                    </svg>
                </div>
                <ul tabindex="0" class="menu menu-sm dropdown-content mt-3 z-1 p-2 shadow-sm bg-base-100 rounded-box w-52">
                    <li>
                        <x-dropdown-link :href="route('profile.edit')">
                            {{ __('Profile') }}
                        </x-dropdown-link>
                    </li>
                    <li>
                        <form method="POST" action="{{ route('logout') }}">
                            @csrf
                            <x-dropdown-link :href="route('logout')"
                                onclick="event.preventDefault(); this.closest('form').submit();">
                                {{ __('Log Out') }}
                            </x-dropdown-link>
                        </form>
                    </li>

                    <li>
                        <x-dropdown-link :href="route('menus.index')">
                            {{ __('Menu_index') }}
                        </x-dropdown-link>
                    </li>
                    <li>
                        <x-dropdown-link :href="route('menus.create')">
                            {{ __('Menu_create') }}
                        </x-dropdown-link>
                    </li>
                </ul>
            </div>
        </div>

        <!-- Mobile Hamburger -->
        <div class="sm:hidden">
            <button @click="open = !open" class="btn btn-ghost btn-sm">
                <svg x-show="!open" class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                        d="M4 6h16M4 12h16M4 18h16" />
                </svg>
                <svg x-show="open" class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                        d="M6 18L18 6M6 6l12 12" />
                </svg>
            </button>
        </div>
    </div>

    <!-- Mobile Navigation -->
    <div :class="{'block': open, 'hidden': !open}" class="sm:hidden">
        <div class="menu menu-compact px-4 pt-2 pb-4">
            <x-responsive-nav-link :href="route('dashboard')" :active="request()->routeIs('dashboard')">
                {{ __('Dashboard') }}
            </x-responsive-nav-link>

            <div class="mt-2 border-t border-base-200 pt-2">
                <div class="text-sm font-semibold text-gray-700">{{ Auth::user()->name }}</div>
                <div class="text-xs text-gray-500">{{ Auth::user()->email }}</div>
            </div>

            <x-responsive-nav-link :href="route('profile.edit')">
                {{ __('Profile') }}
            </x-responsive-nav-link>

            <form method="POST" action="{{ route('logout') }}">
                @csrf
                <x-responsive-nav-link :href="route('logout')"
                    onclick="event.preventDefault(); this.closest('form').submit();">
                    {{ __('Log Out') }}
                </x-responsive-nav-link>
            </form>
        </div>
    </div>
</nav>
