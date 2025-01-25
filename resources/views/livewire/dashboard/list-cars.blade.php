<?php

use App\Models\Cars;
use Livewire\Volt\Component;

new class extends Component {
    public function with()
    {
        return [
            'cars' => Cars::paginate(10),
        ];
    }
}; ?>

<div class="space-y-6">
    <!-- Search and Sort Controls -->
    <div class="flex justify-between items-center mb-4">
        <input wire:model.live="search" type="text" placeholder="Search cars..."
            class="w-full max-w-md px-4 py-2 border rounded-lg dark:bg-gray-700 dark:border-gray-600 dark:text-gray-200">
    </div>

    <!-- Car Grid -->
    <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6">
        @forelse($cars as $car)
            <div class="bg-white dark:bg-gray-800 rounded-lg shadow-md overflow-hidden group">
                <div class="relative">
                    <img src="{{ $car->image ? asset('storage/' . $car->image) : asset('img/placeholder-car.png') }}"
                        alt="{{ $car->nama_mobil }}"
                        class="w-full h-48 object-cover transition-transform duration-300 group-hover:scale-105">
                    <div
                        class="absolute inset-0 bg-black bg-opacity-0 group-hover:bg-opacity-20 transition-all duration-300">
                    </div>
                </div>
                <div class="p-4">
                    <h3 class="text-xl font-bold text-gray-900 dark:text-gray-200 mb-2">
                        {{ $car->nama_mobil }}
                    </h3>
                    <div class="flex justify-between items-center mb-4">
                        <span class="text-gray-600 dark:text-gray-400">
                            {{ $car->brand }} | {{ $car->tahun_pembuatan }}
                        </span>
                        <span class="font-semibold text-green-600 dark:text-green-400">
                            A/N {{ $car->customer->nama_lengkap }}
                        </span>
                    </div>

                    <!-- Detail Button -->
                    <div class="flex justify-center">
                        <a href="{{ route('cars.show', ['car' => $car->id]) }}"
                            class="px-4 py-2 bg-blue-500 dark:bg-blue-600 text-white rounded-lg
                                   hover:bg-blue-600 dark:hover:bg-blue-700
                                   transition-colors duration-300
                                   text-center w-full"
                            wire:navigate>
                            View Details
                        </a>
                    </div>
                </div>
            </div>
        @empty
            <div class="col-span-full text-center py-8">
                <p class="text-gray-500 dark:text-gray-400">
                    No cars found.
                </p>
            </div>
        @endforelse
    </div>

    <!-- Pagination -->
    <div class="mt-6">
        {{ $cars->links() }}
    </div>

    @if (session('success'))
        <div x-data="{ show: true }" x-show="show" x-init="setTimeout(() => show = false, 3000)"
            class="fixed bottom-5 right-5 bg-green-500 text-white px-6 py-3 rounded-xl shadow-lg">
            {{ session('success') }}
        </div>
    @endif
</div>
