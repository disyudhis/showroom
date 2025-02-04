<?php

use App\Models\Cars;
use App\Models\Image;
use App\Models\Documents;
use function Livewire\Volt\{layout, mount, state, rules, usesFileUploads};
usesFileUploads();
layout('layouts.app');

state([
    'car' => [
        'nama_mobil' => '',
        'deskripsi' => '',
        'no_mesin' => '',
        'pajak_tahunan' => '',
        'pajak_5tahun' => '',
        'no_polisi' => '',
        'tahun_pembuatan' => '',
        'last_service_date' => '',
        'odo' => '',
        'odo_service' => '',
        'brand' => '',
    ],
    'customer' => [
        'nama_lengkap' => '',
        'alamat' => '',
        'no_telp' => '',
    ],
    'existingImages' => [],
    'existingDocuments' => [],
    'newCarImages' => [],
    'newDocuments' => [],
    'carId' => null,
]);

rules([
    'car.nama_mobil' => 'required',
    'car.brand' => 'required',
    'car.no_mesin' => 'required',
    'car.no_polisi' => 'required',
    'car.tahun_pembuatan' => 'required|numeric',
    'newCarImages.*' => 'image|max:5120', // 5MB Max
    'newDocuments.*' => 'image|max:5120',
]);

mount(function (Cars $car) {
    $this->carId = $car->id;
    $this->car = [
        'nama_mobil' => $car->nama_mobil,
        'deskripsi' => $car->deskripsi,
        'no_mesin' => $car->no_mesin,
        'pajak_tahunan' => $car->pajak_tahunan,
        'pajak_5tahun' => $car->pajak_5tahun,
        'no_polisi' => $car->no_polisi,
        'tahun_pembuatan' => $car->tahun_pembuatan,
        'last_service_date' => $car->last_service_date,
        'odo' => $car->odo,
        'odo_service' => $car->odo_service,
        'brand' => $car->brand,
        'status' => $car->status
    ];

    $this->customer = [
        'nama_lengkap' => $car->customer->nama_lengkap,
        'alamat' => $car->customer->alamat,
        'no_telp' => $car->customer->no_telp,
    ];

    $this->existingImages = $car->images
        ->map(function ($image) {
            return [
                'id' => $image->id,
                'url' => $image->url,
                'public_id' => $image->public_id,
            ];
        })
        ->toArray();

    $this->existingDocuments = $car->documents
        ->map(function ($doc) {
            return [
                'id' => $doc->id,
                'url' => $doc->url,
                'public_id' => $doc->public_id,
            ];
        })
        ->toArray();
});

$removeExistingImage = function ($imageId) {
    try {
        // Cari gambar di database
        $image = Image::findOrFail($imageId);

        // Hapus gambar dari Cloudinary menggunakan public_id
        if ($image->public_id) {
            cloudinary()->destroy($image->public_id);
        }

        // Hapus dari database
        $image->delete();

        // Update state
        $this->existingImages = array_filter($this->existingImages, function ($img) use ($imageId) {
            return $img['id'] !== $imageId;
        });

        session()->flash('success', 'Gambar berhasil dihapus');
    } catch (\Exception $e) {
        // Log error untuk debugging
        \Log::error('Error deleting image: ' . $e->getMessage());

        session()->flash('error', 'Gambar gagal dihapus: ' . $e->getMessage());
    }
};

$removeExistingDocument = function ($documentId) {
    try {
        $document = Documents::findOrFail($documentId);

        // Delete the physical file
        if ($document->public_id) {
            cloudinary()->destroy($document->public_id);
        }

        // Delete from database
        $document->delete();

        // Update the state
        $this->existingDocuments = array_filter($this->existingDocuments, function ($doc) use ($documentId) {
            return $doc['id'] !== $documentId;
        });

        session()->flash('success', 'Gambar berhasil dihapus');
    } catch (\Exception $e) {
        session()->flash('error', 'Gambar gagal dihapus');
    }
};

