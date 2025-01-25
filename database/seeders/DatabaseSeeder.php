<?php

namespace Database\Seeders;

use Carbon\Carbon;
use Hash;
// use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use App\Models\Cars;
use App\Models\User;
use Carbon\Traits\Date;
use App\Models\Customers;
use Illuminate\Database\Seeder;

class DatabaseSeeder extends Seeder
{
    /**
     * Seed the application's database.
     */
    public function run(): void
    {
        // User::factory(10)->create();

        User::factory()->create([
            'name' => 'Admin',
            'email' => 'admin@gmail.com',
            'password' => Hash::make('password')
        ]);

        $customer = Customers::create([
            'nama_lengkap' => 'Razzan Zaki Muhammad',
            'alamat' => 'Jalan Ikan Tongkol, Malang'
        ]);

        Cars::create([
            'nama_mobil' => 'Raize GR TSS 1.0 Turbo',
            'no_mesin' => '1KRA599047',
            'pajak_tahunan' => Carbon::parse('28-07-2025'),
            'pajak_5tahun' => Carbon::parse('28-07-2026'),
            'no_polisi' => 'N 1745 AAK',
            'tahun_pembuatan' => '2021',
            'last_service_date' => '28-07-2026',
            'odo' => '21.756',
            'brand' => 'Toyota',
            'customers_id' => $customer->id
        ]);

    }
}
