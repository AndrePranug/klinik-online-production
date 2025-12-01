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
        // Membuat tabel 'doctors' untuk menyimpan data profil dokter
        Schema::create('doctors', function (Blueprint $table) {
            $table->id();                                                           // Primary key (auto increment)
            $table->foreignId('user_id')->constrained()->onDelete('cascade');       // Foreign key ke tabel users (1 user = 1 dokter)
                                                                                    // onDelete('cascade'): jika user dihapus, data dokter ikut terhapus
            $table->foreignId('specialization_id')->constrained()->onDelete('cascade'); // Foreign key ke tabel specializations
                                                                                        // onDelete('cascade'): jika spesialisasi dihapus, dokter ikut terhapus
            $table->string('license_number')->unique();                             // Nomor izin praktik dokter (STR - harus unik)
            $table->text('bio')->nullable();                                        // Biografi/profil dokter (bisa kosong)
            $table->integer('experience_years')->default(0);                        // Tahun pengalaman praktik (default: 0 tahun)
            $table->decimal('consultation_fee', 10, 2);                             // Biaya konsultasi (max: 99,999,999.99)
                                                                                    // 10 = total digit, 2 = digit desimal
            $table->string('photo')->nullable();                                    // Path/nama file foto profil dokter (bisa kosong)
            $table->timestamps();                                                   // Menambahkan created_at & updated_at otomatis
        });
    }

    /**
     * Reverse the migrations.
     * Method ini dijalankan saat menjalankan command: php artisan migrate:rollback
     */
    public function down(): void
    {
        Schema::dropIfExists('doctors'); // Hapus tabel doctors saat rollback
    }
};