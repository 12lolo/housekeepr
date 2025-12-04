<?php

namespace Tests\Feature\Admin;

use App\Models\User;
use App\Models\Hotel;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Mail;
use App\Mail\OwnerInviteMail;
use Tests\TestCase;

class OwnerManagementTest extends TestCase
{
    use RefreshDatabase;

    protected $admin;

    protected function setUp(): void
    {
        parent::setUp();

        // Create admin user
        $this->admin = User::factory()->create([
            'role' => 'admin',
            'status' => 'active',
        ]);
    }

    /** @test */
    public function admin_can_view_owners_list()
    {
        $response = $this->actingAs($this->admin)
            ->get(route('admin.owners.index'));

        $response->assertStatus(200);
        $response->assertViewIs('admin.owners.index');
    }

    /** @test */
    public function non_admin_cannot_access_owners_list()
    {
        $owner = User::factory()->create(['role' => 'owner']);

        $response = $this->actingAs($owner)
            ->get(route('admin.owners.index'));

        $response->assertStatus(403);
    }

    /** @test */
    public function admin_can_create_owner_with_hotel()
    {
        Mail::fake();

        $response = $this->actingAs($this->admin)
            ->post(route('admin.owners.store'), [
                'name' => 'Test Owner',
                'email' => 'owner@test.com',
                'hotel_name' => 'Test Hotel',
            ]);

        $response->assertRedirect(route('admin.owners.index'));
        $response->assertSessionHas('success');

        // Verify owner created
        $this->assertDatabaseHas('users', [
            'email' => 'owner@test.com',
            'role' => 'owner',
            'status' => 'pending',
        ]);

        // Verify hotel created
        $owner = User::where('email', 'owner@test.com')->first();
        $this->assertDatabaseHas('hotels', [
            'name' => 'Test Hotel',
            'owner_id' => $owner->id,
        ]);

        // Verify email sent
        Mail::assertSent(OwnerInviteMail::class, function ($mail) use ($owner) {
            return $mail->hasTo('owner@test.com');
        });
    }

    /** @test */
    public function admin_cannot_create_owner_with_duplicate_email()
    {
        User::factory()->create(['email' => 'existing@test.com']);

        $response = $this->actingAs($this->admin)
            ->post(route('admin.owners.store'), [
                'name' => 'Test Owner',
                'email' => 'existing@test.com',
                'hotel_name' => 'Test Hotel',
            ]);

        $response->assertSessionHasErrors('email');
    }

    /** @test */
    public function admin_cannot_create_owner_without_hotel_name()
    {
        $response = $this->actingAs($this->admin)
            ->post(route('admin.owners.store'), [
                'name' => 'Test Owner',
                'email' => 'owner@test.com',
                'hotel_name' => '',
            ]);

        $response->assertSessionHasErrors('hotel_name');
    }

    /** @test */
    public function admin_can_deactivate_owner()
    {
        $owner = User::factory()->create([
            'role' => 'owner',
            'status' => 'active',
        ]);

        $response = $this->actingAs($this->admin)
            ->post(route('admin.owners.deactivate', $owner));

        $response->assertRedirect();
        $response->assertSessionHas('success');

        $this->assertDatabaseHas('users', [
            'id' => $owner->id,
            'status' => 'deactivated',
        ]);
    }

    /** @test */
    public function admin_can_activate_owner()
    {
        $owner = User::factory()->create([
            'role' => 'owner',
            'status' => 'deactivated',
        ]);

        $response = $this->actingAs($this->admin)
            ->post(route('admin.owners.activate', $owner));

        $response->assertRedirect();
        $response->assertSessionHas('success');

        $this->assertDatabaseHas('users', [
            'id' => $owner->id,
            'status' => 'active',
        ]);
    }

    /** @test */
    public function admin_can_update_owner_and_hotel()
    {
        $owner = User::factory()->create(['role' => 'owner']);
        $hotel = Hotel::factory()->create(['owner_id' => $owner->id]);

        $response = $this->actingAs($this->admin)
            ->put(route('admin.owners.update', $owner), [
                'name' => 'Updated Name',
                'email' => $owner->email,
                'hotel_name' => 'Updated Hotel',
            ]);

        $response->assertRedirect(route('admin.owners.index'));

        $this->assertDatabaseHas('users', [
            'id' => $owner->id,
            'name' => 'Updated Name',
        ]);

        $this->assertDatabaseHas('hotels', [
            'id' => $hotel->id,
            'name' => 'Updated Hotel',
        ]);
    }

    /** @test */
    public function admin_can_delete_owner()
    {
        $owner = User::factory()->create(['role' => 'owner']);

        $response = $this->actingAs($this->admin)
            ->delete(route('admin.owners.destroy', $owner));

        $response->assertRedirect(route('admin.owners.index'));

        $this->assertDatabaseMissing('users', [
            'id' => $owner->id,
        ]);
    }

    /** @test */
    public function rollback_occurs_if_email_fails()
    {
        Mail::fake();
        Mail::shouldReceive('to')->andThrow(new \Exception('Email failed'));

        $response = $this->actingAs($this->admin)
            ->post(route('admin.owners.store'), [
                'name' => 'Test Owner',
                'email' => 'owner@test.com',
                'hotel_name' => 'Test Hotel',
            ]);

        // Should not create owner or hotel if email fails
        $this->assertDatabaseMissing('users', [
            'email' => 'owner@test.com',
        ]);

        $this->assertDatabaseMissing('hotels', [
            'name' => 'Test Hotel',
        ]);
    }
}
