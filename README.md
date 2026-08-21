# Task API - Laravel REST API

Task 7 magang Udacoding Batch 21. REST API buat manajemen task, dibikin pakai Laravel. Belum ada proteksi auth di endpoint task-nya, itu bagian Task 8. Yang sudah ada di sini cuma endpoint login yang ngeluarin token Sanctum.

## Stack

| Bagian | Dipakai |
|---|---|
| Framework | Laravel 12 |
| PHP | 8.2 |
| Database | SQLite |
| Token | Laravel Sanctum 4 |

Catatan versi: brief task nyebut Laravel 11, tapi semua rilis 11.x sekarang kena security advisory dan diblokir Composer. Jadi dipakai Laravel 12, yang API dan cara kerjanya sama persis untuk kebutuhan task ini.

## Setup

```bash
composer install
cp .env.example .env
php artisan key:generate
touch database/database.sqlite
php artisan migrate --seed
php artisan serve
```

Server jalan di `http://127.0.0.1:8000`.

Akun hasil seeder:

```
email    : ridho@udacoding.test
password : password123
```

## Endpoint

| Method | Endpoint | Auth | Keterangan |
|---|---|---|---|
| GET | `/api/tasks` | tidak | List semua task |
| GET | `/api/tasks/{id}` | tidak | Detail satu task |
| POST | `/api/tasks` | tidak | Buat task baru |
| PUT | `/api/tasks/{id}` | tidak | Update task |
| PATCH | `/api/tasks/{id}` | tidak | Update sebagian field |
| DELETE | `/api/tasks/{id}` | tidak | Hapus task |
| POST | `/api/login` | tidak | Login, balikin token |
| GET | `/api/me` | ya | Data user pemilik token |
| POST | `/api/logout` | ya | Cabut token yang lagi dipakai |

`GET /api/tasks` nerima dua query opsional: `status` (todo, progress, done) dan `q` buat cari di judul.

## Field task

| Field | Tipe | Aturan |
|---|---|---|
| title | string | wajib waktu POST, maks 255 |
| description | text | boleh kosong |
| status | enum | todo, progress, done. Default todo |
| due_date | date | boleh kosong |

## Contoh pakai curl

Ambil daftar task:

```bash
curl -H "Accept: application/json" http://127.0.0.1:8000/api/tasks
```

Buat task baru:

```bash
curl -X POST http://127.0.0.1:8000/api/tasks \
  -H "Accept: application/json" \
  -H "Content-Type: application/json" \
  -d '{"title":"Belajar Sanctum","status":"todo","due_date":"2026-09-01"}'
```

Update status doang:

```bash
curl -X PUT http://127.0.0.1:8000/api/tasks/1 \
  -H "Accept: application/json" \
  -H "Content-Type: application/json" \
  -d '{"status":"done"}'
```

Login dan pakai tokennya:

```bash
curl -X POST http://127.0.0.1:8000/api/login \
  -H "Accept: application/json" \
  -H "Content-Type: application/json" \
  -d '{"email":"ridho@udacoding.test","password":"password123"}'

curl http://127.0.0.1:8000/api/me \
  -H "Accept: application/json" \
  -H "Authorization: Bearer TOKEN_DARI_LOGIN"
```

## Bentuk response

Sukses:

```json
{
  "message": "Task berhasil dibuat.",
  "data": {
    "id": 7,
    "title": "Belajar Sanctum",
    "description": null,
    "status": "todo",
    "due_date": "2026-09-01",
    "created_at": "2026-08-21T06:52:32+00:00",
    "updated_at": "2026-08-21T06:52:32+00:00"
  }
}
```

Validasi gagal, status 422:

```json
{
  "message": "Judul task wajib diisi. (and 1 more error)",
  "errors": {
    "title": ["Judul task wajib diisi."],
    "status": ["Status hanya boleh todo, progress, atau done."]
  }
}
```

ID nggak ketemu, status 404:

```json
{ "message": "Data yang dicari tidak ditemukan." }
```

## Catatan implementasi

Bentuk JSON-nya diseragamkan lewat `TaskResource`, jadi kolom internal database nggak bocor ke response dan formatnya konsisten di semua endpoint.

Handler 404 khusus API ditaruh di [bootstrap/app.php](bootstrap/app.php). Tanpa itu, ID yang nggak ketemu balikin halaman error HTML, yang bikin client API bingung.

Validasi POST dan PUT dipakai bareng di satu `TaskRequest`. Bedanya cuma di aturan `title`: `required` waktu POST, `sometimes` waktu update, biar field yang nggak dikirim nggak ikut kehapus.

---

Ridho Dwi Syahputra, Web Developer Intern Udacoding Batch 21
