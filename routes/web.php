<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\LandingController;
use App\Http\Controllers\AuthController;
use App\Http\Controllers\MessageController;
use App\Http\Controllers\DashboardController;
use App\Http\Controllers\AdminPerawatController;
use App\Http\Controllers\AdminProfileController;
use App\Http\Controllers\BankSoalController;
use App\Http\Controllers\FormController;
use App\Http\Controllers\PerawatDrhController;
use App\Http\Controllers\TelegramController;
use App\Http\Controllers\ManajemenAkunController;
use App\Http\Controllers\PenanggungJawabUjianController;
use App\Http\Controllers\UserFormController;
use App\Http\Controllers\AdminPengajuanController;
use App\Http\Controllers\PengajuanSertifikatController;
use App\Http\Controllers\AdminPengajuanWawancaraController;
use App\Http\Controllers\AdminLisensiController;
use App\Http\Controllers\PerawatLisensiController;
use App\Http\Controllers\PewawancaraController;
use App\Http\Controllers\BankItemRkkController;
use App\Http\Controllers\PerawatArsipDokumenController;
use Illuminate\Support\Facades\Http;
/*
|--------------------------------------------------------------------------
| PUBLIC ROUTES
|--------------------------------------------------------------------------
*/
// Jembatan untuk ambil data wilayah tanpa CORS
Route::get('/api-wilayah-lokal/{folder}/{code?}', function($folder, $code = null) {
    // Jika code kosong (untuk provinsi), panggil provinces.json
    $url = $code 
        ? "https://wilayah.id/api/{$folder}/{$code}.json" 
        : "https://wilayah.id/api/provinces.json";

    return Http::withOptions(['verify' => false])->get($url)->json();
});
Route::get('/landing', [LandingController::class, 'index'])->name('landing');
// Route ini harus public (jangan masukkan ke middleware auth)
Route::get('/verify/sertifikat/{id}', [PengajuanSertifikatController::class, 'verifyPublic'])->name('sertifikat.verify');
// Authentication
Route::get('/login', [AuthController::class, 'showLoginForm'])->name('login');
Route::post('/login', [AuthController::class, 'login'])->name('login.process');
Route::get('/register/perawat', [AuthController::class, 'showPerawatRegisterForm'])->name('register.perawat');
Route::post('/register/perawat', [AuthController::class, 'registerPerawat'])->name('register.perawat.process');
Route::get('/logout', [AuthController::class, 'logout'])->name('logout');
// Forget Password
Route::get('forget-password', [AuthController::class, 'showForgetPasswordForm'])->name('forget.password.get');
Route::post('forget-password', [AuthController::class, 'sendResetLinkEmail'])->name('forget.password.post');

