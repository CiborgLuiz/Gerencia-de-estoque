<nav
    x-data="{
        open: false,
        darkMode: document.documentElement.classList.contains('dark'),
        toggleTheme() {
            this.darkMode = !this.darkMode;
            window.setTheme(this.darkMode ? 'dark' : 'light');
        }
    }"
    class="bg-white border-b border-gray-100 dark:bg-gray-900 dark:border-gray-800"
>
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
        <div class="flex min-h-16 items-center justify-between gap-4 py-2">
            <div class="flex min-w-0 items-center gap-6">
                <div class="shrink-0 flex items-center">
                    <a href="{{ route('dashboard') }}" class="text-lg font-semibold text-gray-900 dark:text-gray-100">Gerência de Estoque</a>
                </div>

                <div class="hidden min-w-0 flex-1 sm:flex">
                    <div class="flex min-w-0 items-center gap-6 overflow-x-auto whitespace-nowrap pb-1 [scrollbar-width:none] [-ms-overflow-style:none]">
                        <x-nav-link :href="route('dashboard')" :active="request()->routeIs('dashboard')">
                            {{ __('Dashboard') }}
                        </x-nav-link>

                        <x-nav-link :href="route('products.manage')" :active="request()->routeIs('products.*')">Produtos</x-nav-link>
                        <x-nav-link :href="route('sales.catalog')" :active="request()->routeIs('sales.*')">Vendas</x-nav-link>
                        <x-nav-link :href="route('invoices.index')" :active="request()->routeIs('invoices.*') || request()->routeIs('nfse.*')">Notas Fiscais</x-nav-link>
                        @if (Auth::user()->hasRole('dono', 'admin', 'gerente'))
                            <x-nav-link :href="route('admin.dashboard')" :active="request()->routeIs('admin.*')">Admin</x-nav-link>
                        @endif
                        @if (Auth::user()->hasRole('dono', 'admin'))
                            <x-nav-link :href="route('admin.access-keys.index')" :active="request()->routeIs('admin.access-keys.*')">Chaves</x-nav-link>
                        @endif
                        @if (Auth::user()->hasRole('dono'))
                            <x-nav-link :href="route('admin.employees.index')" :active="request()->routeIs('admin.employees.*')">Funcionários</x-nav-link>
                        @endif
                    </div>
                </div>
            </div>

            <div class="hidden sm:flex sm:items-center sm:ms-6 gap-3">
                <button
                    type="button"
                    @click="toggleTheme"
                    class="inline-flex items-center rounded-md border border-gray-300 bg-white px-3 py-2 text-xs font-semibold uppercase tracking-wide text-gray-700 hover:bg-gray-100 dark:border-gray-700 dark:bg-gray-800 dark:text-gray-100 dark:hover:bg-gray-700"
                >
                    <span x-text="darkMode ? 'Claro' : 'Escuro'"></span>
                </button>

                <x-dropdown align="right" width="48">
                    <x-slot name="trigger">
                        <button class="inline-flex items-center px-3 py-2 border border-transparent text-sm leading-4 font-medium rounded-md text-gray-500 bg-white hover:text-gray-700 focus:outline-none transition ease-in-out duration-150 dark:text-gray-300 dark:bg-gray-900 dark:hover:text-white">
                            <div>{{ Auth::user()->name }}</div>

                            <div class="ms-1">
                                <svg class="fill-current h-4 w-4" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 20 20">
                                    <path fill-rule="evenodd" d="M5.293 7.293a1 1 0 011.414 0L10 10.586l3.293-3.293a1 1 0 111.414 1.414l-4 4a1 1 0 01-1.414 0l-4-4a1 1 0 010-1.414z" clip-rule="evenodd" />
                                </svg>
                            </div>
                        </button>
                    </x-slot>

                    <x-slot name="content">
                        <x-dropdown-link :href="route('profile.edit')">
                            {{ __('Profile') }}
                        </x-dropdown-link>

                        <form method="POST" action="{{ route('logout') }}">
                            @csrf
                            <x-dropdown-link :href="route('logout')" onclick="event.preventDefault(); this.closest('form').submit();">
                                {{ __('Log Out') }}
                            </x-dropdown-link>
                        </form>
                    </x-slot>
                </x-dropdown>
            </div>

            <div class="-me-2 flex items-center sm:hidden">
                <button @click="open = ! open" class="inline-flex items-center justify-center p-2 rounded-md text-gray-500 hover:text-gray-700 hover:bg-gray-100 focus:outline-none focus:bg-gray-100 transition duration-150 ease-in-out dark:text-gray-400 dark:hover:text-gray-200 dark:hover:bg-gray-800 dark:focus:bg-gray-800">
                    <svg class="h-6 w-6" stroke="currentColor" fill="none" viewBox="0 0 24 24">
                        <path :class="{'hidden': open, 'inline-flex': ! open }" class="inline-flex" stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 6h16M4 12h16M4 18h16" />
                        <path :class="{'hidden': ! open, 'inline-flex': open }" class="hidden" stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12" />
                    </svg>
                </button>
            </div>
        </div>
    </div>

    <div :class="{'block': open, 'hidden': ! open}" class="hidden sm:hidden">
        <div class="pt-2 pb-3 space-y-1">
            <x-responsive-nav-link :href="route('dashboard')" :active="request()->routeIs('dashboard')">
                {{ __('Dashboard') }}
            </x-responsive-nav-link>
            <x-responsive-nav-link :href="route('products.manage')" :active="request()->routeIs('products.*')">Produtos</x-responsive-nav-link>
            <x-responsive-nav-link :href="route('sales.catalog')" :active="request()->routeIs('sales.*')">Vendas</x-responsive-nav-link>
            <x-responsive-nav-link :href="route('invoices.index')" :active="request()->routeIs('invoices.*') || request()->routeIs('nfse.*')">Notas Fiscais</x-responsive-nav-link>
            @if (Auth::user()->hasRole('dono', 'admin', 'gerente'))
                <x-responsive-nav-link :href="route('admin.dashboard')" :active="request()->routeIs('admin.*')">Admin</x-responsive-nav-link>
            @endif
            @if (Auth::user()->hasRole('dono', 'admin'))
                <x-responsive-nav-link :href="route('admin.access-keys.index')" :active="request()->routeIs('admin.access-keys.*')">Chaves</x-responsive-nav-link>
            @endif
            @if (Auth::user()->hasRole('dono'))
                <x-responsive-nav-link :href="route('admin.employees.index')" :active="request()->routeIs('admin.employees.*')">Funcionários</x-responsive-nav-link>
            @endif
            <div class="px-3 pt-2">
                <button
                    type="button"
                    @click="toggleTheme"
                    class="w-full rounded-md border border-gray-300 bg-white px-3 py-2 text-xs font-semibold uppercase tracking-wide text-gray-700 hover:bg-gray-100 dark:border-gray-700 dark:bg-gray-800 dark:text-gray-100 dark:hover:bg-gray-700"
                >
                    <span x-text="darkMode ? 'Trocar para claro' : 'Trocar para escuro'"></span>
                </button>
            </div>
        </div>

        <div class="pt-4 pb-1 border-t border-gray-200 dark:border-gray-600">
            <div class="px-4">
                <div class="font-medium text-base text-gray-800 dark:text-gray-200">{{ Auth::user()->name }}</div>
                <div class="font-medium text-sm text-gray-500">{{ Auth::user()->email }}</div>
            </div>

            <div class="mt-3 space-y-1">
                <x-responsive-nav-link :href="route('profile.edit')">
                    {{ __('Profile') }}
                </x-responsive-nav-link>

                <form method="POST" action="{{ route('logout') }}">
                    @csrf
                    <x-responsive-nav-link :href="route('logout')" onclick="event.preventDefault(); this.closest('form').submit();">
                        {{ __('Log Out') }}
                    </x-responsive-nav-link>
                </form>
            </div>
        </div>
    </div>
</nav>
