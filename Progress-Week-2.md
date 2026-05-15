1. Febrian Timotius Sugiarto (2472039)
- Membuat fitur Recurring Transaction menggunakan Design Pattern Factory Pattern
- CRUD Recurring Transaction
- Fitur ini berfungsi untuk user menginput Income atau Expense tetap secara berkala (example : Gaji Bulanan, Cicilan, dll)

2. Errvin Junius (2472024)
- Membuat fitur membership menggunakan Design Pattern Decorator Pattern
- Mekanisme Feature Gating untuk membatasi akses fitur spesifik berdasarkan status keanggotaan user tanpa mengubah kode utama
- Menggunakan Middleware untuk mengecek status keanggotaan user
- Tampilan Charts di index khusus user dengan status Premium

3. Rafael Adiputra Hanjoyo (2472025)
- Membuat fitur export data transaksi harian dan history transaksi menggunakan Design Pattern Strategy Pattern
- Data dapat di export ke PDF atau Excel sesuai filter yang di terapkan (tanggal, kategori).
- Kelas Strategi Excel: Membuat ExcelExportStrategy yang berisi logika spesifik untuk menggunakan library PhpSpreadsheet.
- Kelas Strategi PDF: Membuat PdfExportStrategy yang berisi logika spesifik untuk menggunakan library DomPDF.
- Refaktor Controller: Memperbarui ExportController sehingga hanya bertugas menyiapkan data, lalu mendelegasikan pembuatan file ke kelas strategi yang sesuai.