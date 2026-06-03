Laporan Progres Week 4 - BUNREK (Budget Tracker)

Anggota Tim dan Pembagian Tugas

Berikut adalah rincian tugas yang dikerjakan oleh masing-masing anggota tim pada minggu ini:

1. Bryan Christian (2472022)
    Middleware Membership:
    Ditambahkan sistem middleware untuk membedakan user free dan berbayar, sehingga akses fitur bisa dikontrol dari satu titik sebelum masuk ke controller.

2. Errvin Junius (2472024)
    Fix Sidebar & Akses Fitur:
    Sebelumnya sidebar menampilkan semua menu meski ada yang terkunci. Sekarang menu yang tidak bisa diakses oleh user free otomatis disembunyikan dari sidebar.

3. Rafael Adiputra (2472025)
    Modul Transaksi & Dashboard
    - form edit diperbaiki tampilannya, 
    - kategori hanya akan muncul saat tipe transaksinya pengeluaran pengeluaran, 
    - transaksi ditampilkan secara harian (grouped by date) untuk pengeluaran,pemasukan.
    - halaman dashboard baru dibuat.

4. Febrian Timotius Sugiarto (2472039)
    - Reminder custom per recurring: H-10, H-7, H-5, H-3, H-1, H-0 (checkbox multi-pilih)
    - Toggle on/off reminder per transaksi rutin
    - Pesan reminder custom per transaksi (fallback ke pesan otomatis)
    - Strategy Pattern 3 channel notifikasi: Email (queue), Pop-up in-app, Google Calendar
    - Budget Planning: buat & kelola batas pengeluaran per kategori & periode
    - Budget Alert via Observer: auto-notif saat spending >= threshold yang dikonfigurasi user
    - Pop-up global di semua halaman via Alpine.js + endpoint AJAX unread/mark-as-read
    - Artisan command reminders:send dijadwalkan harian jam 07:00
    - Idempotency: reminder_logs mencegah double-send per (recurring, hari, channel)
    - 4 migration baru: reminders, reminder_logs, budgets, budget_reminder_settings
