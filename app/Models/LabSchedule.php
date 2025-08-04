<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class LabSchedule extends Model
{
    protected $fillable = [
        'day',
        'hour',
        'subject',
        'class',
        'teacher',
        'is_fixed'
    ];

    protected $casts = [
        'is_fixed' => 'boolean',
    ];

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
}
