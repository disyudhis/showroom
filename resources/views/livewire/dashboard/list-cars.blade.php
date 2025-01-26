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
    <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6 p-6 bg-white dark:bg-gray-900 transition-colors duration-300">
        @forelse($cars as $car)
            <div class="bg-white dark:bg-gray-800 shadow-lg rounded-xl overflow-hidden transform transition-all duration-300 hover:scale-105 hover:shadow-2xl group">
                @php
                    $clickbaitImage = $car->images->first();
                @endphp

                <div class="relative">
                    @if($clickbaitImage)
                        <img src="{{ Storage::url($clickbaitImage->image) }}"
                             alt="{{ $car->nama_mobil }}"
                             class="w-full h-64 object-cover transition-transform duration-300 group-hover:scale-110">
                    @else
                        <div class="w-full h-64 bg-gray-200 dark:bg-gray-700 flex items-center justify-center">
                            No Image Available
                        </div>
                    @endif

                    <div class="absolute top-4 right-4 bg-white/80 dark:bg-gray-900/80 px-3 py-1 rounded-full text-sm font-semibold">
                        {{ $car->tahun_pembuatan }}
                    </div>
                </div>

                <div class="p-6 space-y-4">
                    <div>
                        <h3 class="text-xl font-bold text-gray-800 dark:text-gray-100 mb-2">{{ $car->nama_mobil }}</h3>
                        <p class="text-gray-600 dark:text-gray-400 flex items-center">
                            <i class="ri-car-line mr-2"></i>
                            {{ $car->brand }}
                        </p>
                    </div>

                    <div class="flex items-center text-gray-700 dark:text-gray-300">
                        <i class="ri-user-line mr-2 text-blue-500"></i>
                        {{ $car->customer->nama_lengkap }}
                    </div>

                    <div class="flex justify-between items-center mt-4">
                        <a href="{{ route('dashboard.show', ['car' => $car->id]) }}"
                           class="inline-flex items-center px-4 py-2 bg-blue-500 text-white rounded-lg hover:bg-blue-600 dark:bg-blue-600 dark:hover:bg-blue-500 transition-colors">
                            View Details
                            <i class="ri-arrow-right-line ml-2"></i>
                        </a>
                    </div>
                </div>
            </div>
        @empty
            <div class="col-span-full text-center py-12 bg-gray-100 dark:bg-gray-800 rounded-lg">
                <i class="ri-car-line text-5xl text-gray-600 dark:text-gray-400 mb-4"></i>
                <h3 class="text-2xl text-gray-600 dark:text-gray-300 mb-4">No Cars Available</h3>
                <p class="text-gray-500 dark:text-gray-400">Check back later or add new cars</p>
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
