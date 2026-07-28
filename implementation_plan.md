# Rencana Implementasi Peningkatan Fitur Keuangan Ridho Interior

Rencana ini memaparkan langkah-langkah peningkatan fitur aplikasi manajemen keuangan Ridho Interior sesuai dengan permintaan user:
1. **Fungsionalitas CRUD Penuh** untuk:
   - Klien (Clients)
   - Tukang (Workers)
   - Upah Tukang (Worker Ledgers) - menambahkan fitur Edit dan Hapus transaksi upah.
   - Proyek & Termin (Projects & Payments) - menambahkan fitur Edit pada Termin, serta CRUD Pengeluaran (Expenses).
   - User (khusus Superadmin).
2. **Pencarian (Searching) & Paginasi (Paging)** untuk:
   - Proyek (dengan tambahan filter bulan & rentang tanggal).
   - Klien.
   - Tukang & Upah Tukang (Worker Ledger).
3. **Dashboard & Laporan Baru**:
   - Menampilkan laporan bulanan kumulatif (semua bulan), bukan hanya bulan ini.
   - Grafik Pemasukan vs Pengeluaran (Chart.js) yang dinamis.
   - Fitur Cetak Laporan PDF (desain print-friendly yang memicu dialog cetak/save PDF).
   - Pemisahan laporan per workshop/cabang.
4. **Fitur Superadmin**:
   - Switch workshop (memilih cabang mana yang ingin dilihat datanya).
   - CRUD User (Manajemen Admin & Superadmin beserta penempatan cabang/workshop).

---

## User Review Required

> [!IMPORTANT]
> - **Fitur Cetak PDF**: Akan diimplementasikan menggunakan halaman HTML ramah-cetak khusus (`window.print()`). Metode ini sangat kompatibel, cepat, andal tanpa memerlukan dependensi PDF PHP yang berat (seperti FPDF/Dompdf) yang rentan terhadap masalah kompatibilitas versi PHP.
> - **Paginasi & Pencarian**: Dilakukan di sisi server (server-side query) menggunakan parameter GET di controller/model CodeIgniter 3 agar performa tetap cepat meskipun data bertambah banyak.

---

## Proposed Changes

### 1. Database & Core Infrastructure

#### [MODIFY] [MY_Controller.php](file:///d:/GALERI%20PROJECT/keuangan_ridhointerior/application/core/MY_Controller.php)
- Modifikasi `_check_auth()` agar untuk user ber-role `superadmin`, `workshop_id` diambil dari session `selected_workshop_id` jika diset. Jika tidak, gunakan `workshop_id` bawaan user tersebut.
- Sediakan data semua workshop ke views agar superadmin dapat menampilkan dropdown switcher workshop di header.

### 2. Fitur Switch Workshop (Superadmin)

#### [MODIFY] [header.php](file:///d:/GALERI%20PROJECT/keuangan_ridhointerior/application/views/layouts/header.php) dan [sidebar.php](file:///d:/GALERI%20PROJECT/keuangan_ridhointerior/application/views/layouts/sidebar.php)
- Tampilkan dropdown pemilihan workshop di topbar jika role user saat ini adalah `superadmin`.
- Saat dropdown diubah, kirim request AJAX atau redirect ke `auth/switch_workshop/<id>` untuk memperbarui session `selected_workshop_id` dan refresh halaman.

#### [MODIFY] [Auth.php](file:///d:/GALERI%20PROJECT/keuangan_ridhointerior/application/controllers/Auth.php)
- Tambahkan method `switch_workshop($id)` untuk mengganti `selected_workshop_id` di session dan mengarahkannya kembali ke halaman sebelumnya (referrer).

---

### 3. Peningkatan CRUD, Searching, dan Paginasi

#### [MODIFY] [Clients.php](file:///d:/GALERI%20PROJECT/keuangan_ridhointerior/application/controllers/Clients.php) & [Client_model.php](file:///d:/GALERI%20PROJECT/keuangan_ridhointerior/application/models/Client_model.php)
- Implementasikan pencarian (`q`) dan paginasi (`page`) pada query `get_all()`.
- Sesuaikan view `clients/index.php` untuk menampilkan input pencarian, tombol navigasi halaman (paging), dan memperbaiki aksi tambah/edit/hapus klien.

#### [MODIFY] [Workers.php](file:///d:/GALERI%20PROJECT/keuangan_ridhointerior/application/controllers/Workers.php) & [Worker_model.php](file:///d:/GALERI%20PROJECT/keuangan_ridhointerior/application/models/Worker_model.php)
- Tambahkan filter searching & paging pada daftar Tukang.
- Tambahkan method `update_ledger($id, $data)` dan `delete_ledger($id)` di `Worker_model.php`.
- Tambahkan method `edit_ledger()`, `update_ledger()`, dan `delete_ledger($id)` di `Workers.php` untuk melengkapi CRUD Upah Tukang.
- Tambahkan paginasi dan pencarian di halaman detail upah tukang (Worker Ledger).
- Sediakan tombol edit/delete pada tabel riwayat transaksi di view `workers/detail.php`.

