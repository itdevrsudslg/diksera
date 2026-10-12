# Panduan Admin

Semua halaman `/admin` hanya bisa dibuka akun **ADMIN**.

## 1. Dashboard

Statistik pengguna, pengajuan per status, dokumen kedaluwarsa, dan tombol cetak SPK massal per jenjang klinis.

## 2. Manajemen Akun

- Menyetujui (**✓**), menolak (**✕**), atau menghapus akun pendaftar. Filter berdasarkan status dan role, cari nama/email/NIK.
- **Klik baris akun perawat** untuk membuka detail pegawai.
- Admin tidak bisa menolak atau menghapus akunnya sendiri.

> Menghapus akun bersifat **permanen** dan ikut menghapus data terkait (pengajuan, logbook, dll.).

## 3. Bidan dan Perawat

- Daftar seluruh perawat/bidan, filter, dan **Export Excel** (pilih kolom).
- **Detail** perawat: DRH lengkap, dokumen, sertifikat, tombol **Riwayat Kredensialing** (timeline setiap pengajuan dari dibuat sampai lisensi terbit).
- Edit DRH dan dokumen (STR/SIP/lisensi/tambahan) atas nama perawat. Unggahan hanya **PDF/JPG/PNG maks. 5 MB**.
- Buka/tutup akses **OPPE** per perawat (otomatis / buka / tutup).
- Arsip dokumen dan pesan (chat) ke perawat.

## 4. Kredensialing

### Approval Pengajuan

Alur status pengajuan:

| Status | Aksi admin |
|---|---|
| **Menunggu** | Periksa berkas → **Setujui** (isi tanggal mulai & akhir berlaku) atau **Tolak** |
| **Tahap Ujian** | Tunggu peserta ujian. **Acc Nilai** bila nilai ≥ 75 (bisa massal) |
| **Lulus Ujian / Siap Wawancara** | Peserta memilih jadwal; admin bisa menjadwalkan massal |
| **Wawancara Terjadwal** | Tunggu pewawancara menilai |
| **Wawancara disetujui & dinilai** | **Terbitkan Lisensi** di halaman detail pengajuan |
| **Selesai** | Dokumen bisa diunduh peserta |

- Tombol **Riwayat Peserta** di detail pengajuan membuka timeline lengkap peserta.
- Edit pengajuan untuk memperbaiki data, berkas, jadwal, atau penilaian.
- Terbitkan **SPK kolektif** untuk beberapa peserta sekaligus per jenjang.

### Uji Kompetensi & Kredensialing (Data Lisensi)

Daftar lisensi hasil jalur UKOM dan Kredensialing: cetak RKK, SPK, SPKK, sertifikat; edit; hapus massal.

### OPPE

Memantau seluruh OPPE dan mencetak OPPE yang sudah selesai.

### Penanda Tangan

Mengatur pejabat penanda tangan dokumen:

| Pejabat | Dipakai di |
|---|---|
| **Direktur** | SPK, SPKK, RKK, Sertifikat |
| **Ketua Komite Keperawatan** | RKK |
| **Ketua Sub Komite Kredensial** | RKK |
| **Kepala Seksi Keperawatan** | Rekap Logbook |

- Isi **Ganti pejabat** dengan nama, NIP, pangkat, dan **Berlaku mulai**.
- Dokumen memakai pejabat yang menjabat **pada tanggal terbit dokumen**, sehingga dokumen lama tetap memakai nama lama.
- Riwayat bisa dihapus bila salah input.
- **Label kolom TTD** pada cetak OPPE dan rekap logbook bisa diubah (kosongkan untuk kembali ke bawaan).

## 5. Logbook

### Rekap Logbook

- Pilih periode **bulanan / triwulan / semester**, filter unit, **cari nama/NIP**.
- **Download Excel**: sheet Ringkasan, Rekap Tindakan, Detail Harian.
- **Cetak Semua (PDF)**: satu file berisi lembar rekap semua perawat (satu halaman per orang).
- Klik ikon **mata** untuk melihat rekap satu perawat.

### Periode Logbook

Setiap bulan berstatus **Otomatis** (tutup H+5), **Buka** manual, atau **Tutup** manual. Klik **Otomatis** untuk kembali ke jadwal normal.

### Master Tindakan

- **Sinkron Sekarang**: ambil daftar tindakan dari SIMRS Khanza (rawat jalan & rawat inap). Memerlukan pengaturan `KHANZA_DB_*` di server.
- **Tambah manual**: kode + nama.
- **Template / Export Excel / Import Excel**: kolom *Kode, Nama Tindakan, Kategori (ralan/ranap/manual), Aktif (Ya/Tidak)*. Data yang sudah ada diperbarui, tidak ada yang dihapus.
- Aktifkan/nonaktifkan tindakan dengan tombol status. Tindakan nonaktif tidak muncul saat perawat mengisi logbook.

## 6. Manajemen Ujian

| Menu | Fungsi |
|---|---|
| **Penanggung Jawab** | Data pewawancara/penanggung jawab ujian (sekaligus akun pewawancara) |
| **Form Ujian** | Membuat ujian: judul, waktu mulai–selesai, durasi, target peserta (semua / KFK / khusus), memilih soal & bobot, melihat hasil, **reset hasil** peserta |
| **Bank Soal** | Kelola soal pilihan ganda, import/export |
| **Bank Item RKK** | Butir Rincian Kewenangan Klinis per jenjang, import/export |

## 7. Pengaturan

Profil admin dan koneksi Telegram admin (notifikasi grup).

## 8. Update Aplikasi (untuk tim IT)

Pembaruan aplikasi dilakukan dari komputer pengembang dengan `update.bat` (menu: update, deploy ulang, lihat error server, riwayat). Bila ada perubahan database, backup dulu lalu jalankan `.\update "pesan" -Migrate`.
