<?php

namespace App\Http\Controllers;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use App\Models\User;
use App\Models\PerawatProfile;
use App\Models\PerawatPendidikan;
use App\Models\PerawatPelatihan;
use App\Models\PerawatPekerjaan;
use App\Models\PerawatKeluarga;
use App\Models\PerawatOrganisasi;
use App\Models\PerawatTandaJasa;
use App\Models\PerawatSip;
use App\Models\PerawatStr;
use App\Models\PerawatDataTambahan;
use App\Models\PerawatLisensi;
use App\Models\PengajuanSertifikat;
use App\Models\ExamResult;
use App\Services\PkRecommendationService;
class AdminPerawatController extends Controller
{
    // LIST SEMUA PERAWAT
    // public function index(Request $request)
    // {
    //      $search = $request->input('search');

    //      $perawat = User::where('role', 'perawat')
    //          ->with(['profile', 'strs', 'sips', 'lisensis', 'dataTambahans'])
    //          ->when($search, function ($q) use ($search) {
    //              $q->where('name', 'like', "%$search%")
    //                  ->orWhereHas('profile', function ($q) use ($search) {
    //                      $q->where('nik', 'like', "%$search%")
    //                          ->orWhere('no_hp', 'like', "%$search%")
    //                          ->orWhere('alamat', 'like', "%$search%");
    //                  });
    //          })
    //          ->orderBy('name')
    //          ->paginate(10)
    //          ->withQueryString();
            
            
    //      $allKfk = PerawatProfile::whereNotNull('type_perawat')
    //      ->pluck('type_perawat')
    //      ->flatten()
    //      ->unique()
    //      ->sort()
    //      ->values();

    //      // Hitung jumlah dokumen expired untuk setiap perawat
    //      $now = now();
    //      foreach ($perawat as $p) {
    //          $p->expired_docs_count = self::countExpiredDocuments($p, $now);
    //      }

    //    return view('admin.perawat.index', compact('perawat', 'search', 'allKfk'));
    // }

    /**
     * Hitung jumlah dokumen expired untuk 1 user
     * @param \App\Models\User $user
     * @param \Carbon\Carbon $now
     * @return int
     */
    public static function countExpiredDocuments($user, $now)
    {
        $count = 0;
        if ($user->relationLoaded('strs')) {
            $count += $user->strs->where('tgl_expired', '<', $now)->count();
        }
        if ($user->relationLoaded('sips')) {
            $count += $user->sips->where('tgl_expired', '<', $now)->count();
        }
        if ($user->relationLoaded('lisensis')) {
            $count += $user->lisensis->where('tgl_expired', '<', $now)->count();
        }
        if ($user->relationLoaded('dataTambahans')) {
            $count += $user->dataTambahans->where('tgl_expired', '<', $now)->count();
        }
        return $count;
    }

// ==========================================
    // FUNGSI UPDATE SERTIFIKAT DARI HALAMAN EDIT
    // ==========================================
    public function updateStr(Request $request, $id)
    {
        $request->validate(['dokumen' => 'nullable|file|mimes:pdf,jpg,jpeg,png|max:5120']);
        $str = \App\Models\PerawatStr::findOrFail($id);
        $str->nomor = $request->nomor;
        $str->tgl_terbit = $request->tgl_terbit;
        $str->tgl_expired = $request->tgl_expired;
        
        if ($request->hasFile('dokumen')) {
            if ($str->file_path && \Storage::disk('public')->exists($str->file_path)) {
                \Storage::disk('public')->delete($str->file_path);
            }
            $str->file_path = $request->file('dokumen')->store('dokumen_str', 'public');
        }
        $str->save();
        return back()->with('success', 'Data STR berhasil diupdate.');
    }

    public function updateSip(Request $request, $id)
    {
        $request->validate(['dokumen' => 'nullable|file|mimes:pdf,jpg,jpeg,png|max:5120']);
        $sip = \App\Models\PerawatSip::findOrFail($id);
        $sip->nomor = $request->nomor;
        $sip->tgl_terbit = $request->tgl_terbit;
        $sip->tgl_expired = $request->tgl_expired;
        
        if ($request->hasFile('dokumen')) {
            if ($sip->file_path && \Storage::disk('public')->exists($sip->file_path)) {
                \Storage::disk('public')->delete($sip->file_path);
            }
            $sip->file_path = $request->file('dokumen')->store('dokumen_sip', 'public');
        }
        $sip->save();
        return back()->with('success', 'Data SIP berhasil diupdate.');
    }

    public function updateLisensi(Request $request, $id)
    {
        $request->validate(['dokumen' => 'nullable|file|mimes:pdf,jpg,jpeg,png|max:5120']);
        $lisensi = \App\Models\PerawatLisensi::findOrFail($id);
        $lisensi->nomor = $request->nomor;
        $lisensi->nama = $request->nama;
        $lisensi->lembaga = $request->lembaga;
        $lisensi->tgl_terbit = $request->tgl_terbit;
        $lisensi->tgl_expired = $request->tgl_expired;
        
        if ($request->hasFile('dokumen')) {
            if ($lisensi->file_path && \Storage::disk('public')->exists($lisensi->file_path)) {
                \Storage::disk('public')->delete($lisensi->file_path);
            }
            $lisensi->file_path = $request->file('dokumen')->store('dokumen_lisensi', 'public');
        }
        $lisensi->save();
        return back()->with('success', 'Data Lisensi berhasil diupdate.');
    }

