<?php

namespace Tests\Feature;

use App\Models\User;
use App\Services\Penandatangan;
use Illuminate\Foundation\Testing\DatabaseTransactions;
use Tests\TestCase;

/**
 * Jalankan terhadap database uji, contoh:
 *   DB_DATABASE=diksera_logbook_test DB_USERNAME=root DB_PASSWORD= php artisan test --filter=PenandatanganTest
 */
class PenandatanganTest extends TestCase
{
    use DatabaseTransactions;

    protected function setUp(): void
    {
        parent::setUp();
        \DB::table('penandatangans')->delete();
        \DB::table('pengaturans')->delete();
        Penandatangan::lupakanCache();
    }

    public function test_nilai_bawaan_sama_dengan_sebelumnya()
    {
        $d = Penandatangan::untuk('direktur', '2026-01-01');
        $this->assertSame('dr. TONY WIDYANTO, Sp.OG (K)', $d->nama);
        $this->assertSame('19750714 200212 1 006', $d->nip);
        $this->assertSame('Pembina Tingkat I / IV b', $d->pangkat);

        // QR identik dengan string lama yang ditulis langsung di controller
        $url = 'http://x/verify/1?doc=rkk';
        $this->assertSame(
            "https://api.qrserver.com/v1/create-qr-code/?size=100x100&data=" . urlencode("Ketua Komite Keperawatan: BAMBANG WAHYU SETYONO. Validasi: " . $url),
            Penandatangan::qr('ketua_komite', $url, '2026-01-01')
        );
        $this->assertSame(
            "https://api.qrserver.com/v1/create-qr-code/?size=100x100&data=" . urlencode("Direktur UOBK RSUD SLG: dr. TONY WIDYANTO. Validasi: " . $url),
            Penandatangan::qr('direktur', $url, null)
        );
        $this->assertSame('Teman Sejawat', Penandatangan::label('oppe.ttd1'));
    }

    public function test_ganti_pejabat_tidak_mengubah_dokumen_lama()
    {
        $admin = User::forceCreate(['name' => 'Admin TTD', 'email' => uniqid() . '@test.local', 'password' => bcrypt('x' . uniqid()), 'role' => 'admin']);

        $this->actingAs($admin)->get(route('admin.penandatangan.index'))->assertOk()->assertSee('dr. TONY WIDYANTO');

        $this->actingAs($admin)->post(route('admin.penandatangan.simpan'), [
            'slot' => 'direktur', 'nama' => 'dr. DIREKTUR BARU, M.Kes', 'nip' => '19800101 200501 1 001',
            'pangkat' => 'Pembina Utama Muda / IV c', 'berlaku_mulai' => '2026-11-01',
        ])->assertRedirect();
        Penandatangan::lupakanCache();

        $this->assertSame('dr. TONY WIDYANTO, Sp.OG (K)', Penandatangan::untuk('direktur', '2026-10-31')->nama);
        $this->assertSame('dr. DIREKTUR BARU, M.Kes', Penandatangan::untuk('direktur', '2026-11-01')->nama);
        $this->assertSame('dr. DIREKTUR BARU', Penandatangan::untuk('direktur', '2027-05-01')->nama_qr);

        // Pejabat lain tidak terpengaruh
        $this->assertSame('HARIS LISTYAWAN, S.Kep.,Ns', Penandatangan::untuk('ketua_subkomite', '2027-01-01')->nama);

        // Label
        $this->actingAs($admin)->post(route('admin.penandatangan.label'), ['label' => ['oppe.ttd3' => 'Kepala Ruangan', 'oppe.ttd1' => '']])->assertRedirect();
        Penandatangan::lupakanCache();
        $this->assertSame('Kepala Ruangan', Penandatangan::label('oppe.ttd3'));
        $this->assertSame('Teman Sejawat', Penandatangan::label('oppe.ttd1'));

        // Perawat tidak boleh membuka
        $perawat = User::forceCreate(['name' => 'P', 'email' => uniqid() . '@test.local', 'password' => bcrypt('x' . uniqid()), 'role' => 'perawat']);
        $this->actingAs($perawat)->get(route('admin.penandatangan.index'))->assertRedirect(route('dashboard'));
    }
}
