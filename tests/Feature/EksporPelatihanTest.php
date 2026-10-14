<?php

namespace Tests\Feature;

use App\Models\PerawatPelatihan;
use App\Models\PerawatProfile;
use App\Models\User;
use Illuminate\Foundation\Testing\DatabaseTransactions;
use Tests\TestCase;

/**
 * DB_DATABASE=diksera_logbook_test DB_USERNAME=root DB_PASSWORD= php artisan test --filter=EksporPelatihanTest
 */
class EksporPelatihanTest extends TestCase
{
    use DatabaseTransactions;

    protected function setUp(): void
    {
        parent::setUp();
        // Isolasi: perawat lain di database uji tidak ikut diekspor (dibatalkan otomatis oleh transaksi)
        User::where('role', 'perawat')->update(['role' => 'perawat_lama_uji']);
    }

    private function perawat(string $nama, array $pelatihan, bool $profil = true): User
    {
        $u = User::forceCreate(['name' => $nama, 'email' => uniqid() . '@test.local', 'password' => bcrypt('x' . uniqid()), 'role' => 'perawat', 'status_akun' => 'active']);
        if ($profil) PerawatProfile::forceCreate(['user_id' => $u->id, 'nama_lengkap' => $nama, 'nik' => '35' . rand(100000, 999999)]);
        foreach ($pelatihan as [$nm, $tgl, $skp]) {
            PerawatPelatihan::forceCreate(['user_id' => $u->id, 'nama_pelatihan' => $nm, 'tanggal_selesai' => $tgl, 'skp' => $skp]);
        }
        return $u;
    }

    public function test_kolom_dan_filter_pelatihan()
    {
        $admin = User::forceCreate(['name' => 'Admin Ekspor', 'email' => uniqid() . '@test.local', 'password' => bcrypt('x' . uniqid()), 'role' => 'admin', 'status_akun' => 'active']);
        $this->perawat('PERAWAT ANDI', [['BTCLS 2024', '2024-05-01', 5], ['Pelatihan PPI Dasar', '2023-02-10', 3]]);
        $this->perawat('PERAWAT BUDI', [['PPGD', '2022-01-01', 2]]);
        $this->perawat('PERAWAT CICI', [], false); // tanpa profil: sebelumnya membuat ekspor error

        // Semua perawat + kolom pelatihan
        $res = $this->actingAs($admin)->get(route('admin.perawat.export_custom', [
            'kolom' => ['nama', 'pk', 'pelatihan_semua', 'pelatihan_jumlah', 'pelatihan_skp'],
            'pelatihan' => ['btcls', 'PPI', 'btcls'],
        ]))->assertOk();
        $this->assertStringContainsString('application/vnd.ms-excel', $res->headers->get('Content-Type'));
        $html = $res->getContent();
        $this->assertStringContainsString('PELATIHAN: BTCLS', $html);
        $this->assertSame(1, substr_count($html, 'PELATIHAN: BTCLS'), 'kata kunci dobel diabaikan');
        $this->assertStringContainsString('Ya (01-05-2024)', $html);
        $this->assertStringContainsString('BTCLS 2024 (01-05-2024)', $html);
        $this->assertStringContainsString('<td>8</td>', $html, 'total SKP Andi 5+3');
        $this->assertStringContainsString('PERAWAT CICI', $html);

        // Filter: hanya yang punya salah satu
        $html = $this->actingAs($admin)->get(route('admin.perawat.export_custom', [
            'kolom' => ['nama'], 'pelatihan' => ['btcls', 'ppgd'], 'filter_pelatihan' => 'salah_satu',
        ]))->getContent();
        $this->assertStringContainsString('PERAWAT ANDI', $html);
        $this->assertStringContainsString('PERAWAT BUDI', $html);
        $this->assertStringNotContainsString('PERAWAT CICI', $html);

        // Filter: harus punya semua
        $html = $this->actingAs($admin)->get(route('admin.perawat.export_custom', [
            'kolom' => ['nama'], 'pelatihan' => ['btcls', 'ppi'], 'filter_pelatihan' => 'semua',
        ]))->getContent();
        $this->assertStringContainsString('PERAWAT ANDI', $html);
        $this->assertStringNotContainsString('PERAWAT BUDI', $html);
    }

    public function test_halaman_menampilkan_saran_pelatihan()
    {
        $admin = User::forceCreate(['name' => 'Admin Saran', 'email' => uniqid() . '@test.local', 'password' => bcrypt('x' . uniqid()), 'role' => 'admin', 'status_akun' => 'active']);
        $this->perawat('PERAWAT DEDI', [['BHD Awam', '2024-01-01', 1]]);
        $this->actingAs($admin)->get(route('admin.perawat.index'))->assertOk()
            ->assertSee('id="inputPelatihan"', false)->assertSee('id="daftarPelatihanPilih"', false)
            ->assertSee('BHD Awam')->assertSee('id="btnPreviewEkspor"', false);
    }

    public function test_preview_json_sama_dengan_isi_excel()
    {
        $admin = User::forceCreate(['name' => 'Admin Preview', 'email' => uniqid() . '@test.local', 'password' => bcrypt('x' . uniqid()), 'role' => 'admin', 'status_akun' => 'active']);
        $this->perawat('PERAWAT EKA', [['BTCLS Lanjut', '2025-03-03', 4]]);
        $this->perawat('PERAWAT FANI', [['Komunikasi Efektif', '2025-01-01', 1]]);

        $param = ['kolom' => ['nama', 'nik', 'pelatihan_skp'], 'pelatihan' => ['btcls'], 'filter_pelatihan' => 'salah_satu'];
        $json = $this->actingAs($admin)->getJson(route('admin.perawat.export_custom', $param + ['preview' => 1]))->assertOk()->json();

        $this->assertSame(['NO', 'NAMA LENGKAP', 'NIK', 'TOTAL SKP PELATIHAN', 'PELATIHAN: BTCLS'], $json['judul']);
        $this->assertSame(1, $json['total']);
        $this->assertSame('PERAWAT EKA', $json['baris'][0][1]);
        $this->assertSame('Ya (03-03-2025)', $json['baris'][0][4]);

        $excel = $this->actingAs($admin)->get(route('admin.perawat.export_custom', $param))->getContent();
        $this->assertStringContainsString('PERAWAT EKA', $excel);
        $this->assertStringNotContainsString('PERAWAT FANI', $excel);
        $this->assertStringContainsString($json['baris'][0][2] . '&nbsp;', $excel, 'NIK tetap diberi spasi agar tidak jadi angka');
    }
}
