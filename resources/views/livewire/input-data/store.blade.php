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
    public $status = '';

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
            'images.*' => 'image|max:1024', // 1MB max per image
            'vehicle_documents.*' => 'image|max:1024', // 1MB max per document
            'status' => 'required',
        ];
    }

    public function messages()
    {
        return [
            'images.*.max' => 'Ukuran gambar tidak boleh lebih dari 1MB.',
            'vehicle_documents.*.max' => 'Ukuran dokumen tidak boleh lebih dari 1MB.',
        ];
    }

    public function save()
    {
        // Validasi data terlebih dahulu
        $validatedData = $this->validate();

        try {
            DB::transaction(function () use ($validatedData) {
                // Buat atau temukan customer
                $customer = Customers::firstOrCreate(
                    ['no_telp' => $this->customer_no_hp],
                    [
                        'nama_lengkap' => $this->customer_nama_lengkap,
                        'alamat' => $this->customer_alamat,
                        'no_telp' => $this->customer_no_hp,
                    ],
                );

                // Buat mobil
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
                    'status' => $this->status,
                ]);

                // Upload gambar mobil
                if (!empty($this->images)) {
                    $this->uploadImages($car, $this->images, 'cars', $car->images());
                }

                // Upload dokumen kendaraan
                if (!empty($this->vehicle_documents)) {
                    $this->uploadImages($car, $this->vehicle_documents, 'vehicle_documents', $car->documents());
                }
            });

            // Reset form dan redirect setelah berhasil
            $this->reset();
            session()->flash('success', 'Mobil berhasil ditambahkan!');
            return redirect()->to('/dashboard');
        } catch (\Exception $e) {
            // Tangani error yang mungkin terjadi
            DB::rollBack();
            session()->flash('error', 'Gagal menambahkan mobil: ' . $e->getMessage());
            \Log::error('Error adding car: ' . $e->getMessage());
        }
    }

    // Method baru untuk upload gambar/dokumen
    protected function uploadImages($car, $files, $folder, $relation)
    {
        foreach ($files as $file) {
            try {
                // Upload ke Cloudinary
                $cloudinaryImage = cloudinary()->upload($file->getRealPath(), [
                    'folder' => $folder,
                    'overwrite' => true,
                    'resource_type' => 'auto',
                ]);

                // Simpan informasi file
                $relation->create([
                    'url' => $cloudinaryImage->getSecurePath(),
                    'public_id' => $cloudinaryImage->getPublicId(),
                    'car_id' => $car->id,
                ]);
            } catch (\Exception $e) {
                // Log error untuk setiap file yang gagal diupload
                \Log::error("Failed to upload file in $folder: " . $e->getMessage());

                // Optional: Lanjutkan proses upload file lainnya jika satu file gagal
                continue;
            }
        }
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
                    <input type="text" wire:model="odo_service"
                        class="w-full px-4 py-2 border border-gray-300 dark:border-gray-700
                               rounded-xl focus:ring-2 focus:ring-blue-500
                               bg-white dark:bg-gray-700 text-gray-900 dark:text-gray-200"
                        placeholder="Odo Servis Terakhir">
                </div>
                <div>
                    <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-4">
                        Status Terjual
                    </label>
                    <div class="flex gap-4">
                        <!-- Available Option -->
                        <label class="relative flex-1">
                            <input type="radio" wire:model="status" name="status" value="available"
                                class="peer sr-only">
                            <div
                                class="w-full cursor-pointer rounded-xl border-2 border-gray-200 dark:border-gray-700 bg-white dark:bg-gray-800 p-4
                                        transition-all duration-300 ease-in-out
                                        hover:border-blue-400 dark:hover:border-blue-500 hover:shadow-md
                                        peer-checked:border-blue-500 peer-checked:bg-blue-50 dark:peer-checked:bg-blue-900/30
                                        peer-checked:shadow-lg">
                                <div class="flex items-center justify-between">
                                    <div class="flex items-center space-x-2">
                                        <i
                                            class="ri-checkbox-blank-circle-line text-xl text-gray-400 dark:text-gray-500
                                                transition-colors duration-300
                                                peer-checked:text-blue-500 group-hover:text-blue-400"></i>
                                        <span
                                            class="font-medium text-gray-600 dark:text-gray-300
                                                   transition-colors duration-300
                                                   group-hover:text-blue-500 peer-checked:text-blue-700 dark:peer-checked:text-blue-400">
                                            Available
                                        </span>
                                    </div>
                                </div>
                                <p class="mt-1 text-sm text-gray-500 dark:text-gray-400">Unit tersedia untuk dijual</p>
                            </div>
                        </label>

                        <!-- Sold Option -->
                        <label class="relative flex-1">
                            <input type="radio" wire:model="status" name="status" value="sold"
                                class="peer sr-only">
                            <div
                                class="w-full cursor-pointer rounded-xl border-2 border-gray-200 dark:border-gray-700 bg-white dark:bg-gray-800 p-4
                                        transition-all duration-300 ease-in-out
                                        hover:border-blue-400 dark:hover:border-blue-500 hover:shadow-md
                                        peer-checked:border-blue-500 peer-checked:bg-blue-50 dark:peer-checked:bg-blue-900/30
                                        peer-checked:shadow-lg">
                                <div class="flex items-center justify-between">
                                    <div class="flex items-center space-x-2">
                                        <i
                                            class="ri-checkbox-blank-circle-line text-xl text-gray-400 dark:text-gray-500
                                                transition-colors duration-300
                                                peer-checked:text-blue-500 group-hover:text-blue-400"></i>
                                        <span
                                            class="font-medium text-gray-600 dark:text-gray-300
                                                   transition-colors duration-300
                                                   group-hover:text-blue-500 peer-checked:text-blue-700 dark:peer-checked:text-blue-400">
                                            Sold
                                        </span>
                                    </div>
                                </div>
                                <p class="mt-1 text-sm text-gray-500 dark:text-gray-400">Unit sudah terjual</p>
                            </div>
                        </label>
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
                                <p
                                    class="lowercase text-sm text-gray-400 group-hover:text-blue-600 pt-1 tracking-wider">
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
                                    <img src="{{ $image->temporaryUrl() }}"
                                        class="w-full h-24 object-cover rounded-lg">
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
                                <p
                                    class="lowercase text-sm text-gray-400 group-hover:text-blue-600 pt-1 tracking-wider">
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
                        <div class="mt-4 grid grid-cols-4 gap-4">
                            @foreach ($vehicle_documents as $index => $image)
                                <div class="relative">
                                    <img src="{{ $image->temporaryUrl() }}"
                                        class="w-full h-24 object-cover rounded-lg">
                                    <button type="button" wire:click="removeVehicleDocuments({{ $index }})"
                                        class="absolute top-0 right-0 bg-red-500 text-white p-1 rounded-full text-xs">
                                        X
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
            <button type="submit" wire:click.prevent="save" wire:loading.attr="disabled" wire:target="save"
                class="w-full relative overflow-hidden group min-h-[48px] rounded-lg">
                <!-- Base Button -->
                <div class="absolute inset-0 bg-blue-600 group-hover:bg-blue-700 transition-colors"></div>

                <!-- Shine Effect -->
                <div
                    class="absolute inset-0 bg-gradient-to-r from-transparent via-white/10 to-transparent -translate-x-full group-hover:translate-x-full transition-transform duration-1000">
                </div>

                <!-- Content Container -->
                <div class="relative flex items-center justify-center px-6 py-3 text-white">
                    <!-- Normal State -->
                    <div wire:loading.remove wire:target="save"
                        class="flex items-center justify-center gap-2 transition-all duration-300">
                        <svg class="w-5 h-5 transition-transform group-hover:rotate-12 group-hover:scale-110"
                            xmlns="http://www.w3.org/2000/svg" viewBox="0 0 20 20" fill="currentColor">
                            <path fill-rule="evenodd"
                                d="M10 18a8 8 0 100-16 8 8 0 000 16zm3.707-8.707l-3-3a1 1 0 00-1.414 1.414L10.586 9H7a1 1 0 100 2h3.586l-1.293 1.293a1 1 0 101.414 1.414l3-3a1 1 0 000-1.414z"
                                clip-rule="evenodd" />
                        </svg>
                        <span class="font-medium">Simpan Data Mobil</span>
                    </div>

                    <!-- Loading State -->
                    <div wire:loading wire:target="save" class="flex gap-2">
                        <div class="w-5 h-5 border-2 border-t-transparent border-white rounded-full animate-spin">
                        </div>
                    </div>
                </div>
            </button>
        </div>
    </form>
</div>
