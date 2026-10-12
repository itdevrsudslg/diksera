<?php

namespace Tests\Feature;

use App\Models\LogbookEntry;
use App\Models\LogbookPeriode;
use App\Models\LogbookRekap;
use App\Models\LogbookTindakan;
use App\Models\PerawatPekerjaan;
use App\Models\PerawatProfile;
use App\Models\User;
use Illuminate\Foundation\Testing\DatabaseTransactions;
use Tests\TestCase;

/**
 * Alur logbook: perawat isi -> karu validasi & TTD -> kasi mengetahui; admin buka/tutup periode.
 * Jalankan terhadap database uji (BUKAN database aplikasi), contoh:
 *   DB_DATABASE=diksera_logbook_test DB_USERNAME=root DB_PASSWORD= php artisan test --filter=LogbookTest
 */
class LogbookTest extends TestCase
{
    use DatabaseTransactions;

    private function buatUser(string $nama, string $role = 'perawat', string $jabatan = 'pelaksana', ?string $unit = 'Ruang Melati'): User
    {
        $u = User::forceCreate([
            'name' => $nama,
            'email' => strtolower(str_replace(' ', '.', $nama)) . '.' . uniqid() . '@test.local',
            'password' => bcrypt(\Illuminate\Support\Str::random(32)),
            'role' => $role,
        ]);
        if ($role === 'perawat') {
            PerawatProfile::forceCreate(['user_id' => $u->id, 'nama_lengkap' => $nama, 'nip' => '1990' . rand(1000, 9999), 'jabatan_struktural' => $jabatan]);
            if ($unit) PerawatPekerjaan::forceCreate(['user_id' => $u->id, 'unit_kerja' => $unit, 'tahun_mulai' => 2020]);
        }
        return $u->fresh();
    }

    private function payload(array $tindakanIds, string $tanggal = null): array
    {
        return [
            'tanggal' => $tanggal ?? now()->toDateString(),
            'no_rm' => '000123',
            'nama_pasien' => 'Pasien Uji',
            'kegiatan' => [
                ['nama' => 'Perawatan luka', 'tindakan' => [['id' => $tindakanIds[0], 'jumlah' => 2], ['id' => $tindakanIds[1], 'jumlah' => 1]]],
                ['nama' => 'Observasi', 'tindakan' => [['id' => $tindakanIds[1], 'jumlah' => 3]]],
            ],
        ];
    }

