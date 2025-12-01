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
        // Membuat tabel 'users' untuk menyimpan data pengguna
        Schema::create('users', function (Blueprint $table) {
            $table->id();                                    // Primary key (auto increment)
            $table->string('name');                          // Nama lengkap user
            $table->string('email')->unique();               // Email user (harus unik, tidak boleh duplikat)
            $table->timestamp('email_verified_at')->nullable(); // Waktu verifikasi email (bisa kosong)
            $table->string('password');                      // Password user (terenkripsi)
            $table->rememberToken();                         // Token untuk fitur "remember me" saat login
            $table->timestamps();                            // Menambahkan created_at & updated_at otomatis
        });

        // Membuat tabel 'password_reset_tokens' untuk menyimpan token reset password
        Schema::create('password_reset_tokens', function (Blueprint $table) {
            $table->string('email')->primary();             // Email sebagai primary key
            $table->string('token');                        // Token unik untuk reset password
            $table->timestamp('created_at')->nullable();    // Waktu token dibuat
        });

        // Membuat tabel 'sessions' untuk menyimpan sesi login user
        // (digunakan jika config session driver menggunakan database)
        Schema::create('sessions', function (Blueprint $table) {
            $table->string('id')->primary();                // ID session sebagai primary key
            $table->foreignId('user_id')->nullable()->index(); // ID user yang login (bisa kosong untuk guest)
            $table->string('ip_address', 45)->nullable();   // IP address user (support IPv4 & IPv6)
            $table->text('user_agent')->nullable();         // Informasi browser/device user
            $table->longText('payload');                    // Data session yang disimpan (terenkripsi)
            $table->integer('last_activity')->index();      // Timestamp aktivitas terakhir (untuk cek session expired)
        });
    }

    /**
     * Reverse the migrations.
     * Method ini dijalankan saat menjalankan command: php artisan migrate:rollback
     * Menghapus tabel-tabel yang dibuat di method up()
     */
    public function down(): void
    {
        Schema::dropIfExists('users');                      // Hapus tabel users
        Schema::dropIfExists('password_reset_tokens');      // Hapus tabel password_reset_tokens
        Schema::dropIfExists('sessions');                   // Hapus tabel sessions
    }
};