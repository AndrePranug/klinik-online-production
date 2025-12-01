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
        // Membuat tabel 'appointments' untuk menyimpan data janji temu pasien dengan dokter
        Schema::create('appointments', function (Blueprint $table) {
            $table->id();                                                       // Primary key (auto increment)
            $table->foreignId('user_id')->constrained()->onDelete('cascade');   // Foreign key ke tabel users (pasien yang booking)
                                                                                // onDelete('cascade'): jika user dihapus, appointment ikut terhapus
            $table->foreignId('doctor_id')->constrained()->onDelete('cascade'); // Foreign key ke tabel doctors (dokter yang dituju)
                                                                                // onDelete('cascade'): jika dokter dihapus, appointment ikut terhapus
            $table->string('queue_number');                                     // Nomor antrian (contoh: A001, B002, atau 001, 002)
            $table->date('appointment_date');                                   // Tanggal janji temu (contoh: 2024-12-15)
            $table->time('appointment_time');                                   // Waktu janji temu (contoh: 09:00:00)
            $table->enum('status', ['pending', 'confirmed', 'completed', 'cancelled'])->default('pending');
                                                                                // Status appointment:
                                                                                // - pending: Menunggu konfirmasi
                                                                                // - confirmed: Sudah dikonfirmasi
                                                                                // - completed: Sudah selesai konsultasi
                                                                                // - cancelled: Dibatalkan
            $table->text('complaint')->nullable();                              // Keluhan pasien (bisa kosong, diisi saat booking)
            $table->text('diagnosis')->nullable();                              // Diagnosis dokter (bisa kosong, diisi setelah konsultasi)
            $table->text('prescription')->nullable();                           // Resep obat dari dokter (bisa kosong, diisi setelah konsultasi)
            $table->timestamps();                                               // Menambahkan created_at & updated_at otomatis
        });
    }

    /**
     * Reverse the migrations.
     * Method ini dijalankan saat menjalankan command: php artisan migrate:rollback
     */
    public function down(): void
    {
        Schema::dropIfExists('appointments'); // Hapus tabel appointments saat rollback
    }
};