Laporan Progres Week 1 - BUNREK (Budget Tracker)

Proyek BUNREK adalah aplikasi budget tracker keuangan pribadi berbasis web. Laporan ini merangkum pembagian tugas dan progres pengerjaan pada minggu pertama. Aplikasi ini dibangun menggunakan framework Laravel dengan arsitektur MVC (Model-View-Controller) dan mengimplementasikan Design Pattern (seperti Observer Pattern).

Anggota Tim dan Pembagian Tugas

Berikut adalah rincian tugas yang dikerjakan oleh masing-masing anggota tim pada sprint minggu pertama ini:

1. Bryan Christian (2472022)
Fitur: User Authentication (Register, Login, Logout)
Implementasi: Menggunakan sistem otentikasi bawaan Laravel (Breeze/UI) yang masih simple.
Detail Pengerjaan:
- Membuat sistem registrasi pengguna baru, login, dan logout dengan manajemen session yang aman.
- Memastikan isolasi data: Setiap transaksi, kategori, dan pengaturan terikat pada user_id yang spesifik. Pengguna hanya dapat melihat dan memanipulasi data keuangan milik mereka sendiri.
- Pengaturan struktur database awal untuk tabel users dan migrasi autentikasi.

2. Errvin Junius (2472024)
Fitur: Filter, Pencarian Transaksi, & Manajemen Kategori
Detail Pengerjaan:
- Membangun fitur History/Riwayat Transaksi.
- Implementasi filter multi-kriteria: pengguna dapat menyaring data transaksi berdasarkan rentang tanggal (Start Date - End Date), tipe transaksi (Pemasukan/Pengeluaran), dan Kategori.
- Mengelola tampilan tabel riwayat transaksi beserta kalkulasi total pemasukan, pengeluaran, dan saldo berdasarkan filter yang aktif secara sederhana.

3. Rafael Adiputra (2472025)
Fitur: Manajemen Transaksi Harian (CRUD)
Implementasi: Mengelola operasi Create, Read,Delete.
Detail Pengerjaan:
- Membuat form pencatatan transaksi harian yang sederhana (Input Pemasukan & Pengeluaran).
- Mengelola penyimpanan data transaksi yang mencakup: tanggal, jumlah (amount), deskripsi, tipe transaksi (dihubungkan ke tabel transactiontype), dan kategori (dihubungkan ke tabel category).
- Menampilkan daftar transaksi terbaru di halaman utama Dashboard.

4. Febrian Timotius Sugiarto (2472039)
Fitur: Visualisasi Data (Grafik & Metrik)
Implementasi: Integrasi Chart.js dengan backend Laravel (Observer Pattern).
Detail Pengerjaan:
- Observer Integration: Membuat ChartObserver yang mendengarkan perubahan dari TransactionSubject untuk memicu pembaruan data secara otomatis tanpa hard-reload data yang tidak perlu.
- Pie/Donut Chart: Menampilkan distribusi pengeluaran berdasarkan kategori pada bulan berjalan. Dilengkapi dengan logika penggabungan kategori kecil menjadi "Lainnya" jika lebih dari 6 kategori.
- Bar Chart (Tren Bulanan): Menampilkan perbandingan pemasukan vs pengeluaran selama beberapa bulan terakhir (Grouped Bar Chart). Dilengkapi dengan fitur toggle untuk melihat Nominal atau Persentase Pertumbuhan MoM (% MoM).
- Kartu Metrik & Logika Membership: Menampilkan ringkasan Total Pemasukan, Total Pengeluaran, Saldo (dengan peringatan defisit), dan Rasio Pengeluaran. Menerapkan pembatasan tampilan grafik berdasarkan tipe membership (Free/Premium).