    public function updateTambahan(Request $request, $id)
    {
        $request->validate(['dokumen' => 'nullable|file|mimes:pdf,jpg,jpeg,png|max:5120']);
        $tambahan = \App\Models\PerawatDataTambahan::findOrFail($id);
        $tambahan->nomor = $request->nomor;
        $tambahan->nama = $request->nama;
        $tambahan->lembaga = $request->lembaga;
        $tambahan->tgl_terbit = $request->tgl_terbit;
        $tambahan->tgl_expired = $request->tgl_expired;
        $tambahan->skp = $request->skp;
        $tambahan->is_lifetime = $request->has('is_lifetime') ? 1 : 0;
        
        if ($request->hasFile('dokumen')) {
            if ($tambahan->file_path && \Storage::disk('public')->exists($tambahan->file_path)) {
                \Storage::disk('public')->delete($tambahan->file_path);
            }
            $tambahan->file_path = $request->file('dokumen')->store('dokumen_tambahan', 'public');
        }
        $tambahan->save();
        return back()->with('success', 'Data Tambahan berhasil diupdate.');
    }
    // DETAIL DRH PER PERAWAT
    public function show($id)
    {
        $user = User::where('role', 'perawat')->findOrFail($id);

        $profile    = PerawatProfile::where('user_id', $user->id)->first();
        $pendidikan = PerawatPendidikan::where('user_id', $user->id)->get();
        $pelatihan  = PerawatPelatihan::where('user_id', $user->id)->get();
        $pekerjaan  = PerawatPekerjaan::where('user_id', $user->id)->get();
        $keluarga   = PerawatKeluarga::where('user_id', $user->id)->get();
        $organisasi = PerawatOrganisasi::where('user_id', $user->id)->get();
        $tandajasa  = PerawatTandaJasa::where('user_id', $user->id)->get();

        // Tambahkan data sertifikat
        $sip = PerawatSip::where('user_id', $user->id)->get();
        $str = PerawatStr::where('user_id', $user->id)->get();
        $dataTambahan = PerawatDataTambahan::where('user_id', $user->id)->get();
        $lisensi = PerawatLisensi::where('user_id', $user->id)->get();

        return view('admin.perawat.show', compact(
            'user',
            'profile',
            'pendidikan',
            'pelatihan',
            'pekerjaan',
            'keluarga',
            'organisasi',
            'tandajasa',
            'sip',
            'str',
            'dataTambahan',
            'lisensi'
        ));
    }

