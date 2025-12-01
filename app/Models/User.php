<?php

namespace App\Models;

use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Spatie\Permission\Traits\HasRoles; // Package untuk manajemen role & permission

class User extends Authenticatable
{
    // Notifiable: untuk notifikasi (email, SMS, dll)
    // HasRoles: menambahkan fitur role & permission dari package Spatie
    use Notifiable, HasRoles;

    // Kolom-kolom yang dapat diisi secara massal (mass assignment)
    protected $fillable = [
        'name',           // Nama lengkap user
        'email',          // Email user (untuk login)
        'password',       // Password user (terenkripsi)
        'phone',          // Nomor telepon user
        'date_of_birth',  // Tanggal lahir user
        'gender',         // Jenis kelamin (contoh: male, female)
        'address',        // Alamat lengkap user
    ];

    // Kolom yang disembunyikan saat data dikonversi ke array/JSON
    // (untuk keamanan, agar password tidak terlihat)
    protected $hidden = [
        'password',        // Password tidak ditampilkan di response
        'remember_token',  // Token untuk fitur "remember me" tidak ditampilkan
    ];

    // Mengkonversi tipe data kolom tertentu
    protected function casts(): array
    {
        return [
            'email_verified_at' => 'datetime',  // Waktu verifikasi email menjadi objek Carbon
            'password' => 'hashed',             // Password otomatis di-hash saat disimpan
            'date_of_birth' => 'date',          // Tanggal lahir menjadi objek Carbon Date
        ];
    }

    // Relasi: User memiliki satu Doctor (jika user adalah dokter)
    // Satu user bisa menjadi dokter dengan data profil dokter
    public function doctor()
    {
        return $this->hasOne(Doctor::class);
    }

    // Relasi: User memiliki banyak Appointment (jika user adalah pasien)
    // Satu user/pasien bisa membuat banyak janji temu
    public function appointments()
    {
        return $this->hasMany(Appointment::class);
    }

    // Method untuk cek apakah user adalah Admin
    // Menggunakan Spatie Permission package
    public function isAdmin()
    {
        return $this->hasRole('admin');
    }

    // Method untuk cek apakah user adalah Doctor
    public function isDoctor()
    {
        return $this->hasRole('doctor');
    }

    // Method untuk cek apakah user adalah Patient/Pasien
    public function isPatient()
    {
        return $this->hasRole('patient');
    }
}