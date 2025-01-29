<?php

use App\Models\Car;
use App\Models\Customer;
use App\Models\Customers;
use Livewire\Volt\Component;
use Livewire\WithFileUploads;
use Illuminate\Support\Facades\DB;

new class extends Component {
    use WithFileUploads;

    // Car attributes
    public $nama_mobil = '';
    public $deskripsi = '';
    public $no_mesin = '';
    public $pajak_tahunan = '';
    public $pajak_5tahun = '';
    public $no_polisi = '';
    public $tahun_pembuatan = '';
    public $last_service_date = '';
    public $odo = '';
    public $brand = '';
    public $odo_service = '';

    // Customer attributes
    public $customer_nama_lengkap = '';
    public $customer_no_hp = '';
    public $customer_alamat = '';

    // Image uploads
    public $images = [];
    public $vehicle_documents = [];

    // Validation rules
    public function rules()
    {
        return [
            'nama_mobil' => 'required|string|max:255',
            'no_mesin' => 'required|string|max:50',
            'no_polisi' => 'required|string|max:20',
            'tahun_pembuatan' => 'required|integer|min:1900|max:' . date('Y'),
            'last_service_date' => 'nullable|date',
            'customer_nama_lengkap' => 'required|string|max:255',
            'customer_no_hp' => 'required|string|max:20',
            'odo_service' => 'required',
            'images.*' => 'image|max:5120', // 5MB max per image
            'vehicle_documents.*' => 'image|max:10240', // 10MB max per document
        ];
    }

    public function save()
    {
        $this->validate();

        DB::transaction(function () {
            // Create or find customer
            $customer = Customers::firstOrCreate(
                ['no_telp' => $this->customer_no_hp],
                [
                    'nama_lengkap' => $this->customer_nama_lengkap,
                    'alamat' => $this->customer_alamat,
                    'no_telp' => $this->customer_no_hp,
                ],
            );

            // Create car
            $car = $customer->cars()->create([
                'nama_mobil' => $this->nama_mobil,
                'deskripsi' => $this->deskripsi,
                'no_mesin' => $this->no_mesin,
                'pajak_tahunan' => $this->pajak_tahunan,
                'pajak_5tahun' => $this->pajak_5tahun,
                'no_polisi' => $this->no_polisi,
                'tahun_pembuatan' => $this->tahun_pembuatan,
                'last_service_date' => $this->last_service_date,
                'odo' => $this->odo,
                'brand' => $this->brand,
                'odo_service' => $this->odo_service,
            ]);

            // Handle multiple image uploads
            foreach ($this->images as $image) {
                $path = $image->store('cars', 'public');
                $car->images()->create(['image' => $path]);
            }

            // Handle service document uploads
            foreach ($this->vehicle_documents as $document) {
                $path = $document->store('vehicle_documents', 'public');
                $car->documents()->create(['image' => $path]);
            }
        });

        // Reset form after successful submission
        $this->reset();
        session()->flash('success', 'Mobil berhasil ditambahkan!');
        return redirect()->to('/dashboard');
    }

    // Method to remove an image before upload
    public function removeImage($index)
    {
        array_splice($this->images, $index, 1);
    }

    // Method to remove a service document before upload
    public function removeVehicleDocuments($index)
    {
        array_splice($this->vehicle_documents, $index, 1);
    }
}; ?>