    /**
     * RIWAYAT TIMELINE KREDENSIALING / UKOM PER PESERTA
     * Disusun dari data yang sudah ada (pengajuan, hasil ujian, jadwal & penilaian wawancara, lisensi).
     */
    public function timeline($id)
    {
        $user = User::where('role', 'perawat')->findOrFail($id);
        $profile = PerawatProfile::where('user_id', $user->id)->first();

        $pengajuans = PengajuanSertifikat::with(['jadwalWawancara.penilaian', 'jadwalWawancara.pewawancara', 'lisensiLama'])
            ->where('user_id', $user->id)
            ->orderBy('created_at')
            ->get();

        $exams = ExamResult::with('form')
            ->where('user_id', $user->id)
            ->orderBy('created_at')
            ->get();

        $metodeLabel = [
            'pg_only'        => 'Ujian Tulis (PG)',
            'pg_interview'   => 'Ujian Tulis + Wawancara',
            'interview_only' => 'Wawancara Saja',
        ];

        $statusLabel = [
            'pending'             => ['Menunggu Verifikasi', 'warning'],
            'verified'            => ['Terverifikasi', 'info'],
            'method_selected'     => ['Tahap Ujian', 'info'],
            'exam_passed'         => ['Lulus Ujian, Menunggu Wawancara', 'info'],
            'interview_scheduled' => ['Wawancara Terjadwal', 'info'],
            'interview_failed'    => ['Tidak Lulus Wawancara', 'danger'],
            'completed'           => ['Selesai - Lisensi Terbit', 'success'],
            'disetujui'           => ['Selesai - Lisensi Terbit', 'success'],
            'rejected'            => ['Ditolak', 'danger'],
        ];

        $tgl = fn($d) => $d ? \Carbon\Carbon::parse($d)->translatedFormat('d M Y') : '-';

        $riwayat = $pengajuans->values()->map(function ($p, $i) use ($pengajuans, $exams, $metodeLabel, $statusLabel, $tgl) {
            $next = $pengajuans->get($i + 1);
            $steps = [];

            // 1. Pengajuan dibuat
            $steps[] = [
                'state' => 'done',
                'icon'  => 'bi-file-earmark-plus',
                'title' => 'Pengajuan ' . ucfirst($p->jenis_pengajuan ?? '') . ' dibuat',
                'time'  => $p->created_at,
                'desc'  => $p->lisensiLama && $p->jenis_pengajuan === 'perpanjangan'
                    ? 'Perpanjangan dari lisensi ' . ($p->lisensiLama->nomor ?? '-')
                    : null,
            ];

            // 2. Verifikasi berkas & pemilihan metode
            $lewatVerifikasi = !in_array($p->status, ['pending', 'rejected']) || $p->metode;
            $steps[] = [
                'state' => $lewatVerifikasi ? 'done' : ($p->status === 'rejected' ? 'skip' : 'current'),
                'icon'  => 'bi-patch-check',
                'title' => 'Verifikasi berkas & penentuan metode',
                'time'  => null,
                'desc'  => $p->metode ? 'Metode: ' . ($metodeLabel[$p->metode] ?? $p->metode) : 'Menunggu verifikasi admin',
            ];

            // 3. Ujian tulis (semua percobaan dalam rentang pengajuan ini)
            if ($p->metode !== 'interview_only') {
                $attempts = $exams->filter(function ($e) use ($p, $next) {
                    return $e->created_at >= $p->created_at && (!$next || $e->created_at < $next->created_at);
                })->values();

                foreach ($attempts as $n => $e) {
                    $lulus = $e->total_nilai >= 75;
                    $steps[] = [
                        'state' => $lulus ? 'done' : 'failed',
                        'icon'  => 'bi-pencil-square',
                        'title' => 'Ujian tulis' . ($attempts->count() > 1 ? ' (percobaan ' . ($n + 1) . ')' : '') . ($e->remidi ? ' - Remidi' : ''),
                        'time'  => $e->waktu_selesai ?? $e->created_at,
                        'desc'  => ($e->form->judul ?? 'Ujian') . ' · Nilai ' . $e->total_nilai
                            . ' (benar ' . $e->total_benar . ', salah ' . $e->total_salah . ') · ' . ($lulus ? 'Memenuhi KKM' : 'Di bawah KKM 75'),
                    ];
                }

                if ($attempts->isEmpty() && in_array($p->status, ['pending', 'verified', 'method_selected'])) {
                    $steps[] = ['state' => $p->status === 'method_selected' ? 'current' : 'todo', 'icon' => 'bi-pencil-square',
                        'title' => 'Ujian tulis', 'time' => null, 'desc' => 'Belum mengerjakan ujian'];
                }

                $nilaiValid = in_array($p->status, ['exam_passed', 'interview_scheduled', 'interview_failed', 'completed', 'disetujui']);
                if ($attempts->isNotEmpty() || $nilaiValid) {
                    $steps[] = [
                        'state' => $nilaiValid ? 'done' : ($p->status === 'rejected' ? 'skip' : 'current'),
                        'icon'  => 'bi-clipboard-check',
                        'title' => 'Validasi nilai oleh admin',
                        'time'  => null,
                        'desc'  => $nilaiValid ? 'Nilai disetujui' : 'Menunggu validasi',
                    ];
                }
            }

            // 4. Wawancara / uji skill
            if ($p->metode !== 'pg_only') {
                $jadwal = $p->jadwalWawancara;
                if ($jadwal) {
                    $steps[] = [
                        'state' => 'done',
                        'icon'  => 'bi-calendar-event',
                        'title' => 'Jadwal wawancara ditetapkan',
                        'time'  => $jadwal->created_at,
                        'desc'  => 'Pelaksanaan ' . optional($jadwal->waktu_wawancara)->translatedFormat('d M Y, H:i')
                            . ' · ' . ($jadwal->lokasi ?? '-')
                            . ($jadwal->pewawancara ? ' · Pewawancara: ' . $jadwal->pewawancara->nama : ''),
                    ];

                    if ($pen = $jadwal->penilaian) {
                        $lulus = $pen->keputusan === 'lulus';
                        $steps[] = [
                            'state' => $lulus ? 'done' : 'failed',
                            'icon'  => 'bi-person-check',
                            'title' => 'Hasil wawancara: ' . ($lulus ? 'Lulus' : 'Tidak Lulus'),
                            'time'  => $pen->created_at,
                            'desc'  => $pen->catatan_pewawancara,
                        ];
                    } else {
                        $steps[] = ['state' => in_array($p->status, ['rejected']) ? 'skip' : 'current', 'icon' => 'bi-person-check',
                            'title' => 'Penilaian wawancara', 'time' => null, 'desc' => 'Menunggu penilaian pewawancara'];
                    }
                } elseif (!in_array($p->status, ['rejected', 'completed', 'disetujui'])) {
                    $steps[] = ['state' => $p->status === 'exam_passed' || $p->metode === 'interview_only' ? 'current' : 'todo',
                        'icon' => 'bi-calendar-event', 'title' => 'Wawancara / uji skill', 'time' => null, 'desc' => 'Belum dijadwalkan'];
                }
            }

            // 5. Hasil akhir
            if (in_array($p->status, ['completed', 'disetujui'])) {
                $lis = $p->lisensiLama;
                $steps[] = [
                    'state' => 'done',
                    'icon'  => 'bi-award',
                    'title' => 'Lisensi / sertifikat terbit',
                    'time'  => $p->updated_at,
                    'desc'  => $lis
                        ? ($lis->nomor ?? '-') . ' · Berlaku ' . $tgl($lis->tgl_terbit) . ' s/d ' . $tgl($lis->tgl_expired)
                        : null,
                ];
            } elseif ($p->status === 'rejected') {
                $steps[] = ['state' => 'failed', 'icon' => 'bi-x-octagon', 'title' => 'Pengajuan ditolak',
                    'time' => $p->updated_at, 'desc' => $p->keterangan];
            } elseif ($p->status === 'interview_failed') {
                $steps[] = ['state' => 'failed', 'icon' => 'bi-x-octagon', 'title' => 'Proses berhenti: tidak lulus wawancara',
                    'time' => $p->updated_at, 'desc' => $p->keterangan];
            } else {
                $steps[] = ['state' => 'todo', 'icon' => 'bi-award', 'title' => 'Lisensi / sertifikat terbit', 'time' => null, 'desc' => null];
            }

            return [
                'pengajuan' => $p,
                'metode'    => $metodeLabel[$p->metode] ?? '-',
                'status'    => $statusLabel[$p->status] ?? [ucfirst(str_replace('_', ' ', $p->status)), 'secondary'],
                'steps'     => $steps,
            ];
        })->reverse()->values();

        return view('admin.perawat.timeline', compact('user', 'profile', 'riwayat'));
    }

