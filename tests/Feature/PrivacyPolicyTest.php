<?php

namespace Tests\Feature;

use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class PrivacyPolicyTest extends TestCase
{
    use RefreshDatabase;

    public function test_privacy_policy_page_returns_success_status(): void
    {
        $response = $this->get(route('privacy-policy'));
        $response->assertStatus(200);
        $response->assertSee('policy-container');
        $response->assertSee('AmaTrung');
    }

    public function test_registering_without_privacy_policy_agreement_fails_validation(): void
    {
        $response = $this->post(route('register'), [
            'name' => 'Nguyễn Bệnh Nhân',
            'email' => 'patient@gmail.com',
            'phone' => '0987654321',
            'password' => 'password123',
            'password_confirmation' => 'password123',
            // 'agree_privacy' is not checked
        ]);

        $response->assertSessionHasErrors('agree_privacy');
        $this->assertDatabaseMissing('users', [
            'email' => 'patient@gmail.com'
        ]);
    }

    public function test_registering_with_privacy_policy_agreement_succeeds(): void
    {
        $response = $this->post(route('register'), [
            'name' => 'Nguyễn Bệnh Nhân',
            'email' => 'patient@gmail.com',
            'phone' => '0987654321',
            'password' => 'password123',
            'password_confirmation' => 'password123',
            'agree_privacy' => '1',
        ]);

        $response->assertRedirect('/dashboard');
        $this->assertDatabaseHas('users', [
            'email' => 'patient@gmail.com',
            'role' => 'user'
        ]);
    }
}
