<?php

namespace App\Providers;

use Illuminate\Support\ServiceProvider;
use Illuminate\Support\Facades\View;
use Illuminate\Support\Facades\Auth;
use App\Models\Form;
use Carbon\Carbon;

class AppServiceProvider extends ServiceProvider
{
    /**
     * Register any application services.
     *
     * @return void
     */
    public function register()
    {
        //
    }

    /**
     * Bootstrap any application services.
     *
     * @return void
     */
    public function boot()
    {
        // View Composer: Berjalan di layout utama (layouts.app) atau semua view (*)
        // Menggunakan '*' agar aman jika variabel dipakai di file lain selain layout.
        View::composer('*', function ($view) {

            $ujianActiveCount = 0;

            // 1. Cek Login & Role
            if (Auth::check() && Auth::user()->role === 'perawat') {

                $user = Auth::user();
                $now = Carbon::now();

                // 2. Ambil Data Form (Query Database)
                // Kita ubah dari count() menjadi get() karena butuh ngecek isi datanya (hasil ujian)
                $forms = Form::with(['examResults' => function ($q) use ($user) {
                    $q->where('user_id', $user->id); // Load hasil ujian user ini
                }])
                    ->where('status', 'publish')
                    ->where('waktu_selesai', '>', $now) // Hanya yang belum kedaluwarsa
                    ->where(function ($query) use ($user) {
                        // Logika target peserta (Semua OR Khusus user ini)
                        $query->where('target_peserta', 'semua')
                            ->orWhereHas('participants', function ($q) use ($user) {
                                $q->where('users.id', $user->id);
                            });
                    })
                    ->get();

                // 3. Filter Logika Status (PHP)
                // Hitung hanya jika: (Belum Mengerjakan) ATAU (Sudah Mengerjakan TAPI Remidi)
                $ujianActiveCount = $forms->filter(function ($form) use ($now) {

                    // Cek apakah ujian sudah mulai
                    if ($now->lessThan($form->waktu_mulai)) {
                        return false; // Belum mulai -> Badge tidak dihitung
                    }

                    // Cek Hasil Terakhir
                    $lastResult = $form->examResults->sortByDesc('id')->first();
                    $isSubmitted = $lastResult !== null;

                    // Cek Status Remidi (True jika remidi atau nilai < 75)
                    $isRemidi = $lastResult && ($lastResult->remidi ?? ($lastResult->total_nilai < 75));

                    // KONDISI 1: Belum Mengerjakan -> HITUNG (Badge Muncul)
                    if (!$isSubmitted) {
                        return true;
                    }

                    // KONDISI 2: Sudah Mengerjakan TAPI Remidi -> HITUNG (Badge Muncul)
                    if ($isSubmitted && $isRemidi) {
                        return true;
                    }

                    // KONDISI 3: Sudah Lulus -> JANGAN HITUNG (Badge Hilang)
                    return false;
                })->count();
            }

            // Kirim variabel ke view
            $view->with('ujianActiveCount', $ujianActiveCount);
        });
    }
}