$save = function () {
    try {
        $this->validate();

        DB::beginTransaction();
        // Update car data
        $car = Cars::findOrFail($this->carId);
        $car->update($this->car);

        // Update customer data
        $customer = $car->customer;
        $customer->update($this->customer);

        // Handle new car images
        if ($this->newCarImages) {
            foreach ($this->newCarImages as $image) {
                $cloudinaryImage = cloudinary()->upload($image->getRealPath(), [
                    'folder' => 'cars',
                    'overwrite' => true,
                    'resource_type' => 'auto',
                ]);

                $car->images()->create([
                    'url' => $cloudinaryImage->getSecurePath(),
                    'public_id' => $cloudinaryImage->getPublicId(),
                    'car_id' => $car->id,
                ]);
            }
        }

        // Handle new documents
        if ($this->newDocuments) {
            foreach ($this->newDocuments as $document) {
                $cloudinaryImage = cloudinary()->upload($document->getRealPath(), [
                    'folder' => 'vehicle_documents',
                    'overwrite' => true,
                    'resource_type' => 'auto',
                ]);
                $car->documents()->create([
                    'url' => $cloudinaryImage->getSecurePath(),
                    'public_id' => $cloudinaryImage->getPublicId(),
                    'car_id' => $car->id,
                ]);
            }
        }

        DB::commit();

        // Reset file inputs
        $this->newCarImages = [];
        $this->newDocuments = [];

        // Refresh existing images and documents
        $this->existingImages = $car->images
            ->fresh()
            ->map(function ($image) {
                return [
                    'id' => $image->id,
                    'url' => $image->url,
                    'public_id' => $image->public_id,
                ];
            })
            ->toArray();

        $this->existingDocuments = $car->documents
            ->fresh()
            ->map(function ($doc) {
                return [
                    'id' => $doc->id,
                    'url' => $doc->url,
                    'public_id' => $doc->public_id,
                ];
            })
            ->toArray();

        return redirect()->route('dashboard.index')->with('success', 'Data berhasil diupdate');
    } catch (\Exception $e) {
        DB::rollBack();
        session()->flash('error', $e->getMessage());
    }
};

?>

