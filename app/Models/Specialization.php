<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Specialization extends Model
{
    // Kolom-kolom yang dapat diisi secara massal (mass assignment)
    protected $fillable = [
        'name',         // Nama spesialisasi dokter (contoh: Dokter Umum, Dokter Gigi, Spesialis Jantung)
        'description'   // Deskripsi tentang spesialisasi tersebut
    ];

    // Relasi: Specialization memiliki banyak Doctor
    // Satu spesialisasi bisa dimiliki oleh banyak dokter
    // (contoh: spesialisasi "Dokter Gigi" bisa dimiliki oleh beberapa dokter)
    public function doctors()
    {
        return $this->hasMany(Doctor::class);
    }
}