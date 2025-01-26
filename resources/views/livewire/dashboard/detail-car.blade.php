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

$delete = function () {
    $this->car->delete();
    $this->car->images()->delete();
    $this->car->documents()->delete();
    $this->car->customer()->delete();
    return redirect()->route('dashboard');
};

?>

<div x-data="{ activeTab: 'details', activeDocTab: 'car_images' }" class="container mx-auto px-4 py-8">
    <div class="max-w-7xl mx-auto grid md:grid-cols-2 gap-8">
        <!-- Left Column: Image Sections -->
        <div class="space-y-8">
            <!-- Car Images Carousel -->
            <div>
                <h2 class="text-2xl font-bold mb-4 text-gray-900 dark:text-gray-100">Foto Mobil</h2>
                @include('components.image-carousel', [
                    'images' => $this->car->images,
                    'title' => $this->car->nama_mobil
                ])
            </div>

            <!-- Document Images Carousel -->
            <div>
                <h2 class="text-2xl font-bold mb-4 text-gray-900 dark:text-gray-100">Berkas Kendaraan</h2>
                @include('components.image-carousel', [
                    'images' => $this->car->documents,
                    'title' => 'Dokumen Kendaraan'
                ])
            </div>
        </div>

        <!-- Right Column: Car and Owner Details -->
        <div>
            <!-- Header with Back Button -->
            <div class="flex items-center mb-8">
                <button
                    onclick="window.history.back()"
                    class="mr-4 text-gray-600 dark:text-gray-300 hover:text-blue-500 dark:hover:text-blue-400 transition-colors"
                >
                    <i class="ri-arrow-left-line text-3xl"></i>
                </button>
                <h1 class="text-3xl font-bold text-gray-900 dark:text-gray-100">
                    {{ $this->car->nama_mobil }}
                </h1>
            </div>

            <!-- Interactive Tabs -->
            <div class="bg-white dark:bg-gray-800 rounded-2xl shadow-lg">
                <div class="border-b border-gray-200 dark:border-gray-700">
                    <nav class="-mb-px flex">
                        <button
                            @click="activeTab = 'details'"
                            class="w-1/3 py-4 text-center transition-colors"
                            :class="{
                                'text-blue-600 border-blue-600 border-b-2': activeTab === 'details',
                                'text-gray-500 dark:text-gray-400 hover:text-gray-700 dark:hover:text-gray-300': activeTab !== 'details'
                            }"
                        >
                            <i class="ri-car-line mr-2"></i>
                            Details
                        </button>
                        <button
                            @click="activeTab = 'owner'"
                            class="w-1/3 py-4 text-center transition-colors"
                            :class="{
                                'text-blue-600 border-blue-600 border-b-2': activeTab === 'owner',
                                'text-gray-500 dark:text-gray-400 hover:text-gray-700 dark:hover:text-gray-300': activeTab !== 'owner'
                            }"
                        >
                            <i class="ri-user-line mr-2"></i>
                            Pemilik
                        </button>
                        <button
                            @click="activeTab = 'additional'"
                            class="w-1/3 py-4 text-center transition-colors"
                            :class="{
                                'text-blue-600 border-blue-600 border-b-2': activeTab === 'additional',
                                'text-gray-500 dark:text-gray-400 hover:text-gray-700 dark:hover:text-gray-300': activeTab !== 'additional'
                            }"
                        >
                            <i class="ri-information-line mr-2"></i>
                            Tambahan
                        </button>
                    </nav>
                </div>

                <!-- Tab Content -->
                <div class="p-6">
                    <!-- Car Details Tab -->
                    <div x-show="activeTab === 'details'" class="space-y-4">
                        <div class="bg-gray-50 dark:bg-gray-700 p-4 rounded-xl">
                            <p class="text-sm text-gray-500 dark:text-gray-400 mb-1">Brand</p>
                            <p class="text-lg font-semibold text-gray-800 dark:text-gray-200">
                                {{ $this->car->brand }}
                            </p>
                        </div>
                        <div class="bg-gray-50 dark:bg-gray-700 p-4 rounded-xl">
                            <p class="text-sm text-gray-500 dark:text-gray-400 mb-1">Tahun Pembuatan</p>
                            <p class="text-lg font-semibold text-gray-800 dark:text-gray-200">
                                {{ $this->car->tahun_pembuatan }}
                            </p>
                        </div>
                        <div class="bg-gray-50 dark:bg-gray-700 p-4 rounded-xl">
                            <p class="text-sm text-gray-500 dark:text-gray-400 mb-1">Nomor Mesin</p>
                            <p class="text-lg font-semibold text-gray-800 dark:text-gray-200">
                                {{ $this->car->no_mesin }}
                            </p>
                        </div>
                        <div class="bg-gray-50 dark:bg-gray-700 p-4 rounded-xl">
                            <p class="text-sm text-gray-500 dark:text-gray-400 mb-1">Nomor Polisi</p>
                            <p class="text-lg font-semibold text-gray-800 dark:text-gray-200">
                                {{ $this->car->no_polisi }}
                            </p>
                        </div>
                        <div class="bg-gray-50 dark:bg-gray-700 p-4 rounded-xl">
                            <p class="text-sm text-gray-500 dark:text-gray-400 mb-1">Kilometer ODO</p>
                            <p class="text-lg font-semibold text-gray-800 dark:text-gray-200">
                                {{ $this->car->odo }} km
                            </p>
                        </div>
                    </div>

                    <!-- Owner Details Tab -->
                    <div x-show="activeTab === 'owner'" class="space-y-4">
                        <div class="bg-gray-50 dark:bg-gray-700 p-4 rounded-xl">
                            <p class="text-sm text-gray-500 dark:text-gray-400 mb-1">Nama Lengkap</p>
                            <p class="text-lg font-semibold text-gray-800 dark:text-gray-200">
                                {{ $this->car->customer->nama_lengkap }}
                            </p>
                        </div>
                        <div class="bg-gray-50 dark:bg-gray-700 p-4 rounded-xl">
                            <p class="text-sm text-gray-500 dark:text-gray-400 mb-1">Nomor Telepon</p>
                            <p class="text-lg font-semibold text-gray-800 dark:text-gray-200">
                                {{ $this->car->customer->no_hp }}
                            </p>
                        </div>
                        <div class="bg-gray-50 dark:bg-gray-700 p-4 rounded-xl">
                            <p class="text-sm text-gray-500 dark:text-gray-400 mb-1">Alamat</p>
                            <p class="text-lg font-semibold text-gray-800 dark:text-gray-200">
                                {{ $this->car->customer->alamat }}
                            </p>
                        </div>
                    </div>

                    <!-- Additional Information Tab -->
                    <div x-show="activeTab === 'additional'" class="space-y-4">
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

            <!-- Action Buttons -->
            <div class="mt-8 flex space-x-4">
                <a href="#" class="flex-1 btn btn-primary flex items-center justify-center space-x-2 py-3 bg-blue-500 text-white rounded-xl hover:bg-blue-600 transition-colors">
                    <i class="ri-edit-line"></i>
                    <span>Edit</span>
                </a>
                <button
                    wire:click="delete"
                    wire:confirm="Yakin ingin menghapus mobil ini?"
                    class="flex-1 btn btn-danger flex items-center justify-center space-x-2 py-3 bg-red-500 text-white rounded-xl hover:bg-red-600 transition-colors"
                >
                    <i class="ri-delete-bin-line"></i>
                    <span>Hapus</span>
                </button>
            </div>
        </div>
    </div>
</div>