    public function edit($id)
    {
        $user = User::where('role', 'perawat')->findOrFail($id);

        // Gunakan firstOrCreate untuk menghindari error jika profil belum dibuat user
        $profile = PerawatProfile::firstOrCreate(['user_id' => $user->id]);

        // AMBIL DATA RELASI
        $pendidikan = PerawatPendidikan::where('user_id', $id)->orderBy('tahun_lulus', 'desc')->get();
        $pelatihan  = PerawatPelatihan::where('user_id', $id)->orderBy('tanggal_mulai', 'desc')->get();
        $pekerjaan  = PerawatPekerjaan::where('user_id', $id)->orderBy('tahun_mulai', 'desc')->get();
        $keluarga   = PerawatKeluarga::where('user_id', $id)->get();
        $organisasi = PerawatOrganisasi::where('user_id', $id)->orderBy('tahun_mulai', 'desc')->get();
        $tandajasa  = PerawatTandaJasa::where('user_id', $id)->orderBy('tahun', 'desc')->get();

        // Tambahkan data sertifikat
        $sip = PerawatSip::where('user_id', $id)->orderBy('tgl_expired', 'desc')->get();
        $str = PerawatStr::where('user_id', $id)->orderBy('tgl_expired', 'desc')->get();
        $dataTambahan = PerawatDataTambahan::where('user_id', $id)->orderBy('tgl_expired', 'desc')->get();
        $lisensi = PerawatLisensi::where('user_id', $id)->orderBy('tgl_expired', 'desc')->get();

        return view('admin.perawat.edit', compact(
            'user',
            'profile',
            'pendidikan',
            'pelatihan',
            'pekerjaan',
            'keluarga',
            'organisasi',
            'tandajasa',
            'sip',
            'str',
            'dataTambahan',
            'lisensi'
        ));
    }
// public function index(Request $request, PkRecommendationService $pkService) // <-- Inject Service
//     {
//         $search = $request->input('search');

//         $perawat = User::where('role', 'perawat')
//             ->with(['profile', 'strs', 'sips', 'lisensis', 'dataTambahans', 'perawatPekerjaans']) // Pastikan perawatPekerjaans ikut di-load
//             ->when($search, function ($q) use ($search) {
//                 $q->where('name', 'like', "%$search%")
//                     ->orWhereHas('profile', function ($q) use ($search) {
//                         $q->where('nik', 'like', "%$search%")
//                             ->orWhere('no_hp', 'like', "%$search%");
//                     });
//             })
//             ->orderBy('name')
//             ->paginate(10)
//             ->withQueryString();

//         $allKfk = PerawatProfile::whereNotNull('type_perawat')
//             ->get()
//             ->pluck('type_perawat')
//             ->flatten()
//             ->filter()
//             ->unique()
//             ->sort()
//             ->values();

//         $now = now();
//         foreach ($perawat as $p) {
//             $p->expired_docs_count = self::countExpiredDocuments($p, $now);
//             // SISIPKAN HASIL REKOMENDASI PK KE SETIAP USER
//             $p->rekomendasiPk = $pkService->getRecommendation($p->id);
//         }

//         return view('admin.perawat.index', compact('perawat', 'search', 'allKfk'));
//     }

