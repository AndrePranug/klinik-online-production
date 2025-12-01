<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Doctor extends Model
{
    // Kolom-kolom yang dapat diisi secara massal (mass assignment)
    protected $fillable = [
        'user_id',            // ID user yang terkait dengan dokter (akun login dokter)
        'specialization_id',  // ID spesialisasi/keahlian dokter (contoh: Umum, Gigi, Jantung)
        'license_number',     // Nomor izin praktik dokter (STR - Surat Tanda Registrasi)
        'bio',                // Biografi/deskripsi singkat tentang dokter
        'experience_years',   // Jumlah tahun pengalaman praktik dokter
        'consultation_fee',   // Biaya konsultasi per kunjungan
        'photo',              // Path/URL foto profil dokter
    ];

    // Mengkonversi tipe data kolom tertentu
    protected function casts(): array
    {
        return [
            'consultation_fee' => 'decimal:2',  // Mengubah biaya konsultasi menjadi format desimal 2 digit (contoh: 150000.00)
        ];
    }

    // Relasi: Doctor dimiliki oleh satu User
    // Satu dokter terkait dengan satu akun user untuk login
    public function user()
    {
        return $this->belongsTo(User::class);
    }

    // Relasi: Doctor dimiliki oleh satu Specialization
    // Satu dokter memiliki satu spesialisasi keahlian
    public function specialization()
    {
        return $this->belongsTo(Specialization::class);
    }

    // Relasi: Doctor memiliki banyak Schedule
    // Satu dokter bisa memiliki banyak jadwal praktik
    public function schedules()
    {
        return $this->hasMany(Schedule::class);
    }

    // Relasi: Doctor memiliki banyak Appointment
    // Satu dokter bisa menangani banyak janji temu dengan pasien
    public function appointments()
    {
        return $this->hasMany(Appointment::class);
    }
}