// Reset Password
Route::get('reset-password/{token}', [AuthController::class, 'showResetPasswordForm'])->name('password.reset');
Route::post('reset-password', [AuthController::class, 'resetPassword'])->name('password.update');
/*
|--------------------------------------------------------------------------
| PROTECTED ROUTES (Harus Login)
|--------------------------------------------------------------------------
*/
Route::middleware(['auth'])->group(function () {

    // === DASHBOARD ===
    
    Route::post('/messages/send', [MessageController::class, 'store'])->name('messages.send');
Route::get('/messages/history/{userId}', [MessageController::class, 'getChat'])->name('messages.history');
    Route::get('/dashboard', [DashboardController::class, 'index'])->name('dashboard');
    Route::get('dashboard/admin', [DashboardController::class, 'adminIndex'])->middleware('role:admin')->name('dashboard.admin');
    Route::get('/dashboard/pewawancara', [PewawancaraController::class, 'index'])->name('dashboard.pewawancara');
 // ====================================================
    // GROUP ADMIN
    // ====================================================
    Route::prefix('admin')->name('admin.')->middleware('role:admin')->group(function () {
        // Route khusus untuk paksa cetak semua SPK berdasarkan Jenjang Klinis (Dari Dashboard)
Route::get('/dashboard/cetak-spk-massal/{jenjang}', [App\Http\Controllers\PerawatLisensiController::class, 'printSpkMassalDashboard'])->name('dashboard.cetak_spk_massal');
       // === ROUTE CETAK PDF (SERKOM, RKK, SPK, SPKK) UNTUK ADMIN ===
        
        // Panggil fungsi BARU khusus Admin di AdminPengajuanController
        Route::get('/pengajuan/{id}/cetak-serkom', [AdminPengajuanController::class, 'cetakSerkom'])->name('pengajuan.cetak_serkom');
        Route::get('/pengajuan/{id}/cetak-rkk', [AdminPengajuanController::class, 'cetakRkk'])->name('pengajuan.cetak_rkk');
        
        // SPK & SPKK tetap aman menggunakan PerawatLisensiController
        Route::get('/pengajuan/{id}/cetak-spk', [PerawatLisensiController::class, 'printSpk'])->name('pengajuan.cetak_spk');
        Route::get('/pengajuan/{id}/cetak-spkk', [PerawatLisensiController::class, 'printSpkk'])->name('pengajuan.cetak_spkk');
        Route::get('/pengajuan/{id}/edit', [AdminPengajuanController::class, 'edit'])->name('pengajuan.edit');
Route::put('/pengajuan/{id}/update', [AdminPengajuanController::class, 'update'])->name('pengajuan.update');
        // Route untuk Edit Tanggal Jadwal Wawancara dari Dashboard Asesor
Route::put('/penanggung-jawab/jadwal/{id}/update-tanggal', [PenanggungJawabUjianController::class, 'updateTanggal'])->name('penanggung-jawab.update_tanggal');
        Route::patch('/perawat/{id}/toggle-oppe', [AdminPerawatController::class, 'toggleOppe'])->name('perawat.toggle_oppe');
        Route::post('/pengajuan/terbitkan-kolektif', [App\Http\Controllers\AdminPengajuanController::class, 'terbitkanKolektif'])->name('pengajuan.terbitkan_kolektif');
        // Route untuk mengeksekusi penerbitan SPK & SPKK oleh Admin
Route::post('/pengajuan/{id}/terbitkan-spk', [AdminPengajuanController::class, 'terbitkanSpk'])->name('pengajuan.terbitkan_spk');
        Route::get('/perawat/export-custom', [AdminPerawatController::class, 'exportCustom'])->name('perawat.export_custom');
        // === KELOLA BANK ITEM RKK ===
Route::get('bank-item-rkk', [BankItemRkkController::class, 'index'])->name('bank-item-rkk.index');
Route::get('bank-item-rkk/create', [BankItemRkkController::class, 'create'])->name('bank-item-rkk.create');
Route::post('bank-item-rkk/store', [BankItemRkkController::class, 'store'])->name('bank-item-rkk.store');
Route::get('bank-item-rkk/{id}/edit', [BankItemRkkController::class, 'edit'])->name('bank-item-rkk.edit');
Route::post('bank-item-rkk/{id}/update', [BankItemRkkController::class, 'update'])->name('bank-item-rkk.update');
Route::post('bank-item-rkk/{id}/delete', [BankItemRkkController::class, 'destroy'])->name('bank-item-rkk.delete');
Route::get('bank-item-rkk/export-json', [BankItemRkkController::class, 'exportJsonData'])->name('bank-item-rkk.export-json');
Route::post('bank-item-rkk/import-json', [BankItemRkkController::class, 'importJson'])->name('bank-item-rkk.import-json');
        Route::get('bank-soal/export-json', [BankSoalController::class, 'exportJsonData'])->name('bank-soal.export-json');
        Route::get('/perawat-export-pk-kfk', [AdminPerawatController::class, 'exportDataByPkKfk'])->name('perawat.export_pk_kfk');
        Route::get('/perawat', [AdminPerawatController::class, 'index'])->name('perawat.index');
        Route::get('/perawat/{id}', [AdminPerawatController::class, 'show'])->name('perawat.show');
        Route::get('/perawat/{id}/edit', [AdminPerawatController::class, 'edit'])->name('perawat.edit');
        Route::put('/perawat/{id}', [AdminPerawatController::class, 'update'])->name('perawat.update');
        Route::delete('/perawat/{id}', [AdminPerawatController::class, 'destroy'])->name('perawat.destroy');
        Route::get('/perawat/{id}/sertifikat', [AdminPerawatController::class, 'sertifikat'])->name('perawat.sertifikat');
        Route::get('/perawat/{id}/timeline', [AdminPerawatController::class, 'timeline'])->name('perawat.timeline');
        // Route Edit Dokumen Sertifikat dari Halaman Admin
Route::put('/perawat/sertifikat/str/{id}', [AdminPerawatController::class, 'updateStr'])->name('perawat.sertifikat.update_str');
Route::put('/perawat/sertifikat/sip/{id}', [AdminPerawatController::class, 'updateSip'])->name('perawat.sertifikat.update_sip');
Route::put('/perawat/sertifikat/lisensi/{id}', [AdminPerawatController::class, 'updateLisensi'])->name('perawat.sertifikat.update_lisensi');
Route::put('/perawat/sertifikat/tambahan/{id}', [AdminPerawatController::class, 'updateTambahan'])->name('perawat.sertifikat.update_tambahan');
Route::get('/perawat-export', [AdminPerawatController::class, 'exportData'])->name('perawat.export');
        // === DOKUMEN MASTER LISENSI ===
    Route::prefix('lisensi_pg_interview')->name('lisensi_pg_interview.')->group(function () {
    Route::get('/', [AdminLisensiController::class, 'lisensiIndex'])->defaults('metode', 'pg_interview')->name('index');
    Route::get('/create', [AdminLisensiController::class, 'lisensiCreate'])->defaults('metode', 'pg_interview')->name('create');
    Route::post('/', [AdminLisensiController::class, 'lisensiStore'])->defaults('metode', 'pg_interview')->name('store');
    
    // TARUH ROUTE BULK DESTROY DI SINI (SEBELUM {id})
    Route::delete('/bulk-destroy', [AdminLisensiController::class, 'lisensiBulkDestroy'])->defaults('metode', 'pg_interview')->name('bulk_destroy');
    
    Route::get('/{id}/edit', [AdminLisensiController::class, 'lisensiEdit'])->defaults('metode', 'pg_interview')->name('edit');
    Route::put('/{id}', [AdminLisensiController::class, 'lisensiUpdate'])->defaults('metode', 'pg_interview')->name('update');
    Route::delete('/{id}', [AdminLisensiController::class, 'lisensiDestroy'])->defaults('metode', 'pg_interview')->name('destroy');
});

        Route::prefix('lisensi_interview_only')->name('lisensi_interview_only.')->group(function () {
            Route::get('/', [AdminLisensiController::class, 'lisensiIndex'])->defaults('metode', 'interview_only')->name('index');
            Route::get('/create', [AdminLisensiController::class, 'lisensiCreate'])->defaults('metode', 'interview_only')->name('create');
            Route::post('/', [AdminLisensiController::class, 'lisensiStore'])->defaults('metode', 'interview_only')->name('store');
            Route::get('/{id}/edit', [AdminLisensiController::class, 'lisensiEdit'])->defaults('metode', 'interview_only')->name('edit');
            Route::put('/{id}', [AdminLisensiController::class, 'lisensiUpdate'])->defaults('metode', 'interview_only')->name('update');
            Route::delete('/bulk-destroy', [AdminLisensiController::class, 'lisensiBulkDestroy'])->defaults('metode', 'interview_only')->name('bulk_destroy');
            Route::delete('/{id}', [AdminLisensiController::class, 'lisensiDestroy'])->defaults('metode', 'interview_only')->name('destroy');
            Route::get('/{id}/cetak-rkk', [AdminLisensiController::class, 'cetakRkkBundle'])->name('cetak_rkk');
        });

        // === APPROVAL PENGAJUAN ===
        Route::get('/pengajuan', [AdminPengajuanController::class, 'index'])->name('pengajuan.index');
        Route::get('/pengajuan/export/jadwal-wawancara', [AdminPengajuanController::class, 'exportJadwal'])->name('pengajuan.export_jadwal');

        // Bulk Actions
        Route::delete('/pengajuan/bulk-destroy', [AdminPengajuanController::class, 'bulkDestroy'])->name('pengajuan.bulk_destroy');
        Route::post('/pengajuan/bulk-approve', [AdminPengajuanController::class, 'bulkApprove'])->name('pengajuan.bulk_approve');
        Route::post('/pengajuan/bulk-approve-score', [AdminPengajuanController::class, 'bulkApproveScore'])->name('pengajuan.bulk_approve_score');
        Route::post('/pengajuan/bulk-approve-interview', [AdminPengajuanController::class, 'bulkApproveInterview'])->name('pengajuan.bulk_approve_interview');

        // Single Actions
        Route::get('/pengajuan/{id}', [AdminPengajuanController::class, 'show'])->name('pengajuan.show');
        Route::get('/pengajuan/{id}/complete', [AdminPengajuanController::class, 'completeProcess'])->name('pengajuan.complete');
        Route::get('/pengajuan/{id}/approve-score', [AdminPengajuanController::class, 'approveExamScore'])->name('pengajuan.approve_score');
        Route::post('/pengajuan/{id}/approve', [AdminPengajuanController::class, 'approve'])->name('pengajuan.approve');
        Route::post('/pengajuan/{id}/reject', [AdminPengajuanController::class, 'reject'])->name('pengajuan.reject');
        Route::delete('/pengajuan/{id}', [AdminPengajuanController::class, 'destroy'])->name('pengajuan.destroy');
        // Route::get('/pengajuan/export-jadwal', [AdminPengajuanController::class, 'exportJadwal'])->name('pengajuan.export_jadwal');

        // Update Tanggal
        Route::patch('/pengajuan/{id}/update-dates', [AdminPengajuanController::class, 'updateDates'])->name('pengajuan.updateDates');

        // PENGAJUAN WAWANCARA
        Route::prefix('pengajuan-wawancara')->name('pengajuan_wawancara.')->group(function () {
            Route::post('/{id}/approve', [AdminPengajuanWawancaraController::class, 'approveJadwal'])->name('approve');
            Route::post('/{id}/reject', [AdminPengajuanWawancaraController::class, 'rejectJadwal'])->name('reject');
            Route::get('/{id}/penilaian', [AdminPengajuanWawancaraController::class, 'showPenilaian'])->name('penilaian');
            Route::post('/{id}/penilaian', [AdminPengajuanWawancaraController::class, 'storePenilaian'])->name('store_penilaian');
        });

        // Verifikasi kelayakan dokumen
        Route::post('/perawat/verifikasi-kelayakan', [AdminPerawatController::class, 'verifikasiKelayakan'])->name('perawat.verifikasi.kelayakan');

        Route::get('/profile', [AdminProfileController::class, 'index'])->name('profile.index');
        Route::post('/telegram/generate', [AdminProfileController::class, 'generateCode'])->name('telegram.generate');
        Route::post('/telegram/unlink', [AdminProfileController::class, 'unlink'])->name('telegram.unlink');
        Route::post('/telegram/test', [AdminProfileController::class, 'testMessage'])->name('telegram.test');

        // === MANAJEMEN AKUN ===
        Route::get('/manajemen-akun', [ManajemenAkunController::class, 'index'])->name('manajemen_akun.index');
        Route::put('/manajemen-akun/{id}/update', [ManajemenAkunController::class, 'updateStatus'])->name('manajemen_akun.update');
        Route::delete('/manajemen-akun/{id}', [ManajemenAkunController::class, 'destroy'])->name('manajemen_akun.destroy');

        // FORM MANAGEMENT
        Route::get('/forms', [FormController::class, 'index'])->name('form.index');
        Route::get('/forms/create', [FormController::class, 'create'])->name('form.create');
        Route::post('/forms', [FormController::class, 'store'])->name('form.store');
        Route::patch('form/{form}/update-status', [FormController::class, 'updateStatus'])->name('form.update-status');
        Route::get('/forms/{form}/edit', [FormController::class, 'edit'])->name('form.edit');
        Route::put('/forms/{form}', [FormController::class, 'update'])->name('form.update');
        Route::delete('/forms/{form}', [FormController::class, 'destroy'])->name('form.destroy');

        // PENANGGUNG JAWAB UJIAN
        Route::resource('penanggung-jawab', PenanggungJawabUjianController::class);

        // === KELOLA SOAL ===
        Route::get('bank-soal', [BankSoalController::class, 'index'])->name('bank-soal.index');
        Route::get('bank-soal/create', [BankSoalController::class, 'create'])->name('bank-soal.create');
        Route::post('bank-soal/store', [BankSoalController::class, 'store'])->name('bank-soal.store');
        Route::get('bank-soal/{id}/edit', [BankSoalController::class, 'edit'])->name('bank-soal.edit');
        Route::post('bank-soal/{id}/update', [BankSoalController::class, 'update'])->name('bank-soal.update');
        Route::post('bank-soal/{id}/delete', [BankSoalController::class, 'destroy'])->name('bank-soal.delete');
        Route::post('/bank-soal/import-json', [BankSoalController::class, 'importJson'])->name('bank-soal.import-json');

        // === REKAP HASIL UJIAN ===
        Route::get('/forms/{form}/hasil', [FormController::class, 'hasil'])->name('form.hasil');
        Route::delete('/hasil-ujian/{result}', [FormController::class, 'resetHasil'])->name('form.reset-hasil');
        Route::post('/forms/{form}/generate-soal', [FormController::class, 'generateSoal'])->name('form.generate-soal');
        Route::get('/forms/{form}/kelola-soal', [FormController::class, 'kelolaSoal'])->name('form.kelola-soal');
        Route::post('/forms/{form}/kelola-soal', [FormController::class, 'simpanSoal'])->name('form.simpan-soal');
    });

    // ====================================================
    // GROUP PERAWAT
    // ====================================================
    Route::prefix('perawat')->name('perawat.')->group(function () {
        // ARSIP DOKUMEN
Route::get('/arsip-dokumen', [PerawatArsipDokumenController::class, 'index'])->name('arsip.index');
Route::get('/arsip-dokumen/create', [PerawatArsipDokumenController::class, 'create'])->name('arsip.create');
Route::post('/arsip-dokumen', [PerawatArsipDokumenController::class, 'store'])->name('arsip.store');
Route::get('/arsip-dokumen/{id}/edit', [PerawatArsipDokumenController::class, 'edit'])->name('arsip.edit');
Route::put('/arsip-dokumen/{id}', [PerawatArsipDokumenController::class, 'update'])->name('arsip.update');
Route::delete('/arsip-dokumen/{id}', [PerawatArsipDokumenController::class, 'destroy'])->name('arsip.destroy');
        // KUNJUNGAN LUAR NEGERI
        Route::get('/kunjungan', [PerawatDrhController::class, 'kunjunganIndex'])->name('kunjungan.index');
        Route::get('/kunjungan/create', [PerawatDrhController::class, 'kunjunganCreate'])->name('kunjungan.create');
        Route::post('/kunjungan', [PerawatDrhController::class, 'kunjunganStore'])->name('kunjungan.store');
        Route::get('/kunjungan/{id}/edit', [PerawatDrhController::class, 'kunjunganEdit'])->name('kunjungan.edit');
        Route::put('/kunjungan/{id}', [PerawatDrhController::class, 'kunjunganUpdate'])->name('kunjungan.update');
        Route::delete('/kunjungan/{id}', [PerawatDrhController::class, 'kunjunganDestroy'])->name('kunjungan.destroy');
        // === PENGAJUAN OPPE ===
        Route::get('/oppe/{id}/cetak', [\App\Http\Controllers\OppeController::class, 'cetak'])->name('oppe.cetak');
        Route::get('/oppe/evaluasi-list', [App\Http\Controllers\OppeController::class, 'listEvaluasi'])->name('oppe.list_evaluasi');
        Route::get('/oppe/create', [App\Http\Controllers\OppeController::class, 'create'])->name('oppe.create');
        Route::post('/oppe/store', [App\Http\Controllers\OppeController::class, 'store'])->name('oppe.store');
        Route::get('/oppe/{id}/evaluate', [App\Http\Controllers\OppeController::class, 'evaluate'])->name('oppe.evaluate');
        Route::post('/oppe/{id}/store-evaluation', [App\Http\Controllers\OppeController::class, 'storeEvaluation'])->name('oppe.store_evaluation');
        Route::delete('/oppe/{id}', [App\Http\Controllers\OppeController::class, 'destroy'])->name('oppe.destroy');
        // Nanti kita buat route untuk isi self-assessment setelah ini
        Route::get('/drh/download-pdf', [PerawatDrhController::class, 'downloadPdf'])->name('drh.download_pdf');
        Route::get('/api/rekomendasi-pk', [DashboardController::class, 'getRekomendasiPk'])->name('api.rekomendasi.pk');
        Route::post('/ujian/hard-reset', [UserFormController::class, 'hardResetUjian'])->name('ujian.hard_reset');
        // --- 1. ALUR UTAMA: PENGAJUAN (UPLOAD -> UJIAN -> WAWANCARA -> SERTIFIKAT) ---

        // Halaman Riwayat & Status Pengajuan
        Route::get('/pengajuan', [PengajuanSertifikatController::class, 'index'])->name('pengajuan.index');
Route::get('/pengajuan/{id}/print-spkk', [PerawatLisensiController::class, 'printSpkk'])->name('pengajuan.print_spkk');
Route::get('/pengajuan/{id}/print-spk', [PerawatLisensiController::class, 'printSpk'])->name('pengajuan.print_spk');
        // Halaman Form Upload Berkas (Create) - [ROUTE UTAMA PENGAJUAN]
        Route::get('/lisensi/create/{metode?}', [PengajuanSertifikatController::class, 'create'])
            ->name('lisensi.create');

        // Proses Simpan Berkas
        Route::post('/pengajuan', [PengajuanSertifikatController::class, 'store'])->name('pengajuan.store');

        // Proses Simpan Jadwal Wawancara (Dipanggil setelah lulus ujian)
        Route::post('/pengajuan/{id}/wawancara', [PengajuanSertifikatController::class, 'storeWawancara'])->name('pengajuan.store_wawancara');

        // Cetak Sertifikat (Tombol dari halaman riwayat / selesai)
        Route::get('/pengajuan/{id}/print', [PengajuanSertifikatController::class, 'printSertifikat'])->name('pengajuan.print');


        // --- 2. HASIL AKHIR: DATA LISENSI SAYA ---

        // Menampilkan tabel lisensi yang sudah AKTIF
        Route::get('/lisensi-saya', [PerawatLisensiController::class, 'index'])->name('lisensi.index');

        // Tombol Download di halaman Lisensi Saya (Alias ke fungsi print)
        Route::get('/lisensi/{id}/generate', [PengajuanSertifikatController::class, 'printSertifikat'])->name('lisensi.generate');

        // --- 3. MENU UJIAN (CBT) ---
        Route::get('/ujian-aktif', [UserFormController::class, 'index'])->name('ujian.index');

        // Route untuk Mulai Ujian dari tombol "KERJAKAN UJIAN" di Pengajuan
        Route::get('/ujian/{form:slug}/kerjakan', [UserFormController::class, 'kerjakan'])->name('ujian.kerjakan');
        Route::post('/ujian/{form:slug}/submit', [UserFormController::class, 'submit'])->name('ujian.submit');
    // Route untuk melihat hasil ujian (Langsung pakai ID Result)
Route::get('/ujian/hasil/{result_id}', [UserFormController::class, 'lihatHasil'])->name('ujian.hasil');


        // --- 4. DRH & DATA DIRI (EXISTING) ---
        Route::get('/drh', [PerawatDrhController::class, 'index'])->name('drh');
        Route::get('/drh/identitas', [PerawatDrhController::class, 'editIdentitas'])->name('identitas.edit');
        Route::post('/drh/identitas', [PerawatDrhController::class, 'updateIdentitas'])->name('identitas.update');
        Route::get('/drh/data-lengkap', [PerawatDrhController::class, 'showDataLengkap'])->name('data.lengkap');

        // PENDIDIKAN (Manual Route Only - Resource dihapus agar tidak bentrok)
        Route::get('/pendidikan-list', [PerawatDrhController::class, 'pendidikanIndex'])->name('pendidikan.index');
        Route::get('/pendidikan/create', [PerawatDrhController::class, 'pendidikanCreate'])->name('pendidikan.create');
        Route::post('/pendidikan', [PerawatDrhController::class, 'pendidikanStore'])->name('pendidikan.store');
        Route::put('/pendidikan/{id}', [PerawatDrhController::class, 'pendidikanUpdate'])->name('pendidikan.update');
        Route::delete('/pendidikan/{id}', [PerawatDrhController::class, 'pendidikanDestroy'])->name('pendidikan.destroy');
        Route::get('/pendidikan/{id}/edit', [PerawatDrhController::class, 'pendidikanEdit'])->name('pendidikan.edit');

        // PELATIHAN
        Route::get('/pelatihan', [PerawatDrhController::class, 'pelatihanIndex'])->name('pelatihan.index');
        Route::get('/pelatihan/create', [PerawatDrhController::class, 'pelatihanCreate'])->name('pelatihan.create');
        Route::post('/pelatihan', [PerawatDrhController::class, 'pelatihanStore'])->name('pelatihan.store');
        Route::get('/pelatihan/{id}/edit', [PerawatDrhController::class, 'pelatihanEdit'])->name('pelatihan.edit');
        Route::put('/pelatihan/{id}', [PerawatDrhController::class, 'pelatihanUpdate'])->name('pelatihan.update');
        Route::delete('/pelatihan/{id}', [PerawatDrhController::class, 'pelatihanDestroy'])->name('pelatihan.destroy');

        // PEKERJAAN
        Route::get('/pekerjaan', [PerawatDrhController::class, 'pekerjaanIndex'])->name('pekerjaan.index');
        Route::get('/pekerjaan/create', [PerawatDrhController::class, 'pekerjaanCreate'])->name('pekerjaan.create');
        Route::post('/pekerjaan', [PerawatDrhController::class, 'pekerjaanStore'])->name('pekerjaan.store');
        Route::get('/pekerjaan/{id}/edit', [PerawatDrhController::class, 'pekerjaanEdit'])->name('pekerjaan.edit');
        Route::put('/pekerjaan/{id}', [PerawatDrhController::class, 'pekerjaanUpdate'])->name('pekerjaan.update');
        Route::delete('/pekerjaan/{id}', [PerawatDrhController::class, 'pekerjaanDestroy'])->name('pekerjaan.destroy');

        // KELUARGA
        Route::get('/keluarga', [PerawatDrhController::class, 'keluargaIndex'])->name('keluarga.index');
        Route::get('/keluarga/create', [PerawatDrhController::class, 'keluargaCreate'])->name('keluarga.create');
        Route::post('/keluarga', [PerawatDrhController::class, 'keluargaStore'])->name('keluarga.store');
        Route::get('/keluarga/{id}/edit', [PerawatDrhController::class, 'keluargaEdit'])->name('keluarga.edit');
        Route::put('/keluarga/{id}', [PerawatDrhController::class, 'keluargaUpdate'])->name('keluarga.update');
        Route::delete('/keluarga/{id}', [PerawatDrhController::class, 'keluargaDestroy'])->name('keluarga.destroy');

        // ORGANISASI
        Route::get('/organisasi', [PerawatDrhController::class, 'organisasiIndex'])->name('organisasi.index');
        Route::get('/organisasi/create', [PerawatDrhController::class, 'organisasiCreate'])->name('organisasi.create');
        Route::post('/organisasi', [PerawatDrhController::class, 'organisasiStore'])->name('organisasi.store');
        Route::get('/organisasi/{id}/edit', [PerawatDrhController::class, 'organisasiEdit'])->name('organisasi.edit');
        Route::put('/organisasi/{id}', [PerawatDrhController::class, 'organisasiUpdate'])->name('organisasi.update');
        Route::delete('/organisasi/{id}', [PerawatDrhController::class, 'organisasiDestroy'])->name('organisasi.destroy');

        // TANDA JASA
        Route::get('/tanda-jasa', [PerawatDrhController::class, 'tandajasaIndex'])->name('tandajasa.index');
        Route::get('/tanda-jasa/create', [PerawatDrhController::class, 'tandajasaCreate'])->name('tandajasa.create');
        Route::post('/tanda-jasa', [PerawatDrhController::class, 'tandajasaStore'])->name('tandajasa.store');
        Route::get('/tanda-jasa/{id}/edit', [PerawatDrhController::class, 'tandajasaEdit'])->name('tandajasa.edit');
        Route::put('/tanda-jasa/{id}', [PerawatDrhController::class, 'tandajasaUpdate'])->name('tandajasa.update');
        Route::delete('/tanda-jasa/{id}', [PerawatDrhController::class, 'tandajasaDestroy'])->name('tandajasa.destroy');

        // DOKUMEN STR & SIP (Data Diri, bukan Pengajuan)
        Route::get('/dokumen/str', [PerawatDrhController::class, 'strIndex'])->name('str.index');
        Route::get('/dokumen/str/create', [PerawatDrhController::class, 'strCreate'])->name('str.create');
        Route::post('/dokumen/str', [PerawatDrhController::class, 'strStore'])->name('str.store');
        Route::get('/dokumen/str/{id}/edit', [PerawatDrhController::class, 'strEdit'])->name('str.edit');
        Route::put('/dokumen/str/{id}', [PerawatDrhController::class, 'strUpdate'])->name('str.update');
        Route::delete('/dokumen/str/{id}', [PerawatDrhController::class, 'strDestroy'])->name('str.destroy');

        Route::get('/dokumen/sip', [PerawatDrhController::class, 'sipIndex'])->name('sip.index');
        Route::get('/dokumen/sip/create', [PerawatDrhController::class, 'sipCreate'])->name('sip.create');
        Route::post('/dokumen/sip', [PerawatDrhController::class, 'sipStore'])->name('sip.store');
        Route::get('/dokumen/sip/{id}/edit', [PerawatDrhController::class, 'sipEdit'])->name('sip.edit');
        Route::put('/dokumen/sip/{id}', [PerawatDrhController::class, 'sipUpdate'])->name('sip.update');
        Route::delete('/dokumen/sip/{id}', [PerawatDrhController::class, 'sipDestroy'])->name('sip.destroy');

        // DOKUMEN TAMBAHAN
        Route::get('/dokumen/tambahan', [PerawatDrhController::class, 'tambahanIndex'])->name('tambahan.index');
        Route::get('/dokumen/tambahan/create', [PerawatDrhController::class, 'tambahanCreate'])->name('tambahan.create');
        Route::post('/dokumen/tambahan', [PerawatDrhController::class, 'tambahanStore'])->name('tambahan.store');
        Route::get('/dokumen/tambahan/{id}/edit', [PerawatDrhController::class, 'tambahanEdit'])->name('tambahan.edit');
        Route::put('/dokumen/tambahan/{id}', [PerawatDrhController::class, 'tambahanUpdate'])->name('tambahan.update');
        Route::delete('/dokumen/tambahan/{id}', [PerawatDrhController::class, 'tambahanDestroy'])->name('tambahan.destroy');
    
        // Telegram
        Route::get('/telegram/link', [TelegramController::class, 'linkTelegram'])->name('telegram.link');
        Route::post('/telegram/generate-code', [TelegramController::class, 'generateCode'])->name('telegram.generate-code');
        Route::post('/telegram/unlink', [TelegramController::class, 'unlinkTelegram'])->name('telegram.unlink');
    });

    // === GROUP PEWAWANCARA ===
    Route::prefix('pewawancara')->name('pewawancara.')->group(function () {
        Route::post('/penilaian/{id}/batal-selesai', [PewawancaraController::class, 'batalSelesai'])->name('penilaian.batal_selesai');
        Route::get('/dashboard', [PewawancaraController::class, 'index'])->name('dashboard');
        Route::get('/antrian', [PewawancaraController::class, 'antrian'])->name('antrian');
        Route::get('/penilaian/{id}', [PewawancaraController::class, 'showPenilaian'])->name('penilaian');
        Route::post('/penilaian/{id}', [PewawancaraController::class, 'storePenilaian'])->name('penilaian.store');
        Route::get('/riwayat', [PewawancaraController::class, 'riwayat'])->name('riwayat');
        Route::get('/settings', [PewawancaraController::class, 'settings'])->name('settings');
        // Telegram Pewawancara
        Route::post('/telegram/generate', [AdminProfileController::class, 'generateCode'])->name('telegram.generate-code');
        Route::post('/telegram/unlink', [AdminProfileController::class, 'unlink'])->name('telegram.unlink');
        Route::post('/telegram/test', [AdminProfileController::class, 'testMessage'])->name('telegram.test');
    });

    Route::post('/webhook', [TelegramController::class, 'webhook'])->name('webhook');
});
