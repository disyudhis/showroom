<?php
use function Livewire\Volt\{layout, mount, state};
use App\Models\Cars;

layout('layouts.app');

state([
    'car' => fn() => $car,
    'currentImageIndex' => 0,
]);

mount(function (Cars $car) {
    $this->car = $car;
});

$nextImage = function () {
    $images = json_decode($this->car->image ?? '[]');
    $this->currentImageIndex = ($this->currentImageIndex + 1) % count($images);
};

$prevImage = function () {
    $images = json_decode($this->car->image ?? '[]');
    $this->currentImageIndex = ($this->currentImageIndex - 1 + count($images)) % count($images);
};

$delete = function () {
    $this->car->delete();
    $this->car->customer()->delete();
    return redirect()->route('dashboard');
};

?>
<div class="container mx-auto px-4 py-12">
    <div class="max-w-7xl mx-auto">
        <div class="grid md:grid-cols-2 gap-8">
            <!-- Image Section -->
            <div class="space-y-6">
                <div class="bg-white dark:bg-gray-800 rounded-2xl shadow-lg overflow-hidden">
                    @php
                        $images = json_decode($this->car->image ?? '[]');
                    @endphp

                    @if (!empty($images))
                        <div class="relative w-full aspect-video">
                            <img
                                src="{{ asset('storage/' . $images[$this->currentImageIndex]) }}"
                                alt="{{ $this->car->nama_mobil }}"
                                class="w-full h-full object-cover"
                            >

                            @if(count($images) > 1)
                                <div class="absolute inset-0 flex items-center justify-between px-4">
                                    <button
                                        wire:click="{{ $prevImage }}"
                                        class="bg-white/50 dark:bg-gray-800/50 p-2 rounded-full
                                        hover:bg-white/70 dark:hover:bg-gray-800/70
                                        transition-all duration-300"
                                    >
                                        <svg xmlns="http://www.w3.org/2000/svg" class="h-6 w-6" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                            <path strokeLinecap="round" strokeLinejoin="round" strokeWidth={2} d="M15 19l-7-7 7-7" />
                                        </svg>
                                    </button>
                                    <button
                                        wire:click="{{ $nextImage }}"
                                        class="bg-white/50 dark:bg-gray-800/50 p-2 rounded-full
                                        hover:bg-white/70 dark:hover:bg-gray-800/70
                                        transition-all duration-300"
                                    >
                                        <svg xmlns="http://www.w3.org/2000/svg" class="h-6 w-6" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                            <path strokeLinecap="round" strokeLinejoin="round" strokeWidth={2} d="M9 5l7 7-7 7" />
                                        </svg>
                                    </button>
                                </div>
                            @endif
                        </div>
                    @else
                        <img
                            src="{{ asset('img/placeholder-car.png') }}"
                            alt="No Image"
                            class="w-full aspect-video object-cover"
                        >
                    @endif
                </div>
            </div>

            <!-- Car Details Section -->
            <div class="space-y-6">
                <!-- Owner Details -->
                <div class="bg-white dark:bg-gray-800 rounded-2xl shadow-lg p-6">
                    <h3 class="text-xl font-semibold text-gray-900 dark:text-gray-100 mb-4">
                        Detail Pemilik
                    </h3>
                    <div class="space-y-2">
                        <p class="text-lg font-medium text-gray-800 dark:text-gray-200">
                            {{ $this->car->customer->nama_lengkap }}
                        </p>
                        <p class="text-gray-600 dark:text-gray-400">
                            {{ $this->car->customer->no_hp }}
                        </p>
                        <p class="text-gray-600 dark:text-gray-400">
                            {{ $this->car->customer->alamat }}
                        </p>
                    </div>
                </div>

                <!-- Car Details -->
                <div class="bg-white dark:bg-gray-800 rounded-2xl shadow-lg p-6">
                    <h1 class="text-3xl font-bold text-gray-900 dark:text-gray-100 mb-6">
                        {{ $this->car->nama_mobil }}
                    </h1>

                    <div class="grid grid-cols-2 gap-4 mb-6">
                        <div class="bg-gray-50 dark:bg-gray-700 p-4 rounded-xl">
                            <p class="text-sm text-gray-500 dark:text-gray-400 mb-1">Brand</p>
                            <p class="text-lg font-semibold text-gray-800 dark:text-gray-200">
                                {{ $this->car->brand }}
                            </p>
                        </div>
                        <div class="bg-gray-50 dark:bg-gray-700 p-4 rounded-xl">
                            <p class="text-sm text-gray-500 dark:text-gray-400 mb-1">Tahun</p>
                            <p class="text-lg font-semibold text-gray-800 dark:text-gray-200">
                                {{ $this->car->tahun_pembuatan }}
                            </p>
                        </div>
                    </div>

                    <div class="space-y-4">
                        <div>
                            <p class="text-sm text-gray-500 dark:text-gray-400 mb-1">Nomor Mesin</p>
                            <p class="text-lg font-semibold text-gray-800 dark:text-gray-200">
                                {{ $this->car->no_mesin }}
                            </p>
                        </div>

                        <div>
                            <p class="text-sm text-gray-500 dark:text-gray-400 mb-1">Nomor Polisi</p>
                            <p class="text-lg font-semibold text-gray-800 dark:text-gray-200">
                                {{ $this->car->no_polisi }}
                            </p>
                        </div>

                        <div>
                            <p class="text-sm text-gray-500 dark:text-gray-400 mb-1">Kilometer ODO</p>
                            <p class="text-lg font-semibold text-gray-800 dark:text-gray-200">
                               {{ $this->car->odo }} km
                            </p>
                        </div>
                    </div>
                </div>

                <!-- Action Buttons -->
                <div class="flex space-x-4">
                    <a href="#"
                       class="flex-1 btn btn-primary flex items-center justify-center space-x-2
                              py-3 bg-blue-500 text-white rounded-xl
                              hover:bg-blue-600 transition-colors">
                        <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5" viewBox="0 0 20 20" fill="currentColor">
                            <path d="M13.586 3.586a2 2 0 112.828 2.828l-.793.793-2.828-2.828.793-.793zM11.379 5.793L3 14.172V17h2.828l8.38-8.379-2.83-2.828z" />
                        </svg>
                        <span>Edit</span>
                    </a>
                    <button wire:click="delete"
                            wire:confirm="Yakin ingin menghapus mobil ini?"
                            class="flex-1 btn btn-danger flex items-center justify-center space-x-2
                                   py-3 bg-red-500 text-white rounded-xl
                                   hover:bg-red-600 transition-colors">
                        <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5" viewBox="0 0 20 20" fill="currentColor">
                            <path fillRule="evenodd" d="M9 2a1 1 0 00-.894.553L7.382 4H4a1 1 0 000 2v10a2 2 0 002 2h8a2 2 0 002-2V6a1 1 0 100-2h-3.382l-.724-1.447A1 1 0 0011 2H9zM7 8a1 1 0 012 0v6a1 1 0 11-2 0V8zm5-1a1 1 0 00-1 1v6a1 1 0 102 0V8a1 1 0 00-1-1z" clipRule="evenodd" />
                        </svg>
                        <span>Hapus</span>
                    </button>
                </div>

                <!-- Additional Information -->
                <div class="bg-white dark:bg-gray-800 rounded-2xl shadow-lg p-6">
                    <h2 class="text-xl font-semibold text-gray-900 dark:text-gray-100 mb-4">
                        Informasi Tambahan
                    </h2>
                    <div class="grid md:grid-cols-3 gap-4">
                        <div class="bg-gray-50 dark:bg-gray-700 p-4 rounded-xl">
                            <p class="text-sm text-gray-500 dark:text-gray-400 mb-1">Pajak Tahunan</p>
                            <p class="text-lg font-semibold text-gray-800 dark:text-gray-200">
                                {{ $this->car->pajak_tahunan }}
                            </p>
                        </div>
                        <div class="bg-gray-50 dark:bg-gray-700 p-4 rounded-xl">
                            <p class="text-sm text-gray-500 dark:text-gray-400 mb-1">Pajak 5 Tahun</p>
                            <p class="text-lg font-semibold text-gray-800 dark:text-gray-200">
                                {{ $this->car->pajak_5tahun }}
                            </p>
                        </div>
                        <div class="bg-gray-50 dark:bg-gray-700 p-4 rounded-xl">
                            <p class="text-sm text-gray-500 dark:text-gray-400 mb-1">Terakhir Service</p>
                            <p class="text-lg font-semibold text-gray-800 dark:text-gray-200">
                                {{ $this->car->last_service_date ? \Carbon\Carbon::parse($this->car->last_service_date)->format('d M Y') : 'Tidak tercatat' }}
                            </p>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
