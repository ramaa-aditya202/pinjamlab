<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use App\Models\User;
use App\Models\LabSchedule;
use Illuminate\Support\Facades\Hash;

class AdminSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // Buat user admin
        User::create([
            'name' => 'Administrator',
            'email' => 'admin@lab.com',
            'password' => Hash::make('password'),
            'role' => 'admin',
        ]);

        // Buat user guru contoh
        User::create([
            'name' => 'Pak Ahmad',
            'email' => 'ahmad@lab.com',
            'password' => Hash::make('password'),
            'role' => 'guru',
        ]);

        User::create([
            'name' => 'Bu Sari',
            'email' => 'sari@lab.com',
            'password' => Hash::make('password'),
            'role' => 'guru',
        ]);

        // Buat beberapa jadwal pakem contoh
        $schedules = [
            [
                'day' => 'senin',
                'hour' => 1,
                'subject' => 'Pemrograman Web',
                'class' => 'XII RPL 1',
                'teacher' => 'Pak Budi',
                'is_fixed' => true,
            ],
            [
                'day' => 'senin',
                'hour' => 3,
                'subject' => 'Basis Data',
                'class' => 'XI RPL 2',
                'teacher' => 'Bu Ani',
                'is_fixed' => true,
            ],
            [
                'day' => 'selasa',
                'hour' => 2,
                'subject' => 'Jaringan Komputer',
                'class' => 'XI TKJ 1',
                'teacher' => 'Pak Candra',
                'is_fixed' => true,
            ],
            [
                'day' => 'rabu',
                'hour' => 4,
                'subject' => 'Pemrograman Mobile',
                'class' => 'XII RPL 2',
                'teacher' => 'Bu Desi',
                'is_fixed' => true,
            ],
            [
                'day' => 'kamis',
                'hour' => 1,
                'subject' => 'Sistem Operasi',
                'class' => 'XI TKJ 2',
                'teacher' => 'Pak Eko',
                'is_fixed' => true,
            ],
            [
                'day' => 'jumat',
                'hour' => 2,
                'subject' => 'Multimedia',
                'class' => 'XII MM 1',
                'teacher' => 'Bu Fitri',
                'is_fixed' => true,
            ],
        ];

        foreach ($schedules as $schedule) {
            LabSchedule::create($schedule);
        }
    }
}
