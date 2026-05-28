<?php

namespace Tests\Feature;

use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Hash;
use Tests\TestCase;

class AccountManagementSecurityTest extends TestCase
{
    use RefreshDatabase;

    protected function setUp(): void
    {
        parent::setUp();
        $this->seed(\Database\Seeders\RoleAndPermissionSeeder::class);
    }

    public function test_admin_cannot_edit_update_or_delete_user_role_account(): void
    {
        $admin = User::factory()->create(['role' => 'admin']);
        $userAccount = User::factory()->create(['role' => 'user', 'is_active' => true]);

        // 1. Chặn Edit view
        $response = $this->actingAs($admin)->get(route('admin.users.edit', $userAccount));
        $response->assertRedirect(route('admin.users.index'));
        $response->assertSessionHas('error', 'Không được phép thay đổi tài khoản của khách hàng.');

        // 2. Chặn Update action
        $response = $this->actingAs($admin)->put(route('admin.users.update', $userAccount), [
            'name' => 'New Name',
            'email' => 'newemail@gmail.com',
            'role' => 'staff',
        ]);
        $response->assertRedirect(route('admin.users.index'));
        $response->assertSessionHas('error', 'Không được phép thay đổi tài khoản của khách hàng.');

        // 3. Chặn Toggle status (Lock/Unlock)
        $response = $this->actingAs($admin)->patch(route('admin.users.toggle-status', $userAccount));
        $response->assertRedirect(route('admin.users.index'));
        $response->assertSessionHas('error', 'Không được phép khóa/mở khóa tài khoản của khách hàng.');
        $this->assertTrue($userAccount->fresh()->is_active);

        // 4. Chặn Reset password
        $response = $this->actingAs($admin)->patch(route('admin.users.reset-password', $userAccount));
        $response->assertRedirect(route('admin.users.index'));
        $response->assertSessionHas('error', 'Không được phép đặt lại mật khẩu tài khoản của khách hàng.');
    }

    public function test_admin_can_manage_staff_account_and_reset_password_to_amatrung123(): void
    {
        $admin = User::factory()->create(['role' => 'admin']);
        $staffAccount = User::factory()->create(['role' => 'staff', 'is_active' => true]);

        // 1. Vào edit view của staff được
        $response = $this->actingAs($admin)->get(route('admin.users.edit', $staffAccount));
        $response->assertStatus(200);

        // 2. Update staff được
        $response = $this->actingAs($admin)->put(route('admin.users.update', $staffAccount), [
            'name' => 'Updated Staff Name',
            'email' => $staffAccount->email,
            'role' => 'staff',
        ]);
        $response->assertRedirect(route('admin.users.index'));
        $this->assertEquals('Updated Staff Name', $staffAccount->fresh()->name);

        // 3. Khóa staff được
        $response = $this->actingAs($admin)->patch(route('admin.users.toggle-status', $staffAccount));
        $response->assertRedirect();
        $this->assertFalse($staffAccount->fresh()->is_active);

        // 4. Reset password của staff thành amatrung@123
        $response = $this->actingAs($admin)->patch(route('admin.users.reset-password', $staffAccount));
        $response->assertRedirect();
        $response->assertSessionHas('success', "Đã đặt lại mật khẩu thành 'amatrung@123'.");
        
        $this->assertTrue(Hash::check('amatrung@123', $staffAccount->fresh()->password));
    }

    public function test_admin_cannot_create_account_with_user_role(): void
    {
        $admin = User::factory()->create(['role' => 'admin']);

        $response = $this->actingAs($admin)->post(route('admin.users.store'), [
            'name' => 'New Customer',
            'email' => 'customer@gmail.com',
            'password' => '12345678',
            'role' => 'user', // should trigger validation error
        ]);

        $response->assertSessionHasErrors('role');
    }
}
