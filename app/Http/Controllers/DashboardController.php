<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Carbon\Carbon;
use App\Models\Message;
// Import Semua Model
use App\Models\User;
use App\Models\PerawatProfile;
use App\Models\PerawatPendidikan;
use App\Models\PerawatPelatihan;
use App\Models\PerawatPekerjaan;
use App\Models\PerawatTandaJasa;
use App\Models\PerawatKeluarga;
use App\Models\PerawatOrganisasi;
use App\Models\PengajuanSertifikat;
use App\Models\JadwalWawancara;
use App\Models\PerawatStr;
use App\Models\PerawatSip;
use App\Models\PerawatLisensi;
use App\Models\PerawatDataTambahan;

class DashboardController extends Controller
{
    /**
     * DASHBOARD PERAWAT (Route: /dashboard)
     */
    public function index()
    {
        $user = Auth::user();

        if ($user->role !== 'perawat') {
            return redirect()->route('dashboard.admin');
        }

  $profile = PerawatProfile::where('user_id', $user->id)->first();

// --- LOGIC NORMALISASI TYPE PERAWAT (Sesuai cara Bapak) ---
$typePerawat = [];
if ($profile && $profile->type_perawat) {
    $tp = $profile->type_perawat;
    if (is_string($tp)) {
        $decoded = json_decode($tp, true);
        if (json_last_error() === JSON_ERROR_NONE && is_array($decoded)) {
            $typePerawat = $decoded;
        } else {
            $typePerawat = array_map('trim', explode(',', $tp));
        }
    } else {
        $typePerawat = $tp;
    }
}

// Cek Kelengkapan (Minimal 2: KFK dan PK/BK)
$isTypePerawatLengkap = count($typePerawat) >= 2;
// -------------------------------------------------------

// Tambahkan variabel age kembali agar tidak error
$age = ($profile && $profile->tanggal_lahir) ? \Carbon\Carbon::parse($profile->tanggal_lahir)->age : '-';
        // ==========================================
        // 2. HITUNG JUMLAH DATA (PORTFOLIO)
        // ==========================================
        // Menggunakan sintaks spesifik yang diminta user
        $counts = [
            'pendidikan' => PerawatPendidikan::where('user_id', $user->id)->count(),
            'pelatihan'  => PerawatPelatihan::where('user_id', $user->id)->count(),
            'pekerjaan'  => PerawatPekerjaan::where('user_id', $user->id)->count(),
            'keluarga'   => PerawatKeluarga::where('user_id', $user->id)->count(),
            'organisasi' => PerawatOrganisasi::where('user_id', $user->id)->count(),
            'tandajasa'  => PerawatTandaJasa::where('user_id', $user->id)->count(),
            'tambahan'   => PerawatDataTambahan::where('user_id', $user->id)->count(),
            'str'        => PerawatStr::where('user_id', $user->id)->count(),
            'sip'        => PerawatSip::where('user_id', $user->id)->count(),
        ];

        // ==========================================
        // 3. LOGIKA KELENGKAPAN DRH (Detailed)
        // ==========================================
        $sections = [
            [
                'id' => 'bio', 'nama' => 'Biodata Diri', 'icon' => 'bi-person-vcard',
                'status' => ($profile && $profile->nik && $profile->no_hp), 'wajib' => true
            ],
            [
                'id' => 'edu', 'nama' => 'Riwayat Pendidikan', 'icon' => 'bi-mortarboard',
                'status' => $counts['pendidikan'] > 0, 'wajib' => true
            ],
            [
                'id' => 'job', 'nama' => 'Riwayat Pekerjaan', 'icon' => 'bi-briefcase',
                'status' => $counts['pekerjaan'] > 0, 'wajib' => false
            ],
            [
                'id' => 'train', 'nama' => 'Pelatihan / Seminar', 'icon' => 'bi-ticket-perforated',
                'status' => $counts['pelatihan'] > 0, 'wajib' => true
            ],
            [
                'id' => 'fam', 'nama' => 'Data Keluarga', 'icon' => 'bi-people',
                'status' => $counts['keluarga'] > 0, 'wajib' => false
            ],
            [
                'id' => 'doc', 'nama' => 'Legalitas (STR/SIP)', 'icon' => 'bi-file-earmark-medical',
                'status' => ($counts['str'] > 0 || $counts['sip'] > 0), 'wajib' => true
            ],
            
            [
        'id' => 'bio', // Diarahkan ke bio/identitas juga
        'nama' => 'Kompetensi (KFK & PK/BK)', 
        'icon' => 'bi-shield-check',
        'status' => $isTypePerawatLengkap, 
        'wajib' => true
    ],
        ];

        $totalSections = count($sections);
        $completed = collect($sections)->where('status', true)->count();
        $progressPercent = $totalSections > 0 ? round(($completed / $totalSections) * 100) : 0;

        // ==========================================
        // 4. CEK LEGALITAS & COUNTDOWN
        // ==========================================
        $strData = PerawatStr::where('user_id', $user->id)->latest()->first();
        $sipData = PerawatSip::where('user_id', $user->id)->latest()->first();

        // Helper untuk status dokumen
        $checkDoc = function($doc) {
            if (!$doc) return ['status' => 'missing', 'msg' => 'Belum Upload', 'color' => 'secondary', 'days' => 0];
            $days = now()->diffInDays($doc->tgl_expired, false);
            if ($days < 0) return ['status' => 'expired', 'msg' => 'Kadaluwarsa', 'color' => 'danger', 'days' => $days];
            if ($days < 180) return ['status' => 'warning', 'msg' => 'Segera Habis', 'color' => 'warning', 'days' => $days];
            return ['status' => 'active', 'msg' => 'Aktif', 'color' => 'success', 'days' => $days];
        };

        $legalitas = [
            'str' => array_merge(['data' => $strData], $checkDoc($strData)),
            'sip' => array_merge(['data' => $sipData], $checkDoc($sipData)),
        ];

        // ==========================================
        // 5. STATUS PENGAJUAN TERAKHIR
        // ==========================================
        $latestPengajuan = PengajuanSertifikat::where('user_id', $user->id)
            ->with(['jadwalWawancara', 'penanggungJawab'])
            ->latest()
            ->first();
$warnings = [];
        // Jika status STR tidak aktif (missing/expired/warning), masukkan ke warnings
        if ($legalitas['str']['status'] !== 'active') {
            $warnings[] = 'STR (' . $legalitas['str']['msg'] . ')';
        }
        // Jika status SIP tidak aktif, masukkan ke warnings
        if ($legalitas['sip']['status'] !== 'active') {
            $warnings[] = 'SIP (' . $legalitas['sip']['msg'] . ')';
        }
        // ==========================================
        // 6. CHART DATA (Portfolio Composition)
        // ==========================================
        $chartData = [
            'labels' => ['Pendidikan', 'Pelatihan', 'Pekerjaan', 'Organisasi', 'Tanda Jasa'],
            'data'   => [$counts['pendidikan'], $counts['pelatihan'], $counts['pekerjaan'], $counts['organisasi'], $counts['tandajasa']]
        ];
        
       // Ambil pesan terakhir yang dikirim OLEH Admin KE Perawat ini
$lastMessage = Message::where('receiver_id', $user->id)
    ->whereHas('sender', function($q) {
        $q->where('role', 'admin');
    })
    ->latest()
    ->first();

// Hitung pesan yang belum dibaca khusus dari Admin
$unreadCount = Message::where('receiver_id', $user->id)
    ->where('is_read', false)
    ->whereHas('sender', function($q) {
        $q->where('role', 'admin');
    })
    ->count();

// Kirim ID pengirimnya biar modal tau harus bales ke admin yang mana
$adminId = $lastMessage ? $lastMessage->sender_id : null;
$adminName = $lastMessage ? ($lastMessage->sender->name ?? 'Admin') : 'Admin';

        return view('dashboard.index', compact(
            'user', 'profile', 'age', 'counts', 'sections', 'progressPercent', 
            'legalitas', 'latestPengajuan', 'chartData','warnings','isTypePerawatLengkap','typePerawat','lastMessage', 'unreadCount','adminName'
        ));
    }

