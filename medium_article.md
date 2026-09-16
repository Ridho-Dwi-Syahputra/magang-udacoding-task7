Membangun RESTful API Tangguh dengan Laravel 11 dan Sanctum: Lebih dari Sekadar CRUD

Pengembangan backend modern menuntut API yang tidak hanya berfungsi untuk operasi dasar, tetapi juga memiliki standar keamanan, konsistensi respons, dan penanganan kesalahan yang baik. 

Dalam artikel ini, kita akan membahas cara membangun RESTful API yang aman dan konsisten menggunakan framework Laravel 11. Fokus utama kita adalah manajemen autentikasi dengan Sanctum, pencegahan eksploitasi data, dan standarisasi penanganan error.

Kode sumber lengkap dari proyek ini dapat Anda temukan di GitHub:
https://github.com/Ridho-Dwi-Syahputra/magang-udacoding-task7


1. Konsistensi Format Respons JSON

Mempertahankan format balasan API yang seragam sangat penting agar data mudah diolah oleh sisi frontend. Seluruh endpoint API sebaiknya distandarisasi menggunakan struktur JSON baku yang terdiri dari tiga atribut utama: success, message, dan data. 

Contoh implementasi standar balasan yang kita gunakan di controller:

```php
return response()->json([
    'success' => true,
    'message' => 'Daftar data berhasil diambil.',
    'data' => $data
]);
```


2. Autentikasi dan Manajemen Sesi dengan Laravel Sanctum

Keamanan API kita kelola menggunakan Laravel Sanctum melalui mekanisme Bearer Token. Ada dua kebijakan keamanan tambahan yang sangat direkomendasikan untuk diterapkan:

A. Single Session
Saat pengguna melakukan proses login, sistem idealnya menghapus seluruh token lama milik pengguna tersebut dari database. Hal ini memastikan hanya ada satu sesi aktif per pengguna dalam satu waktu, mencegah penyalahgunaan token lama.

```php
// Hapus semua token lama milik user ini (Single Session)
$user->tokens()->delete();

return response()->json([
    'success' => true,
    'message' => 'Login berhasil.',
    'data' => [
        'token' => $user->createToken('api_token')->plainTextToken,
    ]
]);
```

B. Token Expiration (Batas Waktu Token)
Secara bawaan, token Sanctum tidak memiliki masa kedaluwarsa. Kita bisa mengaturnya menjadi 120 menit (2 jam) melalui konfigurasi sistem (config/sanctum.php). Pembatasan durasi ini bertujuan untuk memperkecil risiko penyalahgunaan token jika tersadap.

```php
// config/sanctum.php
'expiration' => 120,
```


3. Keamanan Data dengan Random String ID

Penggunaan ID berupa angka berurut (misalnya 1, 2, 3) sangat rentan terhadap celah ID Enumeration, di mana pihak luar dapat dengan mudah menebak jumlah atau urutan data di dalam sistem.

Sebagai solusi, struktur database dimodifikasi agar tabel pengguna (users) menggunakan ID berupa 16 karakter acak (String). Penyesuaian juga wajib dilakukan pada sistem bawaan Sanctum agar dapat mengenali dan menyimpan ID berjenis teks, bukan angka integer.

```php
// Contoh boot method di Model User
protected static function booted()
{
    static::creating(function ($model) {
        if (empty($model->{$model->getKeyName()})) {
            $model->{$model->getKeyName()} = Str::random(16);
        }
    });
}
```


4. Penanganan Kesalahan Global (Global Exception Handler)

Pada arsitektur Laravel 11, pengaturan lalu lintas API dapat diamankan di tingkat konfigurasi utama (bootstrap/app.php) untuk mencegah kebocoran pesan error sistem ke klien frontend.

A. Memaksa Format JSON
Sistem harus dikonfigurasi agar selalu mengembalikan respons JSON untuk semua rute API, bahkan ketika klien lupa mengirimkan header Accept: application/json. Hal ini mencegah server mengarahkan pengguna ke halaman HTML login bawaan web.

B. Kustomisasi Pesan Kesalahan
Berbagai pesan penolakan bawaan sistem ditangkap dan diganti dengan pesan Bahasa Indonesia yang informatif serta mengikuti struktur standar API.

```php
// bootstrap/app.php
->withExceptions(function (Exceptions $exceptions) {
    
    // Paksa rute API selalu merender JSON
    $exceptions->shouldRenderJsonWhen(function (Request $request, Throwable $e) {
        if ($request->is('api/*')) {
            return true;
        }
        return $request->expectsJson();
    });

    // Handle 404 (Endpoint & Data Not Found)
    $exceptions->render(function (NotFoundHttpException $e, Request $request) {
        if ($request->is('api/*')) {
            return response()->json([
                'success' => false,
                'message' => 'Endpoint atau data tidak ditemukan.',
                'data' => null
            ], 404);
        }
    });

    // Handle Auth Error
    $exceptions->render(function (AuthenticationException $e, Request $request) {
        if ($request->is('api/*')) {
            return response()->json([
                'success' => false,
                'message' => 'Sesi login Anda tidak valid atau telah habis.',
                'data' => null
            ], 401);
        }
    });

});
```

Kesimpulan

Membangun API tidak hanya sebatas menyalurkan data dari database ke klien. Diperlukan penegakan keamanan melalui ID acak, manajemen autentikasi yang disiplin, serta penanganan kesalahan global yang bersih. Dengan menggabungkan praktik-praktik ini di Laravel 11, kita dapat menciptakan API yang stabil, konsisten, dan siap diimplementasikan pada ekosistem industri modern.
