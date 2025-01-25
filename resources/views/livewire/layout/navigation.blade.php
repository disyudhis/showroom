<?php
use App\Livewire\Actions\Logout;
use Livewire\Volt\Component;

new class extends Component {
    public $darkMode = false;

    public function mount()
    {
        // Check user's preference from local storage or database
        $this->darkMode = session('dark_mode', false);
    }

    public function toggleDarkMode()
    {
        $this->darkMode = !$this->darkMode;

        // Save preference to session
        session(['dark_mode' => $this->darkMode]);
    }
    public function logout(Logout $logout): void
    {
        $logout();
        $this->redirect('/', navigate: true);
    }
};
?>

<nav class="bg-white dark:bg-gray-800 shadow-md" x-data="{
    open: false,
    darkMode: @entangle('darkMode'),
    initDarkMode() {
        if (localStorage.theme === 'dark' || (!('theme' in localStorage) && window.matchMedia('(prefers-color-scheme: dark)').matches)) {
            document.documentElement.classList.add('dark')
        } else {
            document.documentElement.classList.remove('dark')
        }
    },
    toggleDarkMode() {
        document.documentElement.classList.toggle('dark')
        localStorage.setItem('theme', document.documentElement.classList.contains('dark') ? 'dark' : 'light')
        this.$wire.toggleDarkMode()
    }
}" x-init="initDarkMode()">
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
        <div class="flex justify-between h-16">
            <!-- Logo -->
            <div class="flex items-center">
                <a href="{{ route('dashboard') }}" wire:navigate class="flex items-center">
                    <img src="{{ asset('img/Logo tanpa kotak.png') }}" alt="KAC Logo" class="block h-28 w-auto" />
                    <span class="ml-2 text-xl font-bold text-gray-800 dark:text-gray-200">Karunia Auto Car</span>
                </a>
            </div>

            <!-- Desktop Navigation -->
            <div class="hidden sm:flex sm:items-center sm:ml-6 space-x-4">
                <x-nav-link :href="route('dashboard')" :active="request()->routeIs('dashboard')" wire:navigate>
                    <i class="ri-dashboard-line mr-2"></i>Dashboard
                </x-nav-link>

                <x-nav-link :href="route('cars.store')" :active="request()->routeIs('cars.*')" wire:navigate>
                    <i class="ri-car-line mr-2"></i>Input Data
                </x-nav-link>

                <!-- Dark Mode Toggle -->
                <button @click="toggleDarkMode()"
                    class="text-gray-500 dark:text-gray-300 hover:text-gray-700 dark:hover:text-gray-200 transition duration-150 ease-in-out">
                    <i x-show="!darkMode" class="ri-moon-line"></i>
                    <i x-show="darkMode" class="ri-sun-line"></i>
                </button>
            </div>

            <!-- User Dropdown -->
            <div class="hidden sm:flex sm:items-center sm:ml-6">
                <x-dropdown align="right" width="48">
                    <x-slot name="trigger">
                        <button
                            class="flex items-center text-sm font-medium text-gray-500 hover:text-gray-700 focus:outline-none transition duration-150 ease-in-out">
                            <div>{{ Auth::user()->name }}</div>
                            <div class="ml-1">
                                <i class="ri-arrow-down-s-line"></i>
                            </div>
                        </button>
                    </x-slot>

                    <x-slot name="content">
                        <x-dropdown-link :href="route('profile')" wire:navigate>
                            <i class="ri-user-settings-line mr-2"></i>Profile
                        </x-dropdown-link>

                        <button wire:click="logout" class="w-full text-left">
                            <x-dropdown-link>
                                <i class="ri-logout-box-r-line mr-2"></i>Log Out
                            </x-dropdown-link>
                        </button>
                    </x-slot>
                </x-dropdown>
            </div>

            <!-- Mobile Menu Button -->
            <div class="-mr-2 flex items-center sm:hidden">
                <button @click="open = ! open"
                    class="inline-flex items-center justify-center p-2 rounded-md text-gray-400 hover:text-gray-500 hover:bg-gray-100 focus:outline-none focus:bg-gray-100 focus:text-gray-500 transition duration-150 ease-in-out">
                    <i x-show="!open" class="ri-menu-line"></i>
                    <i x-show="open" class="ri-close-line"></i>
                </button>
            </div>
        </div>
    </div>

    <!-- Mobile Navigation Menu -->
    <div x-show="open" class="sm:hidden">
        <div class="pt-2 pb-3 space-y-1">
            <x-responsive-nav-link :href="route('dashboard')" :active="request()->routeIs('dashboard')" wire:navigate>
                <i class="ri-dashboard-line mr-2"></i>Dashboard
            </x-responsive-nav-link>

            <x-responsive-nav-link :href="route('cars.store')" :active="request()->routeIs('cars.*')" wire:navigate>
                <i class="ri-car-line mr-2"></i>Input Data
            </x-responsive-nav-link>
        </div>

        <!-- Mobile User Section -->
        <div class="pt-4 pb-1 border-t border-gray-200">
            <div class="px-4">
                <div class="font-medium text-base text-gray-800 dark:text-gray-200">{{ Auth::user()->name }}</div>
                <div class="font-medium text-sm text-gray-500 dark:text-gray-400">{{ Auth::user()->email }}</div>
            </div>

            <div class="mt-3 space-y-1">
                <x-responsive-nav-link :href="route('profile')" wire:navigate>
                    <i class="ri-user-settings-line mr-2"></i>Profile
                </x-responsive-nav-link>

                <button wire:click="logout" class="w-full text-left">
                    <x-responsive-nav-link>
                        <i class="ri-logout-box-r-line mr-2"></i>Log Out
                    </x-responsive-nav-link>
                </button>
            </div>
        </div>
    </div>
</nav>
