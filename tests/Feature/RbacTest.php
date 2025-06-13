<?php

namespace Tests\Feature;

use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class RbacTest extends TestCase
{
    use RefreshDatabase;

    public function test_admin_can_access_admin_dashboard(): void
    {
        $admin = User::factory()->asAdmin()->create();
        $response = $this->actingAs($admin)->get('/admin');
        $response->assertStatus(200);
        // For the simple text response we set up in routes/web.php:
        $response->assertSeeText('Admin Dashboard');
    }

    public function test_admin_can_access_user_dashboard(): void
    {
        $admin = User::factory()->asAdmin()->create();
        // Assuming 'dashboard' renders an Inertia component
        // If it were a simple text response, you'd assertSeeText or similar.
        // For Inertia, you might assert the component name if a more complex setup is available.
        // For now, a 200 status is the primary check.
        $response = $this->actingAs($admin)->get('/dashboard');
        $response->assertStatus(200);
    }

    public function test_customer_can_access_user_dashboard(): void
    {
        $customer = User::factory()->asCustomer()->create();
        $response = $this->actingAs($customer)->get('/dashboard');
        $response->assertStatus(200);
    }

    public function test_customer_cannot_access_admin_dashboard(): void
    {
        $customer = User::factory()->asCustomer()->create();
        $response = $this->actingAs($customer)->get('/admin');
        $response->assertStatus(403);
    }

    public function test_guest_user_cannot_access_user_dashboard(): void
    {
        $guest = User::factory()->asGuest()->create();
        $response = $this->actingAs($guest)->get('/dashboard');
        $response->assertStatus(403);
    }

    public function test_guest_user_cannot_access_admin_dashboard(): void
    {
        $guest = User::factory()->asGuest()->create();
        $response = $this->actingAs($guest)->get('/admin');
        $response->assertStatus(403);
    }

    public function test_unauthenticated_user_cannot_access_user_dashboard(): void
    {
        $response = $this->get('/dashboard');
        $response->assertRedirect('/login');
    }

    public function test_unauthenticated_user_cannot_access_admin_dashboard(): void
    {
        $response = $this->get('/admin');
        $response->assertRedirect('/login');
    }
}