    /**
     * DASHBOARD ADMIN (Route: /dashboard/admin)
     */
    public function adminIndex()
    {
        // ==========================================
        // 1. STATISTIK UTAMA (BIG NUMBERS)
        // ==========================================
        $now = Carbon::now();
        
        $stats = [
            'total_perawat' => User::where('role', 'perawat')->count(),
            'total_users'   => User::count(),
            'pending_verif' => PengajuanSertifikat::where('status', 'pending')->count(),
            'lulus_total'   => PengajuanSertifikat::where('status', 'disetujui')->count(),
            'active_str'    => PerawatStr::where('tgl_expired', '>', $now)->count(),
            'expired_str'   => PerawatStr::where('tgl_expired', '<', $now)->count(),
        ];

        // ==========================================
        // 2. STATISTIK DEMOGRAFI (GENDER)
        // ==========================================
        $genderStats = PerawatProfile::select('jenis_kelamin', DB::raw('count(*) as total'))
            ->groupBy('jenis_kelamin')
            ->pluck('total', 'jenis_kelamin')
            ->toArray();
        
        // Normalisasi data gender (Laki-laki/Perempuan/Lainnya)
        $chartGender = [
            'Laki-laki' => $genderStats['Laki-laki'] ?? 0,
            'Perempuan' => $genderStats['Perempuan'] ?? 0,
        ];

        // ==========================================
        // 3. STATISTIK PENDIDIKAN (JENJANG)
        // ==========================================
        // Mengambil data pendidikan terakhir (asumsi logic: ambil jenjang terbanyak)
        $eduStats = PerawatPendidikan::select('jenjang', DB::raw('count(*) as total'))
            ->groupBy('jenjang')
            ->orderBy('total', 'desc')
            ->limit(5) // Top 5 jenjang
            ->pluck('total', 'jenjang')
            ->toArray();

        // ==========================================
        // 4. CHART TREND BULANAN (PENGAJUAN)
        // ==========================================
        $monthlyData = PengajuanSertifikat::select(
                DB::raw('MONTH(created_at) as month'),
                DB::raw('COUNT(*) as total'),
                DB::raw('SUM(CASE WHEN status = "disetujui" THEN 1 ELSE 0 END) as approved')
            )
            ->whereYear('created_at', $now->year)
            ->groupBy('month')
            ->get();

        $chartTrend = [
            'labels' => ['Jan', 'Feb', 'Mar', 'Apr', 'Mei', 'Jun', 'Jul', 'Agu', 'Sep', 'Okt', 'Nov', 'Des'],
            'total' => array_fill(0, 12, 0),
            'approved' => array_fill(0, 12, 0),
        ];

        foreach ($monthlyData as $row) {
            $chartTrend['total'][$row->month - 1] = $row->total;
            $chartTrend['approved'][$row->month - 1] = $row->approved;
        }

        // ==========================================
        // 5. DATA TABEL TERBARU (PENGAJUAN & USER)
        // ==========================================
        $recentPengajuans = PengajuanSertifikat::with(['user.profile'])
            ->latest()
            ->take(6)
            ->get();

        $newUsers = User::where('role', 'perawat')
            ->with('profile')
            ->latest()
            ->take(5)
            ->get();

        // ==========================================
        // 6. JADWAL WAWANCARA TERDEKAT
        // ==========================================
        $upcomingInterviews = JadwalWawancara::with(['pengajuan.user.profile', 'pewawancara'])
            ->where('waktu_wawancara', '>=', $now)
            ->orderBy('waktu_wawancara', 'asc')
            ->take(5)
            ->get();

       $semuaPengajuan = PengajuanSertifikat::with('user.profile')
        ->whereNotIn('status', ['ditolak', 'gagal']) // Ambil yang sedang jalan atau selesai
        ->get();

    $progressKlinis = [];

   foreach ($semuaPengajuan as $p) {
        $profesi = $p->user->profile->profesi ?? '';
        
        $kfkRaw = is_array($p->kfk) ? implode(' ', $p->kfk) : $p->kfk;
        $gabungan = ($p->bidang ?? '') . ' ' . $kfkRaw;
        
        $type = '';
        $level = '';

        if (preg_match('/(PK|BK)\s*([1-5]|I{1,3}|IV|V)/i', $gabungan, $matches)) {
            $type = strtoupper($matches[1]);
            $level = strtoupper($matches[2]);
        } elseif (preg_match('/(PK|BK)/i', $gabungan, $matches)) {
            $type = strtoupper($matches[1]);
        }

        // KOREKSI OTOMATIS BERDASARKAN PROFESI DI DASHBOARD ADMIN
        $profesiStr = strtolower(trim($profesi));
        if (str_contains($profesiStr, 'bidan')) $type = 'BK';
        elseif (str_contains($profesiStr, 'perawat')) $type = 'PK';

        $jenjang = '-';
        if ($type && $level) {
            $map = ['1' => 'I', '2' => 'II', '3' => 'III', '4' => 'IV', '5' => 'V'];
            $level = $map[$level] ?? $level;
            $jenjang = $type . ' ' . $level;
        }

        if ($jenjang === '-') continue; // Skip jika tetap tidak terdeteksi

        if (!isset($progressKlinis[$jenjang])) {
            $progressKlinis[$jenjang] = [
                'nama_jenjang' => $jenjang,
                'total' => 0,
                'selesai' => 0,
                'list_selesai' => [],
                'list_belum' => [],
                'id_pengajuan' => []
            ];
        }

        $progressKlinis[$jenjang]['total'] += 1;
        $progressKlinis[$jenjang]['id_pengajuan'][] = $p->id;

        if (in_array($p->status, ['completed', 'disetujui'])) {
            $progressKlinis[$jenjang]['selesai'] += 1;
            $progressKlinis[$jenjang]['list_selesai'][] = $p;
        } else {
            $progressKlinis[$jenjang]['list_belum'][] = $p;
        }
    }
    // Hitung persentase untuk masing-masing jenjang
    foreach ($progressKlinis as $key => &$pk) {
        $pk['persen'] = $pk['total'] > 0 ? round(($pk['selesai'] / $pk['total']) * 100) : 0;
    }

    return view('dashboard.admin', compact('stats', 'chartGender', 'eduStats', 'chartTrend', 'recentPengajuans', 'upcomingInterviews', 'progressKlinis'));
}
}