    public function test_alur_lengkap_logbook()
    {
        $perawat = $this->buatUser('Perawat Satu');
        $karu = $this->buatUser('Karu Melati', 'perawat', 'kepala_ruangan');
        $kasi = $this->buatUser('Kasi Keperawatan', 'perawat', 'kepala_seksi', 'Bidang Keperawatan');
        $admin = $this->buatUser('Admin Uji', 'admin');
        LogbookPeriode::where(['tahun' => now()->year, 'bulan' => now()->month])->delete();

        $t1 = LogbookTindakan::create(['kode' => 'T01', 'nama' => 'Rawat luka', 'kategori' => 'manual']);
        $t2 = LogbookTindakan::create(['kode' => 'T02', 'nama' => 'Ukur TTV', 'kategori' => 'manual']);

        // Halaman-halaman terbuka tanpa error
        $this->actingAs($perawat)->get(route('perawat.logbook.index'))->assertOk()->assertSee('Logbook');
        $this->actingAs($perawat)->get(route('perawat.logbook.create'))->assertOk()->assertSee('Nomor RM');
        $this->actingAs($perawat)->getJson(route('perawat.logbook.cari_tindakan', ['q' => 'TTV']))
            ->assertOk()->assertJsonFragment(['value' => $t2->id]);

        // Perawat simpan entri: 1 pasien, 2 kegiatan, 3 tindakan (total jumlah 6)
        $this->actingAs($perawat)->post(route('perawat.logbook.store'), $this->payload([$t1->id, $t2->id]))->assertRedirect();
        $entry = LogbookEntry::where('user_id', $perawat->id)->latest('id')->first();
        $this->assertNotNull($entry);
        $this->assertSame('Ruang Melati', $entry->unit_kerja);
        $this->assertSame(2, $entry->kegiatans()->count());
        $this->assertSame(6, $entry->load('kegiatans.tindakans')->jumlah_tindakan);

        // Tanggal masa depan ditolak
        $this->actingAs($perawat)->post(route('perawat.logbook.store'), $this->payload([$t1->id, $t2->id], now()->addDay()->toDateString()))
            ->assertSessionHasErrors('tanggal');

        // Perawat tidak bisa buka halaman admin / validasi
        $this->actingAs($perawat)->get(route('admin.logbook.periode'))->assertRedirect(route('dashboard'));
        $this->actingAs($perawat)->get(route('perawat.logbook.validasi'))->assertForbidden();

        // Perawat TTD rekap
        $this->actingAs($perawat)->post(route('perawat.logbook.ttd'), ['bulan' => now()->format('Y-m')])->assertRedirect();
        $rekap = LogbookRekap::where('user_id', $perawat->id)->first();
        $this->assertNotNull($rekap->perawat_signed_at);

        // Karu belum bisa TTD sebelum entri divalidasi
        $this->actingAs($karu)->get(route('perawat.logbook.validasi'))->assertOk()->assertSee('Perawat Satu');
        $this->actingAs($karu)->post(route('perawat.logbook.validasi.ttd', $rekap->id));
        $this->assertNull($rekap->fresh()->karu_signed_at);

        // Karu tolak dulu -> perawat perbaiki -> karu validasi
        $this->actingAs($karu)->post(route('perawat.logbook.validasi.entri'), ['ids' => [$entry->id], 'aksi' => 'ditolak', 'catatan' => 'RM salah'])->assertRedirect();
        $this->assertSame('ditolak', $entry->fresh()->status);
        $this->actingAs($perawat)->put(route('perawat.logbook.update', $entry->id), $this->payload([$t1->id, $t2->id]))->assertRedirect();
        $this->assertSame('menunggu', $entry->fresh()->status);
        $this->actingAs($karu)->post(route('perawat.logbook.validasi.entri'), ['ids' => [$entry->id], 'aksi' => 'valid'])->assertRedirect();
        $this->assertSame('valid', $entry->fresh()->status);

        // Entri valid terkunci bagi perawat
        $this->actingAs($perawat)->delete(route('perawat.logbook.destroy', $entry->id))->assertForbidden();

        // Karu TTD, kasi mengetahui
        $this->actingAs($karu)->post(route('perawat.logbook.validasi.ttd', $rekap->id))->assertRedirect();
        $this->assertNotNull($rekap->fresh()->karu_signed_at);
        $this->actingAs($kasi)->get(route('perawat.logbook.validasi'))->assertOk()->assertSee('Perawat Satu');
        $this->actingAs($kasi)->post(route('perawat.logbook.validasi.ttd', $rekap->id))->assertRedirect();
        $this->assertNotNull($rekap->fresh()->kasi_signed_at);

        // Rekap bulanan / triwulan / semester + cetak
        foreach (['bulan' => now()->month, 'triwulan' => intdiv(now()->month - 1, 3) + 1, 'semester' => now()->month <= 6 ? 1 : 2] as $jenis => $ke) {
            $this->actingAs($perawat)->get(route('perawat.logbook.rekap', ['jenis' => $jenis, 'tahun' => now()->year, 'ke' => $ke]))
                ->assertOk()->assertSee('Rawat luka')->assertSee('Karu Melati');
        }
        $this->actingAs($kasi)->get(route('perawat.logbook.rekap', ['userId' => $perawat->id, 'cetak' => 1]))->assertOk()->assertSee('Ditandatangani elektronik');

        // Perawat lain tidak boleh melihat rekap orang lain
        $lain = $this->buatUser('Perawat Lain', 'perawat', 'pelaksana', 'Ruang Mawar');
        $this->actingAs($lain)->get(route('perawat.logbook.rekap', ['userId' => $perawat->id]))->assertForbidden();

        // Admin: halaman & tutup periode -> perawat tidak bisa menambah
        $this->actingAs($admin)->get(route('admin.logbook.periode'))->assertOk();
        $this->actingAs($admin)->get(route('admin.logbook.tindakan'))->assertOk()->assertSee('Ukur TTV');
        $this->actingAs($admin)->get(route('admin.logbook.rekap'))->assertOk()->assertSee('Perawat Satu');
        $this->actingAs($admin)->post(route('admin.logbook.periode.ubah'), ['tahun' => now()->year, 'bulan' => now()->month, 'status' => 'closed'])->assertRedirect();
        $this->assertFalse(LogbookPeriode::untuk(now()->year, now()->month)->terbuka);
        $this->actingAs($perawat)->post(route('perawat.logbook.store'), $this->payload([$t1->id, $t2->id]))->assertSessionHasErrors('tanggal');

        // Buka lagi
        $this->actingAs($admin)->post(route('admin.logbook.periode.ubah'), ['tahun' => now()->year, 'bulan' => now()->month, 'status' => 'open'])->assertRedirect();
        $this->assertTrue(LogbookPeriode::untuk(now()->year, now()->month)->terbuka);
    }

