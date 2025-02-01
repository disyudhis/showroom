<?php

use App\Models\User;
use Illuminate\Auth\Events\Registered;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Validation\Rules;
use Livewire\Attributes\Layout;
use Livewire\Volt\Component;

new #[Layout('layouts.guest')] class extends Component
{
    public string $name = '';
    public string $email = '';
    public string $password = '';
    public string $password_confirmation = '';

    /**
     * Handle an incoming registration request.
     */
    public function register(): void
    {
        $validated = $this->validate([
            'name' => ['required', 'string', 'max:255'],
            'email' => ['required', 'string', 'lowercase', 'email', 'max:255', 'unique:'.User::class],
            'password' => ['required', 'string', 'confirmed', Rules\Password::defaults()],
        ]);

        $validated['password'] = Hash::make($validated['password']);

        event(new Registered($user = User::create($validated)));

        Auth::login($user);

        $this->redirect(route('dashboard.index', absolute: false), navigate: true);
    }
}; ?>

<div>
    <div>
        <div class="text-center mb-6">
            <h2 class="text-3xl font-bold text-gray-800 dark:text-gray-100">Register</h2>
            <p class="text-gray-600 dark:text-gray-400 mt-2">Create your Showroom Manager account</p>
        </div>

        <form wire:submit="register" class="space-y-6">
            <!-- Name Input -->
            <div>
                <x-input-label for="name" :value="__('Name')" class="text-gray-700 dark:text-gray-300 mb-2" />
                <div class="relative">
                    <div class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none">
                        <i class="ri-user-line text-gray-400"></i>
                    </div>
                    <x-text-input
                        wire:model="name"
                        id="name"
                        class="block w-full pl-10 pr-3 py-2.5 border border-gray-300 dark:border-gray-700 rounded-lg focus:ring-2 focus:ring-red-500 dark:focus:ring-red-700 dark:bg-black dark:text-gray-100"
                        type="text"
                        name="name"
                        required
                        autofocus
                        autocomplete="name"
                        placeholder="Enter your full name"
                    />
                </div>
                <x-input-error :messages="$errors->get('name')" class="mt-2 text-red-500" />
            </div>

            <!-- Email Input -->
            <div>
                <x-input-label for="email" :value="__('Email')" class="text-gray-700 dark:text-gray-300 mb-2" />
                <div class="relative">
                    <div class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none">
                        <i class="ri-mail-line text-gray-400"></i>
                    </div>
                    <x-text-input
                        wire:model="email"
                        id="email"
                        class="block w-full pl-10 pr-3 py-2.5 border border-gray-300 dark:border-red-900/20 rounded-lg focus:ring-2 focus:ring-red-500 dark:focus:ring-red-700 dark:bg-black dark:text-gray-100"
                        type="email"
                        name="email"
                        required
                        autocomplete="email"
                        placeholder="Enter your email"
                    />
                </div>
                <x-input-error :messages="$errors->get('email')" class="mt-2 text-red-500" />
            </div>

            <!-- Password Input -->
            <div>
                <x-input-label for="password" :value="__('Password')" class="text-gray-700 dark:text-gray-300 mb-2" />
                <div class="relative">
                    <div class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none">
                        <i class="ri-lock-line text-gray-400"></i>
                    </div>
                    <x-text-input
                        wire:model="password"
                        id="password"
                        class="block w-full pl-10 pr-3 py-2.5 border border-gray-300 dark:border-red-900/20 rounded-lg focus:ring-2 focus:ring-red-500 dark:focus:ring-red-700 dark:bg-black dark:text-gray-100"
                        type="password"
                        name="password"
                        required
                        autocomplete="new-password"
                        placeholder="Create a strong password"
                    />
                </div>
                <x-input-error :messages="$errors->get('password')" class="mt-2 text-red-500" />
            </div>

            <!-- Confirm Password -->
            <div>
                <x-input-label for="password_confirmation" :value="__('Confirm Password')" class="text-gray-700 dark:text-gray-300 mb-2" />
                <div class="relative">
                    <div class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none">
                        <i class="ri-lock-2-line text-gray-400"></i>
                    </div>
                    <x-text-input
                        wire:model="password_confirmation"
                        id="password_confirmation"
                        class="block w-full pl-10 pr-3 py-2.5 border border-gray-300 dark:border-red-900/20 rounded-lg focus:ring-2 focus:ring-red-500 dark:focus:ring-red-700 dark:bg-black dark:text-gray-100"
                        type="password"
                        name="password_confirmation"
                        required
                        autocomplete="new-password"
                        placeholder="Confirm your password"
                    />
                </div>
                <x-input-error :messages="$errors->get('password_confirmation')" class="mt-2 text-red-500" />
            </div>

            <!-- Register Button -->
            <div>
                <x-primary-button class="w-full flex justify-center py-2.5 px-4 rounded-lg bg-gradient-to-r from-red-600 to-red-800 hover:from-red-700 hover:to-red-900 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-red-500 text-white">
                    {{ __('Register') }}
                </x-primary-button>
            </div>

            <!-- Login Link -->
            <div class="text-center mt-4">
                <p class="text-sm text-gray-600 dark:text-gray-400">
                    Already have an account?
                    <a
                        href="{{ route('login') }}"
                        wire:navigate
                        class="text-red-600 hover:text-red-700 dark:text-red-500 dark:hover:text-red-400"
                    >
                        Login here
                    </a>
                </p>
            </div>
        </form>
    </div>
</div>