    // 2. TAMBAHKAN FUNGSI EXPORT BARU
    public function exportDataByPkKfk(Request $request)
    {
        $reqPk = $request->input('filter_pk');
        $reqKfk = $request->input('filter_kfk');

        $query = User::where('role', 'perawat')->with(['profile', 'perawatPekerjaans']);

        if ($reqPk || $reqKfk) {
            $query->whereHas('profile', function($q) use ($reqPk, $reqKfk) {
                if ($reqPk && $reqKfk) {
                     $q->where('type_perawat', 'LIKE', "%{$reqPk}%")
                       ->where('type_perawat', 'LIKE', "%{$reqKfk}%");
                } elseif ($reqPk) {
                     $q->where('type_perawat', 'LIKE', "%{$reqPk}%");
                } elseif ($reqKfk) {
                     $q->where('type_perawat', 'LIKE', "%{$reqKfk}%");
                }
            });
        }

        $data = $query->get();

        $namaFile = "Data_Perawat_" . ($reqPk ? str_replace(' ', '', $reqPk) : 'ALLPK') . "_" . ($reqKfk ? str_replace(' ', '', $reqKfk) : 'ALLKFK') . "_" . date('Ymd') . ".xls";

        header("Content-Type: application/vnd.ms-excel");
        header("Content-Disposition: attachment; filename=$namaFile");
        header("Pragma: no-cache");
        header("Expires: 0");

        echo '<table border="1">';
        echo '<tr>
                <th style="background-color: #D3D3D3;">NO</th>
                <th style="background-color: #D3D3D3;">NAMA LENGKAP</th>
                <th style="background-color: #D3D3D3;">NIK</th>
                <th style="background-color: #D3D3D3;">RUANGAN (UNIT KERJA)</th>
                <th style="background-color: #D3D3D3;">JENJANG PK</th>
                <th style="background-color: #D3D3D3;">KFK</th>
              </tr>';

        foreach ($data as $i => $u) {
            $p = $u->profile;
            
            // Ambil Ruangan/Unit Kerja dari riwayat pekerjaan terbaru
            $pekerjaanAktif = $u->perawatPekerjaans->sortByDesc('tahun_mulai')->first();
            $ruangan = $pekerjaanAktif ? $pekerjaanAktif->unit_kerja : '-';

            // Pisahkan array JSON type_perawat menjadi PK dan KFK
            $types = is_string($p->type_perawat) ? json_decode($p->type_perawat, true) : ($p->type_perawat ?? []);
            $kfkList = [];
            $pkList = [];
            
            if(is_array($types)) {
                foreach($types as $t) {
                    if(str_contains(strtoupper($t), 'KFK')) {
                        $kfkList[] = $t;
                    } else if(str_contains(strtoupper($t), 'PK') || str_contains(strtoupper($t), 'BK')) {
                        $pkList[] = $t;
                    }
                }
            }
            
            $strPk = !empty($pkList) ? implode(', ', $pkList) : 'PRA PK';
            $strKfk = !empty($kfkList) ? implode(', ', $kfkList) : '-';

            echo '<tr>';
            echo '<td>' . ($i + 1) . '</td>';
            echo '<td>' . $u->name . '</td>';
            echo '<td>' . ($p->nik ?? '-') . '&nbsp;</td>'; // Tambah &nbsp; agar NIK tidak jadi scientific format di Excel
            echo '<td>' . $ruangan . '</td>';
            echo '<td>' . $strPk . '</td>';
            echo '<td>' . $strKfk . '</td>';
            echo '</tr>';
        }
        echo '</table>';
        exit;
    }
 // TOGGLE OPPE (AUTO, ON, OFF)
    public function toggleOppe($id)
    {
        $profile = \App\Models\PerawatProfile::where('user_id', $id)->first();
        if (!$profile) {
            return back()->with('error', 'Profil perawat tidak ditemukan.');
        }

        // 1. Cek apakah masuk jalur AUTO ON (Tanggal 7-10 Januari ATAU Bulan Terbit RKK)
        $isAutoOpen = false;
        $today = now();
        
        // Cek Jalur Serentak (7 - 10 Januari)
        if ($today->month == 7 && $today->day >= 1 && $today->day <= 30) {
            $isAutoOpen = true;
        }

        // Cek Jalur RKK
        $user = \App\Models\User::with('lisensis')->find($id);
        $rkkActive = $user->lisensis->where('status', 'active')->sortByDesc('tgl_terbit')->first();
        
        if ($rkkActive && $rkkActive->tgl_terbit) {
            $mTerbit = \Carbon\Carbon::parse($rkkActive->tgl_terbit)->month;
            $mAfter = ($mTerbit % 12) + 1;
            if ($today->month == $mTerbit || $today->month == $mAfter) {
                $isAutoOpen = true;
            }
        }

        // 2. Tentukan status NYATA saat ini (Nyala atau Mati?)
        $isCurrentlyOn = false;
        if ($profile->oppe_status === 'on') {
            $isCurrentlyOn = true;
        } elseif ($profile->oppe_status === 'off') {
            $isCurrentlyOn = false;
        } else {
            // Kalau 'auto', maka status nyata mengikuti jadwal
            $isCurrentlyOn = $isAutoOpen; 
        }

        // 3. Kita BALIKKAN keadaannya
        if ($isCurrentlyOn) {
            $profile->oppe_status = 'off';
            $msg = "Akses OPPE berhasil ditutup paksa (MANUAL OFF).";
        } else {
            $profile->oppe_status = 'on';
            $msg = "Akses OPPE berhasil dibuka paksa (MANUAL ON).";
        }

        $profile->save();
        return back()->with('success', $msg);
    }
public function index(Request $request, PkRecommendationService $pkService)
    {
        $search = $request->input('search');
        $filterPk = $request->input('filter_pk');
        $filterRuangan = $request->input('filter_ruangan');
        $filterStatus = $request->input('filter_status'); // <-- TAMBAHAN BARU

        // 1. Query Dasar
        $perawatQuery = User::where('role', 'perawat')
            ->with(['profile', 'strs', 'sips', 'lisensis', 'dataTambahans', 'perawatPekerjaans', 'arsipDokumens']);
            
        // 2. Filter Search (Nama, NIK, HP)
        if ($search) {
            $perawatQuery->where(function ($q) use ($search) {
                $q->where('name', 'like', "%$search%")
                    ->orWhereHas('profile', function ($qProfile) use ($search) {
                        $qProfile->where('nik', 'like', "%$search%")
                            ->orWhere('no_hp', 'like', "%$search%");
                    });
            });
        }

        // 3. Filter berdasarkan PK/BK
        if ($filterPk) {
            $perawatQuery->whereHas('profile', function ($qProfile) use ($filterPk) {
                $qProfile->where('type_perawat', 'LIKE', "%{$filterPk}%");
            });
        }

        // 4. Filter berdasarkan Ruangan (Unit Kerja)
        if ($filterRuangan) {
            $perawatQuery->whereHas('perawatPekerjaans', function ($qPekerjaan) use ($filterRuangan) {
                $qPekerjaan->where('unit_kerja', $filterRuangan);
            });
        }

        // 5. Filter berdasarkan Status Kepegawaian <-- TAMBAHAN BARU
        if ($filterStatus) {
            $perawatQuery->whereHas('perawatPekerjaans', function ($qPekerjaan) use ($filterStatus) {
                $qPekerjaan->where('status_kepegawaian', $filterStatus);
            });
        }

        // Eksekusi Query Pagination
        $perawat = $perawatQuery->orderBy('name')
            ->paginate(10)
            ->withQueryString();

        // --- EKSTRAK KFK, PK, DAN RUANGAN UNTUK DROPDOWN ---
        $rawTypes = PerawatProfile::whereNotNull('type_perawat')->pluck('type_perawat');
        $kfkArray = [];
        $pkArray = [];

        foreach ($rawTypes as $raw) {
            $decoded = is_string($raw) ? json_decode($raw, true) : $raw;
            if (is_array($decoded)) {
                foreach ($decoded as $item) {
                    $item = trim($item);
                    if (str_contains(strtoupper($item), 'KFK')) {
                        $kfkArray[] = $item;
                    } elseif (str_contains(strtoupper($item), 'PK') || str_contains(strtoupper($item), 'BK') || str_contains(strtoupper($item), 'PRA')) {
                        $pkArray[] = $item;
                    }
                }
            }
        }

        $allKfk = collect($kfkArray)->unique()->sort()->values();
        $allPk = collect($pkArray)->unique()->sort()->values();
        
        // Ekstrak Semua Unit Kerja yang pernah diinput
        $allRuangan = \App\Models\PerawatPekerjaan::whereNotNull('unit_kerja')
            ->pluck('unit_kerja')
            ->unique()
            ->sort()
            ->values();

        // Ekstrak Semua Status Kepegawaian <-- TAMBAHAN BARU
        $allStatus = \App\Models\PerawatPekerjaan::whereNotNull('status_kepegawaian')
            ->pluck('status_kepegawaian')
            ->unique()
            ->sort()
            ->values();
        // -----------------------------------------------------

        $now = now();
        foreach ($perawat as $p) {
            $p->expired_docs_count = self::countExpiredDocuments($p, $now);
            $p->rekomendasiPk = $pkService->getRecommendation($p->id);
        }

        // Sertakan semua variabel filter ke compact
        return view('admin.perawat.index', compact(
            'perawat', 'search', 'filterPk', 'filterRuangan', 'filterStatus', 'allKfk', 'allPk', 'allRuangan', 'allStatus'
        ));
    }
public function exportCustom(Request $request)
    {
        $kolom = $request->input('kolom', ['nama']); 

        $data = User::where('role', 'perawat')
            ->with([
                'profile', 'pendidikan', 'perawatPekerjaans', 
                'strs', 'sips'
            ])
            ->get();

        $fileName = "Data_Kustom_Perawat_RSUD_SLG_" . date('Ymd_His') . ".xls";

        header("Content-Type: application/vnd.ms-excel");
        header("Content-Disposition: attachment; filename=$fileName");
        header("Pragma: no-cache");
        header("Expires: 0");

        echo '<table border="1">';
        
        // --- 1. RENDER HEADER TABEL DINAMIS ---
        echo '<tr>';
        echo '<th style="background-color: #D3D3D3;">NO</th>';
        if (in_array('nama', $kolom)) echo '<th style="background-color: #D3D3D3;">NAMA LENGKAP</th>';
        if (in_array('nik', $kolom)) echo '<th style="background-color: #D3D3D3;">NIK</th>';
        if (in_array('nohp', $kolom)) echo '<th style="background-color: #D3D3D3;">NO. WHATSAPP</th>';
        if (in_array('alamat', $kolom)) echo '<th style="background-color: #D3D3D3;">ALAMAT</th>';
        if (in_array('ruangan', $kolom)) echo '<th style="background-color: #D3D3D3;">RUANGAN (UNIT KERJA)</th>';
        if (in_array('status_kepegawaian', $kolom)) echo '<th style="background-color: #D3D3D3;">STATUS KEPEGAWAIAN</th>'; // <-- TAMBAHAN
        if (in_array('pk', $kolom)) echo '<th style="background-color: #D3D3D3;">JENJANG PK</th>';
        if (in_array('kfk', $kolom)) echo '<th style="background-color: #D3D3D3;">KFK</th>';
        if (in_array('str', $kolom)) echo '<th style="background-color: #D3D3D3;">NO. STR (EXPIRED)</th>';
        if (in_array('sip', $kolom)) echo '<th style="background-color: #D3D3D3;">NO. SIP (EXPIRED)</th>';
        if (in_array('pendidikan', $kolom)) echo '<th style="background-color: #D3D3D3;">PENDIDIKAN TERAKHIR</th>';
        echo '</tr>';

        // --- 2. RENDER ISI DATA ---
        foreach ($data as $i => $u) {
            $p = $u->profile;
            
            // Ekstrak Ruangan & Status Kepegawaian
            $pekerjaanAktif = $u->perawatPekerjaans->sortByDesc('tahun_mulai')->first();
            $ruangan = $pekerjaanAktif ? $pekerjaanAktif->unit_kerja : '-';
            $statusPegawai = $pekerjaanAktif ? ($pekerjaanAktif->status_kepegawaian ?? '-') : '-'; // <-- TAMBAHAN

            // Ekstrak PK & KFK
            $types = is_string($p->type_perawat) ? json_decode($p->type_perawat, true) : ($p->type_perawat ?? []);
            $kfkList = [];
            $pkList = [];
            if(is_array($types)) {
                foreach($types as $t) {
                    if(str_contains(strtoupper($t), 'KFK')) $kfkList[] = $t;
                    else if(str_contains(strtoupper($t), 'PK') || str_contains(strtoupper($t), 'BK')) $pkList[] = $t;
                }
            }
            $strPk = !empty($pkList) ? implode(', ', $pkList) : 'PRA PK';
            $strKfk = !empty($kfkList) ? implode(', ', $kfkList) : '-';

            // Ekstrak Pendidikan
            $pendidikanAktif = $u->pendidikan->sortByDesc('tahun_lulus')->first();
            $pendidikanText = $pendidikanAktif ? $pendidikanAktif->jenjang . ' - ' . $pendidikanAktif->nama_institusi : '-';

            // Ekstrak Dokumen STR/SIP
            $strObj = $u->strs->first();
            $strText = $strObj ? $strObj->nomor . ' (' . $strObj->tgl_expired . ')' : '-';

            $sipObj = $u->sips->first();
            $sipText = $sipObj ? $sipObj->nomor . ' (' . $sipObj->tgl_expired . ')' : '-';

            // Print Row
            echo '<tr>';
            echo '<td>' . ($i + 1) . '</td>';
            if (in_array('nama', $kolom)) echo '<td>' . $u->name . '</td>';
            if (in_array('nik', $kolom)) echo '<td>' . ($p->nik ?? '-') . '&nbsp;</td>';
            if (in_array('nohp', $kolom)) echo '<td>' . ($p->no_hp ?? '-') . '&nbsp;</td>';
            if (in_array('alamat', $kolom)) echo '<td>' . ($p->alamat ?? '-') . '</td>';
            if (in_array('ruangan', $kolom)) echo '<td>' . $ruangan . '</td>';
            if (in_array('status_kepegawaian', $kolom)) echo '<td>' . $statusPegawai . '</td>'; // <-- TAMBAHAN
            if (in_array('pk', $kolom)) echo '<td>' . $strPk . '</td>';
            if (in_array('kfk', $kolom)) echo '<td>' . $strKfk . '</td>';
            if (in_array('str', $kolom)) echo '<td>' . $strText . '</td>';
            if (in_array('sip', $kolom)) echo '<td>' . $sipText . '</td>';
            if (in_array('pendidikan', $kolom)) echo '<td>' . $pendidikanText . '</td>';
            echo '</tr>';
        }
        echo '</table>';
        exit;
    }
public function exportData()
    {
        // Ambil SEMUA perawat dengan SEMUA relasi dalam format JSON
        $data = User::where('role', 'perawat')
            ->with([
                'profile', 'pendidikan', 'pelatihan', 'perawatPekerjaans', 
                'keluarga', 'organisasi', 'tandajasa', 'strs', 
                'sips', 'lisensis', 'dataTambahans'
            ])
            ->get();

        // Kembalikan respons dalam bentuk JSON murni agar bisa dibaca Javascript
        return response()->json($data);
    }

