<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Schedule extends Model
{
    // Kolom-kolom yang dapat diisi secara massal (mass assignment)
    protected $fillable = [
        'doctor_id',      // ID dokter yang memiliki jadwal praktik ini
        'day',            // Hari praktik (contoh: Senin, Selasa, atau 0-6 untuk Sunday-Saturday)
        'start_time',     // Waktu mulai praktik (contoh: 08:00)
        'end_time',       // Waktu selesai praktik (contoh: 17:00)
        'slot_duration',  // Durasi per slot konsultasi dalam menit (contoh: 30 menit per pasien)
    ];

    // Mengkonversi tipe data kolom tertentu
    protected function casts(): array
    {
        return [
            'start_time' => 'datetime:H:i',  // Mengubah start_time menjadi format jam:menit (contoh: 08:00)
            'end_time' => 'datetime:H:i',    // Mengubah end_time menjadi format jam:menit (contoh: 17:00)
        ];
    }

    // Relasi: Schedule dimiliki oleh satu Doctor
    // Satu jadwal praktik dimiliki oleh satu dokter
    public function doctor()
    {
        return $this->belongsTo(Doctor::class);
    }
}