<div class="max-w-7xl mx-auto p-4 sm:p-6 lg:p-8">
    <div class="bg-white dark:bg-gray-800 rounded-2xl shadow-lg">
        <form wire:submit="save" class="space-y-6">
            <!-- Header -->
            <div class="border-b border-gray-200 dark:border-gray-700 p-6">
                <div class="flex justify-between items-center">
                    <h2 class="text-2xl font-bold text-gray-900 dark:text-gray-100">Edit Mobil</h2>
                    <button type="button" onclick="history.back()"
                        class="text-gray-600 hover:text-gray-900 dark:text-gray-400 dark:hover:text-gray-100">
                        <i class="ri-close-line text-2xl"></i>
                    </button>
                </div>
            </div>

            <div class="p-6 space-y-8">
                <!-- Customer Information Section -->
                <div class="space-y-4">
                    <h3 class="text-lg font-semibold text-gray-900 dark:text-gray-100">Data Customer</h3>
                    <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                        <div class="space-y-2">
                            <label class="text-sm font-medium text-gray-700 dark:text-gray-300">Nama Lengkap</label>
                            <input wire:model="customer.nama_lengkap" type="text"
                                class="w-full rounded-lg border-gray-300 dark:border-gray-600 dark:bg-gray-700">
                        </div>
                        <div class="space-y-2">
                            <label class="text-sm font-medium text-gray-700 dark:text-gray-300">No. Telepon</label>
                            <input wire:model="customer.no_telp" type="text"
                                class="w-full rounded-lg border-gray-300 dark:border-gray-600 dark:bg-gray-700">
                        </div>
                        <div class="md:col-span-2 space-y-2">
                            <label class="text-sm font-medium text-gray-700 dark:text-gray-300">Alamat</label>
                            <textarea wire:model="customer.alamat" rows="3"
                                class="w-full rounded-lg border-gray-300 dark:border-gray-600 dark:bg-gray-700"></textarea>
                        </div>
                    </div>
                </div>

                <!-- Car Information Section -->
                <div class="space-y-4">
                    <h3 class="text-lg font-semibold text-gray-900 dark:text-gray-100">Data Kendaraan</h3>
                    <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6">
                        <div class="space-y-2">
                            <label class="text-sm font-medium text-gray-700 dark:text-gray-300">Nama Mobil</label>
                            <input wire:model="car.nama_mobil" type="text"
                                class="w-full rounded-lg border-gray-300 dark:border-gray-600 dark:bg-gray-700">
                        </div>

                        <div class="space-y-2">
                            <label class="text-sm font-medium text-gray-700 dark:text-gray-300">Brand</label>
                            <input wire:model="car.brand" type="text"
                                class="w-full rounded-lg border-gray-300 dark:border-gray-600 dark:bg-gray-700">
                        </div>

                        <div class="space-y-2">
                            <label class="text-sm font-medium text-gray-700 dark:text-gray-300">Deskripsi</label>
                            <textarea wire:model="car.deskripsi" rows="3"
                                class="w-full rounded-lg border-gray-300 dark:border-gray-600 dark:bg-gray-700"></textarea>
                        </div>

                        <div class="space-y-2">
                            <label class="text-sm font-medium text-gray-700 dark:text-gray-300">Nomor Mesin</label>
                            <input wire:model="car.no_mesin" type="text"
                                class="w-full rounded-lg border-gray-300 dark:border-gray-600 dark:bg-gray-700">
                        </div>

                        <div class="space-y-2">
                            <label class="text-sm font-medium text-gray-700 dark:text-gray-300">Nomor Polisi</label>
                            <input wire:model="car.no_polisi" type="text"
                                class="w-full rounded-lg border-gray-300 dark:border-gray-600 dark:bg-gray-700">
                        </div>

                        <div class="space-y-2">
                            <label class="text-sm font-medium text-gray-700 dark:text-gray-300">Tahun Pembuatan</label>
                            <input wire:model="car.tahun_pembuatan" type="number"
                                class="w-full rounded-lg border-gray-300 dark:border-gray-600 dark:bg-gray-700">
                        </div>

                        <div class="space-y-2">
                            <label class="text-sm font-medium text-gray-700 dark:text-gray-300">Kilometer ODO</label>
                            <input wire:model="car.odo" type="number"
                                class="w-full rounded-lg border-gray-300 dark:border-gray-600 dark:bg-gray-700">
                        </div>

                        <div class="space-y-2">
                            <label class="text-sm font-medium text-gray-700 dark:text-gray-300">ODO Service</label>
                            <input wire:model="car.odo_service" type="text"
                                class="w-full rounded-lg border-gray-300 dark:border-gray-600 dark:bg-gray-700">
                        </div>

                        <div class="space-y-2">
                            <label class="text-sm font-medium text-gray-700 dark:text-gray-300">Terakhir Service</label>
                            <input wire:model="car.last_service_date" type="date"
                                class="w-full rounded-lg border-gray-300 dark:border-gray-600 dark:bg-gray-700">
                        </div>

                        <div class="space-y-2">
                            <label class="text-sm font-medium text-gray-700 dark:text-gray-300">Pajak Tahunan</label>
                            <input wire:model="car.pajak_tahunan" type="date"
                                class="w-full rounded-lg border-gray-300 dark:border-gray-600 dark:bg-gray-700">
                        </div>

                        <div class="space-y-2">
                            <label class="text-sm font-medium text-gray-700 dark:text-gray-300">Pajak 5 Tahun</label>
                            <input wire:model="car.pajak_5tahun" type="date"
                                class="w-full rounded-lg border-gray-300 dark:border-gray-600 dark:bg-gray-700">
                        </div>
                        <div class="space-y-2">
                            <label class="text-sm font-medium text-gray-700 dark:text-gray-300">Status Kendaraan</label>
                            <div class="flex flex-col space-y-2">
                                <label class="inline-flex items-center">
                                    <input wire:model="car.status" type="radio" value="SOLD"
                                           class="w-4 h-4 text-blue-600 border-gray-300 focus:ring-blue-500 dark:border-gray-600 dark:bg-gray-700">
                                    <span class="ml-2 text-sm text-gray-700 dark:text-gray-300">TERJUAL</span>
                                </label>

                                <label class="inline-flex items-center">
                                    <input wire:model="car.status" type="radio" value="AVAILABLE"
                                           class="w-4 h-4 text-blue-600 border-gray-300 focus:ring-blue-500 dark:border-gray-600 dark:bg-gray-700">
                                    <span class="ml-2 text-sm text-gray-700 dark:text-gray-300">TERSEDIA</span>
                                </label>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Car Images Section -->
                <div class="space-y-4">
                    <h3 class="text-lg font-semibold text-gray-900 dark:text-gray-100">Berkas Kendaraan</h3>

                    <!-- Existing Documents -->
                    <div class="grid grid-cols-2 md:grid-cols-4 gap-4">
                        @foreach ($existingImages as $image)
                            <div
                                class="relative group aspect-video rounded-lg overflow-hidden bg-gray-100 dark:bg-gray-700">
                                <!-- Image -->
                                <x-cld-image public-id="{{ $image['public_id'] }}" class="w-full h-full object-cover" />

                                <!-- Hover Overlay -->
                                <div
                                    class="absolute inset-0 bg-black bg-opacity-0 group-hover:bg-opacity-30 transition-opacity">
                                    <button type="button" wire:click="removeExistingImage({{ $image['id'] }})"
                                        wire:loading.attr="disabled"
                                        class="absolute top-2 right-2 bg-red-500 text-white p-1 rounded-full opacity-0 group-hover:opacity-100 transition-opacity hover:bg-red-600">
                                        <i class="ri-delete-bin-line"></i>
                                    </button>
                                </div>

                                <!-- Loading Overlay -->
                                <div wire:loading wire:target="removeExistingImage({{ $image['id'] }})"
                                    class="absolute inset-0 bg-black bg-opacity-50 flex items-center justify-center transition-all">
                                    <div class="flex flex-col items-center space-y-2">
                                        <div
                                            class="w-8 h-8 border-4 border-white/30 border-t-white rounded-full animate-spin">
                                        </div>
                                        <span class="text-white text-sm">Menghapus...</span>
                                    </div>
                                </div>
                            </div>
                        @endforeach
                    </div>

                    <!-- Upload New images -->
                    <div class="mt-4">
                        <input type="file" wire:model="newCarImages" multiple accept="image/*"
                            class="block w-full text-sm text-gray-500 file:mr-4 file:py-2 file:px-4 file:rounded-full file:border-0 file:text-sm file:font-semibold file:bg-blue-50 file:text-blue-700 hover:file:bg-blue-100">
                    </div>
                </div>

                <!-- Documents Section -->
                <div class="space-y-4">
                    <h3 class="text-lg font-semibold text-gray-900 dark:text-gray-100">Berkas Kendaraan</h3>

                    <!-- Existing Documents -->
                    <div class="grid grid-cols-2 md:grid-cols-4 gap-4">
                        @foreach ($existingDocuments as $document)
                            <div
                                class="relative group aspect-video rounded-lg overflow-hidden bg-gray-100 dark:bg-gray-700">
                                <!-- Image -->
                                <x-cld-image public-id="{{ $document['public_id'] }}"
                                    class="w-full h-full object-cover" />

                                <!-- Hover Overlay -->
                                <div
                                    class="absolute inset-0 bg-black bg-opacity-0 group-hover:bg-opacity-30 transition-opacity">
                                    <button type="button" wire:click="removeExistingDocument({{ $document['id'] }})"
                                        wire:loading.attr="disabled"
                                        class="absolute top-2 right-2 bg-red-500 text-white p-1 rounded-full opacity-0 group-hover:opacity-100 transition-opacity hover:bg-red-600">
                                        <i class="ri-delete-bin-line"></i>
                                    </button>
                                </div>

                                <!-- Loading Overlay -->
                                <div wire:loading wire:target="removeExistingDocument({{ $document['id'] }})"
                                    class="absolute inset-0 bg-black bg-opacity-50 flex items-center justify-center transition-all">
                                    <div class="flex flex-col items-center space-y-2">
                                        <div
                                            class="w-8 h-8 border-4 border-white/30 border-t-white rounded-full animate-spin">
                                        </div>
                                        <span class="text-white text-sm">Menghapus...</span>
                                    </div>
                                </div>
                            </div>
                        @endforeach
                    </div>

                    <!-- Upload New Documents -->
                    <div class="mt-4">
                        <input type="file" wire:model="newDocuments" multiple accept="image/*"
                            class="block w-full text-sm text-gray-500 file:mr-4 file:py-2 file:px-4 file:rounded-full file:border-0 file:text-sm file:font-semibold file:bg-blue-50 file:text-blue-700 hover:file:bg-blue-100">
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

            <!-- Action Buttons -->
            <div class="flex justify-end space-x-4 p-6 border-t border-gray-200 dark:border-gray-700">
                <button type="button" onclick="history.back()"
                    class="px-6 py-2 border border-gray-300 rounded-lg text-gray-700 hover:bg-gray-50 dark:border-gray-600 dark:text-gray-300 dark:hover:bg-gray-700 transition-colors">
                    Cancel
                </button>

                <button type="submit" wire:click.prevent="save" wire:loading.attr="disabled" wire:target="save"
                    class="px-6 py-2 bg-blue-600 text-white rounded-lg hover:bg-blue-700 transition-colors relative overflow-hidden flex items-center justify-center">

                    <!-- Normal State -->
                    <span wire:loading.remove class="flex items-center space-x-2">
                        <span>Save Changes</span>
                    </span>

                    <!-- Loading State -->
                    <div wire:loading wire:target="save" class="flex items-center justify-center space-x-2">
                        <div class="w-4 h-4 border-2 border-white/50 border-t-white rounded-full animate-spin"></div>
                        <span>Saving...</span>
                    </div>
                </button>
            </div>
        </form>
    </div>
</div>