    // UPDATE DATA PERAWAT
    public function update(Request $request, $id)
    {
        $user = User::where('role', 'perawat')->findOrFail($id);

        // 1. Validasi Input
        $request->validate([
            'name'     => 'required|string|max:255',
            'email'    => 'required|email|unique:users,email,' . $user->id,
            'foto_3x4' => 'nullable|image|mimes:jpeg,png,jpg|max:2048',
            'nik'      => 'nullable|numeric',
        ]);

        // 2. Update Data Akun Utama (User)
        $user->name = $request->name;
        $user->email = $request->email;
        $user->save();

        // 3. Update Data Profil Lengkap
        $profile = PerawatProfile::firstOrCreate(['user_id' => $user->id]);

        if ($request->hasFile('foto_3x4')) {
            if ($profile->foto_3x4 && Storage::disk('public')->exists($profile->foto_3x4)) {
                Storage::disk('public')->delete($profile->foto_3x4);
            }
            $profile->foto_3x4 = $request->file('foto_3x4')->store('perawat_foto', 'public');
        }

        $profile->nik            = $request->nik;
        $profile->no_hp          = $request->no_hp;
        $profile->alamat         = $request->alamat;
        $profile->tempat_lahir   = $request->tempat_lahir;
        $profile->tanggal_lahir  = $request->tanggal_lahir;
        $profile->jenis_kelamin  = $request->jenis_kelamin;
        $profile->agama          = $request->agama;
        $profile->tinggi_badan   = $request->tinggi_badan;
        $profile->berat_badan    = $request->berat_badan;
        $profile->golongan_darah = $request->golongan_darah;
        $profile->save();

        // 4-7. Update Riwayat
        PerawatPendidikan::where('user_id', $user->id)->delete();
        if ($request->has('pendidikan')) {
            foreach ($request->pendidikan as $row) {
                if (!empty($row['jenjang']) || !empty($row['nama_institusi']) || !empty($row['tahun_lulus'])) {
                    PerawatPendidikan::create([
                        'user_id' => $user->id,
                        'jenjang' => $row['jenjang'] ?? '',
                        'nama_institusi' => $row['nama_institusi'] ?? '',
                        'tahun_lulus' => $row['tahun_lulus'] ?: null, // Perbaikan: ubah ke null jika kosong
                    ]);
                }
            }
        }

        PerawatPelatihan::where('user_id', $user->id)->delete();
        if ($request->has('pelatihan')) {
            foreach ($request->pelatihan as $row) {
                if (!empty($row['nama_pelatihan']) || !empty($row['penyelenggara']) || !empty($row['tanggal_mulai'])) {
                    PerawatPelatihan::create([
                        'user_id' => $user->id,
                        'nama_pelatihan' => $row['nama_pelatihan'] ?? '',
                        'penyelenggara' => $row['penyelenggara'] ?? '',
                        'tanggal_mulai' => $row['tanggal_mulai'] ?: null, // Perbaikan: ubah ke null jika kosong
                    ]);
                }
            }
        }

        PerawatPekerjaan::where('user_id', $user->id)->delete();
        if ($request->has('pekerjaan')) {
            foreach ($request->pekerjaan as $row) {
                if (!empty($row['nama_instansi']) || !empty($row['jabatan']) || !empty($row['tahun_mulai']) || !empty($row['tahun_selesai'])) {
                    PerawatPekerjaan::create([
                        'user_id' => $user->id,
                        'nama_instansi' => $row['nama_instansi'] ?? '',
                        'jabatan' => $row['jabatan'] ?? '',
                        'tahun_mulai' => $row['tahun_mulai'] ?: null, // Perbaikan: ubah ke null jika kosong
                        'tahun_selesai' => $row['tahun_selesai'] ?: null, // Perbaikan: ubah ke null jika kosong
                    ]);
                }
            }
        }

        PerawatOrganisasi::where('user_id', $user->id)->delete();
        if ($request->has('organisasi')) {
            foreach ($request->organisasi as $row) {
                if (!empty($row['nama_organisasi']) || !empty($row['jabatan']) || !empty($row['tahun_mulai'])) {
                    PerawatOrganisasi::create([
                        'user_id' => $user->id,
                        'nama_organisasi' => $row['nama_organisasi'] ?? '',
                        'jabatan' => $row['jabatan'] ?? '',
                        'tahun_mulai' => $row['tahun_mulai'] ?: null, // Perbaikan: ubah ke null jika kosong
                    ]);
                }
            }
        }

        return redirect()->route('admin.perawat.index')->with('success', 'Data perawat berhasil diperbarui.');
    }

