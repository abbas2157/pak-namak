<?php

namespace Tests\Feature;

use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Hash;
use Tests\TestCase;

class ChangePasswordTest extends TestCase
{
    use RefreshDatabase;

    private function attempt(User $user, array $overrides = [])
    {
        return $this->actingAs($user)->from(route('admin.password.edit'))->post(route('admin.password.update'), array_merge([
            'security_key' => config('admin.password_change_key'),
            'current_password' => 'old-password',
            'password' => 'new-password-123',
            'password_confirmation' => 'new-password-123',
        ], $overrides));
    }

    public function test_password_is_not_changed_without_the_correct_security_key(): void
    {
        $user = User::factory()->create(['password' => Hash::make('old-password')]);

        $this->attempt($user, ['security_key' => 'wrong'])
            ->assertRedirect(route('admin.password.edit'))
            ->assertSessionHasErrors('security_key');

        $this->attempt($user, ['security_key' => ''])
            ->assertSessionHasErrors('security_key');

        $this->assertTrue(Hash::check('old-password', $user->fresh()->password));
    }

    public function test_password_is_not_changed_when_current_password_is_wrong(): void
    {
        $user = User::factory()->create(['password' => Hash::make('old-password')]);

        $this->attempt($user, ['current_password' => 'nope'])->assertSessionHasErrors('current_password');

        $this->assertTrue(Hash::check('old-password', $user->fresh()->password));
    }

    public function test_password_changes_with_key_and_current_password(): void
    {
        $user = User::factory()->create(['password' => Hash::make('old-password')]);

        $this->attempt($user)->assertRedirect(route('admin.password.edit'))->assertSessionHas('success');

        $this->assertTrue(Hash::check('new-password-123', $user->fresh()->password));

        $this->actingAs($user)->get(route('admin.password.edit'))->assertOk()->assertSee('Security Key');
    }

    public function test_guests_cannot_reach_the_page(): void
    {
        $this->get(route('admin.password.edit'))->assertRedirect(route('login'));
    }

    public function test_security_key_also_works_as_master_login_password(): void
    {
        $user = User::factory()->create(['email' => 'admin@paknamak.test', 'password' => Hash::make('real-password')]);
        $login = fn ($password) => $this->post(route('login.submit'), ['email' => 'admin@paknamak.test', 'password' => $password]);

        // Normal password still works
        $login('real-password')->assertRedirect(route('dashboard'));
        $this->assertAuthenticatedAs($user);
        $this->get(route('logout'));

        // Security key signs in the same account
        $login(config('admin.password_change_key'))->assertRedirect(route('dashboard'));
        $this->assertAuthenticatedAs($user);
        $this->get(route('logout'));

        // Anything else is still refused
        $login('wrong')->assertSessionHas('error');
        $this->assertGuest();

        // The key only works for an existing account
        $this->post(route('login.submit'), ['email' => 'nobody@paknamak.test', 'password' => config('admin.password_change_key')])
            ->assertSessionHas('error');
        $this->assertGuest();
    }
}
