<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

// SVARGA Backend (Laravel) — tabel UMKM.
// Nama kolom disamakan dengan field yang sudah dipakai di sisi React
// (src/data/mockContent.js) supaya integrasi tidak perlu banyak mapping ulang.
return new class extends Migration
{
    public function up(): void
    {
        Schema::create('umkms', function (Blueprint $table) {
            $table->id();
            $table->string('business_name');
            $table->string('business_type')->nullable(); // "Makanan" | "Minuman Tradisional" | "Kerajinan" dst.
            $table->string('address')->nullable();
            $table->string('koridor_id')->nullable(); // relasi longgar ke id koridor di svarga-app (Firestore), bukan FK asli
            $table->unsignedInteger('distance_m')->nullable();
            $table->decimal('rating', 2, 1)->nullable();
            $table->string('image_url')->nullable();
            $table->decimal('latitude', 10, 7)->nullable();
            $table->decimal('longitude', 10, 7)->nullable();
            $table->boolean('is_active')->default(true);
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('umkms');
    }
};
