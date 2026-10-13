<?php

namespace Tests\Feature;

use App\Models\User;
use App\Services\RkkKhusus;
use Illuminate\Foundation\Testing\DatabaseTransactions;
use Illuminate\Support\Facades\DB;
use Tests\TestCase;

/**
 * DB_DATABASE=diksera_logbook_test DB_USERNAME=root DB_PASSWORD= php artisan test --filter=RkkKhususTest
 */
class RkkKhususTest extends TestCase
{
    use DatabaseTransactions;

    /** Buat user + pengajuan + jadwal + penilaian dengan id penilaian tertentu. */
    private function penilaian(int $id, string $nama, array $detail, int $lapis = 1): int
    {
        $u = User::forceCreate(['name' => $nama, 'email' => uniqid() . '@test.local', 'password' => bcrypt('x' . uniqid()), 'role' => 'perawat', 'status_akun' => 'active']);
        $pjUser = User::forceCreate(['name' => 'PJ ' . $nama, 'email' => uniqid() . '@test.local', 'password' => bcrypt('x' . uniqid()), 'role' => 'pewawancara']);
        $pj = DB::table('penanggung_jawab_ujians')->insertGetId(['user_id' => $pjUser->id, 'nama' => 'PJ', 'no_hp' => '0', 'jabatan' => 'x', 'type' => 'pewawancara', 'created_at' => now(), 'updated_at' => now()]);
        $p = DB::table('pengajuan_sertifikats')->insertGetId(['user_id' => $u->id, 'status' => 'completed', 'metode' => 'interview_only', 'created_at' => now(), 'updated_at' => now()]);
        $j = DB::table('jadwal_wawancaras')->insertGetId(['pengajuan_sertifikat_id' => $p, 'penanggung_jawab_id' => $pj, 'waktu_wawancara' => now(), 'lokasi' => 'R', 'status' => 'approved', 'created_at' => now(), 'updated_at' => now()]);
        DB::table('wawancara_penilaians')->where('id', $id)->delete();
        DB::table('wawancara_penilaians')->insert(['id' => $id, 'jadwal_wawancara_id' => $j, 'keputusan' => 'lulus', 'detail_penilaian' => $lapis === 2 ? json_encode(json_encode($detail)) : json_encode($detail), 'created_at' => now(), 'updated_at' => now()]);
        return $p;
    }

    private function detail(int $id): array
    {
        $v = DB::table('wawancara_penilaians')->where('id', $id)->value('detail_penilaian');
        for ($i = 0; is_string($v) && $i < 5; $i++) $v = json_decode($v, true);
        return $v;
    }

    public function test_service_mendeteksi_kelompok_aktif()
    {
        $this->assertSame([], RkkKhusus::aktif(['is_mpp' => '1', 'rkk' => ['5' => []]]));
        $this->assertSame(['hd'], RkkKhusus::aktif(['is_hd' => '1']));
        $this->assertSame(['hd'], RkkKhusus::aktif(json_encode(['rkk' => ['HD-03' => ['jenis' => 'mandiri']]])));
        $this->assertCount(8, RkkKhusus::itemsAktif(['is_hd' => '1']));
        $this->assertSame('HD-01', RkkKhusus::itemsAktif(['is_hd' => '1'])->first()->id);

        // OPPE: switch HD menambah 8 butir tanpa dobel, dimatikan menghapusnya, butir lain tetap
        $awal = [['item_id' => 'PPI-01', 'kategori_tugas' => 'X', 'deskripsi' => 'x']];
        $on = RkkKhusus::terapkanKeInstrumen($awal, new \Illuminate\Http\Request(['is_hd' => '1']));
        $on = RkkKhusus::terapkanKeInstrumen($on, new \Illuminate\Http\Request(['is_hd' => '1']));
        $this->assertCount(9, $on);
        $this->assertSame(['hd'], RkkKhusus::aktifDiInstrumen($on));
        $off = RkkKhusus::terapkanKeInstrumen($on, new \Illuminate\Http\Request([]));
        $this->assertSame(['PPI-01'], array_column($off, 'item_id'));
    }

