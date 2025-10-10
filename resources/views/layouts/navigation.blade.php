<nav x-data="{ open: false }" class="bg-cyan-500 border-b border-cyan-600 shadow-md">
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
        <div class="flex justify-between h-16">
            <div class="flex">
                <div class="shrink-0 flex items-center">
                    @if (Auth::user()->role === 'super_admin')
                        <a href="{{ route('super_admin.dashboard') }}">
                            <x-application-logo class="block h-10 w-auto" />
                        </a>
                    @else
                        <a href="{{ route('dashboard') }}">
                            <x-application-logo class="block h-10 w-auto" />
                        </a>
                    @endif
                </div>

                <div class="hidden space-x-4 sm:ms-10 sm:flex">
                    @if (Auth::user()->role === 'super_admin')
                        <a href="{{ route('super_admin.dashboard') }}" class="inline-flex items-center px-3 py-2 text-sm font-medium leading-5 text-white hover:bg-cyan-600 rounded-lg transition duration-150 ease-in-out">
                            <svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 12l2-2m0 0l7-7 7 7M5 10v10a1 1 0 001 1h3m10-11l2 2m-2-2v10a1 1 0 01-1 1h-3m-6 0a1 1 0 001-1v-4a1 1 0 011-1h2a1 1 0 011 1v4a1 1 0 001 1m-6 0h6"></path></svg>
                            {{ __('Dashboard') }}
                        </a>
                    @else
                        <a href="{{ route('dashboard') }}" class="inline-flex items-center px-3 py-2 text-sm font-medium leading-5 text-white hover:bg-cyan-600 rounded-lg transition duration-150 ease-in-out">
                             <svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 12l2-2m0 0l7-7 7 7M5 10v10a1 1 0 001 1h3m10-11l2 2m-2-2v10a1 1 0 01-1 1h-3m-6 0a1 1 0 001-1v-4a1 1 0 011-1h2a1 1 0 011 1v4a1 1 0 001 1m-6 0h6"></path></svg>
                            {{ __('Dashboard') }}
                        </a>
                    @endif

                    @if (Auth::user()->role === 'super_admin')
                        <a href="#" class="inline-flex items-center px-3 py-2 text-sm font-medium leading-5 text-white hover:bg-cyan-600 rounded-lg transition duration-150 ease-in-out">
                            <svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 17v-2a4 4 0 00-4-4H5a4 4 0 00-4 4v2m14-2v-2a4 4 0 00-4-4h-2m14-3a4 4 0 00-4-4h-2m-4-4a4 4 0 00-4 4v2"></path></svg>
                            {{ __('Rekapan') }}
                        </a>
                        <a href="{{ route('karyawans.index') }}" class="inline-flex items-center px-3 py-2 text-sm font-medium leading-5 text-white hover:bg-cyan-600 rounded-lg transition duration-150 ease-in-out">
                            <svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 20h5v-2a3 3 0 00-5.356-1.857M17 20H7m10 0v-2c0-.656-.126-1.283-.356-1.857M7 20H2v-2a3 3 0 015.356-1.857M7 20v-2c0-.656.126-1.283.356-1.857m0 0a5.002 5.002 0 019.288 0M15 7a3 3 0 11-6 0 3 3 0 016 0zm6 3a2 2 0 11-4 0 2 2 0 014 0zM7 10a2 2 0 11-4 0 2 2 0 014 0z"></path></svg>
                            {{ __('Data Karyawan') }}
                        </a>
                        <a href="{{ route('admins.index') }}" class="inline-flex items-center px-3 py-2 text-sm font-medium leading-5 text-white hover:bg-cyan-600 rounded-lg transition duration-150 ease-in-out">
                             <svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z"></path></svg>
                            {{ __('Data Admin') }}
                        </a>
                    @endif
                </div>
            </div>

            <div class="hidden sm:flex sm:items-center sm:ms-6">
                <x-dropdown align="right" width="48">
                    <x-slot name="trigger">
                        <button class="inline-flex items-center px-3 py-2 border border-transparent text-sm leading-4 font-medium rounded-md text-white bg-cyan-500 hover:text-gray-200 focus:outline-none transition ease-in-out duration-150">
                            <div>{{ Auth::user()->name }}</div>
                            <div class="ms-1"><svg class="fill-current h-4 w-4" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 20 20"><path fill-rule="evenodd" d="M5.293 7.293a1 1 0 011.414 0L10 10.586l3.293-3.293a1 1 0 111.414 1.414l-4 4a1 1 0 01-1.414 0l-4-4a1 1 0 010-1.414z" clip-rule="evenodd" /></svg></div>
                        </button>
                    </x-slot>
                    <x-slot name="content">
                        <x-dropdown-link :href="route('profile.edit')">{{ __('Profile') }}</x-dropdown-link>
                        <form method="POST" action="{{ route('logout') }}"> @csrf <x-dropdown-link :href="route('logout')" onclick="event.preventDefault(); this.closest('form').submit();">{{ __('Log Out') }}</x-dropdown-link></form>
                    </x-slot>
                </x-dropdown>
            </div>

            <div class="-me-2 flex items-center sm:hidden">
                <button @click="open = ! open" class="inline-flex items-center justify-center p-2 rounded-md text-gray-200 hover:text-white hover:bg-cyan-600 focus:outline-none focus:bg-cyan-600 focus:text-white transition duration-150 ease-in-out">
                    <svg class="h-6 w-6" stroke="currentColor" fill="none" viewBox="0 0 24 24"><path :class="{'hidden': open, 'inline-flex': ! open }" class="inline-flex" stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 6h16M4 12h16M4 18h16" /><path :class="{'hidden': ! open, 'inline-flex': open }" class="hidden" stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12" /></svg>
                </button>
            </div>
        </div>
    </div>

    <div :class="{'block': open, 'hidden': ! open}" class="hidden sm:hidden bg-cyan-500">
        <div class="pt-2 pb-3 space-y-1">
            @if (Auth::user()->role === 'super_admin')
                <x-responsive-nav-link :href="route('super_admin.dashboard')" :active="request()->routeIs('super_admin.dashboard')" class="text-white hover:bg-cyan-600">
                    {{ __('Dashboard') }}
                </x-responsive-nav-link>
            @else
                <x-responsive-nav-link :href="route('dashboard')" :active="request()->routeIs('dashboard')" class="text-white hover:bg-cyan-600">
                    {{ __('Dashboard') }}
                </x-responsive-nav-link>
            @endif

            @if (Auth::user()->role === 'super_admin')
                <x-responsive-nav-link href="#" :active="false" class="text-white hover:bg-cyan-600">
                    {{ __('Rekapan Data') }}
                </x-responsive-nav-link>
                <x-responsive-nav-link :href="route('karyawans.index')" :active="request()->routeIs('karyawans.*')" class="text-white hover:bg-cyan-600">
                    {{ __('Data Karyawan') }}
                </x-responsive-nav-link>
                <x-responsive-nav-link :href="route('admins.index')" :active="request()->routeIs('admins.*')" class="text-white hover:bg-cyan-600">
                    {{ __('Data Admin') }}
                </x-responsive-nav-link>
            @endif
        </div>

        <div class="pt-4 pb-1 border-t border-cyan-600">
            <div class="px-4">
                <div class="font-medium text-base text-white">{{ Auth::user()->name }}</div>
                <div class="font-medium text-sm text-gray-200">{{ Auth::user()->email }}</div>
            </div>
            <div class="mt-3 space-y-1">
                <x-responsive-nav-link :href="route('profile.edit')" class="text-white hover:bg-cyan-600">
                    {{ __('Profile') }}
                </x-responsive-nav-link>
                <form method="POST" action="{{ route('logout') }}">
                    @csrf
                    <x-responsive-nav-link :href="route('logout')" onclick="event.preventDefault(); this.closest('form').submit();" class="text-white hover:bg-cyan-600">
                        {{ __('Log Out') }}
                    </x-responsive-nav-link>
                </form>
            </div>
        </div>
    </div>
</nav>