    public function test_karu_divalidasi_kasi_dan_rekap_admin()
    {
        $karu = $this->buatUser('Karu Anggrek', 'perawat', 'kepala_ruangan', 'Ruang Anggrek');
        $kasi = $this->buatUser('Kasi Dua', 'perawat', 'kepala_seksi', 'Bidang Keperawatan');
        $perawat = $this->buatUser('Perawat Anggrek', 'perawat', 'pelaksana', 'Ruang Anggrek');
        $admin = $this->buatUser('Admin Dua', 'admin');
        LogbookPeriode::where(['tahun' => now()->year, 'bulan' => now()->month])->delete();
        $t1 = LogbookTindakan::create(['kode' => 'X01', 'nama' => 'Edukasi pasien', 'kategori' => 'manual']);
        $t2 = LogbookTindakan::create(['kode' => 'X02', 'nama' => 'Mobilisasi', 'kategori' => 'manual']);

        // Karu mengisi logbook sendiri -> divalidasi Kasi (bukan oleh dirinya)
        $this->actingAs($karu)->post(route('perawat.logbook.store'), $this->payload([$t1->id, $t2->id]))->assertRedirect();
        $entry = LogbookEntry::where('user_id', $karu->id)->first();
        $this->actingAs($karu)->post(route('perawat.logbook.validasi.entri'), ['ids' => [$entry->id], 'aksi' => 'valid']);
        $this->assertSame('menunggu', $entry->fresh()->status, 'Karu tidak boleh memvalidasi entrinya sendiri');

        // Karu belum bisa TTD sebelum divalidasi Kasi
        $this->actingAs($karu)->post(route('perawat.logbook.ttd'), ['bulan' => now()->format('Y-m')]);
        $this->assertNull(optional(LogbookRekap::where('user_id', $karu->id)->first())->karu_signed_at);

        $this->actingAs($kasi)->get(route('perawat.logbook.validasi'))->assertOk()->assertSee('Karu Anggrek');
        $this->actingAs($kasi)->post(route('perawat.logbook.validasi.entri'), ['ids' => [$entry->id], 'aksi' => 'valid'])->assertRedirect();
        $this->assertSame('valid', $entry->fresh()->status);

        // Karu TTD -> slot perawat & karu terisi, Kasi lalu mengetahui
        $this->actingAs($karu)->post(route('perawat.logbook.ttd'), ['bulan' => now()->format('Y-m')]);
        $rekap = LogbookRekap::where('user_id', $karu->id)->first();
        $this->assertNotNull($rekap->karu_signed_at);
        $this->actingAs($kasi)->post(route('perawat.logbook.validasi.ttd', $rekap->id))->assertRedirect();
        $this->assertNotNull($rekap->fresh()->kasi_signed_at);

        // Setelah rekap ditandatangani karu, bulan itu terkunci untuk entri baru
        $this->actingAs($karu)->post(route('perawat.logbook.store'), $this->payload([$t1->id, $t2->id]))->assertSessionHasErrors('tanggal');

        // Perawat pelaksana mengisi (menunggu)
        $this->actingAs($perawat)->post(route('perawat.logbook.store'), $this->payload([$t1->id, $t2->id]))->assertRedirect();

        // Admin: cari, export Excel (JSON), cetak semua
        $this->actingAs($admin)->get(route('admin.logbook.rekap', ['q' => 'Anggrek']))->assertOk()->assertSee('Karu Anggrek')->assertSee('Perawat Anggrek');
        $this->actingAs($admin)->get(route('admin.logbook.rekap', ['q' => 'TidakAdaNamaIni']))->assertOk()->assertDontSee('Karu Anggrek');
        foreach (['bulan', 'triwulan', 'semester'] as $jenis) {
            $this->actingAs($admin)->get(route('admin.logbook.rekap', ['jenis' => $jenis]))->assertOk();
        }
        $json = $this->actingAs($admin)->getJson(route('admin.logbook.rekap.export', ['q' => 'Anggrek']))->assertOk()->json();
        $this->assertCount(2, $json['ringkasan']);
        $this->assertNotEmpty($json['tindakan']);
        $this->assertCount(6, $json['detail']); // 2 entri x 3 baris tindakan
        $this->actingAs($admin)->get(route('admin.logbook.rekap.cetak', ['q' => 'Anggrek']))->assertOk()
            ->assertSee('Karu Anggrek')->assertSee('Perawat Anggrek')->assertSee('Ditandatangani elektronik');
    }

    public function test_periode_otomatis_tutup_h_plus_5()
    {
        $p = new LogbookPeriode(['tahun' => 2026, 'bulan' => 9]);

        $this->travelTo(\Carbon\Carbon::create(2026, 10, 5, 23, 0));
        $this->assertTrue($p->terbuka, 'Masih terbuka pada H+5');

        $this->travelTo(\Carbon\Carbon::create(2026, 10, 6, 0, 1));
        $this->assertFalse($p->terbuka, 'Tertutup setelah H+5');

        $this->travelBack();
    }
}
