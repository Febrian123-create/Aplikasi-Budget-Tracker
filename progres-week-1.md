# Laporan Progres Week 1 - BUNREK (Budget Tracker)

Proyek BUNREK adalah aplikasi budget tracker keuangan pribadi berbasis web. Laporan ini merangkum pembagian tugas dan progres pengerjaan pada minggu pertama. Aplikasi ini dibangun menggunakan framework Laravel dengan arsitektur MVC (Model-View-Controller) dan mengimplementasikan beberapa Design Pattern (seperti Observer Pattern).

## Anggota Tim dan Pembagian Tugas

Berikut adalah rincian tugas yang dikerjakan oleh masing-masing anggota tim pada sprint minggu pertama ini:

### 1. Bryan Christian (2472022)
**Fitur: User Authentication (Register, Login, Logout) & Security**
*   **Implementasi:** Menggunakan sistem otentikasi bawaan Laravel (Breeze/UI) yang dimodifikasi.
*   **Detail Pengerjaan:**
    *   Membuat sistem registrasi pengguna baru, login, dan logout dengan manajemen session yang aman.
    *   Mengatur Middleware `auth` untuk membatasi akses halaman dashboard, transaksi, dan visualisasi hanya untuk pengguna yang sudah login.
    *   Memastikan isolasi data: Setiap transaksi, kategori, dan pengaturan terikat pada `user_id` yang spesifik. Pengguna hanya dapat melihat dan memanipulasi data keuangan milik mereka sendiri.
    *   Pengaturan struktur database awal untuk tabel `users` dan migrasi autentikasi.

### 2. Errvin Junius (2472024)
**Fitur: Filter, Pencarian Transaksi, & Manajemen Kategori**
*   **Implementasi:** Menggunakan teknik *Chain of Responsibility* untuk memproses filter secara berurutan.
*   **Detail Pengerjaan:**
    *   Membangun fitur History/Riwayat Transaksi yang kompleks.
    *   Implementasi filter multi-kriteria: pengguna dapat menyaring data transaksi berdasarkan rentang tanggal (Start Date - End Date), tipe transaksi (Pemasukan/Pengeluaran), dan Kategori.
    *   Membuat fitur pencarian dinamis (Search) untuk menemukan transaksi spesifik berdasarkan deskripsi.
    *   Mengelola tampilan tabel riwayat transaksi beserta kalkulasi total pemasukan, pengeluaran, dan saldo berdasarkan filter yang aktif.

### 3. Rafael Adiputra (2472025)
**Fitur: Manajemen Transaksi Harian (CRUD) & Observer Subject**
*   **Implementasi:** Mengelola operasi Create, Read, Update, Delete (CRUD) menggunakan arsitektur Laravel Eloquent ORM.
*   **Detail Pengerjaan:**
    *   Membuat form pencatatan transaksi harian yang intuitif (Input Pemasukan & Pengeluaran).
    *   Mengelola penyimpanan data transaksi yang mencakup: tanggal, jumlah (amount), deskripsi, tipe transaksi (dihubungkan ke tabel `transactiontype`), dan kategori (dihubungkan ke tabel `category`).
    *   Menampilkan daftar transaksi terbaru di halaman utama Dashboard.
    *   **Infrastruktur Pola Desain (Design Pattern):** Menginisiasi `TransactionSubject` (Subject dari *Observer Pattern*), sehingga setiap kali transaksi ditambahkan, diedit, atau dihapus, sistem akan mengirimkan notifikasi (broadcast) ke komponen lain yang membutuhkan (seperti fitur grafik).

### 4. Febrian Timotius Sugiarto (2472039)
**Fitur: Visualisasi Data (Grafik & Metrik)**
*   **Implementasi:** Integrasi *Chart.js* dengan backend Laravel (Service Repository Pattern & Observer Pattern).
*   **Detail Pengerjaan:**
    *   **Observer Integration:** Membuat `ChartObserver` yang mendengarkan perubahan dari `TransactionSubject` untuk memicu pembaruan data secara otomatis tanpa *hard-reload* data yang tidak perlu.
    *   **Pie/Donut Chart:** Menampilkan distribusi pengeluaran berdasarkan kategori pada bulan berjalan. Dilengkapi dengan logika penggabungan kategori kecil menjadi "Lainnya" jika lebih dari 6 kategori.
    *   **Bar Chart (Tren Bulanan):** Menampilkan perbandingan pemasukan vs pengeluaran selama beberapa bulan terakhir (Grouped Bar Chart). Dilengkapi dengan fitur toggle untuk melihat *Nominal* atau *Persentase Pertumbuhan MoM* (% MoM).
    *   **Kartu Metrik & Logika Membership:** Menampilkan ringkasan Total Pemasukan, Total Pengeluaran, Saldo (dengan peringatan defisit), dan Rasio Pengeluaran. Menerapkan pembatasan tampilan grafik berdasarkan tipe membership (Free/Premium).
