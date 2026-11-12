<?php

namespace Tests\Feature;

use App\Models\User;
use Tests\TestCase;

class DashboardLayoutTest extends TestCase
{
    public function test_mahasiswa_dashboard_layout_does_not_have_blank_toolbar_space(): void
    {
        $mahasiswa = User::factory()->mahasiswa()->make();

        $response = $this->actingAs($mahasiswa)->get(route('mahasiswa.beranda'));

        $response->assertStatus(200);
        $response->assertDontSee('toolbar-enabled', false);
        $response->assertDontSee('toolbar-fixed', false);
        $response->assertDontSee('--kt-toolbar-height', false);
        $response->assertSee('id="kt_header"', false);
        $response->assertSee('id="kt_aside"', false);
        $response->assertSee('id="kt_content"', false);
    }

    public function test_dosen_dashboard_layout_does_not_have_blank_toolbar_space(): void
    {
        $dosen = User::factory()->dosen()->make();

        $response = $this->actingAs($dosen)->get(route('dosen.beranda'));

        $response->assertStatus(200);
        $response->assertDontSee('toolbar-enabled', false);
        $response->assertDontSee('toolbar-fixed', false);
        $response->assertDontSee('--kt-toolbar-height', false);
        $response->assertSee('id="kt_header"', false);
        $response->assertSee('id="kt_aside"', false);
        $response->assertSee('id="kt_content"', false);
    }

    public function test_gkm_dashboard_layout_does_not_have_blank_toolbar_space(): void
    {
        $gkm = User::where('role', 'gkm')->first() ?? User::factory()->create(['role' => 'gkm']);

        $response = $this->actingAs($gkm)->get(route('gkm.beranda'));

        $response->assertStatus(200);
        $response->assertDontSee('toolbar-enabled', false);
        $response->assertDontSee('toolbar-fixed', false);
        $response->assertDontSee('--kt-toolbar-height', false);
        $response->assertSee('id="kt_header"', false);
        $response->assertSee('id="kt_aside"', false);
        $response->assertSee('id="kt_content"', false);
    }

    public function test_dewan_skripsi_dashboard_layout_does_not_have_blank_toolbar_space(): void
    {
        $dewan = User::where('role', 'dewan_skripsi')->first() ?? User::factory()->dewanSkripsi()->create();

        $response = $this->actingAs($dewan)->get(route('dewan-skripsi.dashboard'));

        $response->assertStatus(200);
        $response->assertDontSee('toolbar-enabled', false);
        $response->assertDontSee('toolbar-fixed', false);
        $response->assertDontSee('--kt-toolbar-height', false);
        $response->assertSee('id="kt_header"', false);
        $response->assertSee('id="kt_aside"', false);
        $response->assertSee('id="kt_content"', false);
    }

    public function test_admin_dashboard_layout_does_not_have_blank_toolbar_space(): void
    {
        $admin = User::where('role', 'admin')->first() ?? User::factory()->create(['role' => 'admin']);

        $response = $this->actingAs($admin)->get(route('admin.beranda'));

        $response->assertStatus(200);
        $response->assertDontSee('toolbar-enabled', false);
        $response->assertDontSee('toolbar-fixed', false);
        $response->assertDontSee('--kt-toolbar-height', false);
        $response->assertSee('id="kt_header"', false);
        $response->assertSee('id="kt_aside"', false);
        $response->assertSee('id="kt_content"', false);
    }
}
