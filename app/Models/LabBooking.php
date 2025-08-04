<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class LabBooking extends Model
{
    protected $fillable = [
        'user_id',
        'day',
        'hour',
        'teacher_name',
        'class',
        'subject',
        'status',
        'notes'
    ];

    // Relasi ke User
    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    // Helper method untuk mendapatkan nama hari dalam bahasa Indonesia
    public function getDayNameAttribute()
    {
        $days = [
            'senin' => 'Senin',
            'selasa' => 'Selasa',
            'rabu' => 'Rabu',
            'kamis' => 'Kamis',
            'jumat' => 'Jumat'
        ];
        
        return $days[$this->day] ?? $this->day;
    }

    // Helper method untuk mendapatkan status dalam bahasa Indonesia
    public function getStatusNameAttribute()
    {
        $statuses = [
            'pending' => 'Menunggu Persetujuan',
            'approved' => 'Disetujui',
            'rejected' => 'Ditolak'
        ];
        
        return $statuses[$this->status] ?? $this->status;
    }
}
