<?php

namespace Tests\Feature;

use App\Platform;
use Illuminate\Foundation\Testing\DatabaseTransactions;
use Tests\TestCase;
use Illuminate\Foundation\Testing\WithFaker;
use Illuminate\Foundation\Testing\RefreshDatabase;

class PlatformsAPITest extends TestCase
{
    use DatabaseTransactions;

    /**
     * @test
     */
    public function testThatAdminsCanListPlatforms()
    {
        $admin = $this->makeAdminUser();

        $this->actingAs($admin);

        factory(Platform::class, 10)->create();

        $response = $this->get('/web/platforms');

        $response->assertSuccessful();
        $response->assertJsonStructure([
            'data' => [
                [
                    'name',
                ],
            ],
        ]);
    }

    /** @test */
    public function testThatNonAdminsCantListPlatforms()
    {
        $user = $this->makeUser();
        $this->actingAs($user);

        factory(Platform::class, 10)->create();

        $this->get('/web/platforms')->assertForbidden();
    }

    /** @test */
    public function testThatAdminsCanViewAPlatform()
    {
        $admin = $this->makeAdminUser();

        $this->actingAs($admin);

        $platform = factory(Platform::class)->create();

        $response = $this->get('/web/platform/'.$platform->id);
        $response->assertSuccessful();
        $response->assertJsonStructure([
            'name',
            'languages',
        ]);
    }

    /** @test */
    public function testThatNonAdminsCantViewPlatform()
    {
        $user = $this->makeUser();
        $this->actingAs($user);

        $platform = factory(Platform::class)->create();

        $this->get('/web/platform/'.$platform->id)->assertForbidden();
    }

    /** @test */
    public function testThatAdminsCanCreate()
    {
        $admin = $this->makeAdminUser();
        $this->actingAs($admin);

        $this->post('/web/platforms', ['name' => uniqid()])
            ->assertSuccessful()
            ->assertJsonStructure([
                'name',
                'languages',
            ]);
    }

    /** @test */
    public function testThatNonAdminsCantCreate()
    {
        $user = $this->makeUser();
        $this->actingAs($user);

        $this->post('/web/platforms', ['name' => uniqid()])->assertForbidden();
    }

    /** @test */
    public function testThatAdminsCanDeleteAPlatform()
    {
        $admin = $this->makeAdminUser();
        $this->actingAs($admin);

        $platform = factory(Platform::class)->create();
        $this->delete('/web/platform/'.$platform->id)->assertSuccessful();
    }

    /** @test */
    public function testThatNonAdminsCantDeleteAPlatform()
    {
        $user = $this->makeUser();
        $this->actingAs($user);

        $platform = factory(Platform::class)->create();
        $this->delete('/web/platform/'.$platform->id)->assertForbidden();
    }

    /** @test */
    public function testThatAdminCanUpdateAPlatform()
    {
        $admin = $this->makeAdminUser();
        $this->actingAs($admin);

        $platform = factory(Platform::class)->create();
        $this->put('/web/platform/'.$platform->id, [
            'name' => uniqid(),
        ])->assertSuccessful()->assertJsonStructure([
            'name',
            'languages',
        ]);
    }

    /** @test */
    public function testThatNonAdminCantUpdateAPlatform()
    {
        $user = $this->makeUser();
        $this->actingAs($user);

        $platform = factory(Platform::class)->create();
        $this->put('/web/platform/'.$platform->id, [
            'name' => uniqid(),
        ])->assertForbidden();
    }
}
