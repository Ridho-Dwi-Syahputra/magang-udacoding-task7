Membangun RESTful API Tangguh dengan Laravel 11 dan Sanctum

Pengembangan backend modern menuntut API yang tidak hanya berfungsi untuk operasi dasar, tetapi juga memiliki standar keamanan, konsistensi respons, dan penanganan kesalahan yang baik. 

Pada Task 7 program Magang Berdampak Udacoding, saya membangun RESTful API untuk manajemen tugas dengan menerapkan standar industri menggunakan framework Laravel 11. Berikut adalah poin-poin implementasi teknis yang dilakukan:

1. Konsistensi Format Respons JSON
Mempertahankan format balasan API yang seragam sangat penting agar data mudah diolah oleh sisi frontend. Seluruh endpoint API pada proyek ini telah distandarisasi menggunakan struktur JSON yang berisi tiga kunci utama: success, message, dan data. Aturan ini diterapkan secara menyeluruh untuk semua jenis operasi, mulai dari pengambilan data hingga autentikasi.

2. Autentikasi dan Manajemen Sesi dengan Laravel Sanctum
Keamanan API dikelola menggunakan Laravel Sanctum melalui mekanisme Bearer Token. Terdapat dua kebijakan keamanan tambahan yang diterapkan:
- Single Session: Saat pengguna melakukan proses login, sistem secara otomatis menghapus seluruh token lama milik pengguna tersebut dari database. Hal ini memastikan hanya ada satu sesi aktif per pengguna dalam satu waktu.
- Token Expiration: Batas waktu kedaluwarsa token diatur menjadi 120 menit (2 jam) melalui konfigurasi sistem. Pembatasan durasi ini bertujuan untuk memperkecil risiko penyalahgunaan token.

3. Keamanan Data dengan Random String ID
Penggunaan ID berupa angka berurut rentan terhadap celah ID Enumeration, di mana pihak luar dapat menebak jumlah atau urutan data di dalam sistem.
Sebagai solusi, struktur database dimodifikasi agar tabel pengguna dan tugas menggunakan ID berupa 16 karakter acak. Penyesuaian juga dilakukan pada sistem bawaan Sanctum agar dapat mengenali dan menyimpan ID berjenis teks, bukan angka.

4. Penanganan Kesalahan Global
Pada arsitektur Laravel 11, lalu lintas API telah diamankan di tingkat konfigurasi utama (bootstrap) untuk mencegah kebocoran pesan error sistem:
- Memaksa Format JSON: Sistem dikonfigurasi agar selalu mengembalikan respons JSON untuk semua rute API. Hal ini mencegah server mengarahkan pengguna ke halaman HTML web ketika klien lupa menyertakan header spesifik.
- Kustomisasi Pesan Kesalahan: Berbagai pesan penolakan bawaan sistem ditangkap dan diganti dengan pesan Bahasa Indonesia yang informatif. Contohnya, sistem akan membalas "Data yang dicari tidak ditemukan" untuk data yang tidak ada (status 404), dan membalas "Sesi login Anda tidak valid atau telah habis" untuk kendala autentikasi (status 401).

Kesimpulan
Pengerjaan Task 7 difokuskan pada penguatan keamanan dan standarisasi arsitektur API. Penggunaan ID acak, manajemen token yang disiplin, serta penanganan kesalahan global memastikan bahwa API ini stabil, konsisten, dan siap diimplementasikan pada ekosistem industri yang sebenarnya.
