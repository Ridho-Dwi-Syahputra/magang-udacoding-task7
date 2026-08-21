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
            ['title' => 'Setup project Laravel', 'status' => 'done', 'description' => 'Install Laravel dan konfigurasi database.', 'due_date' => '2026-08-18'],
            ['title' => 'Bikin migration tabel tasks', 'status' => 'done', 'description' => 'Kolom title, description, status, due_date.', 'due_date' => '2026-08-19'],
            ['title' => 'Rapikan response JSON pakai API Resource', 'status' => 'progress', 'description' => 'Biar bentuk responsenya konsisten di semua endpoint.', 'due_date' => '2026-08-22'],
            ['title' => 'Testing semua endpoint di Postman', 'status' => 'progress', 'description' => null, 'due_date' => '2026-08-23'],
            ['title' => 'Tambah endpoint login pakai Sanctum', 'status' => 'todo', 'description' => 'Persiapan buat Task 8.', 'due_date' => '2026-08-24'],
            ['title' => 'Tulis README dan contoh curl', 'status' => 'todo', 'description' => null, 'due_date' => null],
        ];

        foreach ($tasks as $task) {
            Task::firstOrCreate(['title' => $task['title']], $task);
        }
    }
}