<div class="container mx-auto px-4 py-12">
    <form wire:submit="save" class="max-w-4xl mx-auto space-y-8">
        <div class="bg-white dark:bg-gray-800 shadow-2xl rounded-2xl p-8">
            <h2 class="text-3xl font-extrabold text-gray-900 dark:text-gray-100 mb-6 border-b-2 border-blue-500 pb-3">
                Tambah Mobil Baru
            </h2>

            <!-- Car Information Section -->
            <div class="grid md:grid-cols-2 gap-6">
                <div>
                    <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">
                        Nama Mobil
                    </label>
                    <input type="text" wire:model="nama_mobil"
                        class="w-full px-4 py-2 border border-gray-300 dark:border-gray-700
                               rounded-xl focus:ring-2 focus:ring-blue-500
                               bg-white dark:bg-gray-700 text-gray-900 dark:text-gray-200"
                        placeholder="Contoh: Honda Civic">
                    @error('nama_mobil')
                        <span class="text-red-500 text-sm mt-1">{{ $message }}</span>
                    @enderror
                </div>

                <div>
                    <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">
                        Brand
                    </label>
                    <input type="text" wire:model="brand"
                        class="w-full px-4 py-2 border border-gray-300 dark:border-gray-700
                               rounded-xl focus:ring-2 focus:ring-blue-500
                               bg-white dark:bg-gray-700 text-gray-900 dark:text-gray-200"
                        placeholder="Contoh: Honda">
                </div>

                <div>
                    <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">
                        Nomor Mesin
                    </label>
                    <input type="text" wire:model="no_mesin"
                        class="w-full px-4 py-2 border border-gray-300 dark:border-gray-700
                               rounded-xl focus:ring-2 focus:ring-blue-500
                               bg-white dark:bg-gray-700 text-gray-900 dark:text-gray-200"
                        placeholder="Nomor Mesin">
                    @error('no_mesin')
                        <span class="text-red-500 text-sm mt-1">{{ $message }}</span>
                    @enderror
                </div>

                <div>
                    <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">
                        Nomor Polisi
                    </label>
                    <input type="text" wire:model="no_polisi"
                        class="w-full px-4 py-2 border border-gray-300 dark:border-gray-700
                               rounded-xl focus:ring-2 focus:ring-blue-500
                               bg-white dark:bg-gray-700 text-gray-900 dark:text-gray-200"
                        placeholder="Nomor Polisi">
                    @error('no_polisi')
                        <span class="text-red-500 text-sm mt-1">{{ $message }}</span>
                    @enderror
                </div>

                <div>
                    <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">
                        Tahun Pembuatan
                    </label>
                    <input type="number" wire:model="tahun_pembuatan"
                        class="w-full px-4 py-2 border border-gray-300 dark:border-gray-700
                               rounded-xl focus:ring-2 focus:ring-blue-500
                               bg-white dark:bg-gray-700 text-gray-900 dark:text-gray-200"
                        placeholder="Tahun">
                    @error('tahun_pembuatan')
                        <span class="text-red-500 text-sm mt-1">{{ $message }}</span>
                    @enderror
                </div>

                <div>
                    <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">
                        Kilometer ODO
                    </label>
                    <input type="number" wire:model="odo"
                        class="w-full px-4 py-2 border border-gray-300 dark:border-gray-700
                               rounded-xl focus:ring-2 focus:ring-blue-500
                               bg-white dark:bg-gray-700 text-gray-900 dark:text-gray-200"
                        placeholder="Kilometer">
                </div>
            </div>

            <!-- Deskripsi dan Pajak -->
            <div class="mt-6 space-y-4">
                <div>
                    <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">
                        Deskripsi
                    </label>
                    <textarea wire:model="deskripsi" rows="4"
                        class="w-full px-4 py-2 border border-gray-300 dark:border-gray-700
                               rounded-xl focus:ring-2 focus:ring-blue-500
                               bg-white dark:bg-gray-700 text-gray-900 dark:text-gray-200"
                        placeholder="Deskripsi mobil"></textarea>
                </div>

                <div class="grid md:grid-cols-2 gap-6">
                    <div>
                        <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">
                            Pajak Tahunan
                        </label>
                        <input type="date" wire:model="pajak_tahunan"
                            class="w-full px-4 py-2 border border-gray-300 dark:border-gray-700
                                   rounded-xl focus:ring-2 focus:ring-blue-500
                                   bg-white dark:bg-gray-700 text-gray-900 dark:text-gray-200"
                            placeholder="Pajak Tahunan">
                    </div>

                    <div>
                        <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">
                            Pajak 5 Tahun
                        </label>
                        <input type="date" wire:model="pajak_5tahun"
                            class="w-full px-4 py-2 border border-gray-300 dark:border-gray-700
                                   rounded-xl focus:ring-2 focus:ring-blue-500
                                   bg-white dark:bg-gray-700 text-gray-900 dark:text-gray-200"
                            placeholder="Pajak 5 Tahun">
                    </div>
                </div>

                <div>
                    <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">
                        Tanggal Servis Terakhir
                    </label>
                    <input type="date" wire:model="last_service_date"
                        class="w-full px-4 py-2 border border-gray-300 dark:border-gray-700
                               rounded-xl focus:ring-2 focus:ring-blue-500
                               bg-white dark:bg-gray-700 text-gray-900 dark:text-gray-200"
                        placeholder="Tanggal Servis Terakhir">
                </div>
                <div>
                    <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">
                        Odo Servis Terakhir
                    </label>
                    <input type="date" wire:model="odo_service"
                        class="w-full px-4 py-2 border border-gray-300 dark:border-gray-700
                               rounded-xl focus:ring-2 focus:ring-blue-500
                               bg-white dark:bg-gray-700 text-gray-900 dark:text-gray-200"
                        placeholder="Odo Servis Terakhir">
                </div>
            </div>

            <!-- Image Upload -->
            <div class="mt-6">
                <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">
                    Foto Mobil (Bisa Multiple)
                </label>
                <div class="flex items-center justify-center w-full">
                    <label
                        class="flex flex-col border-4 border-dashed w-full h-32 hover:bg-gray-100 hover:border-blue-300 group">
                        <div class="flex flex-col items-center justify-center pt-7">
                            <svg class="w-10 h-10 text-gray-400 group-hover:text-blue-600" fill="none"
                                stroke="currentColor" viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                    d="M4 16l4.586-4.586a2 2 0 012.828 0L16 16m-2-2l1.586-1.586a2 2 0 012.828 0L20 14m-6-6h.01M6 20h12a2 2 0 002-2V6a2 2 0 00-2-2H6a2 2 0 00-2 2v12a2 2 0 002 2z">
                                </path>
                            </svg>
                            <p class="lowercase text-sm text-gray-400 group-hover:text-blue-600 pt-1 tracking-wider">
                                Select a photo
                            </p>
                        </div>
                        <input type="file" wire:model="images" multiple class="hidden" accept="image/*" />
                    </label>
                </div>
                @error('images.*')
                    <span class="text-red-500 text-sm mt-1">{{ $message }}</span>
                @enderror

                @if ($images)
                    <div class="mt-4 grid grid-cols-4 gap-4">
                        @foreach ($images as $index => $image)
                            <div class="relative">
                                <img src="{{ $image->temporaryUrl() }}" class="w-full h-24 object-cover rounded-lg">
                                <button type="button" wire:click="removeImage({{ $index }})"
                                    class="absolute top-0 right-0 bg-red-500 text-white p-1 rounded-full text-xs">
                                    X
                                </button>
                            </div>
                        @endforeach
                    </div>
                @endif
            </div>

            <div class="mt-6">
                <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">
                    Berkas Kendaraan (STNK, KIR, dll)
                </label>
                <div class="flex items-center justify-center w-full">
                    <label
                        class="flex flex-col border-4 border-dashed w-full h-32 hover:bg-gray-100 hover:border-blue-300 group">
                        <div class="flex flex-col items-center justify-center pt-7">
                            <svg class="w-10 h-10 text-gray-400 group-hover:text-blue-600" fill="none"
                                stroke="currentColor" viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                    d="M7 21h10a2 2 0 002-2V9.414a1 1 0 00-.293-.707l-5.414-5.414A1 1 0 0013.586 3H7a2 2 0 00-2 2v14a2 2 0 002 2z" />
                            </svg>
                            <p class="lowercase text-sm text-gray-400 group-hover:text-blue-600 pt-1 tracking-wider">
                                Unggah Berkas Kendaraan (STNK, KIR)
                            </p>
                        </div>
                        <input type="file" wire:model="vehicle_documents" multiple class="hidden" />
                    </label>
                </div>
                @error('vehicle_documents.*')
                    <span class="text-red-500 text-sm mt-1">{{ $message }}</span>
                @enderror

                @if ($vehicle_documents)
                    <div class="mt-4 space-y-2">
                        @foreach ($vehicle_documents as $index => $document)
                            <div class="flex items-center justify-between bg-gray-100 p-2 rounded-lg">
                                <span class="text-sm truncate">{{ $document->getClientOriginalName() }}</span>
                                <button type="button" wire:click="removeVehicleDocument({{ $index }})"
                                    class="text-red-500 text-xs">
                                    Hapus
                                </button>
                            </div>
                        @endforeach
                    </div>
                @endif
            </div>

            <!-- Customer Information Section -->
            <div class="mt-8 bg-white dark:bg-gray-800 rounded-2xl p-8">
                <h2 class="text-2xl font-bold text-gray-900 dark:text-gray-100 mb-6">
                    Informasi Pemilik
                </h2>

                <div class="grid md:grid-cols-2 gap-6">
                    <div>
                        <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">
                            Nama Lengkap
                        </label>
                        <input type="text" wire:model="customer_nama_lengkap"
                            class="w-full px-4 py-2 border border-gray-300 dark:border-gray-700
                               rounded-xl focus:ring-2 focus:ring-blue-500
                               bg-white dark:bg-gray-700 text-gray-900 dark:text-gray-200"
                            placeholder="Nama Pemilik">
                        @error('customer_nama_lengkap')
                            <span class="text-red-500 text-sm mt-1">{{ $message }}</span>
                        @enderror
                    </div>
                    <div>
                        <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">
                            Nomor HP
                        </label>
                        <input type="text" wire:model="customer_no_hp"
                            class="w-full px-4 py-2 border border-gray-300 dark:border-gray-700
                                       rounded-xl focus:ring-2 focus:ring-blue-500
                                       bg-white dark:bg-gray-700 text-gray-900 dark:text-gray-200"
                            placeholder="Nomor Telepon">
                        @error('customer_no_hp')
                            <span class="text-red-500 text-sm mt-1">{{ $message }}</span>
                        @enderror
                    </div>
                </div>

                <div class="col-span-full">
                    <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">
                        Alamat
                    </label>
                    <textarea wire:model="customer_alamat" rows="3"
                        class="w-full px-4 py-2 border border-gray-300 dark:border-gray-700
                                   rounded-xl focus:ring-2 focus:ring-blue-500
                                   bg-white dark:bg-gray-700 text-gray-900 dark:text-gray-200"
                        placeholder="Alamat Lengkap"></textarea>
                </div>
            </div>
        </div>

        <!-- Submit Button -->
        <div class="mt-8 flex justify-end">
            <button type="submit"
                class="px-8 py-3 bg-blue-600 text-white rounded-xl
                           hover:bg-blue-700 transition-all duration-300
                           transform hover:-translate-y-1 hover:scale-105
                           flex items-center space-x-2
                           font-semibold shadow-lg">
                <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5" viewBox="0 0 20 20" fill="currentColor">
                    <path fill-rule="evenodd"
                        d="M10 18a8 8 0 100-16 8 8 0 000 16zm3.707-8.707l-3-3a1 1 0 00-1.414 1.414L10.586 9H7a1 1 0 100 2h3.586l-1.293 1.293a1 1 0 101.414 1.414l3-3a1 1 0 000-1.414z"
                        clip-rule="evenodd" />
                </svg>
                <span>Simpan Data Mobil</span>
            </button>
        </div>
    </form>
</div>
