<?php

namespace Tests\Feature;

use App\Models\User;
use Illuminate\Foundation\Testing\DatabaseTransactions;
use Tests\TestCase;

/**
 * DB_DATABASE=diksera_logbook_test DB_USERNAME=root DB_PASSWORD= php artisan test --filter=PanduanTest
 */
class PanduanTest extends TestCase
{
    use DatabaseTransactions;

    private function user(string $role): User
    {
        return User::forceCreate(['name' => ucfirst($role) . ' Panduan', 'email' => uniqid() . '@test.local',
            'password' => bcrypt(\Illuminate\Support\Str::random(32)), 'role' => $role, 'status_akun' => 'active']);
    }

    public function test_bagian_panduan_sesuai_peran()
    {
        $this->actingAs($this->user('perawat'))->get(route('panduan'))->assertOk()
            ->assertSee('Panduan Perawat &amp; Bidan', false)->assertSee('Logbook Harian')
            ->assertSee('Riwayat Perubahan')->assertDontSee('Panduan Admin')->assertDontSee('Panduan Pewawancara');

        $this->actingAs($this->user('pewawancara'))->get(route('panduan'))->assertOk()
            ->assertSee('Panduan Pewawancara')->assertDontSee('Panduan Admin')->assertDontSee('Panduan Perawat &amp; Bidan', false);

        $this->actingAs($this->user('admin'))->get(route('panduan'))->assertOk()
            ->assertSee('Panduan Admin')->assertSee('Panduan Pewawancara')->assertSee('Penanda Tangan');
    }

    public function test_panduan_butuh_login()
    {
        $this->get(route('panduan'))->assertRedirect();
    }
}
