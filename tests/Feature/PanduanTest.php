<?php

namespace Tests\Feature;

use App\Models\PerawatProfile;
use App\Models\User;
use Illuminate\Foundation\Testing\DatabaseTransactions;
use Tests\TestCase;

/**
 * DB_DATABASE=diksera_logbook_test DB_USERNAME=root DB_PASSWORD= php artisan test --filter=PanduanTest
 */
class PanduanTest extends TestCase
{
    use DatabaseTransactions;

    private function user(string $role, ?string $jabatan = null): User
    {
        $u = User::forceCreate(['name' => ucfirst($role) . ' Panduan', 'email' => uniqid() . '@test.local',
            'password' => bcrypt(\Illuminate\Support\Str::random(32)), 'role' => $role, 'status_akun' => 'active']);
        if ($jabatan) {
            PerawatProfile::forceCreate(['user_id' => $u->id, 'nama_lengkap' => $u->name, 'jabatan_struktural' => $jabatan]);
        }
        return $u->fresh();
    }

    public function test_perawat_pelaksana()
    {
        $this->actingAs($this->user('perawat', 'pelaksana'))->get(route('panduan'))->assertOk()
            ->assertSee('Panduan Perawat &amp; Bidan', false)->assertSee('Riwayat Perubahan')
            ->assertDontSee('Panduan Kepala Ruangan')->assertDontSee('Panduan Kepala Seksi')
            ->assertDontSee('Panduan Admin')->assertDontSee('Panduan Pewawancara')->assertDontSee('name="sebagai"', false);
    }

    public function test_karu_melihat_panduan_karu_di_atas()
    {
        $html = $this->actingAs($this->user('perawat', 'kepala_ruangan'))->get(route('panduan'))->assertOk()
            ->assertSee('Panduan Kepala Ruangan (KARU)')->assertSee('Panduan Perawat &amp; Bidan', false)
            ->assertDontSee('Panduan Kepala Seksi')->assertDontSee('Panduan Admin')->getContent();
        $this->assertTrue(strpos($html, 'Panduan Kepala Ruangan (KARU)') < strpos($html, 'Panduan Perawat &amp; Bidan'), 'Panduan karu tampil sebelum panduan perawat');
    }

    public function test_kasi()
    {
        $this->actingAs($this->user('perawat', 'kepala_seksi'))->get(route('panduan'))->assertOk()
            ->assertSee('Panduan Kepala Seksi Keperawatan (KASI)')->assertSee('SUJIMAN')
            ->assertDontSee('Panduan Kepala Ruangan (KARU)')->assertDontSee('Panduan Admin');
    }

    public function test_pewawancara()
    {
        $this->actingAs($this->user('pewawancara'))->get(route('panduan'))->assertOk()
            ->assertSee('Panduan Pewawancara')->assertDontSee('Panduan Admin')->assertDontSee('Panduan Perawat &amp; Bidan', false);
    }

    public function test_admin_melihat_semua_dan_bisa_lihat_sebagai()
    {
        $admin = $this->user('admin');
        $this->actingAs($admin)->get(route('panduan'))->assertOk()
            ->assertSee('Panduan Admin')->assertSee('Panduan Kepala Ruangan (KARU)')->assertSee('Panduan Kepala Seksi Keperawatan (KASI)')
            ->assertSee('Panduan Pewawancara')->assertSee('name="sebagai"', false);

        $this->actingAs($admin)->get(route('panduan', ['sebagai' => 'karu']))->assertOk()
            ->assertSee('Panduan Kepala Ruangan (KARU)')->assertDontSee('Panduan Admin');
    }

    public function test_non_admin_tidak_bisa_lihat_sebagai_admin()
    {
        $this->actingAs($this->user('perawat', 'pelaksana'))->get(route('panduan', ['sebagai' => 'admin']))->assertOk()
            ->assertDontSee('Panduan Admin');
    }

    public function test_panduan_butuh_login()
    {
        $this->get(route('panduan'))->assertRedirect();
    }
}