    // HAPUS DATA PERAWAT
    public function destroy($id)
    {
        $user = User::where('role', 'perawat')->findOrFail($id);

        $profile = PerawatProfile::where('user_id', $id)->first();
        if ($profile && $profile->foto_3x4 && Storage::disk('public')->exists($profile->foto_3x4)) {
            Storage::disk('public')->delete($profile->foto_3x4);
        }

        // Hapus file sertifikat
        $sips = PerawatSip::where('user_id', $id)->get();
        foreach ($sips as $sip) {
            if ($sip->file_path && Storage::disk('public')->exists($sip->file_path)) {
                Storage::disk('public')->delete($sip->file_path);
            }
        }

        $strs = PerawatStr::where('user_id', $id)->get();
        foreach ($strs as $str) {
            if ($str->file_path && Storage::disk('public')->exists($str->file_path)) {
                Storage::disk('public')->delete($str->file_path);
            }
        }

        $lisensis = PerawatLisensi::where('user_id', $id)->get();
        foreach ($lisensis as $lis) {
            if ($lis->file_path && Storage::disk('public')->exists($lis->file_path)) {
                Storage::disk('public')->delete($lis->file_path);
            }
        }

        $dataTambahans = PerawatDataTambahan::where('user_id', $id)->get();
        foreach ($dataTambahans as $data) {
            if ($data->file_path && Storage::disk('public')->exists($data->file_path)) {
                Storage::disk('public')->delete($data->file_path);
            }
        }

        // Hapus records
        PerawatProfile::where('user_id', $id)->delete();
        PerawatPendidikan::where('user_id', $id)->delete();
        PerawatPelatihan::where('user_id', $id)->delete();
        PerawatPekerjaan::where('user_id', $id)->delete();
        PerawatKeluarga::where('user_id', $id)->delete();
        PerawatOrganisasi::where('user_id', $id)->delete();
        PerawatTandaJasa::where('user_id', $id)->delete();
        PerawatSip::where('user_id', $id)->delete();
        PerawatStr::where('user_id', $id)->delete();
        PerawatDataTambahan::where('user_id', $id)->delete();
        PerawatLisensi::where('user_id', $id)->delete();

        $user->delete();

        return redirect()->route('admin.perawat.index')->with('success', 'Data perawat berhasil dihapus.');
    }

