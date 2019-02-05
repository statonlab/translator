<?php

namespace Tests\Feature;

use App\User;
use Illuminate\Foundation\Testing\DatabaseTransactions;
use Tests\TestCase;
use Illuminate\Foundation\Testing\WithFaker;
use Illuminate\Foundation\Testing\RefreshDatabase;

class UsersAPITest extends TestCase
{
    use DatabaseTransactions;

    /** @test */
    public function testThatNonAdminsCantList()
    {
        // Non-admin user
        $user = $this->makeUser();
        $this->actingAs($user);

        $this->get('/web/users')->assertStatus(403);
    }

    /** @test */
    public function testThatAdminHaveAccessToListing()
    {
        // Admin user
        $admin = $this->makeAdminUser();
        $this->actingAs($admin);
        $response = $this->get('/web/users');
        $response->assertSuccessful();
        $response->assertJsonStructure([
            'data' => [
                [
                    'id',
                    'name',
                    'email',
                    'registered_at',
                    'role' => [
                        'name',
                    ],
                ],
            ],
        ]);
    }

    /** @test */
    public function testThatAdminsCanUpdateAUser()
    {
        $user = $this->makeUser();
        $admin = $this->makeAdminUser();

        $this->actingAs($admin);

        $this->patch("/web/user/$user->id", [
            'name' => 'New Name',
        ])->assertSuccessful();

        // Refresh the instance
        $user = User::find($user->id);
        $this->assertEquals('New Name', $user->name);
    }

    /** @test */
    public function testThatUsersCanUpdateTheirOwnInfo()
    {
        $user = $this->makeUser();

        $this->actingAs($user);

        $response = $this->patch("/web/user", [
            'name' => 'test name',
        ]);
        $response->assertSuccessful();
        $response->assertJson(['name' => 'test name']);
    }

    /** @test */
    public function testThatNotFoundResponseWorksWithPatch()
    {
        $user = $this->makeAdminUser();

        $this->actingAs($user);

        $response = $this->patch('/web/user/'.uniqid());
        $response->assertNotFound();
    }

    /** @test */
    public function testThatNotFoundResponseWorksWithUpdate()
    {
        $user = $this->makeAdminUser();

        $this->actingAs($user);

        $response = $this->put('/web/user/'.uniqid());
        $response->assertNotFound();
    }

    /** @test */
    public function testThatNotFoundResponseWorksWithShow()
    {
        $user = $this->makeAdminUser();

        $this->actingAs($user);

        $response = $this->get('/web/user/'.uniqid());
        $response->assertNotFound();
    }
}
