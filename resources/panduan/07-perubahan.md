# Riwayat Perubahan

Catatan setiap pembaruan DIKSERA, terbaru di atas.

## 14 Oktober 2026

### Baru
- **Ekspor Excel Kustom berdasarkan pelatihan** (Admin → Bidan dan Perawat): kolom daftar pelatihan, jumlah pelatihan, total SKP, kolom per pelatihan tertentu (mis. BTCLS: *Ya (tanggal)*), dan filter hanya perawat yang memiliki pelatihan terpilih.

### Perbaikan
- Ekspor Excel Kustom tidak lagi gagal bila ada akun perawat yang belum mengisi profil.
- Ekspor Excel Kustom: daftar pelatihan bisa **dicari dan diklik** (dengan jumlah orang), dan ada **Lihat Preview** hasil ekspor lengkap dengan pencarian sebelum mengunduh.
- Tampilan Ekspor Excel Kustom dirapikan: modal lebih lebar (kolom data & pelatihan berdampingan), pelatihan dipilih dengan **centang** (bisa banyak), pilihan **Punya salah satu (atau) / Punya semua (dan)**, dan tombol **Unduh Excel** yang sebelumnya tertutup kini selalu terlihat.

### Perbaikan Data
- RKK Hemodialisa butir *Melakukan program sesuai Prescripe dokter (QB, QD, UF dan Anti Koagulan)*: kolom kewenangan **Lendra Kusdiantoro** diubah menjadi **Dengan Supervisi**. **Kiki Riska Nuraini** sudah *Dengan Supervisi* (PK 1), tidak berubah.

## 13 Oktober 2026

### Baru
- **RKK tugas spesifik Hemodialisa (HD)** dengan 8 butir kompetensi, bisa ditandai seperti MPP & PPI di penilaian pewawancara, Edit Pengajuan (admin), dan OPPE; tercetak sebagai bagian tersendiri di PDF RKK.
- RKK Hemodialisa ditambahkan untuk 8 perawat: Desy Natalia, Dismalyansa, Siska Heni Rahayu, Lendra Kusdiantoro (PK 2), Hurria Alfisana, Kiki Riska Nuraini (PK 1), Basuki Rahmad, Slamet Hidayat (PK 3). Kewenangan: PK 2 & 3 *Mandiri – Berwenang sendiri*, PK 1 *Mandiri – Dengan supervisi*.

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
- Buku Panduan disesuaikan per peran: perawat/bidan, **kepala ruangan**, **kasi**, pewawancara, dan admin masing-masing melihat bagian yang relevan; panduan Karu dan Kasi dipisah & dirinci. Admin bisa memilih **Lihat sebagai**.
- Daftar isi Buku Panduan tetap menempel di atas saat halaman di-scroll.
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
