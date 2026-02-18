================================================================================
SIHANPANGAN851 - Sistem Informasi Hasil Pangan
================================================================================
YONIF TP 851/BBC
================================================================================

📋 FILE PENTING
================================================================================

HALAMAN UTAMA:
- detail_styled.php (Halaman detail tanaman/hewan)
- admin/input-detail.php (Input data detail)

TOOLS:
- check-tables.php (Cek database)
- hapus-double.php (Hapus data duplikat)

DATABASE:
- sihanpangan851 (1).sql (Database lengkap - GUNAKAN INI)

================================================================================
🚀 CARA MENGGUNAKAN
================================================================================

1. IMPORT DATABASE:
   - Buka phpMyAdmin
   - Create database: sihanpangan851
   - Import: sihanpangan851 (1).sql

2. LOGIN ADMIN:
   http://localhost/sihanpangan851/admin/login.php

3. ISI DATA:
   - Pilih "Kelola Tanaman" atau "Kelola Hewan"
   - Klik tombol "Detail" (biru)
   - Isi form dan simpan

4. LIHAT HASIL:
   http://localhost/sihanpangan851/detail_styled.php?id=72&type=hewan

================================================================================
🔧 TOOLS
================================================================================

CEK DATABASE:
http://localhost/sihanpangan851/check-tables.php

HAPUS DATA DOUBLE:
http://localhost/sihanpangan851/hapus-double.php

================================================================================
🎨 FITUR
================================================================================

- Layout 2 kolom (foto kiri, info kanan)
- Badge tanggal update
- Frame foto kuning lime
- Logo dinamis: 🐄 (peternakan) / 🌾 (pertanian)
- Tabel keterangan dengan border hijau
- Box informasi kegiatan
- Responsive design

================================================================================
🐛 TROUBLESHOOTING
================================================================================

Halaman kosong? → Isi data via admin/input-detail.php
Data double? → Buka hapus-double.php
Tabel tidak ada? → Import ulang sihanpangan851 (1).sql

================================================================================
✅ SISTEM SIAP DIGUNAKAN
================================================================================