    public function test_migration_data_8_perawat_hanya_menambah_dan_sesuai_jenjang()
    {
        $lama = ['poin_penilaian' => ['a' => 1], 'rkk' => ['12' => ['jenis' => 'delegasi', 'kewenangan' => 'supervisi']], 'is_mpp' => '0', 'is_ppi' => '0'];
        $this->penilaian(226, 'DESY NATALIA', $lama, 2);       // PK 2, format server: JSON 2 lapis
        $this->penilaian(418, 'KIKI RISKA NURAINI, S.Kep.,Ns', $lama); // PK 1
        $this->penilaian(101, 'ORANG LAIN', $lama);            // nama tidak cocok -> dilewati

        $m = require database_path('migrations/2026_10_13_090000_tambah_rkk_hemodialisa_8_perawat.php');
        $m = is_object($m) ? $m : new \TambahRkkHemodialisa8Perawat();
        $m->up();
        $m->up(); // idempoten

        $desy = $this->detail(226);
        $this->assertSame('1', $desy['is_hd']);
        $this->assertSame(['jenis' => 'mandiri', 'kewenangan' => 'sendiri'], $desy['rkk']['HD-01']);
        $this->assertCount(9, $desy['rkk'], '1 jawaban lama + 8 butir HD');
        $this->assertSame(['jenis' => 'delegasi', 'kewenangan' => 'supervisi'], $desy['rkk']['12'], 'jawaban lama tidak berubah');
        $this->assertSame(['a' => 1], $desy['poin_penilaian']);

        // Format penyimpanan tetap 2 lapis & tetap terbaca seperti di aplikasi (model cast + json_decode)
        $raw = DB::table('wawancara_penilaians')->where('id', 226)->value('detail_penilaian');
        $this->assertIsString(json_decode($raw, true), 'tetap 2 lapis');
        $model = \App\Models\WawancaraPenilaian::find(226)->detail_penilaian;
        $dibaca = is_string($model) ? json_decode($model, true) : $model;
        $this->assertSame('1', $dibaca['is_hd']);
        $this->assertSame(['hd'], RkkKhusus::aktif($raw), 'service membaca format 2 lapis');

        $this->assertSame('supervisi', $this->detail(418)['rkk']['HD-08']['kewenangan'], 'PK 1 dengan supervisi');
        $this->assertArrayNotHasKey('is_hd', $this->detail(101), 'nama tidak cocok tidak diubah');

        $m->down();
        $this->assertArrayNotHasKey('is_hd', $this->detail(226));
        $this->assertEquals([12], array_keys($this->detail(226)['rkk']), 'kembali hanya jawaban lama');
    }

    public function test_pdf_rkk_menampilkan_bagian_hemodialisa()
    {
        $detail = ['rkk' => ['HD-01' => ['jenis' => 'mandiri', 'kewenangan' => 'sendiri']], 'is_hd' => '1'];
        $html = view('perawat.pengajuan.kredensial_pdf', [
            'tglMulai' => '1 Januari 2026', 'tglAkhir' => '1 Januari 2029', 'tglTerbit' => '1 Januari 2026',
            'bidang' => 'KEPERAWATAN', 'namaLengkap' => 'DESY NATALIA', 'unitKerja' => 'UNIT HEMODIALISA', 'nip' => '-',
            'pendidikanFormal' => 'S1', 'kualifikasi' => 'PK 2', 'namaJenjang' => 'PK 2',
            'rkkGrouped' => collect(), 'rkkDraft' => $detail['rkk'], 'isMpp' => false, 'isPpi' => false, 'rkkMpp' => [], 'rkkPpi' => [],
            'qrBambang' => '', 'qrHaris' => '', 'qrDirektur' => '',
            'rkkKhusus' => RkkKhusus::untukPdf($detail),
        ])->render();

        $this->assertStringContainsString('HEMODIALISA (HD)', $html);
        $this->assertStringContainsString('Melakukan Punksi Akses Vaskuler AV Fistule', $html);
        $this->assertStringContainsString('&#10003;', $html);
    }

    public function test_admin_edit_pengajuan_menampilkan_switch_dan_butir_hd()
    {
        $admin = User::forceCreate(['name' => 'Admin HD', 'email' => uniqid() . '@test.local', 'password' => bcrypt('x' . uniqid()), 'role' => 'admin', 'status_akun' => 'active']);
        $p = $this->penilaian(9001, 'PESERTA HD', ['rkk' => [], 'is_hd' => '1']);

        $this->actingAs($admin)->get(route('admin.pengajuan.edit', $p))->assertOk()
            ->assertSee('name="is_hd"', false)->assertSee('Tandai Hemodialisa (HD)')
            ->assertSee('Memulai Tindakan HD');
    }
}
