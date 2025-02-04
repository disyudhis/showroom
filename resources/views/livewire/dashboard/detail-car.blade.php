<?php
use function Livewire\Volt\{layout, mount, state, computed};
use App\Models\Cars;

layout('layouts.app');

state([
    'car' => fn() => $car,
    'currentImageIndex' => 0,
    'hpp' => null,
    'hppForm' => [
        'items' => [['name' => '', 'price' => 0]],
    ],
    'showHppModal' => false,
]);

mount(function (Cars $car) {
    $this->car = $car;
    $this->hpp = $car->hpps()->first();

    if (empty($this->hppForm['items'])) {
        $this->hppForm['items'][] = [
            'name' => '',
            'price' => null,
        ];
    }
});

$addHppItem = function () {
    $this->hppForm['items'][] = ['name' => '', 'price' => null];
};

$removeHppItem = function ($index) {
    unset($this->hppForm['items'][$index]);
    $this->hppForm['items'] = array_values($this->hppForm['items']);
};

$openHppModal = function () {
    $this->hppForm['items'] = [
        [
            'name' => '',
            'price' => null,
        ],
    ];
    $this->showHppModal = true;
};

$closeHppModal = function () {
    $this->showHppModal = false;
    $this->hppForm['items'] = [
        [
            'name' => '',
            'price' => null,
        ],
    ];
};

$calculateTotal = computed(function () {
    return collect($this->hppForm['items'])->sum('price');
});

$createHpp = function () {
    $validated = $this->validate([
        'hppForm.items' => 'required|array|min:1',
        'hppForm.items.*.name' => 'required|string',
        'hppForm.items.*.price' => 'required|numeric|min:0',
    ]);

    try {
        $items = collect($validated['hppForm']['items'])
            ->map(function ($item) {
                return [
                    'name' => $item['name'],
                    'price' => floatval($item['price']),
                ];
            })
            ->all();

        $total = collect($items)->sum('price');

        $hpp = $this->car->hpps()->create([
            'deskripsi' => ['items' => $items],
            'total' => $total,
        ]);

        $this->hpp = $hpp;
        $this->closeHppModal();
        $this->dispatch('hpp-created');
        session()->flash('success', 'HPP berhasil dibuat');
    } catch (\Exception $e) {
        session()->flash('error', 'Gagal membuat HPP: ' . $e->getMessage());
    }
};

$delete = function () {
    try {
        foreach ($this->car->images as $image) {
            cloudinary()->destroy($image->public_id);
        }

        foreach ($this->car->documents as $document) {
            cloudinary()->destroy($document->public_id);
        }

        // Delete database records
        $this->car->images()->delete();
        $this->car->documents()->delete();
        $this->car->customer()->delete();
        $this->car->delete();

        return redirect()->route('dashboard.index')->with('success', 'Data mobil berhasil dihapus');
    } catch (\Exception $e) {
        session()->flash('error', 'Data mobil belum berhasil di hapus');
    }
};

?>