    // TAMPILKAN HALAMAN SERTIFIKAT
    public function sertifikat($id)
    {
        $user = User::where('role', 'perawat')->findOrFail($id);

        $sip = PerawatSip::where('user_id', $id)->orderBy('tgl_expired', 'desc')->get();
        $str = PerawatStr::where('user_id', $id)->orderBy('tgl_expired', 'desc')->get();
        $dataTambahan = PerawatDataTambahan::where('user_id', $id)->orderBy('tgl_expired', 'desc')->get();
        $lisensi = PerawatLisensi::where('user_id', $id)->orderBy('tgl_expired', 'desc')->get();

        return view('admin.perawat.sertifikat', compact('user', 'sip', 'str', 'dataTambahan', 'lisensi'));
    }

    public function verifikasiKelayakan(Request $request)
    {
        // 1. Validasi Input
        $request->validate([
            'tipe' => 'required|in:str,sip,lisensi,tambahan',
            'id' => 'required|integer',
            'kelayakan' => 'required|in:layak,tidak_layak,pending',
            'approve_lifetime' => 'nullable|in:1',
        ]);

        $model = null;
        
        // 2. Gunakan find() alih-alih findOrFail() agar tidak melempar 404
        switch ($request->tipe) {
            case 'str':
                $model = \App\Models\PerawatStr::find($request->id);
                break;
            case 'sip':
                $model = \App\Models\PerawatSip::find($request->id);
                break;
            case 'lisensi':
                $model = \App\Models\PerawatLisensi::find($request->id);
                break;
            case 'tambahan':
                $model = \App\Models\PerawatDataTambahan::find($request->id);
                break;
        }

        // 3. Tangkap error jika ID tidak ada di tabel (Mencegah 404)
        if (!$model) {
            return redirect()->back()->with(
                'error', 
                'Verifikasi Gagal: Data ' . strtoupper($request->tipe) . ' tidak ditemukan.'
            );
        }

        // 4. Update data jika model ditemukan
        $model->kelayakan = $request->kelayakan;

        // Jika dokumen tambahan dan admin menekan approve_lifetime
        if ($request->tipe === 'tambahan' && $request->has('approve_lifetime') && $request->input('approve_lifetime') == '1') {
            $model->lifetime_approved = true;
            $model->lifetime_approved_by = auth()->id();
            $model->lifetime_approved_at = now();
            $model->kelayakan = 'layak';
        }

        $model->save();

        // 5. Redirect kembali dengan pesan sukses
        return redirect()->back()->with('success', 'Status kelayakan dokumen berhasil diperbarui.');
    }
}