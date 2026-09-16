<?php

namespace Database\Seeders;

use App\Models\Task;
use App\Models\User;
use Illuminate\Database\Seeder;

class DatabaseSeeder extends Seeder
{
    public function run(): void
    {
        User::updateOrCreate(
            ['email' => 'ridho@udacoding.test'],
            ['name' => 'Ridho Dwi Syahputra', 'password' => 'password123'],
        );

        $tasks = [
            ['title' => 'Task 1: Instalasi dan Konfigurasi Awal', 'status' => 'done', 'description' => 'Melakukan instalasi framework dan konfigurasi environment.', 'due_date' => '2026-09-10'],
            ['title' => 'Task 2: Pembuatan Skema Database', 'status' => 'done', 'description' => 'Merancang dan mengimplementasikan tabel pada basis data.', 'due_date' => '2026-09-12'],
            ['title' => 'Task 3: Implementasi API Endpoint', 'status' => 'progress', 'description' => 'Membuat rute dan controller untuk metode GET, POST, PUT, dan DELETE.', 'due_date' => '2026-09-15'],
            ['title' => 'Task 4: Pengujian API', 'status' => 'todo', 'description' => 'Melakukan pengujian seluruh endpoint menggunakan perangkat lunak klien.', 'due_date' => '2026-09-18'],
            ['title' => 'Task 5: Integrasi Autentikasi', 'status' => 'todo', 'description' => 'Menambahkan sistem login dan verifikasi token akses.', 'due_date' => '2026-09-20'],
            ['title' => 'Task 6: Penyusunan Dokumentasi', 'status' => 'todo', 'description' => 'Menyusun dokumen panduan penggunaan dan API referensi.', 'due_date' => '2026-09-25'],
        ];

        foreach ($tasks as $task) {
            Task::firstOrCreate(['title' => $task['title']], $task);
        }
    }
}
