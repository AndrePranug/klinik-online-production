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
        // Membuat tabel 'schedules' untuk menyimpan jadwal praktik dokter
        Schema::create('schedules', function (Blueprint $table) {
            $table->id();                                                     // Primary key (auto increment)
            $table->foreignId('doctor_id')->constrained()->onDelete('cascade'); // Foreign key ke tabel doctors
                                                                              // onDelete('cascade'): jika dokter dihapus, jadwalnya ikut terhapus
            $table->string('day');                                            // Hari praktik (contoh: Senin, Selasa, Rabu, dst)
                                                                              // Bisa juga pakai angka: 0=Minggu, 1=Senin, ..., 6=Sabtu
            $table->time('start_time');                                       // Waktu mulai praktik (contoh: 08:00:00)
            $table->time('end_time');                                         // Waktu selesai praktik (contoh: 17:00:00)
            $table->integer('slot_duration')->default(30);                    // Durasi per slot konsultasi dalam menit (default: 30 menit)
                                                                              // Misal: 08:00-08:30 (slot 1), 08:30-09:00 (slot 2), dst
            $table->timestamps();                                             // Menambahkan created_at & updated_at otomatis
        });
    }

    /**
     * Reverse the migrations.
     * Method ini dijalankan saat menjalankan command: php artisan migrate:rollback
     */
    public function down(): void
    {
        Schema::dropIfExists('schedules'); // Hapus tabel schedules saat rollback
    }
};