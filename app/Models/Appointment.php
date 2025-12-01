<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Appointment extends Model
{
    // Kolom-kolom yang dapat diisi secara massal (mass assignment)
    protected $fillable = [
        'user_id',           // ID pengguna/pasien yang membuat appointment
        'doctor_id',         // ID dokter yang dituju
        'queue_number',      // Nomor antrian pasien
        'appointment_date',  // Tanggal janji temu
        'appointment_time',  // Waktu janji temu
        'status',            // Status appointment (pending, confirmed, completed, cancelled)
        'complaint',         // Keluhan pasien
        'diagnosis',         // Diagnosis dari dokter
        'prescription',      // Resep obat yang diberikan
    ];

    // Mengkonversi tipe data kolom tertentu
    protected function casts(): array
    {
        return [
            'appointment_date' => 'date',           // Mengubah appointment_date menjadi objek Carbon Date
            'appointment_time' => 'datetime:H:i',   // Mengubah appointment_time menjadi format jam:menit (contoh: 14:30)
        ];
    }

    // Relasi: Appointment dimiliki oleh satu User (pasien)
    // Satu appointment milik satu user/pasien
    public function user()
    {
        return $this->belongsTo(User::class);
    }

    // Relasi: Appointment dimiliki oleh satu Doctor
    // Satu appointment ditangani oleh satu dokter
    public function doctor()
    {
        return $this->belongsTo(Doctor::class);
    }
}