# Riwayat Perubahan

Catatan setiap pembaruan DIKSERA, terbaru di atas.

## 12 Oktober 2026

### Baru
- **Buku Panduan** di dalam aplikasi (menu *Buku Panduan*), lengkap per peran, bisa dicari dan dicetak.
- **Rekap logbook format surat resmi**: kop Pemerintah Kabupaten Kediri, rincian **per pasien**, rekap jenis tindakan, tanda tangan berupa **QR**.
- **Rekap logbook kepala ruangan** cukup 2 tanda tangan (Kepala Ruangan & Kasi).
- **Kasi Keperawatan** sebagai penanda tangan rekap logbook (bawaan: SUJIMAN), bisa diganti admin. NIP diambil dari akun Kasi bila pengaturan kosong.
- **Badge peran** di pojok kanan atas: KARU, KASI, KOMITE, PERAWAT, BIDAN, ASESOR, ADMIN.
- **Master tindakan**: Template, Export Excel, Import Excel.
- **Pengaturan Penanda Tangan** (admin): Direktur, Ketua Komite, Ketua Sub Komite Kredensial dengan riwayat *berlaku mulai*; label kolom TTD OPPE & logbook bisa diubah.
- **Rekap logbook admin**: cari nama/NIP, periode bulanan/triwulan/semester, Download Excel, Cetak Semua (PDF).
- **Logbook Harian**: pasien → kegiatan → tindakan, nama pasien dari SIMRS Khanza, validasi kepala ruangan, TTD berjenjang perawat → karu → kasi, periode tutup otomatis H+5, rekap bulanan/triwulan/semester.

### Perbaikan
- Logbook kepala ruangan kini divalidasi Kasi (sebelumnya tidak ada validator).
- Akun perawat tidak bisa lagi membuka halaman admin.
- OPPE: nilai hanya bisa diisi penilai yang sedang mendapat giliran; OPPE selesai hanya bisa dilihat pihak terkait.
- SPK/SPKK tidak bisa dicetak oleh orang lain.
- Ujian: batas 3 kali gagal dan syarat peserta kini diperiksa server.
- Tombol *Terbitkan Lisensi* dan *Acc Nilai* lebih aman (tidak terpicu lewat tautan).
- Pembatasan percobaan login, lupa password, dan pendaftaran.
- Nomor lisensi tidak lagi bisa dobel.
- Pengingat dokumen kedaluwarsa tidak lagi dikirim setiap hari (kini seminggu sekali setelah kedaluwarsa) dan H-30 tepat waktu.
- Unggahan dokumen admin dibatasi PDF/JPG/PNG.
- Admin tidak bisa menghapus akunnya sendiri.

## 1–2 Oktober 2026

### Baru
- **Riwayat Kredensialing** per peserta (timeline pengajuan sampai lisensi terbit).
- **Manajemen Akun**: klik baris perawat langsung ke detail pegawai.

### Perbaikan
- Error setelah mengirim ujian (halaman hasil tidak ditemukan).
- Tombol *Terbitkan Lisensi* error di detail pengajuan.
- Pelatihan dengan SKP kosong gagal disimpan.
- Tahun pendidikan lebih dari 4 digit menyebabkan error.
- Data lama yang dinonaktifkan tidak lagi tampil di sumber halaman.
