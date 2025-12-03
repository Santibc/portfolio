<?php

namespace Tests\Feature\Auth;

use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Spatie\Permission\Models\Role;
use Tests\TestCase;

class RoleAuthorizationTest extends TestCase
{
    use RefreshDatabase;

    protected function setUp(): void
    {
        parent::setUp();

        // Crear roles necesarios
        Role::create(['name' => 'Administrador']);
        Role::create(['name' => 'Supervisor']);
        Role::create(['name' => 'Agricultor']);
        Role::create(['name' => 'Inversionista']);
        Role::create(['name' => 'Vendedor']);
    }

    /** @test */
    public function admin_can_access_admin_dashboard()
    {
        $admin = User::factory()->create();
        $admin->assignRole('Administrador');

        $response = $this->actingAs($admin)->get(route('admin.dashboard'));

        $response->assertStatus(200);
    }

    /** @test */
    public function non_admin_cannot_access_admin_dashboard()
    {
        $inversionista = User::factory()->create();
        $inversionista->assignRole('Inversionista');

        $response = $this->actingAs($inversionista)->get(route('admin.dashboard'));

        $response->assertStatus(403); // Forbidden
    }

    /** @test */
    public function supervisor_can_access_supervisor_dashboard()
    {
        $supervisor = User::factory()->create();
        $supervisor->assignRole('Supervisor');

        $response = $this->actingAs($supervisor)->get(route('supervisor.dashboard'));

        $response->assertStatus(200);
    }

    /** @test */
    public function agricultor_can_access_agricultor_dashboard()
    {
        $agricultor = User::factory()->create();
        $agricultor->assignRole('Agricultor');

        $response = $this->actingAs($agricultor)->get(route('agricultor.dashboard'));

        $response->assertStatus(200);
    }

    /** @test */
    public function inversionista_can_access_inversionista_dashboard()
    {
        $inversionista = User::factory()->create();
        $inversionista->assignRole('Inversionista');

        $response = $this->actingAs($inversionista)->get(route('inversionista.dashboard'));

        $response->assertStatus(200);
    }

    /** @test */
    public function vendedor_can_access_vendedor_dashboard()
    {
        $vendedor = User::factory()->create();
        $vendedor->assignRole('Vendedor');

        $response = $this->actingAs($vendedor)->get(route('vendedor.dashboard'));

        $response->assertStatus(200);
    }

    /** @test */
    public function inversionista_with_kyc_pending_can_access_kyc_page()
    {
        $inversionista = User::factory()->create([
            'kyc_status' => 'pendiente',
        ]);
        $inversionista->assignRole('Inversionista');

        $response = $this->actingAs($inversionista)->get(route('inversionista.kyc.index'));

        $response->assertStatus(200);
    }

    /** @test */
    public function admin_redirects_to_admin_dashboard()
    {
        $admin = User::factory()->create();
        $admin->assignRole('Administrador');

        $response = $this->actingAs($admin)->get(route('dashboard'));

        $response->assertRedirect(route('admin.dashboard'));
    }

    /** @test */
    public function inversionista_redirects_to_inversionista_dashboard()
    {
        $inversionista = User::factory()->create();
        $inversionista->assignRole('Inversionista');

        $response = $this->actingAs($inversionista)->get(route('dashboard'));

        $response->assertRedirect(route('inversionista.dashboard'));
    }

    /** @test */
    public function agricultor_redirects_to_agricultor_dashboard()
    {
        $agricultor = User::factory()->create();
        $agricultor->assignRole('Agricultor');

        $response = $this->actingAs($agricultor)->get(route('dashboard'));

        $response->assertRedirect(route('agricultor.dashboard'));
    }

    /** @test */
    public function vendedor_redirects_to_vendedor_dashboard()
    {
        $vendedor = User::factory()->create();
        $vendedor->assignRole('Vendedor');

        $response = $this->actingAs($vendedor)->get(route('dashboard'));

        $response->assertRedirect(route('vendedor.dashboard'));
    }

    /** @test */
    public function supervisor_redirects_to_supervisor_dashboard()
    {
        $supervisor = User::factory()->create();
        $supervisor->assignRole('Supervisor');

        $response = $this->actingAs($supervisor)->get(route('dashboard'));

        $response->assertRedirect(route('supervisor.dashboard'));
    }

    /** @test */
    public function guest_cannot_access_protected_routes()
    {
        $response = $this->get(route('admin.dashboard'));
        $response->assertRedirect(route('login'));

        $response = $this->get(route('inversionista.dashboard'));
        $response->assertRedirect(route('login'));

        $response = $this->get(route('agricultor.dashboard'));
        $response->assertRedirect(route('login'));
    }

    /** @test */
    public function role_service_returns_correct_dashboard_route_for_each_role()
    {
        $roleService = app(\App\Services\Auth\RoleService::class);

        $admin = User::factory()->create();
        $admin->assignRole('Administrador');
        $this->assertEquals('admin.dashboard', $roleService->getDashboardRoute($admin));

        $supervisor = User::factory()->create();
        $supervisor->assignRole('Supervisor');
        $this->assertEquals('supervisor.dashboard', $roleService->getDashboardRoute($supervisor));

        $agricultor = User::factory()->create();
        $agricultor->assignRole('Agricultor');
        $this->assertEquals('agricultor.dashboard', $roleService->getDashboardRoute($agricultor));

        $inversionista = User::factory()->create();
        $inversionista->assignRole('Inversionista');
        $this->assertEquals('inversionista.dashboard', $roleService->getDashboardRoute($inversionista));

        $vendedor = User::factory()->create();
        $vendedor->assignRole('Vendedor');
        $this->assertEquals('vendedor.dashboard', $roleService->getDashboardRoute($vendedor));
    }
}
