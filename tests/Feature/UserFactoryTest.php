<?php

namespace Tests\Feature;

use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class UserFactoryTest extends TestCase
{
    use RefreshDatabase;

    public function test_it_creates_an_admin_user()
    {
        $admin = User::factory()->create([
            'role' => 'admin',
            'account_status' => 'active',
        ]);

        $this->assertDatabaseHas('users', [
            'id' => $admin->id,
            'role' => 'admin',
            'account_status' => 'active',
        ]);
    }

    public function test_it_creates_an_editor_user()
    {
        $editor = User::factory()->create([
            'role' => 'editor',
            'account_status' => 'active',
        ]);

        $this->assertDatabaseHas('users', [
            'id' => $editor->id,
            'role' => 'editor',
        ]);
    }

    public function test_it_creates_a_viewer_user()
    {
        $viewer = User::factory()->create([
            'role' => 'viewer',
            'account_status' => 'active',
        ]);

        $this->assertDatabaseHas('users', [
            'id' => $viewer->id,
            'role' => 'viewer',
        ]);
    }
}