#### [MODIFY] [Projects.php](file:///d:/GALERI%20PROJECT/keuangan_ridhointerior/application/controllers/Projects.php) & [Project_model.php](file:///d:/GALERI%20PROJECT/keuangan_ridhointerior/application/models/Project_model.php)
- Tambahkan filter searching (`q`), paginasi (`page`), serta filter tanggal (`start_date` & `end_date`) atau filter bulanan (`month`) pada query proyek.
- Tambahkan edit termin di controller `Projects.php` (method `update_payment()`).
- Sesuaikan view `projects/index.php` untuk input pencarian, pilihan filter bulan/tanggal, dan kontrol paginasi.
- Sesuaikan view `projects/detail.php` untuk menambahkan aksi Edit Termin.

#### [NEW] [Expenses.php](file:///d:/GALERI%20PROJECT/keuangan_ridhointerior/application/controllers/Expenses.php) & [expenses/index.php](file:///d:/GALERI%20PROJECT/keuangan_ridhointerior/application/views/expenses/index.php)
- Buat Controller dan View CRUD Pengeluaran untuk mencatat pengeluaran operasional umum atau pengeluaran terkait proyek tertentu (Bahan Baku, Transportasi, Operasional, dll.) dengan paging dan pencarian.

---

### 4. Redesain Dashboard & Laporan Bulanan

#### [MODIFY] [Dashboard.php](file:///d:/GALERI%20PROJECT/keuangan_ridhointerior/application/controllers/Dashboard.php) & [Dashboard_model.php](file:///d:/GALERI%20PROJECT/keuangan_ridhointerior/application/models/Dashboard_model.php)
- Ubah dashboard agar menampilkan tabel laporan per bulan untuk tahun terpilih (menghitung total pemasukan dari termin proyek dan total pengeluaran per bulan).
- Sediakan data rekapitulasi tahunan/bulanan untuk cetak PDF.
- Tambahkan method `print_report()` untuk memanggil view khusus laporan ramah cetak.

#### [MODIFY] [dashboard/index.php](file:///d:/GALERI%20PROJECT/keuangan_ridhointerior/application/views/dashboard/index.php)
- Tampilkan grafik pemasukan vs pengeluaran menggunakan Chart.js (telah ada, tinggal disesuaikan/dipercantik).
- Tambahkan tabel "Laporan Rekap Bulanan" di dashboard yang memuat data seluruh bulan di tahun berjalan (Kolom: Bulan, Pemasukan, Pengeluaran, Net Cashflow, Aksi: Cetak PDF Bulanan).
- Tambahkan tombol "Cetak Laporan Tahunan" di bagian atas dashboard.

#### [NEW] [dashboard/print_pdf.php](file:///d:/GALERI%20PROJECT/keuangan_ridhointerior/application/views/dashboard/print_pdf.php)
- Halaman HTML terpisah tanpa layout utama (no header, no sidebar) dengan CSS `@media print` untuk mencetak laporan rekapitulasi keuangan secara rapi ke printer atau PDF.

---

### 5. Manajemen User (Khusus Superadmin)

#### [NEW] [Users.php](file:///d:/GALERI%20PROJECT/keuangan_ridhointerior/application/controllers/Users.php)
- Controller baru untuk mengelola user (CRUD). Menyimpan dan memperbarui data user ke tabel `users` (termasuk enkripsi password menggunakan `password_hash`).

#### [MODIFY] [User_model.php](file:///d:/GALERI%20PROJECT/keuangan_ridhointerior/application/models/User_model.php)
- Tambahkan fungsi standard CRUD: `get_all()`, `get_by_id($id)`, `insert($data)`, `update($id, $data)`, `delete($id)`.

#### [NEW] [users/index.php](file:///d:/GALERI%20PROJECT/keuangan_ridhointerior/application/views/users/index.php)
- View manajemen user dengan tabel pencarian, paginasi, modal tambah/edit user, dan konfirmasi hapus.

---

## Verification Plan

### Automated Tests
- Kita akan melakukan penelusuran manual dan integrasi testing melalui browser untuk memastikan tidak ada query error dan routing bekerja dengan baik.

### Manual Verification
1. Login sebagai superadmin, verifikasi keberadaan dropdown "Pilih Workshop" di topbar.
2. Ganti workshop ke "Workshop Cabang - Bandung", pastikan seluruh data proyek, klien, dan tukang berubah sesuai cabang tersebut.
3. Buka halaman Dashboard, pastikan grafik dan tabel Laporan Rekap Bulanan menampilkan data semua bulan.
4. Klik tombol "Cetak PDF" pada baris bulan tertentu, verifikasi tampilan cetak bulanan detail.
5. Buka Menu Utama > Master Data > Klien, lakukan tes searching dan klik tombol paginasi halaman.
6. Buka Menu Utama > Master Data > Upah Tukang, lakukan tes CRUD tukang, lalu klik detail upah tukang dan tes CRUD transaksi upah.
7. Buka menu Proyek, lakukan filter rentang tanggal, searching, dan paginasi.
8. Buka menu Pengeluaran (Keuangan), coba tambah, edit, dan hapus data pengeluaran.
9. Buka menu Manajemen User (khusus Superadmin), coba tambah, edit, dan hapus user admin/superadmin, lalu pastikan login dengan user baru berhasil.
