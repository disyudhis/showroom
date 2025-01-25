<?php

namespace Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\Model>
 */
class CarFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            'nama_mobil' => '',
            'image' => '',
            'deskripsi' => '',
            'no_mesin' => '',
            'pajak_tahunan' => '',
            'pajak_5tahunan' => '',
            'no_polisi' => '',
            'tahun_pembuatan' => '',
            'last_service_date' => '',
            'odo' => '',
            'brand' => '',
        ];
    }
}
