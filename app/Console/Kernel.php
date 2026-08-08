<?php

namespace App\Console;

use Illuminate\Console\Scheduling\Schedule;
use Illuminate\Foundation\Console\Kernel as ConsoleKernel;

class Kernel extends ConsoleKernel
{
    protected function schedule(Schedule $schedule)
    {
        // Jalankan pengecekan sertifikat setiap jam 8 pagi
        $schedule->command('sertifikat:check-expiry')
            ->dailyAt('08:00')
            ->timezone('Asia/Jakarta');

        // HAPUS telegram:polling dari sini!
    }

    protected function commands()
    {
        $this->load(__DIR__ . '/Commands');
        require base_path('routes/console.php');
    }
}