<div x-data="{ activeTab: 'details', activeDocTab: 'car_images' }" class="container mx-auto px-4 py-8">
    <div class="max-w-7xl mx-auto grid md:grid-cols-2 gap-8">
        <!-- Left Column: Image Sections -->
        <div class="space-y-8">
            <!-- Status Badge -->
            <div class="absolute top-4 right-4">
                <span class="px-4 py-2 rounded-full text-sm font-bold uppercase {{ $this->car->status_color }}">
                    {{ $this->car->status ?? 'Unknown' }}
                </span>
            </div>
            <!-- Car Images Carousel -->
            <div>
                <h2 class="text-2xl font-bold mb-4 text-gray-900 dark:text-gray-100">Foto Mobil</h2>
                @include('components.image-carousel', [
                    'images' => $this->car->images,
                    'title' => $this->car->nama_mobil,
                ])
            </div>

            <!-- Document Images Carousel -->
            <div>
                <h2 class="text-2xl font-bold mb-4 text-gray-900 dark:text-gray-100">Berkas Kendaraan</h2>
                @include('components.image-carousel', [
                    'images' => $this->car->documents,
                    'title' => 'Dokumen Kendaraan',
                ])
            </div>
        </div>

        <!-- Right Column: Car and Owner Details -->
        <div>
            <!-- Header with Back Button -->
            <div class="flex items-center mb-8">
                <button onclick="window.history.back()"
                    class="mr-4 text-gray-600 dark:text-gray-300 hover:text-blue-500 dark:hover:text-blue-400 transition-colors">
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
                        <button @click="activeTab = 'details'" class="w-1/3 py-4 text-center transition-colors"
                            :class="{
                                'text-blue-600 border-blue-600 border-b-2': activeTab === 'details',
                                'text-gray-500 dark:text-gray-400 hover:text-gray-700 dark:hover:text-gray-300': activeTab !== 'details'
                            }">
                            <i class="ri-car-line mr-2"></i>
                            Details
                        </button>
                        <button @click="activeTab = 'owner'" class="w-1/3 py-4 text-center transition-colors"
                            :class="{
                                'text-blue-600 border-blue-600 border-b-2': activeTab === 'owner',
                                'text-gray-500 dark:text-gray-400 hover:text-gray-700 dark:hover:text-gray-300': activeTab !== 'owner'
                            }">
                            <i class="ri-user-line mr-2"></i>
                            Pemilik
                        </button>
                        <button @click="activeTab = 'additional'" class="w-1/3 py-4 text-center transition-colors"
                            :class="{
                                'text-blue-600 border-blue-600 border-b-2': activeTab === 'additional',
                                'text-gray-500 dark:text-gray-400 hover:text-gray-700 dark:hover:text-gray-300': activeTab !== 'additional'
                            }">
                            <i class="ri-information-line mr-2"></i>
                            Tambahan
                        </button>
                        <button @click="activeTab = 'hpp'" class="w-1/3 py-4 text-center transition-colors"
                            :class="{
                                'text-blue-600 border-blue-600 border-b-2': activeTab === 'hpp',
                                'text-gray-500 dark:text-gray-400 hover:text-gray-700 dark:hover:text-gray-300': activeTab !== 'hpp'
                            }">
                            <i class="ri-information-line mr-2"></i>
                            HPP
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
                                {{ $this->car->customer->no_telp }}
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
                        <div class="bg-gray-50 dark:bg-gray-700 p-4 rounded-xl">
                            <p class="text-sm text-gray-500 dark:text-gray-400 mb-1">Odo Service</p>
                            <p class="text-lg font-semibold text-gray-800 dark:text-gray-200">
                                {{ $this->car->odo_service ? $this->car->odo_service : 'Tidak tercatat' }}
                            </p>
                        </div>
                    </div>
                    <div x-show="activeTab === 'hpp'" class="space-y-6">
                        @if ($hpp)
                            <div class="bg-white dark:bg-gray-800 rounded-xl shadow-lg overflow-hidden">
                                <div class="p-6">
                                    <h3 class="text-xl font-bold mb-4 text-gray-900 dark:text-white">Detail HPP</h3>

                                    <div class="space-y-4">
                                        @foreach ($hpp->deskripsi['items'] as $item)
                                            <div
                                                class="flex items-center justify-between p-4 bg-gray-50 dark:bg-gray-700 rounded-lg hover:bg-gray-100 dark:hover:bg-gray-600 transition-all">
                                                <div>
                                                    <p class="font-medium text-gray-800 dark:text-gray-200">
                                                        {{ $item['name'] }}</p>
                                                </div>
                                                <div class="text-right">
                                                    <p class="text-lg font-semibold text-blue-600 dark:text-blue-400">
                                                        Rp {{ number_format($item['price'], 0, ',', '.') }}
                                                    </p>
                                                </div>
                                            </div>
                                        @endforeach

                                        <div class="mt-6 p-4 bg-blue-50 dark:bg-blue-900/30 rounded-lg">
                                            <div class="flex justify-between items-center">
                                                <p class="text-lg font-bold text-gray-900 dark:text-white">Total HPP</p>
                                                <p class="text-2xl font-bold text-blue-600 dark:text-blue-400">
                                                    Rp {{ number_format($hpp->total, 0, ',', '.') }}
                                                </p>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        @else
                            <button @click="$wire.openHppModal()"
                                class="w-full group relative overflow-hidden px-6 py-4 bg-blue-600 text-white rounded-xl hover:bg-blue-700 transition-all transform hover:scale-[1.02] active:scale-[0.98] shadow-lg hover:shadow-xl">
                                <div
                                    class="absolute inset-0 bg-white/20 transform -skew-x-12 -translate-x-full group-hover:translate-x-full transition-transform duration-500">
                                </div>
                                <div class="flex items-center justify-center space-x-3">
                                    <i class="ri-add-line text-2xl"></i>
                                    <span class="text-lg font-semibold">Buat HPP Baru</span>
                                </div>
                                <div class="mt-2 text-sm text-blue-100">
                                    Klik untuk menambahkan data Harga Pokok Pembelian
                                </div>
                            </button>
                        @endif
                    </div>
                </div>
            </div>

            <!-- Action Buttons -->
            <div class="mt-8 flex space-x-4">
                <a href="{{ route('cars.edit', ['car' => $this->car->id]) }}" wire:navigate
                    class="flex-1 btn btn-primary flex items-center justify-center space-x-2 py-3 bg-blue-500 text-white rounded-xl hover:bg-blue-600 transition-colors">
                    <i class="ri-edit-line"></i>
                    <span>Edit</span>
                </a>
                <button wire:click="delete" wire:confirm="Yakin ingin menghapus mobil ini?"
                    class="flex-1 relative group overflow-hidden" wire:loading.class="cursor-not-allowed opacity-75"
                    wire:loading.attr="disabled">
                    <div
                        class="flex items-center justify-center space-x-2 py-3 px-4 bg-red-500 hover:bg-red-600 text-white rounded-xl transition-all transform group-hover:scale-[1.02] group-active:scale-[0.98]">
                        <div wire:loading.remove>
                            <i class="ri-delete-bin-line text-lg"></i>
                        </div>
                        <svg wire:loading aria-hidden="true" class="w-5 h-5" viewBox="0 0 100 101" fill="none"
                            xmlns="http://www.w3.org/2000/svg">
                            <path
                                d="M100 50.5908C100 78.2051 77.6142 100.591 50 100.591C22.3858 100.591 0 78.2051 0 50.5908C0 22.9766 22.3858 0.59082 50 0.59082C77.6142 0.59082 100 22.9766 100 50.5908ZM9.08144 50.5908C9.08144 73.1895 27.4013 91.5094 50 91.5094C72.5987 91.5094 90.9186 73.1895 90.9186 50.5908C90.9186 27.9921 72.5987 9.67226 50 9.67226C27.4013 9.67226 9.08144 27.9921 9.08144 50.5908Z"
                                fill="currentColor" fill-opacity="0.3" />
                            <path
                                d="M93.9676 39.0409C96.393 38.4038 97.8624 35.9116 97.0079 33.5539C95.2932 28.8227 92.871 24.3692 89.8167 20.348C85.8452 15.1192 80.8826 10.7238 75.2124 7.41289C69.5422 4.10194 63.2754 1.94025 56.7698 1.05124C51.7666 0.367541 46.6976 0.446843 41.7345 1.27873C39.2613 1.69328 37.813 4.19778 38.4501 6.62326C39.0873 9.04874 41.5694 10.4717 44.0505 10.1071C47.8511 9.54855 51.7191 9.52689 55.5402 10.0491C60.8642 10.7766 65.9928 12.5457 70.6331 15.2552C75.2735 17.9648 79.3347 21.5619 82.5849 25.841C84.9175 28.9121 86.7997 32.2913 88.1811 35.8758C89.083 38.2158 91.5421 39.6781 93.9676 39.0409Z"
                                fill="currentColor" />
                        </svg>
                        <span wire:loading.remove>Hapus</span>
                        <span wire:loading>Menghapus...</span>
                    </div>
                    <div
                        class="absolute inset-0 bg-white/20 transform -skew-x-12 -translate-x-full group-hover:translate-x-full transition-transform duration-500">
                    </div>
                </button>
            </div>
        </div>
    </div>
    @if (session('error'))
        <div x-data="{ show: true }" x-show="show" x-init="setTimeout(() => show = false, 3000)"
            class="fixed bottom-5 right-5 bg-red-500 text-white px-6 py-3 rounded-xl shadow-lg">
            {{ session('error') }}
        </div>
    @endif
    @if (session('success'))
        <div x-data="{ show: true }" x-show="show" x-init="setTimeout(() => show = false, 3000)"
            class="fixed bottom-5 right-5 bg-green-500 text-white px-6 py-3 rounded-xl shadow-lg">
            {{ session('success') }}
        </div>
    @endif

    {{--  --}}
    <div x-data="{ showModal: @entangle('showHppModal') }" x-show="showModal" x-cloak
        class="fixed inset-0 bg-black bg-opacity-50 z-50 flex items-center justify-center" x-transition>
        <div class="bg-white dark:bg-gray-800 rounded-xl p-6 w-full max-w-2xl mx-4"
            @click.away="$wire.closeHppModal()">

            <!-- Rest of your modal content remains the same -->
            <h2 class="text-2xl font-bold mb-4 text-gray-900 dark:text-gray-100">
                Buat HPP Baru
            </h2>

            <form wire:submit.prevent="createHpp">
                <!-- Dynamic Item Input -->
                <div class="space-y-4">
                    @foreach ($hppForm['items'] as $index => $item)
                        <div class="flex items-center space-x-2 bg-gray-50 dark:bg-gray-700 p-3 rounded-lg">
                            <div class="flex-1">
                                <label class="block text-sm text-gray-600 dark:text-gray-300 mb-1">
                                    Nama Item
                                </label>
                                <input type="text" wire:model="hppForm.items.{{ $index }}.name"
                                    placeholder="Contoh: Biaya Pembelian, Pajak, dll"
                                    class="w-full px-3 py-2 border rounded-lg dark:bg-gray-600 dark:text-white"
                                    required>
                            </div>

                            <div class="w-1/4">
                                <label class="block text-sm text-gray-600 dark:text-gray-300 mb-1">
                                    Harga
                                </label>
                                <input type="number" wire:model="hppForm.items.{{ $index }}.price"
                                    placeholder="Nominal"
                                    class="w-full px-3 py-2 border rounded-lg dark:bg-gray-600 dark:text-white"
                                    required>
                            </div>

                            @if ($index > 0)
                                <button type="button" wire:click="removeHppItem({{ $index }})"
                                    class="self-end text-red-500 hover:text-red-700">
                                    <i class="ri-delete-bin-line"></i>
                                </button>
                            @endif
                        </div>
                    @endforeach
                </div>

                <!-- Add Item Button -->
                <div class="mt-4">
                    <button type="button" wire:click="addHppItem"
                        class="btn btn-secondary flex items-center text-blue-600 hover:bg-blue-50 dark:hover:bg-gray-700">
                        <i class="ri-add-line mr-2"></i>
                        Tambah Item
                    </button>
                </div>
                <div class="mt-6 bg-gray-100 dark:bg-gray-700 p-4 rounded-lg">
                    <h3 class="text-lg font-semibold mb-2">Total Biaya</h3>
                    <div class="space-y-2">
                        @php
                            $total = 0;
                        @endphp
                        @foreach ($hppForm['items'] as $item)
                            @php
                                $itemTotal = $item['price'] ?? 0;
                                $total += $itemTotal;
                            @endphp
                            <div class="flex justify-between">
                                <span>{{ $item['name'] ?? 'Item' }}</span>
                                <span>Rp {{ number_format($itemTotal, 0, ',', '.') }}</span>
                            </div>
                        @endforeach
                        <div class="border-t pt-2 flex justify-between font-bold">
                            <span>Total</span>
                            <span>Rp {{ number_format($total, 0, ',', '.') }}</span>
                        </div>
                    </div>
                </div>
                <div class="flex space-x-4 mt-6">
                    <button type="button" @click="$wire.closeHppModal()"
                        class="flex-1 btn btn-secondary bg-gray-200 dark:bg-gray-600 text-gray-800 dark:text-white rounded-xl py-2">
                        Batal
                    </button>
                    <button type="submit"
                        class="flex-1 btn btn-primary bg-blue-500 text-white rounded-xl py-2 hover:bg-blue-600">
                        Simpan HPP
                    </button>
                </div>
            </form>
        </div>
    </div>

    @push('style')
        <style>
            @keyframes spin {
                from {
                    transform: rotate(0deg);
                }

                to {
                    transform: rotate(360deg);
                }
            }
        </style>
    @endpush
</div>
