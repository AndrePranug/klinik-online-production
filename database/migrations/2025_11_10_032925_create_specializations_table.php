<?php
use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     * Method ini dijalankan saat menjalankan command: php artisan migrate
     */
    public function up(): void
    {
        // Membuat tabel 'specializations' untuk menyimpan data spesialisasi dokter
        Schema::create('specializations', function (Blueprint $table) {
            $table->id();                           // Primary key (auto increment)
            $table->string('name');                 // Nama spesialisasi (contoh: Dokter Umum )
            $table->text('description')->nullable(); // Deskripsi spesialisasi (bisa kosong)
            $table->timestamps();                   // Menambahkan created_at & updated_at otomatis
        });
    }

    /**
     * Reverse the migrations.
     * Method ini dijalankan saat menjalankan command: php artisan migrate:rollback
     */
    public function down(): void
    {
        Schema::dropIfExists('specializations'); // Hapus tabel specializations saat rollback
    }
};