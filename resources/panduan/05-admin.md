# Panduan Admin

Semua halaman `/admin` hanya bisa dibuka akun **ADMIN**.

## 1. Dashboard

Statistik pengguna, pengajuan per status, dokumen kedaluwarsa, dan tombol cetak SPK massal per jenjang klinis.

## 2. Manajemen Akun

- Menyetujui (**✓**), menolak (**✕**), atau menghapus akun pendaftar. Filter berdasarkan status dan role, cari nama/email/NIK.
- **Klik baris akun perawat** untuk membuka detail pegawai.
- Admin tidak bisa menolak atau menghapus akunnya sendiri.

> Menghapus akun bersifat **permanen** dan ikut menghapus data terkait (pengajuan, logbook, dll.).

### Mengatur peran Kepala Ruangan & Kasi

Peran **KARU** dan **KASI** tidak dipilih admin di Manajemen Akun, melainkan dari data profil:

| Peran | Syarat |
|---|---|
| **Kepala Ruangan** | *Jabatan Struktural* = **Kepala Ruangan**, dan riwayat **pekerjaan terbaru** berisi unit yang dipimpin |
| **Kasi Keperawatan** | *Jabatan Struktural* = **Kepala Seksi** |
| **Perawat/Bidan** | *Jabatan Struktural* = Pelaksana; badge mengikuti isian **Profesi** |

Jabatan struktural diisi oleh yang bersangkutan di **Profil → Identitas**. Setelah diisi, menu validasi dan badge berubah otomatis saat halaman dimuat ulang.

> Satu unit sebaiknya hanya punya **satu** kepala ruangan aktif. Bila ada dua, keduanya bisa memvalidasi logbook unit tersebut.

## 3. Bidan dan Perawat

- Daftar seluruh perawat/bidan, filter, dan **Ekspor Excel Kustom** (pilih kolom).
  - Bagian **Pelatihan**: *Daftar Semua Pelatihan* (nama + tanggal), *Jumlah Pelatihan*, *Total SKP Pelatihan*.
  - **Pelatihan tertentu**: daftar semua nama pelatihan yang pernah diinput perawat tampil beserta jumlah orangnya. Ketik di kotak cari untuk menyaring (mis. *btc*), lalu **klik** nama pelatihan untuk menambahkannya; atau ketik kata sendiri lalu **Tambah**. Tiap pelatihan menjadi satu kolom berisi *Ya (tanggal)* atau *-*. Pencocokan memakai kata yang **terkandung** di nama pelatihan, jadi "BTCLS" juga cocok dengan "Pelatihan BTCLS 2024".
  - Pilih **Hanya perawat yang punya salah satu / semua pelatihan terpilih** untuk menyaring daftar, misalnya untuk mendata siapa saja yang sudah BTCLS.
  - Klik **Lihat Preview** untuk melihat tabel hasil (isi sama persis dengan file Excel) sebelum mengunduh. Gunakan kotak **Cari di preview** untuk memeriksa nama tertentu. Preview disembunyikan otomatis bila pilihan kolom diubah — klik lagi untuk memperbarui.
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
- **RKK tugas spesifik**: di Edit Pengajuan bagian RKK, centang **Tandai MPP**, **Tandai PPI**, atau **Tandai Hemodialisa (HD)**, simpan, lalu isi kewenangan butir yang muncul. Bagian tersebut tercetak sebagai kelompok tersendiri di PDF RKK. Butir Hemodialisa: asuhan keperawatan pre/intra/post HD, monitoring durante HD, punksi AV fistula, mengalirkan darah ke sistem ekstrakorporeal, memulai HD, program sesuai preskripsi (QB, QD, UF, antikoagulan), penanganan komplikasi durante HD, dan menyiapkan peralatan double lumen HD.
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

## 7. Buku Panduan

Panduan otomatis menyesuaikan peran pembaca (Perawat/Bidan, Kepala Ruangan, Kasi, Pewawancara, Admin). Admin bisa memilih **Lihat sebagai** di atas daftar isi untuk melihat panduan persis seperti yang dilihat peran lain, misalnya sebelum menjelaskan alur kepada karu.

## 8. Pengaturan

Profil admin dan koneksi Telegram admin (notifikasi grup).

## 9. Update Aplikasi (untuk tim IT)

Pembaruan aplikasi dilakukan dari komputer pengembang dengan `update.bat` (menu: update, deploy ulang, lihat error server, riwayat). Bila ada perubahan database, backup dulu lalu jalankan `.\update "pesan" -Migrate`.
