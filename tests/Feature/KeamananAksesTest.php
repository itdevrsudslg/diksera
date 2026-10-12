<?php

namespace Tests\Feature;

use App\Models\ExamResult;
use App\Models\Form;
use App\Models\PengajuanOppe;
use App\Models\PengajuanSertifikat;
use App\Models\PerawatProfile;
use App\Models\User;
use Illuminate\Foundation\Testing\DatabaseTransactions;
use Tests\TestCase;

/**
 * Hak akses yang diperbaiki pada deep review.
 * Jalankan terhadap database uji:
 *   DB_DATABASE=diksera_logbook_test DB_USERNAME=root DB_PASSWORD= php artisan test --filter=KeamananAksesTest
 */
class KeamananAksesTest extends TestCase
{
    use DatabaseTransactions;

    private function user(string $nama, string $role = 'perawat', string $jabatan = 'pelaksana'): User
    {
        $u = User::forceCreate([
            'name' => $nama, 'email' => uniqid() . '@test.local',
            'password' => bcrypt(\Illuminate\Support\Str::random(32)), 'role' => $role, 'status_akun' => 'active',
        ]);
        if ($role === 'perawat') {
            PerawatProfile::forceCreate(['user_id' => $u->id, 'nama_lengkap' => $nama, 'jabatan_struktural' => $jabatan]);
        }
        return $u->fresh();
    }

    public function test_perawat_tidak_bisa_membuka_halaman_admin()
    {
        $perawat = $this->user('Perawat A');
        $this->actingAs($perawat)->get(route('admin.manajemen_akun.index'))->assertRedirect(route('dashboard'));
        $this->actingAs($perawat)->get(route('admin.pengajuan.index'))->assertRedirect(route('dashboard'));
        $this->actingAs($perawat)->get(route('dashboard.admin'))->assertRedirect(route('dashboard'));
    }

    public function test_oppe_hanya_penilai_bergiliran_yang_bisa_menyimpan()
    {
        $pemilik = $this->user('Pemilik OPPE');
        $peer = $this->user('Teman Sejawat');
        $karu = $this->user('Karu', 'perawat', 'kepala_ruangan');
        $lain = $this->user('Orang Lain');

        $oppe = PengajuanOppe::forceCreate([
            'user_id' => $pemilik->id, 'peer_id' => $peer->id, 'evaluator_id' => $karu->id,
            'periode' => '2026', 'status' => 'pending_peer', 'instrumen_penilaian' => [],
        ]);

        // Pemilik mencoba mengisi tahap teman sejawat untuk dirinya sendiri -> ditolak, status tetap
        $this->actingAs($pemilik)->post(route('perawat.oppe.store_evaluation', $oppe->id), ['action' => 'submit'])
            ->assertRedirect(route('dashboard'));
        $this->assertSame('pending_peer', $oppe->fresh()->status);

        // Orang lain tidak bisa melihat OPPE selesai milik orang lain
        $oppe->forceFill(['status' => 'completed'])->save();
        $this->actingAs($lain)->get(route('perawat.oppe.evaluate', $oppe->id))->assertRedirect(route('dashboard'));
        $this->actingAs($lain)->get(route('perawat.oppe.cetak', $oppe->id))->assertForbidden();

        // Karu (pihak terkait) tetap bisa melihat
        $this->actingAs($karu)->get(route('perawat.oppe.evaluate', $oppe->id))->assertOk();
    }

    public function test_spk_tidak_bisa_dicetak_orang_lain()
    {
        $pemilik = $this->user('Pemilik SPK');
        $lain = $this->user('Pengintip');
        $p = PengajuanSertifikat::forceCreate(['user_id' => $pemilik->id, 'status' => 'completed']);

        $this->actingAs($lain)->get(route('perawat.pengajuan.print_spk', $p->id))->assertForbidden();
        $this->actingAs($lain)->get(route('perawat.pengajuan.print_spkk', $p->id))->assertForbidden();
    }

    public function test_aksi_admin_pengajuan_hanya_post()
    {
        $admin = $this->user('Admin X', 'admin');
        $p = PengajuanSertifikat::forceCreate(['user_id' => $this->user('P')->id, 'status' => 'method_selected']);

        $this->actingAs($admin)->get('/admin/pengajuan/' . $p->id . '/complete')->assertStatus(405);
        $this->actingAs($admin)->get('/admin/pengajuan/' . $p->id . '/approve-score')->assertStatus(405);
        $this->assertSame('method_selected', $p->fresh()->status);
    }

    public function test_ujian_diblokir_setelah_tiga_kali_gagal()
    {
        $perawat = $this->user('Peserta Ujian');
        $form = Form::forceCreate([
            'judul' => 'Uji', 'slug' => 'uji-' . uniqid(), 'target_peserta' => 'semua',
            'waktu_mulai' => now()->subHour(), 'waktu_selesai' => now()->addHour(),
        ]);
        foreach ([40, 50, 60] as $n) {
            ExamResult::forceCreate(['user_id' => $perawat->id, 'form_id' => $form->id, 'total_nilai' => $n,
                'total_benar' => 1, 'total_salah' => 1, 'remidi' => true]);
        }

        $this->actingAs($perawat)->post(route('perawat.ujian.submit', $form->slug), ['answers' => []])
            ->assertRedirect(route('perawat.ujian.index'));
        $this->assertSame(3, ExamResult::where('user_id', $perawat->id)->count(), 'Percobaan ke-4 tidak boleh tersimpan');

        $this->actingAs($perawat)->get(route('perawat.ujian.kerjakan', $form->slug))->assertRedirect(route('perawat.ujian.index'));
    }

    public function test_admin_tidak_bisa_menghapus_diri_sendiri()
    {
        $admin = $this->user('Admin Y', 'admin');
        $this->actingAs($admin)->delete(route('admin.manajemen_akun.destroy', $admin->id))->assertRedirect();
        $this->assertNotNull(User::find($admin->id));
    }
}
