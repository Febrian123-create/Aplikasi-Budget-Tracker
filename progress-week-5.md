Laporan Progres Week 5 - BUNREK (Budget Tracker)
Rincian tugas yang dikerjakan oleh masing-masing anggota tim pada minggu ini:

1. Bryan Christian (2472022)
 - Memperbaiki tampilan overview pada page dashboard sehingga lebih mudah dipahami oleh user
 - Menambahkan fitur filter pada overview dashboard sehingga user dapat melihat data pemasukan dan pengeluaran berdasarkan rentang waktu tertentu (keseluruhan, tahunan, bulanan, serta mingguan)
 - Menambahkan logika dan UI untuk user dengan role membership pada halaman dashboard sehingga user yang memiliki role membership dapat melihat visualisasi singkat pengeluaran berdasarkan kategorinya

2. Errvin Junius (2472024)
 - Perbaikan Akses Membership dan Free  
    Akses fitur untuk pengguna free dan membership sudah diperbaiki. Mekanisme kontrol lebih terpusat sehingga hak akses fiturdapat dibedakan dengan jelas sesuai kategori akun
 - Update UI History dan Filter  
    Tampilan di bagian history dan filter sudah dirapikan. Tampilan riwayat transaksi lebih rapih dan filter bisa dipakaidengan lebih gampang untuk mencari data sesuai kebutuhan
 - Perbaikan Logika CRUD Transaksi  
    Logika untuk tambah, edit, hapus, dan lihat transaksi sudah diperbaiki. Sehingga proses CRUD lebih stabil, data lebih konsisten, dan error yang sebelumnya muncul dapat diminimalisir

3. Rafael Adiputra (2472025)
 - Memperbaiki UI Fitur Visualisasi  
    Tampilan untuk fitur visualisasi telah diperbaiki menjadi lebih rapih dan mudah dipahami. Perubahan ini membuat grafik dan data yang ditampilkan lebih jelas sehingga pengguna lebih mudah memahami

 - Membuat Fitur Wishlist  
    Dengan adanya fitur ini, pengguna dapat menyimpan daftar keinginan atau rencana pengeluaran sehingga lebih mudah dalam mengatur prioritas finansial

 - Menambahkan Fitur untuk Menghitung Jumlah Transaksi Sesuai dengan Filter Pada History  
    Menambahkan fungsi untuk menghitung jumlah transaksi sesuai filter yang digunakan pada halaman history. Hal ini memudahkan pengguna dalam mengetahui total transaksi berdasarkan kriteria tertentu secara cepat dan akurat

4. Febrian Timotius Sugiarto (2472039)
 - Budget bulanan/tahunan otomatis ikut periode berjalan tanpa isi tanggal manual, ada opsi durasi N bulan/tahun, dan kartu budget menampilkan label periode + durasi
 - Budget mingguan tetap pakai tanggal manual; form dinamis menyesuaikan field yang ditampilkan berdasarkan periode yang dipilih
 - Pop-up budget alert diperbaiki agar muncul untuk semua user (bukan hanya premium), scoped per user agar tidak bocor antar akun, dan dicek otomatis setiap dashboard dibuka
 - Transaksi rutin tipe Pemasukan tidak perlu pilih kategori lagi — otomatis di-assign ke INCOME, dan field kategori disembunyikan di form tambah maupun edit
 - Implementasi Google Calendar OAuth 2.0 lengkap beserta UI connect/disconnect di halaman Pengaturan Alert dan hint di form reminder recurring
 - Migrasi database: tambah kolom duration di budgets, user_id di reminder_logs, dan google_calendar_token di users