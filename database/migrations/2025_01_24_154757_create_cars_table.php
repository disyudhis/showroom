<?php

use App\Models\Customer;
use App\Models\Customers;
use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::create('cars', function (Blueprint $table) {
            $table->id();
            $table->string('no_mesin')->nullable();
            $table->string('brand')->nullable();
            $table->unsignedBigInteger('customers_id')->nullable();
            $table->foreign('customers_id')->references('id')->on('customers')->onDelete('cascade');
            $table->date('pajak_tahunan')->nullable();
            $table->date('pajak_5tahun')->nullable();
            $table->string('no_polisi')->nullable();
            $table->year('tahun_pembuatan')->nullable();
            $table->date('last_service_date')->nullable();
            $table->date('odo_service')->nullable();
            $table->string('odo')->nullable();
            $table->string('nama_mobil')->nullable();
            $table->text('deskripsi')->nullable();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('cars');
    }
};
