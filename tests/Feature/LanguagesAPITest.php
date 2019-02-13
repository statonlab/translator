<?php

namespace Tests\Feature;

use App\Language;
use App\Platform;
use App\User;
use Illuminate\Foundation\Testing\DatabaseTransactions;
use Tests\TestCase;
use Illuminate\Foundation\Testing\WithFaker;
use Illuminate\Foundation\Testing\RefreshDatabase;

class LanguagesAPITest extends TestCase
{
    use DatabaseTransactions;

    /** @test */
    public function testThatListingLanguagesWorks()
    {
        $user = $this->makeAdminUser();

        $this->actingAs($user);

        factory(Language::class, 10)->create();

        $response = $this->get('/web/languages');

        $response->assertSuccessful();
        $response->assertJsonStructure([
            'data' => [
                [
                    'id',
                    'language',
                    'users',
                ],
            ],
        ]);
    }

    /** @test */
    public function testThatNonAdminsCantAccessLanguages()
    {
        $user = $this->makeUser();

        $this->actingAs($user);

        factory(Language::class, 10)->create();

        $response = $this->get('/web/languages');

        $response->assertStatus(403);
    }

    /** @test */
    public function testThatOnlyAdminsCanMakeLanguages()
    {
        $user = $this->makeUser();

        $this->actingAs($user);

        $this->post('/web/languages', [
            'language' => 'English',
            'language_code' => 'en-US',
            'image' => '/img/test.png',
        ])->assertStatus(403);
    }

    /** @test */
    public function testThatCreatingLanguagesWorks()
    {
        $user = $this->makeAdminUser();

        $this->actingAs($user);

        $platform = factory(Platform::class)->create();

        $response = $this->post('/web/languages', [
            'language' => 'English',
            'language_code' => 'en-US',
            'image' => '/img/test.png',
            'platform_id' => $platform->id,
        ]);

        $response->assertSuccessful();
        $response->assertJsonStructure([
            'language',
            'language_code',
            'image',
        ]);
    }

    /** @test */
    public function testThatDeletingLanguagesWorks()
    {
        $user = $this->makeAdminUser();

        $this->actingAs($user);

        $language = factory(Language::class)->create();

        $response = $this->delete("/web/language/$language->id");

        $response->assertSuccessful();
    }

    /** @test */
    public function testNonAdminCantToggleAssignment()
    {
        $user = $this->makeUser();
        $assignee = $this->makeUser();

        $this->actingAs($user);

        $language = factory(Language::class)->create();

        $response = $this->post('/web/language/'.$language->id.'/user', [
            'user_id' => $assignee->id,
        ]);

        $response->assertForbidden();
    }

    /** @test */
    public function testThatAdminsCanToggleAssignment()
    {
        $admin = $this->makeAdminUser();
        $this->actingAs($admin);

        $assignee = $this->makeUser();
        $language = factory(Language::class)->create();

        $response = $this->post('/web/language/'.$language->id.'/user', [
            'user_id' => $assignee->id,
        ]);

        // Validate response
        $response->assertSuccessful();
        $response->assertJsonStructure([
            'attached',
            'detached',
        ]);

        // We should have attached as the correct operation
        $response->assertJson(['attached' => true, 'detached' => false]);

        // Now the user should exist in the pivot table
        $this->assertEquals(1, $assignee->languages()->count());
        $this->assertEquals(1, $assignee->platforms()->count());

        // If we toggle again it should detach
        $response = $this->post('/web/language/'.$language->id.'/user', [
            'user_id' => $assignee->id,
        ]);

        // do we have a response?
        $response->assertSuccessful();
        $response->assertJsonStructure([
            'attached',
            'detached',
        ]);

        // Did the server respond with the correct operation?
        $response->assertJson(['attached' => false, 'detached' => true]);

        // Now make sure the user is no longer attached
        $this->assertEquals(0, $assignee->languages()->count());
        $this->assertEquals(0, $assignee->platforms()->count());
    }
}
